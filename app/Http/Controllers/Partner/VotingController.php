<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use App\Models\VotingTopic;
use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VotingController extends Controller
{
    /**
     * List open voting topics available to this partner.
     */
    public function index()
    {
        $user = Auth::user();
        $partner = $user->partner;

        if (! $partner) {
            abort(403, 'You are not associated with a partner account.');
        }

        $topics = VotingTopic::open()
            ->withCount('votes')
            ->orderBy('opens_at', 'desc')
            ->get();

        // Attach whether this partner has already voted on each topic
        $existingVotes = Vote::where('partner_id', $partner->id)
            ->get()
            ->keyBy('voting_topic_id');

        return view('partner.voting.index', [
            'partner' => $partner,
            'topics' => $topics,
            'votesByTopic' => $existingVotes,
        ]);
    }

    /**
     * Show a single voting topic and this partner's vote (if any).
     */
    public function show(VotingTopic $votingTopic)
    {
        $user = Auth::user();
        $partner = $user->partner;

        if (! $partner) {
            abort(403, 'You are not associated with a partner account.');
        }

        // Only allow viewing open topics (or already-voted topics for this partner)
        $votingTopic->load('votes');

        $now = now();
        $isOpen = $votingTopic->status === 'open'
            && (! $votingTopic->closes_at || $votingTopic->closes_at->gte($now));

        $existingVote = $votingTopic->votes()
            ->where('partner_id', $partner->id)
            ->first();

        $currentOwnershipPct = $partner->getCurrentOwnershipPercentage();

        return view('partner.voting.show', [
            'topic' => $votingTopic,
            'partner' => $partner,
            'currentOwnershipPct' => $currentOwnershipPct,
            'existingVote' => $existingVote,
            'isOpen' => $isOpen,
        ]);
    }

    /**
    * Cast or update a vote on a topic using current ownership as weight.
    */
    public function store(Request $request, VotingTopic $votingTopic)
    {
        $user = Auth::user();
        $partner = $user->partner;

        if (! $partner) {
            abort(403, 'You are not associated with a partner account.');
        }

        // Topic must be open
        $now = now();
        if (
            $votingTopic->status !== 'open' ||
            ($votingTopic->closes_at && $votingTopic->closes_at->lt($now))
        ) {
            return redirect()
                ->route('partner.voting.show', $votingTopic)
                ->with('error', 'This voting topic is not currently open for voting.');
        }

        $data = $request->validate([
            'choice' => ['required', 'in:yes,no,abstain'],
        ]);

        $weightPct = $partner->getCurrentOwnershipPercentage();
        $weightValue = $weightPct / 100;

        $vote = Vote::updateOrCreate(
            [
                'voting_topic_id' => $votingTopic->id,
                'partner_id' => $partner->id,
            ],
            [
                'choice' => $data['choice'],
                'weight_percentage' => $weightPct,
                'weight_value' => $weightValue,
                'cast_at' => $now,
                'created_by' => $user->id,
            ]
        );

        return redirect()->route('partner.voting.show', $votingTopic)
            ->with('success', 'Your vote has been recorded.');
    }
}
