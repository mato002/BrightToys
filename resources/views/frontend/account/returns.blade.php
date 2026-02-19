@extends('layouts.account')

@section('title', 'Returns & Refunds')
@section('page_title', 'Returns & Refunds')

@section('breadcrumbs')
    <span class="breadcrumb-separator">/</span>
    <span class="text-slate-700">Returns & Refunds</span>
@endsection

@section('content')
    <div class="max-w-3xl space-y-6">
        <div class="bg-white border-2 border-slate-200 rounded-xl p-6 shadow-sm">
            <h2 class="text-lg font-bold text-slate-900 mb-4">Returns & Refund Policy</h2>
            <p class="text-sm text-slate-600 mb-4">
                If you are not satisfied with your order, you may request a return or refund. Please submit a request below and our team will review it and get back to you with next steps.
            </p>
            <ul class="text-sm text-slate-600 space-y-2 list-disc list-inside mb-6">
                <li>Include your order number and reason for the return or refund.</li>
                <li>We typically respond within 1–2 business days.</li>
                <li>Approved refunds are processed according to your original payment method.</li>
            </ul>

            <div class="flex flex-wrap gap-4">
                <a href="{{ route('account.support.create') }}?ticket_type=return"
                   class="inline-flex items-center gap-2 bg-amber-600 hover:bg-amber-700 text-white font-semibold px-5 py-2.5 rounded-lg">
                    <i class="fas fa-undo"></i> Request return or exchange
                </a>
                <a href="{{ route('account.support.create') }}?ticket_type=refund"
                   class="inline-flex items-center gap-2 bg-slate-700 hover:bg-slate-800 text-white font-semibold px-5 py-2.5 rounded-lg">
                    <i class="fas fa-money-bill-wave"></i> Request refund
                </a>
            </div>
        </div>

        <div class="bg-white border-2 border-slate-200 rounded-xl p-6 shadow-sm">
            <h3 class="text-base font-bold text-slate-900 mb-3">Your recent orders</h3>
            <p class="text-xs text-slate-500 mb-4">Select an order when opening a return/refund ticket so we can assist you faster.</p>
            @if($orders->count() > 0)
                <ul class="space-y-2">
                    @foreach($orders->take(10) as $order)
                        <li class="flex items-center justify-between py-2 border-b border-slate-100 last:border-0">
                            <span class="font-medium text-slate-900">{{ $order->order_number ?? '#' . $order->id }}</span>
                            <span class="text-xs text-slate-500">{{ $order->created_at->format('M d, Y') }}</span>
                            <a href="{{ route('account.support.create') }}?ticket_type=return&order_number={{ urlencode($order->order_number ?? $order->id) }}"
                               class="text-xs font-semibold text-amber-600 hover:text-amber-700">Request return</a>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-sm text-slate-500">No orders yet.</p>
            @endif
        </div>

        <p class="text-sm text-slate-500">
            Need general help? <a href="{{ route('account.contact') }}" class="text-amber-600 hover:underline font-medium">Contact us</a> or view <a href="{{ route('account.faq') }}" class="text-amber-600 hover:underline font-medium">FAQ</a>.
        </p>
    </div>
@endsection
