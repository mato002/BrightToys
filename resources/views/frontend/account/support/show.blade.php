@extends('layouts.account')

@section('title', 'Ticket #' . $ticket->id)
@section('page_title', 'Ticket: ' . Str::limit($ticket->subject, 40))

@section('breadcrumbs')
    <span class="breadcrumb-separator">/</span>
    <a href="{{ route('account.support.index') }}" class="text-slate-600 hover:text-amber-600">Support</a>
    <span class="breadcrumb-separator">/</span>
    <span class="text-slate-700">#{{ $ticket->id }}</span>
@endsection

@section('content')
    <div class="space-y-6 max-w-4xl">
        <div class="bg-white border-2 border-slate-200 rounded-xl p-6 shadow-sm">
            <div class="flex flex-wrap items-center gap-2 mb-4">
                <span class="text-lg font-bold text-slate-900">{{ $ticket->subject }}</span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium @if($ticket->status === 'resolved') bg-emerald-100 text-emerald-700 @elseif($ticket->status === 'in_progress') bg-blue-100 text-blue-700 @else bg-amber-100 text-amber-700 @endif">{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</span>
                <span class="text-xs text-slate-500">{{ $ticket->created_at->format('M d, Y H:i') }}</span>
                @if($ticket->order_number)<span class="text-xs text-slate-600">Order: {{ $ticket->order_number }}</span>@endif
            </div>
            <div class="prose prose-slate max-w-none text-sm"><p class="whitespace-pre-wrap text-slate-700">{{ $ticket->message }}</p></div>
        </div>

        @foreach($ticket->replies as $reply)
            <div class="bg-white border-2 rounded-xl p-5 shadow-sm {{ $reply->is_staff ? 'border-amber-200 bg-amber-50/30' : 'border-slate-200' }}">
                <div class="flex items-center gap-2 mb-2">
                    <span class="font-semibold text-slate-900">{{ $reply->is_staff ? 'Support team' : ($reply->user->name ?? 'You') }}</span>
                    @if($reply->is_staff)<span class="text-xs px-2 py-0.5 rounded bg-amber-200 text-amber-800">Staff</span>@endif
                    <span class="text-xs text-slate-500">{{ $reply->created_at->format('M d, Y H:i') }}</span>
                </div>
                <p class="text-sm text-slate-700 whitespace-pre-wrap">{{ $reply->message }}</p>
            </div>
        @endforeach

        @if($ticket->status !== 'resolved')
            <div class="bg-white border-2 border-slate-200 rounded-xl p-6 shadow-sm">
                <h3 class="text-base font-bold text-slate-900 mb-3">Add a reply</h3>
                <form action="{{ route('account.support.reply', $ticket) }}" method="POST">
                    @csrf
                    <textarea name="message" rows="4" required maxlength="5000" placeholder="Type your message..." class="w-full border-2 border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-amber-500 focus:border-amber-500"></textarea>
                    @error('message')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    <button type="submit" class="mt-3 bg-amber-600 hover:bg-amber-700 text-white font-semibold px-4 py-2 rounded-lg">Send reply</button>
                </form>
            </div>
        @endif

        <a href="{{ route('account.support.index') }}" class="inline-flex items-center gap-2 text-slate-600 hover:text-amber-600 font-medium"><i class="fas fa-arrow-left"></i> Back to tickets</a>
    </div>
@endsection
