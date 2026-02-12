@extends('layouts.partner')

@section('title', 'Notifications')

@section('partner_content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Notifications</h1>
        <p class="text-sm text-slate-600 mt-1">
            View a history of important updates about your contributions, financial records, and projects.
        </p>
    </div>

    <div class="bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Message</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Type</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-100">
                    @forelse($notifications as $notification)
                        <tr class="{{ $notification->read_at ? 'bg-white' : 'bg-amber-50/60' }}">
                            <td class="px-6 py-4 text-sm text-slate-700 whitespace-nowrap">
                                {{ $notification->created_at?->format('M d, Y H:i') }}
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-slate-900">
                                {{ $notification->title }}
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-700">
                                {{ $notification->message }}
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-500 whitespace-nowrap">
                                {{ str_replace('_', ' ', $notification->type) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-sm text-slate-500">
                                No notifications yet. Updates about your account and contributions will appear here.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($notifications->hasPages())
            <div class="px-6 py-3 border-t border-slate-100">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
@endsection

