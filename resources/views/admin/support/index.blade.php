@extends('layouts.admin')

@section('page_title', 'Support & Messages')

@section('content')
    <div class="mb-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h2 class="text-lg font-semibold text-slate-900">Support tickets</h2>
            <p class="text-xs text-slate-500">
                Messages sent from the contact form. Use the status to track what still needs attention.
            </p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.support-tickets.export') }}"
               class="inline-flex items-center justify-center bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-semibold px-4 py-2 rounded-md shadow-sm">
                Export CSV
            </a>
            <a href="{{ route('admin.support-tickets.report') }}"
               class="inline-flex items-center justify-center bg-blue-500 hover:bg-blue-600 text-white text-xs font-semibold px-4 py-2 rounded-md shadow-sm">
                Generate Report
            </a>
        </div>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm text-sm">
        <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
            <span class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Inbox</span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full border-separate border-spacing-y-0.5 text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-[0.18em]">
                <tr>
                    <th class="px-3 py-2 text-left font-medium">From</th>
                    <th class="px-3 py-2 text-left font-medium">Subject</th>
                    <th class="px-3 py-2 text-left font-medium">Status</th>
                    <th class="px-3 py-2 text-left font-medium">Received</th>
                    <th class="px-3 py-2 text-right font-medium"></th>
                </tr>
                </thead>
                <tbody>
                @forelse($tickets as $ticket)
                    <tr class="border-t border-slate-100 bg-white hover:bg-slate-50 transition-colors">
                        <td class="px-3 py-2 text-slate-900">
                            <div class="font-medium">{{ $ticket->name }}</div>
                            <div class="text-[11px] text-slate-500">{{ $ticket->email }}</div>
                        </td>
                        <td class="px-3 py-2 text-slate-800">
                            {{ $ticket->subject ?? 'General enquiry' }}
                        </td>
                        <td class="px-3 py-2">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] border
                                @class([
                                    'bg-amber-50 text-amber-700 border-amber-200' => $ticket->status === 'open',
                                    'bg-sky-50 text-sky-700 border-sky-200' => $ticket->status === 'in_progress',
                                    'bg-emerald-50 text-emerald-700 border-emerald-200' => $ticket->status === 'resolved',
                                ])">
                                {{ str_replace('_', ' ', ucfirst($ticket->status)) }}
                            </span>
                        </td>
                        <td class="px-3 py-2 text-[11px] text-slate-500">
                            {{ $ticket->created_at->format('d M Y, H:i') }}
                        </td>
                        <td class="px-3 py-2 text-right">
                            <a href="{{ route('admin.support-tickets.show', $ticket) }}"
                               class="inline-flex items-center gap-1 rounded-full border border-slate-200 px-2.5 py-1 text-[11px] text-slate-700 hover:border-emerald-400 hover:text-emerald-700">
                                View
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M5 12h14M13 6l6 6-6 6" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-3 py-4 text-center text-xs text-slate-500">
                            No support messages yet.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-4 py-3 border-t border-slate-100">
            {{ $tickets->links() }}
        </div>
    </div>
@endsection

