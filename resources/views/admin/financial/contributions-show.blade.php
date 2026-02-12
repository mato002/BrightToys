@extends('layouts.admin')

@section('page_title', 'Contribution Details')

@section('content')
    {{-- Success/Error Messages --}}
    @if(session('success'))
        <div class="mb-4 rounded-lg bg-emerald-50 border border-emerald-200 p-3 text-sm text-emerald-800">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 p-3 text-sm text-red-800">
            {{ session('error') }}
        </div>
    @endif

    <div class="mb-6 flex flex-col md:flex-row md:items-end md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('admin.financial.contributions') }}" class="text-slate-500 hover:text-slate-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M19 12H5M12 19l-7-7 7-7" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-emerald-500">Financial</p>
            </div>
            <h2 class="text-xl md:text-2xl font-semibold text-slate-900 tracking-tight">Contribution Details</h2>
            <p class="text-xs md:text-sm text-slate-500 mt-1 max-w-xl">
                View and manage partner contribution, withdrawal, or profit distribution.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.financial.contributions') }}" 
               class="inline-flex items-center justify-center text-xs font-medium text-slate-600 hover:text-slate-800">
                Back to Contributions
            </a>
        </div>
    </div>

    <div class="grid md:grid-cols-2 gap-6 mb-6">
        {{-- Contribution Information --}}
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm">
            <div class="px-4 py-3 border-b border-slate-100">
                <h2 class="text-sm font-semibold text-slate-900">Contribution Information</h2>
            </div>
            <div class="p-4">
                <dl class="space-y-3 text-xs">
                    <div>
                        <dt class="text-[11px] text-slate-500 mb-1 uppercase tracking-wide">Type</dt>
                        <dd>
                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[10px] font-medium border
                                {{ $contribution->type === 'contribution' ? 'bg-blue-50 text-blue-700 border-blue-200' : 
                                   ($contribution->type === 'withdrawal' ? 'bg-red-50 text-red-700 border-red-200' : 'bg-emerald-50 text-emerald-700 border-emerald-200') }}">
                                {{ ucfirst(str_replace('_', ' ', $contribution->type)) }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-[11px] text-slate-500 mb-1 uppercase tracking-wide">Partner</dt>
                        <dd class="text-slate-900 font-medium">{{ $contribution->partner->name ?? 'N/A' }}</dd>
                    </div>
                    @if($contribution->fund_type)
                    <div>
                        <dt class="text-[11px] text-slate-500 mb-1 uppercase tracking-wide">Fund Type</dt>
                        <dd class="text-slate-700 capitalize">{{ $contribution->fund_type }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-[11px] text-slate-500 mb-1 uppercase tracking-wide">Amount</dt>
                        <dd class="font-semibold text-lg
                            {{ $contribution->type === 'withdrawal' ? 'text-red-600' : 'text-emerald-600' }}">
                            {{ $contribution->currency }} {{ number_format($contribution->amount, 2) }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-[11px] text-slate-500 mb-1 uppercase tracking-wide">Date</dt>
                        <dd class="text-slate-700">{{ $contribution->contributed_at->format('d M Y') }}</dd>
                    </div>
                    @if($contribution->reference)
                    <div>
                        <dt class="text-[11px] text-slate-500 mb-1 uppercase tracking-wide">Reference</dt>
                        <dd class="text-slate-700 font-mono text-[11px]">{{ $contribution->reference }}</dd>
                    </div>
                    @endif
                    @if($contribution->notes)
                    <div>
                        <dt class="text-[11px] text-slate-500 mb-1 uppercase tracking-wide">Notes</dt>
                        <dd class="text-slate-700">{{ $contribution->notes }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-[11px] text-slate-500 mb-1 uppercase tracking-wide">Status</dt>
                        <dd>
                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[10px] font-medium border
                                {{ $contribution->status === 'approved' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 
                                   ($contribution->status === 'rejected' ? 'bg-red-50 text-red-700 border-red-200' : 'bg-amber-50 text-amber-700 border-amber-200') }}">
                                {{ ucfirst($contribution->status) }}
                            </span>
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        {{-- Approval Information --}}
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm">
            <div class="px-4 py-3 border-b border-slate-100">
                <h2 class="text-sm font-semibold text-slate-900">Approval Information</h2>
            </div>
            <div class="p-4">
                <dl class="space-y-3 text-xs">
                    <div>
                        <dt class="text-[11px] text-slate-500 mb-1 uppercase tracking-wide">Created By</dt>
                        <dd class="text-slate-900 font-medium">{{ $contribution->creator->name ?? 'N/A' }}</dd>
                        <dd class="text-[10px] text-slate-500 mt-0.5">{{ $contribution->created_at->format('d M Y, H:i') }}</dd>
                    </div>
                    @if($contribution->approver)
                    <div>
                        <dt class="text-[11px] text-slate-500 mb-1 uppercase tracking-wide">Approved By</dt>
                        <dd class="text-slate-900 font-medium">{{ $contribution->approver->name }}</dd>
                        <dd class="text-[10px] text-slate-500 mt-0.5">{{ $contribution->approved_at->format('d M Y, H:i') }}</dd>
                    </div>
                    @endif
                    @if($contribution->archiver)
                    <div>
                        <dt class="text-[11px] text-slate-500 mb-1 uppercase tracking-wide">Archived By</dt>
                        <dd class="text-slate-900 font-medium">{{ $contribution->archiver->name }}</dd>
                        <dd class="text-[10px] text-slate-500 mt-0.5">{{ $contribution->archived_at->format('d M Y, H:i') }}</dd>
                    </div>
                    @endif
                </dl>

                @if($contribution->status === 'pending')
                <div class="mt-4 pt-4 border-t border-slate-200">
                    <div class="space-y-2">
                        <form action="{{ route('admin.financial.contributions.approve', $contribution) }}" method="POST">
                            @csrf
                            <div class="mb-2">
                                <label class="block text-[11px] font-semibold mb-1 text-slate-700">Comment (optional)</label>
                                <textarea name="comment" rows="2"
                                          class="border border-slate-200 rounded-md w-full px-3 py-2 text-xs focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500"
                                          placeholder="Add approval comment..."></textarea>
                            </div>
                            <button type="submit" 
                                    class="w-full bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-semibold px-4 py-2 rounded-md shadow-sm transition-colors">
                                Approve Contribution
                            </button>
                        </form>
                        <form action="{{ route('admin.financial.contributions.reject', $contribution) }}" method="POST">
                            @csrf
                            <div class="mb-2">
                                <label class="block text-[11px] font-semibold mb-1 text-slate-700">Rejection Reason</label>
                                <textarea name="rejection_reason" rows="2"
                                          class="border border-slate-200 rounded-md w-full px-3 py-2 text-xs focus:ring-1 focus:ring-red-500 focus:border-red-500"
                                          placeholder="Reason for rejection..."></textarea>
                            </div>
                            <button type="submit" 
                                    onclick="return confirm('Are you sure you want to reject this contribution?')"
                                    class="w-full bg-red-500 hover:bg-red-600 text-white text-xs font-semibold px-4 py-2 rounded-md shadow-sm transition-colors">
                                Reject Contribution
                            </button>
                        </form>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Activity Log --}}
    @php
        $activityLogs = \App\Models\ActivityLog::where('subject_type', \App\Models\PartnerContribution::class)
            ->where('subject_id', $contribution->id)
            ->with('user')
            ->latest()
            ->take(20)
            ->get();
    @endphp

    @if($activityLogs->count() > 0)
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm">
        <div class="px-4 py-3 border-b border-slate-100">
            <h2 class="text-sm font-semibold text-slate-900">Activity Log</h2>
        </div>
        <div class="p-4">
            <div class="space-y-2 text-xs">
                @foreach($activityLogs as $log)
                    <div class="flex items-start gap-3 p-3 rounded-lg border border-slate-200 hover:border-slate-300 hover:bg-slate-50 transition-colors">
                        <div class="flex-shrink-0 mt-0.5">
                            @php
                                $iconColor = match(true) {
                                    str_contains($log->action, 'created') => 'text-emerald-600 bg-emerald-50',
                                    str_contains($log->action, 'approved') => 'text-blue-600 bg-blue-50',
                                    str_contains($log->action, 'rejected') => 'text-red-600 bg-red-50',
                                    str_contains($log->action, 'archived') => 'text-slate-600 bg-slate-50',
                                    default => 'text-slate-600 bg-slate-50'
                                };
                            @endphp
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg {{ $iconColor }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2z"/>
                                    <path d="M12 6v6l4 2"/>
                                </svg>
                            </span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex-1">
                                    <p class="font-semibold text-slate-900">
                                        {{ ucfirst(str_replace(['contribution_', '_'], ['', ' '], $log->action)) }}
                                    </p>
                                    <p class="text-[11px] text-slate-500 mt-0.5">
                                        @if($log->user)
                                            <span class="font-medium">{{ $log->user->name }}</span>
                                        @else
                                            <span class="text-slate-400">System</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="flex-shrink-0 text-right">
                                    <p class="text-[10px] text-slate-400">
                                        {{ $log->created_at->format('M d, Y') }}
                                    </p>
                                    <p class="text-[10px] text-slate-400">
                                        {{ $log->created_at->format('H:i:s') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
@endsection
