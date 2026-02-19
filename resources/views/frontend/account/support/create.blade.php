@extends('layouts.account')

@section('title', 'New Support Ticket')
@section('page_title', 'New Support Ticket')

@section('breadcrumbs')
    <span class="breadcrumb-separator">/</span>
    <a href="{{ route('account.support.index') }}" class="text-slate-600 hover:text-amber-600">Support</a>
    <span class="breadcrumb-separator">/</span>
    <span class="text-slate-700">New ticket</span>
@endsection

@section('content')
    <div class="max-w-2xl">
        <div class="bg-white border-2 border-slate-200 rounded-xl p-6 shadow-sm">
            <form action="{{ route('account.support.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Type</label>
                        <select name="ticket_type" class="w-full border-2 border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                            @php $ticketType = old('ticket_type', request('ticket_type', 'general')); @endphp
                            <option value="general" {{ $ticketType === 'general' ? 'selected' : '' }}>General enquiry</option>
                            <option value="complaint" {{ $ticketType === 'complaint' ? 'selected' : '' }}>Complaint</option>
                            <option value="return" {{ $ticketType === 'return' ? 'selected' : '' }}>Return / Exchange</option>
                            <option value="refund" {{ $ticketType === 'refund' ? 'selected' : '' }}>Refund</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Subject *</label>
                        <input type="text" name="subject" value="{{ old('subject') }}" required maxlength="255" class="w-full border-2 border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-amber-500 focus:border-amber-500" placeholder="Brief subject">
                        @error('subject')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Related order (optional)</label>
                        <select name="order_number" class="w-full border-2 border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                            @php $orderNumber = old('order_number', request('order_number')); @endphp
                            <option value="">— None —</option>
                            @foreach($orders as $order)
                                <option value="{{ $order->order_number ?? $order->id }}" {{ $orderNumber == ($order->order_number ?? $order->id) ? 'selected' : '' }}>{{ $order->order_number ?? '#' . $order->id }} — {{ $order->created_at->format('M d, Y') }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Message *</label>
                        <textarea name="message" rows="5" required maxlength="5000" placeholder="Describe your issue or question..." class="w-full border-2 border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-amber-500 focus:border-amber-500">{{ old('message') }}</textarea>
                        @error('message')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="mt-6 flex gap-3">
                    <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white font-semibold px-5 py-2.5 rounded-lg">Submit ticket</button>
                    <a href="{{ route('account.support.index') }}" class="px-5 py-2.5 border-2 border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50 font-semibold">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
