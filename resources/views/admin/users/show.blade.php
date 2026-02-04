@extends('layouts.admin')

@section('page_title', $user->name)

@section('content')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
        <div>
            <h1 class="text-lg font-semibold">{{ $user->name }}</h1>
            <p class="text-xs text-slate-500">Customer overview and recent orders.</p>
        </div>
        <a href="{{ route('admin.users.index') }}"
           class="inline-flex items-center px-3 py-1.5 rounded-md border border-slate-200 text-xs text-slate-600 hover:bg-slate-50">
            Back to list
        </a>
    </div>

    <div class="grid md:grid-cols-3 gap-6 text-sm">
        <div class="bg-white border border-slate-100 rounded-xl p-4 shadow-sm space-y-4">
            <div>
                <span class="text-xs text-slate-500 uppercase tracking-wide block mb-1">Name</span>
                <span class="text-sm font-semibold text-slate-900">{{ $user->name }}</span>
            </div>
            <div>
                <span class="text-xs text-slate-500 uppercase tracking-wide block mb-1">Email</span>
                <span class="text-xs text-slate-800">{{ $user->email }}</span>
            </div>
            <div>
                <span class="text-xs text-slate-500 uppercase tracking-wide block mb-1">Total Orders</span>
                <span class="text-base font-bold text-slate-900">{{ $user->orders->count() }}</span>
            </div>
            <div class="pt-2 border-t border-slate-100">
                <span class="text-xs text-slate-500 uppercase tracking-wide block mb-1">Member Since</span>
                <span class="text-xs text-slate-600">{{ $user->created_at->format('M d, Y') }}</span>
            </div>
        </div>

        <div class="md:col-span-2 bg-white border border-slate-100 rounded-xl p-4 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <h2 class="text-sm font-semibold text-slate-900">Recent Orders</h2>
            </div>

            @if($user->orders->isEmpty())
                <p class="text-xs text-slate-500">No orders for this customer yet.</p>
            @else
                <div class="overflow-x-auto text-xs">
                    <table class="min-w-full">
                        <thead class="bg-slate-50 text-[11px] text-slate-500 uppercase tracking-wide">
                        <tr>
                            <th class="px-3 py-1.5 text-left">Order</th>
                            <th class="px-3 py-1.5 text-left">Total</th>
                            <th class="px-3 py-1.5 text-left">Status</th>
                            <th class="px-3 py-1.5 text-left">Date</th>
                            <th class="px-3 py-1.5 text-right">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($user->orders as $order)
                            <tr class="border-t border-slate-100">
                                <td class="px-3 py-1.5 text-slate-800">#{{ $order->id }}</td>
                                <td class="px-3 py-1.5">Ksh {{ number_format($order->total, 0) }}</td>
                                <td class="px-3 py-1.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px]
                                        @class([
                                            'bg-amber-50 text-amber-700 border border-amber-100' => $order->status === 'pending',
                                            'bg-blue-50 text-blue-700 border border-blue-100' => $order->status === 'processing',
                                            'bg-emerald-50 text-emerald-700 border border-emerald-100' => $order->status === 'completed',
                                            'bg-slate-50 text-slate-700 border border-slate-100' => $order->status === 'shipped',
                                            'bg-red-50 text-red-700 border border-red-100' => $order->status === 'cancelled',
                                        ])>
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="px-3 py-1.5 text-[11px] text-slate-500">
                                    {{ $order->created_at->format('d M Y') }}
                                </td>
                                <td class="px-3 py-1.5 text-right">
                                    <a href="{{ route('admin.orders.show', $order) }}"
                                       class="text-[11px] text-amber-600 hover:underline">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection

