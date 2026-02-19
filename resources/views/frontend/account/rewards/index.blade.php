@extends('layouts.account')

@section('title', 'Coupons & Rewards')
@section('page_title', 'Coupons & Rewards')

@section('breadcrumbs')
    <span class="breadcrumb-separator">/</span>
    <span class="text-slate-700">Rewards</span>
@endsection

@section('content')
    <div class="space-y-6">
        <div class="mb-6">
            <h1 class="text-xl md:text-2xl font-semibold text-slate-900 tracking-tight">Coupons & Rewards</h1>
            <p class="text-xs md:text-sm text-slate-500 mt-1">Your coupon history, reward points, and wallet balance</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-gradient-to-br from-amber-50 to-orange-50 border-2 border-amber-200 rounded-xl p-6 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-amber-700 mb-2">Reward points</p>
                <p class="text-3xl font-bold text-amber-900">{{ number_format($pointsBalance) }}</p>
                <p class="text-xs text-amber-600 mt-1">Earn on purchases; redeem for discounts</p>
            </div>
            <div class="bg-gradient-to-br from-emerald-50 to-teal-50 border-2 border-emerald-200 rounded-xl p-6 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700 mb-2">Wallet balance</p>
                <p class="text-3xl font-bold text-emerald-900">Ksh {{ number_format($walletBalance, 0) }}</p>
                <p class="text-xs text-emerald-600 mt-1">Store credit for future orders</p>
            </div>
            <div class="bg-white border-2 border-slate-200 rounded-xl p-6 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-600 mb-2">Referral code</p>
                @if($referralCode)
                    <p class="text-xl font-bold text-slate-900 font-mono">{{ $referralCode }}</p>
                    <p class="text-xs text-slate-500 mt-1">Share with friends to earn rewards</p>
                @else
                    <p class="text-sm text-slate-500">No referral code yet</p>
                @endif
            </div>
        </div>

        <div class="bg-white border-2 border-slate-200 rounded-xl p-6 shadow-sm">
            <h2 class="text-lg font-bold text-slate-900 mb-4">Coupon history</h2>
            @if($couponHistory->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-slate-200 text-left text-slate-600 font-semibold">
                                <th class="pb-2 pr-4">Date</th>
                                <th class="pb-2 pr-4">Coupon</th>
                                <th class="pb-2 pr-4">Discount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($couponHistory as $redemption)
                                <tr class="border-b border-slate-100">
                                    <td class="py-3 pr-4">{{ $redemption->created_at->format('M d, Y') }}</td>
                                    <td class="py-3 pr-4">{{ $redemption->coupon->code ?? 'N/A' }}</td>
                                    <td class="py-3 font-semibold text-emerald-600">Ksh {{ number_format($redemption->discount_amount, 0) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($couponHistory->hasPages())
                    <div class="mt-4">{{ $couponHistory->links() }}</div>
                @endif
            @else
                <p class="text-slate-500 text-sm">You have not used any coupons yet.</p>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white border-2 border-slate-200 rounded-xl p-6 shadow-sm">
                <h2 class="text-lg font-bold text-slate-900 mb-4">Points history</h2>
                @if($pointsHistory->count() > 0)
                    <ul class="space-y-2 text-sm">
                        @foreach($pointsHistory as $tx)
                            <li class="flex justify-between py-2 border-b border-slate-100">
                                <span class="text-slate-700">{{ $tx->description ?? ucfirst(str_replace('_', ' ', $tx->type)) }}</span>
                                <span class="{{ $tx->points >= 0 ? 'text-emerald-600' : 'text-red-600' }} font-medium">{{ $tx->points >= 0 ? '+' : '' }}{{ $tx->points }}</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-slate-500 text-sm">No points activity yet.</p>
                @endif
            </div>
            <div class="bg-white border-2 border-slate-200 rounded-xl p-6 shadow-sm">
                <h2 class="text-lg font-bold text-slate-900 mb-4">Wallet transactions</h2>
                @if($walletTransactions->count() > 0)
                    <ul class="space-y-2 text-sm">
                        @foreach($walletTransactions as $tx)
                            <li class="flex justify-between py-2 border-b border-slate-100">
                                <span class="text-slate-700">{{ $tx->description ?? ucfirst($tx->type) }}</span>
                                <span class="{{ $tx->amount >= 0 ? 'text-emerald-600' : 'text-red-600' }} font-medium">Ksh {{ number_format($tx->amount, 0) }}</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-slate-500 text-sm">No wallet transactions yet.</p>
                @endif
            </div>
        </div>
    </div>
@endsection
