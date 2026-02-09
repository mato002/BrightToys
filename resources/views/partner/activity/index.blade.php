@extends('layouts.partner')

@section('page_title', 'Activity & Approvals')

@section('partner_content')
    <div class="mb-6">
        <div class="flex items-center justify-between mb-2">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Activity & Audit Log</h1>
                <p class="text-sm text-slate-600 mt-1">
                    Complete audit trail of all financial and administrative actions for full transparency.
                </p>
            </div>
        </div>
    </div>

    @php
        $totalActions = $logs->count();
        $byAction = $logs->groupBy('action');
        $recentActivity = $logs->take(5);
    @endphp

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-semibold text-blue-700 uppercase tracking-wide">Total Actions</p>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <p class="text-2xl font-bold text-blue-900">{{ $totalActions }}</p>
            <p class="text-xs text-blue-700 mt-1">Logged events</p>
        </div>

        <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 border border-emerald-200 rounded-xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-semibold text-emerald-700 uppercase tracking-wide">Financial Actions</p>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <p class="text-2xl font-bold text-emerald-900">
                {{ $logs->filter(fn($log) => str_contains(strtolower($log->action), 'financial') || str_contains(strtolower($log->action), 'contribution') || str_contains(strtolower($log->action), 'expense'))->count() }}
            </p>
            <p class="text-xs text-emerald-700 mt-1">Financial events</p>
        </div>

        <div class="bg-gradient-to-br from-amber-50 to-amber-100 border border-amber-200 rounded-xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-semibold text-amber-700 uppercase tracking-wide">Approvals</p>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-600" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M22 4L12 14.01l-3-3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <p class="text-2xl font-bold text-amber-900">
                {{ $logs->filter(fn($log) => str_contains(strtolower($log->action), 'approve'))->count() }}
            </p>
            <p class="text-xs text-amber-700 mt-1">Approved items</p>
        </div>

        <div class="bg-gradient-to-br from-slate-50 to-slate-100 border border-slate-200 rounded-xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-semibold text-slate-700 uppercase tracking-wide">This Month</p>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-600" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M8 2v4M16 2v4M3 10h18M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <p class="text-2xl font-bold text-slate-900">
                {{ $logs->filter(fn($log) => $log->created_at->isCurrentMonth())->count() }}
            </p>
            <p class="text-xs text-slate-600 mt-1">Current month</p>
        </div>
    </div>

    {{-- Activity Log Table --}}
    <div class="bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Timestamp</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Action</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Subject</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Details</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">IP Address</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-100">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-slate-900">
                                    {{ optional($log->created_at)->format('M d, Y') }}
                                </div>
                                <div class="text-xs text-slate-500">
                                    {{ optional($log->created_at)->format('h:i:s A') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <div class="h-8 w-8 rounded-full bg-amber-100 flex items-center justify-center">
                                        <span class="text-xs font-semibold text-amber-700">
                                            {{ strtoupper(substr($log->user->name ?? 'S', 0, 1)) }}
                                        </span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-slate-900">
                                            {{ $log->user->name ?? 'System' }}
                                        </div>
                                        @if($log->user)
                                            <div class="text-xs text-slate-500">{{ $log->user->email }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                    {{ str_contains(strtolower($log->action), 'approve') ? 'bg-emerald-100 text-emerald-800' :
                                       (str_contains(strtolower($log->action), 'reject') || str_contains(strtolower($log->action), 'delete') ? 'bg-red-100 text-red-800' :
                                        (str_contains(strtolower($log->action), 'create') ? 'bg-blue-100 text-blue-800' :
                                         'bg-slate-100 text-slate-800')) }}">
                                    {{ ucfirst($log->action) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-slate-900">
                                    @if($log->subject_type)
                                        {{ class_basename($log->subject_type) }}
                                        @if($log->subject_id)
                                            #{{ $log->subject_id }}
                                        @endif
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-slate-600 max-w-md">
                                    @if(is_array($log->details))
                                        <div class="space-y-1">
                                            @foreach(array_slice($log->details, 0, 3) as $key => $value)
                                                <div class="text-xs">
                                                    <span class="font-medium text-slate-700">{{ $key }}:</span>
                                                    <span class="text-slate-600">{{ is_array($value) ? json_encode($value) : $value }}</span>
                                                </div>
                                            @endforeach
                                            @if(count($log->details) > 3)
                                                <div class="text-xs text-slate-400">+{{ count($log->details) - 3 }} more</div>
                                            @endif
                                        </div>
                                    @else
                                        {{ \Illuminate\Support\Str::limit((string)$log->details, 100) }}
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-xs font-mono text-slate-600">
                                    {{ $log->ip_address ?? '-' }}
                                </div>
                                @if($log->user_agent)
                                    <div class="text-xs text-slate-400 mt-1 max-w-xs truncate" title="{{ $log->user_agent }}">
                                        {{ \Illuminate\Support\Str::limit($log->user_agent, 30) }}
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-slate-300 mb-3" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <p class="text-sm font-medium text-slate-900 mb-1">No activity recorded</p>
                                <p class="text-xs text-slate-500">Activity logs will appear here as actions are performed</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($logs->hasPages())
        <div class="mt-6">
            {{ $logs->appends(request()->query())->links() }}
        </div>
    @endif
@endsection
