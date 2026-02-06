@extends('layouts.admin')

@section('page_title', 'Activity Log Details')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
        <div>
            <h1 class="text-lg font-semibold">Activity Log Details</h1>
            <p class="text-xs text-slate-500">Detailed information about this activity.</p>
        </div>
        <a href="{{ route('admin.activity-logs.index') }}" class="text-sm text-slate-600 hover:text-slate-800">
            Back to list
        </a>
    </div>

    <div class="grid md:grid-cols-2 gap-6">
        <div class="bg-white border border-slate-100 rounded-lg p-4">
            <h2 class="text-sm font-semibold text-slate-900 mb-4">Activity Information</h2>
            <dl class="space-y-3 text-sm">
                <div>
                    <dt class="text-xs text-slate-500 mb-1">Action</dt>
                    <dd>
                        <span class="inline-flex items-center rounded-full px-2 py-1 text-[10px] font-medium bg-blue-100 text-blue-700">
                            {{ str_replace('_', ' ', $activityLog->action) }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-500 mb-1">Timestamp</dt>
                    <dd class="text-slate-700">{{ $activityLog->created_at->format('d M Y, H:i:s') }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-500 mb-1">User</dt>
                    <dd class="text-slate-700">
                        {{ $activityLog->user ? $activityLog->user->name : 'System' }}
                    </dd>
                    @if($activityLog->user)
                        <dd class="text-xs text-slate-500">{{ $activityLog->user->email }}</dd>
                    @endif
                </div>
                @if($activityLog->subject)
                <div>
                    <dt class="text-xs text-slate-500 mb-1">Subject</dt>
                    <dd class="text-slate-700">
                        {{ class_basename($activityLog->subject_type) }} #{{ $activityLog->subject_id }}
                    </dd>
                </div>
                @endif
            </dl>
        </div>

        <div class="bg-white border border-slate-100 rounded-lg p-4">
            <h2 class="text-sm font-semibold text-slate-900 mb-4">Technical Details</h2>
            <dl class="space-y-3 text-sm">
                <div>
                    <dt class="text-xs text-slate-500 mb-1">IP Address</dt>
                    <dd class="text-slate-700 font-mono text-xs">{{ $activityLog->ip_address ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-500 mb-1">User Agent</dt>
                    <dd class="text-slate-700 text-xs break-all">{{ $activityLog->user_agent ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-500 mb-1">Subject Type</dt>
                    <dd class="text-slate-700 text-xs">{{ $activityLog->subject_type ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-500 mb-1">Subject ID</dt>
                    <dd class="text-slate-700 text-xs">{{ $activityLog->subject_id ?? '—' }}</dd>
                </div>
            </dl>
        </div>
    </div>

    @if($activityLog->details && count($activityLog->details) > 0)
    <div class="mt-6 bg-white border border-slate-100 rounded-lg p-4">
        <h2 class="text-sm font-semibold text-slate-900 mb-4">Additional Details</h2>
        <div class="bg-slate-50 rounded-lg p-4">
            <pre class="text-xs text-slate-700 whitespace-pre-wrap">{{ json_encode($activityLog->details, JSON_PRETTY_PRINT) }}</pre>
        </div>
    </div>
    @endif
@endsection
