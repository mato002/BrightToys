@extends('layouts.partner')

@section('page_title', 'Voting')

@section('partner_content')
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Voting</h1>
                <p class="text-sm text-slate-600 mt-1">
                    Participate in partnership decisions. Your voting power is based on your current ownership percentage.
                </p>
            </div>
        </div>
    </div>

    <div class="bg-white border border-slate-100 rounded-2xl shadow-sm overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-slate-500">Topic</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-slate-500">Status</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-slate-500">Window</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-slate-500">Your Vote</th>
                    <th class="px-4 py-2 text-right text-xs font-semibold text-slate-500">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topics as $topic)
                    @php
                        $vote = $votesByTopic[$topic->id] ?? null;
                    @endphp
                    <tr class="border-t border-slate-100">
                        <td class="px-4 py-2">
                            <div class="font-semibold text-slate-900">{{ $topic->title }}</div>
                            @if($topic->description)
                                <div class="text-[11px] text-slate-500 line-clamp-2">
                                    {{ $topic->description }}
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-2">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px]
                                {{ $topic->status === 'open' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' :
                                   ($topic->status === 'closed' ? 'bg-slate-50 text-slate-700 border border-slate-100' :
                                    'bg-amber-50 text-amber-700 border border-amber-100') }}">
                                {{ ucfirst($topic->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-2 text-xs text-slate-600">
                            @if($topic->opens_at)
                                <div>Opens: {{ $topic->opens_at->format('d M Y H:i') }}</div>
                            @endif
                            @if($topic->closes_at)
                                <div>Closes: {{ $topic->closes_at->format('d M Y H:i') }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-2 text-xs text-slate-700">
                            @if($vote)
                                <span class="capitalize">{{ $vote->choice }}</span>
                                <span class="text-[11px] text-slate-500">({{ number_format($vote->weight_percentage, 2) }}%)</span>
                            @else
                                <span class="text-[11px] text-slate-500">Not yet voted</span>
                            @endif
                        </td>
                        <td class="px-4 py-2 text-right text-xs">
                            <a href="{{ route('partner.voting.show', $topic) }}"
                               class="inline-flex items-center px-3 py-1.5 rounded-lg bg-amber-500 text-white font-semibold hover:bg-amber-600">
                                View / Vote
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-xs text-slate-500">
                            There are no voting topics open at the moment.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection

