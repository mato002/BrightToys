@extends('layouts.admin')

@section('page_title', 'Activity Logs')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
        <div>
            <h1 class="text-lg font-semibold">Activity Logs</h1>
            <p class="text-xs text-slate-500">Complete audit trail of all system activities.</p>
        </div>
    </div>

    <form method="GET" class="mb-4 bg-white border border-slate-100 rounded-lg p-3 text-xs grid md:grid-cols-4 gap-3">
        <input type="text" name="action" value="{{ request('action') }}" placeholder="Search action..."
               class="border border-slate-200 rounded px-3 py-2 text-sm">
        <select name="user_id" class="border border-slate-200 rounded px-3 py-2 text-sm">
            <option value="">All Users</option>
            @foreach(\App\Models\User::where('is_admin', true)->get() as $user)
                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                    {{ $user->name }}
                </option>
            @endforeach
        </select>
        <input type="date" name="from" value="{{ request('from') }}" placeholder="From date"
               class="border border-slate-200 rounded px-3 py-2 text-sm">
        <div class="flex gap-2">
            <input type="date" name="to" value="{{ request('to') }}" placeholder="To date"
                   class="border border-slate-200 rounded px-3 py-2 text-sm flex-1">
            <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2 rounded text-xs">
                Filter
            </button>
        </div>
    </form>

    <div class="bg-white border border-slate-100 rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">Timestamp</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">User</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">Action</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">Subject</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">IP Address</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 text-xs text-slate-600">
                                {{ $log->created_at->format('d M Y, H:i:s') }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-sm font-medium text-slate-900">
                                    {{ $log->user ? $log->user->name : 'System' }}
                                </div>
                                @if($log->user)
                                    <div class="text-xs text-slate-500">{{ $log->user->email }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full px-2 py-1 text-[10px] font-medium bg-blue-100 text-blue-700">
                                    {{ str_replace('_', ' ', $log->action) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-600">
                                @if($log->subject)
                                    {{ class_basename($log->subject_type) }} #{{ $log->subject_id }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-500">{{ $log->ip_address ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.activity-logs.show', $log) }}"
                                   class="text-emerald-600 hover:text-emerald-700 text-xs">View Details</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-slate-500 text-sm">
                                No activity logs found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $logs->links() }}
    </div>
@endsection
