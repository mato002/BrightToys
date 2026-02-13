@extends('layouts.partner')

@section('partner_content')
    <div class="mb-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-lg font-semibold">My Profile</h1>
            <p class="text-xs text-slate-500">Manage your biodata and personal information.</p>
        </div>
        <a href="{{ route('partner.profile.sessions') }}"
           class="inline-flex items-center gap-2 px-4 py-2 text-xs font-medium text-slate-700 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 hover:border-amber-400 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            Active Sessions
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 text-sm text-emerald-700 bg-emerald-50 border border-emerald-200 px-4 py-2 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('partner.profile.update') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-lg border border-slate-100 p-6 shadow-sm space-y-6">
        @csrf
        @method('PUT')

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-lg p-3 text-xs text-red-700">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Personal Information --}}
        <div>
            <h2 class="text-sm font-semibold text-slate-900 mb-4">Personal Information</h2>
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold mb-1 text-slate-700">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $partner->name) }}" required
                           class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold mb-1 text-slate-700">Email Address</label>
                    <input type="email" name="email" value="{{ old('email', $partner->email) }}"
                           class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold mb-1 text-slate-700">Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone', $partner->phone) }}"
                           class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold mb-1 text-slate-700">Date of Birth</label>
                    <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $partner->date_of_birth?->format('Y-m-d')) }}"
                           class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold mb-1 text-slate-700">National ID Number</label>
                    <input type="text" name="national_id_number" value="{{ old('national_id_number', $partner->national_id_number) }}"
                           class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold mb-1 text-slate-700">Address</label>
                    <input type="text" name="address" value="{{ old('address', $partner->address) }}"
                           class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                </div>
            </div>
        </div>

        {{-- ID Document --}}
        <div>
            <h2 class="text-sm font-semibold text-slate-900 mb-4">Identity Document</h2>
            <div>
                <label class="block text-xs font-semibold mb-1 text-slate-700">ID Document (PDF, JPG, PNG)</label>
                <input type="file" name="id_document" accept=".pdf,.jpg,.jpeg,.png"
                       class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                <p class="text-[10px] text-slate-500 mt-1">Upload a scanned copy of your national ID (max 10MB)</p>
                @if($partner->id_document_path)
                    <div class="mt-2">
                        <a href="{{ asset('storage/' . $partner->id_document_path) }}" target="_blank"
                           class="text-emerald-600 hover:text-emerald-700 text-xs underline">
                            View current document
                        </a>
                    </div>
                @endif
            </div>
        </div>

        {{-- Onboarding Status --}}
        <div class="border-t border-slate-200 pt-4">
            <h2 class="text-sm font-semibold text-slate-900 mb-4">Onboarding Status</h2>
            <div class="grid md:grid-cols-2 gap-4 text-xs">
                <div>
                    <span class="text-slate-500">Biodata Completion:</span>
                    <span class="ml-2 font-medium {{ $partner->biodata_completed_at ? 'text-emerald-600' : 'text-amber-600' }}">
                        {{ $partner->biodata_completed_at ? 'Completed ' . $partner->biodata_completed_at->format('d M Y') : 'Not completed' }}
                    </span>
                </div>
                <div>
                    <span class="text-slate-500">ID Verification:</span>
                    <span class="ml-2 font-medium {{ $partner->id_verified_at ? 'text-emerald-600' : 'text-amber-600' }}">
                        {{ $partner->id_verified_at ? 'Verified ' . $partner->id_verified_at->format('d M Y') : 'Not verified' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Entry Contribution Summary --}}
        @if($partner->entryContribution)
        <div class="border-t border-slate-200 pt-4">
            <h2 class="text-sm font-semibold text-slate-900 mb-4">Entry Contribution</h2>
            <div class="grid md:grid-cols-4 gap-4">
                <div class="bg-slate-50 rounded-lg p-3">
                    <div class="text-xs text-slate-500 mb-1">Total Amount</div>
                    <div class="text-lg font-semibold text-slate-900">{{ $partner->entryContribution->currency }} {{ number_format($partner->entryContribution->total_amount, 2) }}</div>
                </div>
                <div class="bg-emerald-50 rounded-lg p-3">
                    <div class="text-xs text-emerald-600 mb-1">Paid Amount</div>
                    <div class="text-lg font-semibold text-emerald-700">{{ $partner->entryContribution->currency }} {{ number_format($partner->entryContribution->paid_amount, 2) }}</div>
                </div>
                <div class="bg-amber-50 rounded-lg p-3">
                    <div class="text-xs text-amber-600 mb-1">Outstanding</div>
                    <div class="text-lg font-semibold text-amber-700">{{ $partner->entryContribution->currency }} {{ number_format($partner->entryContribution->outstanding_balance, 2) }}</div>
                </div>
                <div class="bg-blue-50 rounded-lg p-3">
                    <div class="text-xs text-blue-600 mb-1">Progress</div>
                    <div class="text-lg font-semibold text-blue-700">
                        {{ $partner->entryContribution->total_amount > 0 ? number_format(($partner->entryContribution->paid_amount / $partner->entryContribution->total_amount) * 100, 1) : 0 }}%
                    </div>
                </div>
            </div>

            @if($partner->entryContribution->paymentPlan && $partner->entryContribution->paymentPlan->installments->count() > 0)
            <div class="mt-4">
                <h3 class="text-xs font-semibold text-slate-700 mb-2">Payment Plan Installments</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead class="bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th class="px-3 py-2 text-left font-semibold text-slate-700">#</th>
                                <th class="px-3 py-2 text-left font-semibold text-slate-700">Due Date</th>
                                <th class="px-3 py-2 text-left font-semibold text-slate-700">Amount</th>
                                <th class="px-3 py-2 text-left font-semibold text-slate-700">Paid</th>
                                <th class="px-3 py-2 text-left font-semibold text-slate-700">Penalty</th>
                                <th class="px-3 py-2 text-left font-semibold text-slate-700">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($partner->entryContribution->paymentPlan->installments as $installment)
                            <tr class="{{ $installment->status === 'overdue' || $installment->status === 'missed' ? 'bg-red-50' : ($installment->status === 'paid' ? 'bg-emerald-50' : '') }}">
                                <td class="px-3 py-2 font-medium">{{ $installment->installment_number }}</td>
                                <td class="px-3 py-2">{{ $installment->due_date->format('d M Y') }}</td>
                                <td class="px-3 py-2 font-medium">{{ $partner->entryContribution->currency }} {{ number_format($installment->amount, 2) }}</td>
                                <td class="px-3 py-2">{{ $partner->entryContribution->currency }} {{ number_format($installment->paid_amount, 2) }}</td>
                                <td class="px-3 py-2">
                                    @if($installment->penalty_amount > 0)
                                        <span class="text-red-600 font-semibold">{{ $partner->entryContribution->currency }} {{ number_format($installment->penalty_amount, 2) }}</span>
                                        @if($installment->days_overdue > 0)
                                            <div class="text-[10px] text-red-500">({{ $installment->days_overdue }} days overdue)</div>
                                        @endif
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2">
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium
                                        @if($installment->status === 'paid') bg-emerald-100 text-emerald-700
                                        @elseif($installment->status === 'overdue') bg-red-100 text-red-700
                                        @elseif($installment->status === 'missed') bg-red-200 text-red-800
                                        @else bg-amber-100 text-amber-700 @endif">
                                        {{ ucfirst($installment->status) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
        @endif

        <div class="flex items-center gap-3 pt-4 border-t border-slate-200">
            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-6 py-2 rounded shadow-sm">
                Update Profile
            </button>
            <a href="{{ route('partner.dashboard') }}" class="text-sm text-slate-600 hover:text-slate-800">
                Cancel
            </a>
        </div>
    </form>

    {{-- Wallet Summary & Recent Transactions --}}
    <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white border border-slate-100 rounded-lg p-4 shadow-sm">
            <h2 class="text-sm font-semibold text-slate-900 mb-3">Wallet Balances</h2>
            <dl class="space-y-3 text-sm">
                <div class="flex items-center justify-between">
                    <dt class="text-xs text-slate-500">Welfare Wallet</dt>
                    <dd class="font-semibold text-slate-900">
                        Ksh {{ number_format(optional($welfareWallet)->balance ?? 0, 2) }}
                    </dd>
                </div>
                <div class="flex items-center justify-between">
                    <dt class="text-xs text-slate-500">Investment Wallet</dt>
                    <dd class="font-semibold text-emerald-700">
                        Ksh {{ number_format(optional($investmentWallet)->balance ?? 0, 2) }}
                    </dd>
                </div>
            </dl>
            <p class="mt-3 text-[11px] text-slate-500">
                Balances update automatically when your contributions and withdrawals are approved.
            </p>
        </div>

        <div class="md:col-span-2 bg-white border border-slate-100 rounded-lg p-4 shadow-sm">
            <h2 class="text-sm font-semibold text-slate-900 mb-3">Recent Wallet Transactions</h2>
            @php
                $transactions = collect();
                if(isset($welfareWallet)) {
                    $transactions = $transactions->merge($welfareWallet->transactions);
                }
                if(isset($investmentWallet)) {
                    $transactions = $transactions->merge($investmentWallet->transactions);
                }
                $transactions = $transactions->sortByDesc('occurred_at')->take(10);
            @endphp
            @if($transactions->isEmpty())
                <p class="text-xs text-slate-500">No wallet transactions recorded yet.</p>
            @else
                <div class="overflow-x-auto text-xs">
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-3 py-2 text-left font-semibold text-slate-700">Date</th>
                                <th class="px-3 py-2 text-left font-semibold text-slate-700">Fund</th>
                                <th class="px-3 py-2 text-left font-semibold text-slate-700">Type</th>
                                <th class="px-3 py-2 text-left font-semibold text-slate-700">Direction</th>
                                <th class="px-3 py-2 text-left font-semibold text-slate-700">Amount</th>
                                <th class="px-3 py-2 text-left font-semibold text-slate-700">Balance After</th>
                                <th class="px-3 py-2 text-left font-semibold text-slate-700">Reference</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($transactions as $tx)
                                <tr>
                                    <td class="px-3 py-2">
                                        <div>{{ $tx->occurred_at->format('M d, Y') }}</div>
                                        <div class="text-[10px] text-slate-500">{{ $tx->occurred_at->format('H:i') }}</div>
                                    </td>
                                    <td class="px-3 py-2 text-xs capitalize">
                                        {{ $tx->fund_type }}
                                    </td>
                                    <td class="px-3 py-2 text-xs capitalize">
                                        {{ str_replace('_', ' ', $tx->type) }}
                                    </td>
                                    <td class="px-3 py-2">
                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium
                                            {{ $tx->direction === 'credit' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                            {{ ucfirst($tx->direction) }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2">
                                        <span class="font-semibold {{ $tx->direction === 'credit' ? 'text-emerald-700' : 'text-red-600' }}">
                                            Ksh {{ number_format($tx->amount, 2) }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2">
                                        Ksh {{ number_format($tx->balance_after, 2) }}
                                    </td>
                                    <td class="px-3 py-2 font-mono text-[11px]">
                                        {{ $tx->reference ?? '—' }}
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
