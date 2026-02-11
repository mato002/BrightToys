@extends('layouts.admin')

@section('page_title', 'Voting Topics')

@section('content')
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-lg font-semibold text-slate-900">Voting Topics</h1>
        <a href="{{ route('admin.voting-topics.create') }}"
           class="inline-flex items-center px-3 py-2 rounded-lg bg-emerald-600 text-white text-xs font-semibold hover:bg-emerald-700">
            New Topic
        </a>
    </div>

    <div class="bg-white border border-slate-100 rounded-lg shadow-sm">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-slate-500">Title</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-slate-500">Status</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-slate-500">Window</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-slate-500">Votes</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-slate-500">Result (Yes / No)</th>
                    <th class="px-4 py-2 text-right text-xs font-semibold text-slate-500">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topics as $topic)
                    @php
                        $summary = $summaries[$topic->id] ?? ['yes_pct' => 0, 'no_pct' => 0];
                    @endphp
                    <tr class="border-t border-slate-100">
                        <td class="px-4 py-2">
                            <a href="{{ route('admin.voting-topics.show', $topic) }}" class="text-slate-900 font-semibold hover:underline">
                                {{ $topic->title }}
                            </a>
                            <div class="text-[11px] text-slate-500">
                                Created {{ $topic->created_at->format('d M Y') }}
                            </div>
                        </td>
                        <td class="px-4 py-2">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] {{ $topic->status === 'open' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : ($topic->status === 'closed' ? 'bg-slate-50 text-slate-700 border border-slate-100' : 'bg-amber-50 text-amber-700 border border-amber-100') }}">
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
                            {{ $topic->votes_count }} votes
                        </td>
                        <td class="px-4 py-2 text-xs text-slate-700">
                            {{ $summary['yes_pct'] ?? 0 }}% yes /
                            {{ $summary['no_pct'] ?? 0 }}% no
                        </td>
                        <td class="px-4 py-2 text-right text-xs">
                            <a href="{{ route('admin.voting-topics.show', $topic) }}"
                               class="text-emerald-700 hover:underline">
                                View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-xs text-slate-500">
                            No voting topics have been created yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="px-4 py-3 border-t border-slate-100">
            {{ $topics->links() }}
        </div>
    </div>
@endsection

