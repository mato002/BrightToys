@extends('layouts.admin')

@section('title', 'Notifications & Transparency')

@section('content')
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
            <div class="flex items-center gap-2">
                <button type="button"
                        onclick="window.history.back()"
                        class="inline-flex h-8 w-8 items-center justify-center rounded-full border border-slate-200 text-slate-500 hover:bg-slate-100">
                    <span class="sr-only">Go back</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>
                <div>
                    <h1 class="text-xl font-semibold text-slate-900">Notifications & Transparency</h1>
                    <p class="mt-1 text-sm text-slate-500">
                        Overview of all system notifications, delivery channels, and audit of who was notified about what.
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}"
                   class="inline-flex items-center rounded-md border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                    Export CSV
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
            <div class="bg-white border border-slate-200 rounded-xl p-4">
                <h2 class="text-sm font-semibold text-slate-900 mb-2">Notification Types</h2>
                <ul class="text-xs text-slate-600 space-y-1">
                    <li>· Contribution confirmations & approvals</li>
                    <li>· Financial record approval requests & approvals</li>
                    <li>· Loan / finance reminders (planned)</li>
                    <li>· Project updates (planned)</li>
                    <li>· Monthly summary reports (auto-generated)</li>
                </ul>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-4">
                <h2 class="text-sm font-semibold text-slate-900 mb-2">Delivery Channels</h2>
                <ul class="text-xs text-slate-600 space-y-1">
                    <li><span class="font-semibold">Primary:</span> Email via <code>NotificationService</code></li>
                    <li><span class="font-semibold">Secondary:</span> In-app notifications (partner console)</li>
                    <li><span class="font-semibold">Future:</span> Bulk SMS for OTPs and time-sensitive alerts</li>
                </ul>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-4">
                <h2 class="text-sm font-semibold text-slate-900 mb-2">Member Transparency</h2>
                <p class="text-xs text-slate-600">
                    Partners can see their own notification history inside the system via the
                    <span class="font-medium">Notifications</span> page in the partner console. This list below
                    aggregates all notifications across users for audit.
                </p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 mb-4">
            <form method="GET" class="p-4 grid grid-cols-1 md:grid-cols-4 gap-3 text-sm">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">Type</label>
                    <input type="text" name="type" value="{{ request('type') }}"
                           placeholder="e.g. contribution_approved"
                           class="block w-full rounded-md border-slate-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">Channel</label>
                    <select name="channel" class="block w-full rounded-md border-slate-300 text-sm">
                        <option value="">All</option>
                        <option value="in_app" @selected(request('channel') === 'in_app')>In-app</option>
                        <option value="email" @selected(request('channel') === 'email')>Email</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">User</label>
                    <select name="user_id" class="block w-full rounded-md border-slate-300 text-sm">
                        <option value="">All</option>
                        @foreach($adminUsers as $admin)
                            <option value="{{ $admin->id }}" @selected(request('user_id') == $admin->id)>
                                {{ $admin->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit"
                            class="inline-flex items-center rounded-md bg-emerald-600 px-3 py-2 text-xs font-semibold text-white hover:bg-emerald-700">
                        Filter
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Timestamp</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">User</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Type</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Channel</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Title</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Message</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($notifications as $notification)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 text-xs text-slate-600">
                                    {{ $notification->created_at?->format('Y-m-d H:i:s') }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-sm text-slate-900">
                                        {{ $notification->user?->name ?? 'System' }}
                                    </div>
                                    @if($notification->user)
                                        <div class="text-xs text-slate-500">{{ $notification->user->email }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-xs text-slate-600">
                                    {{ $notification->type }}
                                </td>
                                <td class="px-4 py-3 text-xs">
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium
                                        {{ $notification->channel === 'email' ? 'bg-blue-100 text-blue-700' : 'bg-emerald-50 text-emerald-700' }}">
                                        {{ ucfirst(str_replace('_', ' ', $notification->channel)) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-900">
                                    {{ $notification->title }}
                                </td>
                                <td class="px-4 py-3 text-xs text-slate-600">
                                    {{ $notification->message }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-sm text-slate-500">
                                    No notifications found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($notifications->hasPages())
                <div class="px-4 py-3 border-t border-slate-100">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

