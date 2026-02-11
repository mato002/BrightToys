@extends('layouts.partner')

@section('page_title', $topic->title)

@section('partner_content')
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('partner.voting.index') }}" class="text-slate-500 hover:text-slate-700 text-sm">
                ← Back to voting
            </a>
            <h1 class="text-2xl font-bold text-slate-900">{{ $topic->title }}</h1>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px]
                {{ $topic->status === 'open' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' :
                   ($topic->status === 'closed' ? 'bg-slate-50 text-slate-700 border border-slate-100' :
                    'bg-amber-50 text-amber-700 border border-amber-100') }}">
                {{ ucfirst($topic->status) }}
            </span>
        </div>
    </div>

    <div class="grid md:grid-cols-3 gap-6">
        <div class="md:col-span-2 space-y-4">
            <div class="bg-white border border-slate-100 rounded-lg p-4">
                <h2 class="text-sm font-semibold text-slate-900 mb-2">Question / Description</h2>
                <p class="text-sm text-slate-700 whitespace-pre-line">
                    {{ $topic->description ?: 'No description provided.' }}
                </p>
            </div>

            <div class="bg-white border border-slate-100 rounded-lg p-4">
                <h2 class="text-sm font-semibold text-slate-900 mb-3">Your Vote</h2>

                <p class="text-[11px] text-slate-500 mb-3">
                    Your current ownership is <span class="font-semibold text-slate-900">{{ number_format($currentOwnershipPct, 2) }}%</span>.
                    This is the weight that will be applied to your vote.
                </p>

                @if(! $isOpen)
                    <p class="text-xs text-slate-500 mb-3">
                        This topic is not currently open for voting. You can still see your recorded vote below.
                    </p>
                @endif

                <form method="POST" action="{{ route('partner.voting.store', $topic) }}" class="space-y-4">
                    @csrf

                    <div class="space-y-2">
                        <label class="inline-flex items-center gap-2 text-sm">
                            <input type="radio" name="choice" value="yes"
                                   {{ old('choice', $existingVote->choice ?? '') === 'yes' ? 'checked' : '' }}
                                   {{ $isOpen ? '' : 'disabled' }}>
                            <span>Yes – I support this decision</span>
                        </label>
                        <label class="inline-flex items-center gap-2 text-sm">
                            <input type="radio" name="choice" value="no"
                                   {{ old('choice', $existingVote->choice ?? '') === 'no' ? 'checked' : '' }}
                                   {{ $isOpen ? '' : 'disabled' }}>
                            <span>No – I do not support this decision</span>
                        </label>
                        <label class="inline-flex items-center gap-2 text-sm">
                            <input type="radio" name="choice" value="abstain"
                                   {{ old('choice', $existingVote->choice ?? '') === 'abstain' ? 'checked' : '' }}
                                   {{ $isOpen ? '' : 'disabled' }}>
                            <span>Abstain</span>
                        </label>
                    </div>

                    @error('choice')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                        <div class="text-[11px] text-slate-500">
                            @if($existingVote)
                                Last updated: {{ $existingVote->cast_at?->format('d M Y H:i') ?? '—' }}
                            @else
                                You have not yet cast a vote on this topic.
                            @endif
                        </div>
                        @if($isOpen)
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 rounded-lg bg-amber-500 text-white text-xs font-semibold hover:bg-amber-600">
                                Save Vote
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <div class="space-y-4">
            <div class="bg-white border border-slate-100 rounded-lg p-4">
                <h2 class="text-sm font-semibold text-slate-900 mb-3">Voting Window</h2>
                <dl class="space-y-2 text-xs text-slate-700">
                    <div>
                        <dt class="text-slate-500">Opens At</dt>
                        <dd>{{ $topic->opens_at?->format('d M Y H:i') ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Closes At</dt>
                        <dd>{{ $topic->closes_at?->format('d M Y H:i') ?? '—' }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
@endsection

