@extends('layouts.partner')

@section('page_title', 'New Contribution')

@section('partner_content')
    <div class="mb-6">
        <div class="flex items-center justify-between mb-2">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Submit New Contribution</h1>
                <p class="text-sm text-slate-600 mt-1">
                    Record a deposit you have made. The treasurer will allocate it between investment and welfare accounts.
                </p>
            </div>
            <a href="{{ route('partner.contributions') }}" 
               class="inline-flex items-center gap-2 text-sm text-slate-600 hover:text-slate-900 border border-slate-300 rounded-lg px-4 py-2 hover:bg-slate-50 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M19 12H5M12 19l-7-7 7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Back to Contributions
            </a>
        </div>
    </div>

    {{-- Current Balances --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div class="bg-gradient-to-r from-emerald-50 to-emerald-100 border border-emerald-200 rounded-xl p-4">
            <p class="text-xs font-semibold text-emerald-700 uppercase tracking-wide">Investment Balance</p>
            <p class="mt-1 text-2xl font-bold text-emerald-900">Ksh {{ number_format($investmentBalance ?? 0, 2) }}</p>
        </div>
        <div class="bg-gradient-to-r from-blue-50 to-blue-100 border border-blue-200 rounded-xl p-4">
            <p class="text-xs font-semibold text-blue-700 uppercase tracking-wide">Welfare Balance</p>
            <p class="mt-1 text-2xl font-bold text-blue-900">Ksh {{ number_format($welfareBalance ?? 0, 2) }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Form --}}
        <div class="lg:col-span-2">
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                <div class="bg-gradient-to-r from-amber-500 to-amber-600 px-6 py-4">
                    <h2 class="text-lg font-bold text-white">Contribution Details</h2>
                    <p class="text-sm text-amber-100">Fill in all required information below</p>
                </div>
                
                <form action="{{ route('partner.contributions.store') }}" method="POST" class="p-6 space-y-6">
                    @csrf
                    
                    @if($errors->any())
                        <div class="bg-red-50 border-l-4 border-red-500 rounded-lg p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-semibold text-red-800 mb-2">Please correct the following errors:</h3>
                                    <ul class="text-sm text-red-700 list-disc list-inside space-y-1">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Amount <span class="text-red-500">*</span>
                        </label>
                        <div class="flex gap-3">
                            <span class="inline-flex items-center rounded-lg bg-slate-100 px-3 text-xs font-semibold text-slate-700">
                                KES
                            </span>
                            <input type="number" name="amount" value="{{ old('amount') }}" step="0.01" min="0.01" required
                                   class="flex-1 border border-slate-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors"
                                   placeholder="0.00">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Transaction Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="contributed_at" value="{{ old('contributed_at', now()->format('Y-m-d')) }}" required
                               class="w-full border border-slate-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Reference Number
                        </label>
                        <input type="text" name="reference" value="{{ old('reference') }}"
                               class="w-full border border-slate-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors"
                               placeholder="e.g., Transaction ID, Receipt Number, Bank Reference">
                        <p class="text-xs text-slate-500 mt-2">
                            Optional: Reference number from your bank, payment provider, or transaction receipt
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Additional Notes
                        </label>
                        <textarea name="notes" rows="5"
                                  class="w-full border border-slate-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors resize-none"
                                  placeholder="Add any relevant details, context, or instructions about this transaction...">{{ old('notes') }}</textarea>
                        <p class="text-xs text-slate-500 mt-2">
                            Optional: Provide additional context or instructions for administrators reviewing this request
                        </p>
                    </div>

                    <div class="bg-amber-50 border-l-4 border-amber-500 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-semibold text-amber-800 mb-1">What Happens Next?</h3>
                                <p class="text-sm text-amber-700">
                                    This submission will be created with <strong>Pending Approval</strong> status. The treasurer will
                                    confirm the deposit and allocate it between <strong>Investment</strong> and <strong>Welfare</strong>
                                    accounts based on your plan and current balances. You will be notified once it is processed.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 pt-4 border-t border-slate-200">
                        <button type="submit" 
                                class="flex-1 bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold px-6 py-3 rounded-lg shadow-sm transition-all hover:shadow-md">
                            Submit Contribution Request
                        </button>
                        <a href="{{ route('partner.contributions') }}" 
                           class="px-6 py-3 text-sm text-slate-600 hover:text-slate-900 border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Sidebar Info --}}
        <div class="space-y-6">
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6">
                <h3 class="text-sm font-semibold text-slate-900 mb-4">Quick Information</h3>
                <div class="space-y-4 text-sm">
                    <div>
                        <p class="font-medium text-slate-700 mb-1">Contributions</p>
                        <p class="text-xs text-slate-600">
                            Capital investments you make into the business. These increase your equity position.
                        </p>
                    </div>
                    <div>
                        <p class="font-medium text-slate-700 mb-1">Withdrawals</p>
                        <p class="text-xs text-slate-600">
                            Removing capital from the business. Requires approval and may affect your ownership percentage.
                        </p>
                    </div>
                    <div>
                        <p class="font-medium text-slate-700 mb-1">Processing Time</p>
                        <p class="text-xs text-slate-600">
                            Most requests are reviewed within 1-3 business days. You'll receive a notification once processed.
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-xl p-6">
                <h3 class="text-sm font-semibold text-blue-900 mb-2">Need Help?</h3>
                <p class="text-xs text-blue-700 mb-4">
                    If you have questions about contributions or withdrawals, contact the finance administrator.
                </p>
                <a href="{{ route('partner.dashboard') }}" 
                   class="text-xs font-medium text-blue-700 hover:text-blue-900 underline">
                    View Dashboard →
                </a>
            </div>
        </div>
    </div>
@endsection
