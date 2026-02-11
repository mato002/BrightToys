@extends('layouts.admin')

@section('page_title', $votingTopic->title)

@section('content')
    <div class="mb-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.voting-topics.index') }}" class="text-slate-500 hover:text-slate-700 text-sm">
                ← Back to topics
            </a>
            <h1 class="text-lg font-semibold text-slate-900">{{ $votingTopic->title }}</h1>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px]
                {{ $votingTopic->status === 'open' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' :
                   ($votingTopic->status === 'closed' ? 'bg-slate-50 text-slate-700 border border-slate-100' :
                    'bg-amber-50 text-amber-700 border border-amber-100') }}">
                {{ ucfirst($votingTopic->status) }}
            </span>
        </div>
        <div class="flex items-center gap-2">
            @if($votingTopic->status === 'draft')
                <form action="{{ route('admin.voting-topics.open', $votingTopic) }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="px-3 py-1.5 rounded-lg bg-emerald-600 text-white text-xs font-semibold hover:bg-emerald-700">
                        Open Voting
                    </button>
                </form>
            @elseif($votingTopic->status === 'open')
                <form action="{{ route('admin.voting-topics.close', $votingTopic) }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="px-3 py-1.5 rounded-lg bg-slate-700 text-white text-xs font-semibold hover:bg-slate-800">
                        Close Voting
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="grid md:grid-cols-3 gap-6">
        <div class="md:col-span-2 space-y-4">
            <div class="bg-white border border-slate-100 rounded-lg p-4">
                <h2 class="text-sm font-semibold text-slate-900 mb-2">Question / Description</h2>
                <p class="text-sm text-slate-700 whitespace-pre-line">
                    {{ $votingTopic->description ?: 'No description provided.' }}
                </p>
            </div>

            <div class="bg-white border border-slate-100 rounded-lg p-4">
                <h2 class="text-sm font-semibold text-slate-900 mb-3">Individual Votes</h2>
                <table class="min-w-full text-xs">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-3 py-2 text-left font-semibold text-slate-500">Partner</th>
                            <th class="px-3 py-2 text-left font-semibold text-slate-500">Choice</th>
                            <th class="px-3 py-2 text-left font-semibold text-slate-500">Weight %</th>
                            <th class="px-3 py-2 text-left font-semibold text-slate-500">Cast At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($votes as $vote)
                            <tr class="border-t border-slate-100">
                                <td class="px-3 py-2">
                                    {{ $vote->partner?->name ?? '—' }}
                                </td>
                                <td class="px-3 py-2 capitalize">
                                    {{ $vote->choice }}
                                </td>
                                <td class="px-3 py-2">
                                    {{ number_format($vote->weight_percentage, 2) }}%
                                </td>
                                <td class="px-3 py-2">
                                    {{ $vote->cast_at?->format('d M Y H:i') ?? '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-3 py-4 text-center text-slate-500">
                                    No votes have been cast yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="space-y-4">
            <div class="bg-white border border-slate-100 rounded-lg p-4">
                <h2 class="text-sm font-semibold text-slate-900 mb-3">Voting Window</h2>
                <dl class="space-y-2 text-xs text-slate-700">
                    <div>
                        <dt class="text-slate-500">Opens At</dt>
                        <dd>{{ $votingTopic->opens_at?->format('d M Y H:i') ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Closes At</dt>
                        <dd>{{ $votingTopic->closes_at?->format('d M Y H:i') ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Created By</dt>
                        <dd>{{ $votingTopic->creator?->name ?? '—' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="bg-white border border-slate-100 rounded-lg p-4">
                <h2 class="text-sm font-semibold text-slate-900 mb-3">Weighted Result</h2>
                <dl class="space-y-2 text-xs text-slate-700">
                    <div>
                        <dt class="text-slate-500">Total Voting Weight</dt>
                        <dd>{{ number_format($summary['total_weight'], 3) }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Yes</dt>
                        <dd class="font-semibold text-emerald-700">{{ $summary['yes_pct'] }}%</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">No</dt>
                        <dd class="font-semibold text-red-600">{{ $summary['no_pct'] }}%</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Abstain</dt>
                        <dd class="font-semibold text-slate-700">{{ $summary['abstain_pct'] }}%</dd>
                    </div>
                </dl>
                <p class="mt-3 text-[11px] text-slate-500">
                    Voting weight is calculated from each partner's ownership percentage at the moment they vote.
                </p>
            </div>
        </div>
    </div>
@endsection

