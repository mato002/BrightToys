<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VotingTopic;
use App\Models\Vote;
use App\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VotingTopicController extends Controller
{
    /**
     * Only Chairman (and optionally Super Admin) can manage voting topics.
     */
    protected function ensureChairperson(): void
    {
        $user = Auth::user();

        if ($user->isSuperAdmin()) {
            return;
        }

        if (! $user->hasAdminRole('chairman')) {
            abort(403, 'Only the Chairperson can manage voting topics.');
        }
    }

    public function index()
    {
        $this->ensureChairperson();

        $topics = VotingTopic::withCount('votes')
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        // Precompute simple weighted summaries for listing (yes/no/abstain)
        $summaries = [];
        foreach ($topics as $topic) {
            $votes = $topic->votes;
            $totalWeight = (float) $votes->sum('weight_value');

            $yesWeight = (float) $votes->where('choice', 'yes')->sum('weight_value');
            $noWeight = (float) $votes->where('choice', 'no')->sum('weight_value');
            $abstainWeight = (float) $votes->where('choice', 'abstain')->sum('weight_value');

            $summaries[$topic->id] = [
                'total_weight' => $totalWeight,
                'yes_pct' => $totalWeight > 0 ? round(($yesWeight / $totalWeight) * 100, 1) : 0.0,
                'no_pct' => $totalWeight > 0 ? round(($noWeight / $totalWeight) * 100, 1) : 0.0,
                'abstain_pct' => $totalWeight > 0 ? round(($abstainWeight / $totalWeight) * 100, 1) : 0.0,
            ];
        }

        return view('admin.voting-topics.index', compact('topics', 'summaries'));
    }

    public function create()
    {
        $this->ensureChairperson();

        return view('admin.voting-topics.create');
    }

    public function store(Request $request)
    {
        $this->ensureChairperson();

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:draft,open'],
            'opens_at' => ['nullable', 'date'],
            'closes_at' => ['nullable', 'date', 'after:opens_at'],
        ]);

        $topic = VotingTopic::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'],
            'opens_at' => $validated['opens_at'] ?? null,
            'closes_at' => $validated['closes_at'] ?? null,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('admin.voting-topics.show', $topic)
            ->with('success', 'Voting topic created successfully.');
    }

    public function show(VotingTopic $votingTopic)
    {
        $this->ensureChairperson();

        $votingTopic->load(['creator', 'votes.partner']);

        $votes = $votingTopic->votes;
        $totalWeight = (float) $votes->sum('weight_value');
        $yesWeight = (float) $votes->where('choice', 'yes')->sum('weight_value');
        $noWeight = (float) $votes->where('choice', 'no')->sum('weight_value');
        $abstainWeight = (float) $votes->where('choice', 'abstain')->sum('weight_value');

        $summary = [
            'total_weight' => $totalWeight,
            'yes_pct' => $totalWeight > 0 ? round(($yesWeight / $totalWeight) * 100, 2) : 0.0,
            'no_pct' => $totalWeight > 0 ? round(($noWeight / $totalWeight) * 100, 2) : 0.0,
            'abstain_pct' => $totalWeight > 0 ? round(($abstainWeight / $totalWeight) * 100, 2) : 0.0,
        ];

        return view('admin.voting-topics.show', compact('votingTopic', 'votes', 'summary'));
    }

    public function edit(VotingTopic $votingTopic)
    {
        $this->ensureChairperson();

        if ($votingTopic->status !== 'draft') {
            abort(403, 'Only draft topics can be edited.');
        }

        return view('admin.voting-topics.edit', compact('votingTopic'));
    }

    public function update(Request $request, VotingTopic $votingTopic)
    {
        $this->ensureChairperson();

        if ($votingTopic->status !== 'draft') {
            abort(403, 'Only draft topics can be edited.');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'opens_at' => ['nullable', 'date'],
            'closes_at' => ['nullable', 'date', 'after:opens_at'],
        ]);

        $votingTopic->update($validated);

        return redirect()->route('admin.voting-topics.show', $votingTopic)
            ->with('success', 'Voting topic updated successfully.');
    }

    public function destroy(VotingTopic $votingTopic)
    {
        $this->ensureChairperson();

        if ($votingTopic->status === 'open') {
            abort(403, 'Open topics cannot be deleted. Close the topic first.');
        }

        $votingTopic->delete();

        return redirect()->route('admin.voting-topics.index')
            ->with('success', 'Voting topic deleted successfully.');
    }

    public function open(VotingTopic $votingTopic)
    {
        $this->ensureChairperson();

        $votingTopic->update([
            'status' => 'open',
            'opens_at' => $votingTopic->opens_at ?? now(),
        ]);

        return redirect()->route('admin.voting-topics.show', $votingTopic)
            ->with('success', 'Voting topic opened.');
    }

    public function close(VotingTopic $votingTopic)
    {
        $this->ensureChairperson();

        $votingTopic->update([
            'status' => 'closed',
            'closes_at' => $votingTopic->closes_at ?? now(),
        ]);

        return redirect()->route('admin.voting-topics.show', $votingTopic)
            ->with('success', 'Voting topic closed.');
    }
}
