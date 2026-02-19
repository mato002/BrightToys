@extends('layouts.account')

@section('title', 'My Support Tickets')
@section('page_title', 'My Support Tickets')

@section('breadcrumbs')
    <span class="breadcrumb-separator">/</span>
    <span class="text-slate-700">Support</span>
@endsection

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-slate-900 tracking-tight">My Support Tickets</h1>
                <p class="text-xs md:text-sm text-slate-500 mt-1">View and manage your support requests</p>
            </div>
            <a href="{{ route('account.support.create') }}" class="inline-flex items-center gap-2 bg-amber-600 hover:bg-amber-700 text-white font-semibold px-4 py-2.5 rounded-lg">
                <i class="fas fa-plus"></i> New ticket
            </a>
        </div>

        @forelse($tickets as $ticket)
            <div class="bg-white border-2 border-slate-200 rounded-xl p-5 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex-1">
                        <a href="{{ route('account.support.show', $ticket) }}" class="text-base font-bold text-slate-900 hover:text-amber-600">{{ $ticket->subject }}</a>
                        <p class="text-xs text-slate-500 mt-1">#{{ $ticket->id }} &middot; {{ ucfirst($ticket->ticket_type ?? 'general') }} &middot; {{ $ticket->created_at->format('M d, Y') }} &middot; {{ $ticket->replies_count }} {{ Str::plural('reply', $ticket->replies_count) }}</p>
                        <span class="inline-flex items-center mt-2 px-2 py-0.5 rounded-full text-xs font-medium @if($ticket->status === 'resolved') bg-emerald-100 text-emerald-700 @elseif($ticket->status === 'in_progress') bg-blue-100 text-blue-700 @else bg-amber-100 text-amber-700 @endif">{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</span>
                    </div>
                    <a href="{{ route('account.support.show', $ticket) }}" class="inline-flex items-center gap-2 text-amber-600 hover:text-amber-700 font-semibold text-sm">View <i class="fas fa-chevron-right"></i></a>
                </div>
            </div>
        @empty
            <div class="bg-white border-2 border-dashed border-slate-300 rounded-xl p-12 text-center">
                <i class="fas fa-ticket-alt text-4xl text-slate-300 mb-4"></i>
                <h3 class="text-xl font-bold text-slate-900 mb-2">No support tickets</h3>
                <p class="text-sm text-slate-500 mb-6">Create a ticket for help, complaints, returns, or refunds.</p>
                <a href="{{ route('account.support.create') }}" class="inline-flex items-center gap-2 bg-amber-600 hover:bg-amber-700 text-white font-semibold px-6 py-3 rounded-lg"><i class="fas fa-plus"></i> New ticket</a>
            </div>
        @endforelse

        @if($tickets->hasPages())
            <div class="mt-6">{{ $tickets->links() }}</div>
        @endif
    </div>
@endsection
