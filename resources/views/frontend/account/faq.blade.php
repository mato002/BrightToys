@extends('layouts.account')

@section('title', 'FAQ')
@section('page_title', 'Frequently Asked Questions')

@section('breadcrumbs')
    <span class="breadcrumb-separator">/</span>
    <span class="text-slate-700">FAQ</span>
@endsection

@section('content')
    <div class="max-w-3xl space-y-4">
        <p class="text-slate-600 mb-6">Find answers to common questions about orders, shipping, returns, and your account.</p>

        <div x-data="{ open: null }" class="space-y-2">
            @php
                $faqs = [
                    ['q' => 'How can I track my order?', 'a' => 'Go to My Orders, then click "Track Order" on the order you want to track. You will see the current status and tracking number if available.'],
                    ['q' => 'What payment methods do you accept?', 'a' => 'We accept M-Pesa, Paybill, card payments, and cash on delivery (COD) where available.'],
                    ['q' => 'How do I return or exchange an item?', 'a' => 'Open a support ticket from the Support section and choose "Return / Exchange". Mention your order number and reason. Our team will guide you through the process.'],
                    ['q' => 'How do I get a refund?', 'a' => 'Submit a support ticket with type "Refund" and your order details. We will process eligible refunds as per our policy.'],
                    ['q' => 'Can I cancel my order?', 'a' => 'You can cancel orders that are still "Pending" or "Processing" from the order details page. Cancelled orders will have stock restored.'],
                    ['q' => 'Where is my referral code?', 'a' => 'Your referral code is shown in the Coupons & Rewards section. Share it with friends to earn reward points when they sign up and purchase.'],
                    ['q' => 'How do I use a coupon?', 'a' => 'At checkout, enter your coupon code in the discount field. Valid coupons will reduce your order total. You can see your coupon history under Coupons & Rewards.'],
                    ['q' => 'How do I contact support?', 'a' => 'Use the Support section to create a ticket for general enquiries, complaints, returns, or refunds. You can also use the Contact page or the Help link in the sidebar.'],
                ];
            @endphp
            @foreach($faqs as $i => $faq)
                <div class="bg-white border-2 border-slate-200 rounded-xl overflow-hidden">
                    <button type="button" @click="open = open === {{ $i }} ? null : {{ $i }}"
                            class="w-full flex items-center justify-between px-5 py-4 text-left font-semibold text-slate-900 hover:bg-slate-50 transition-colors">
                        <span>{{ $faq['q'] }}</span>
                        <i class="fas fa-chevron-down text-slate-400 transition-transform" :class="open === {{ $i }} ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open === {{ $i }}" x-transition class="px-5 pb-4 text-sm text-slate-600 border-t border-slate-100">
                        <p class="pt-3">{{ $faq['a'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <p class="text-sm text-slate-500 mt-8">Still have questions? <a href="{{ route('account.support.create') }}" class="text-amber-600 hover:underline font-medium">Create a support ticket</a> or <a href="{{ route('pages.contact') }}" class="text-amber-600 hover:underline font-medium">contact us</a>.</p>
    </div>
@endsection
