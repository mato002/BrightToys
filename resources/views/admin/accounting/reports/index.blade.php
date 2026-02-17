@extends('layouts.admin')

@section('page_title', 'Accruals & Reports')

@section('content')
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 mb-4">
        <div>
            <a href="{{ route('admin.accounting.dashboard') }}" class="text-emerald-600 hover:text-emerald-700 text-sm mb-2 inline-flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Books of Account
            </a>
            <h1 class="text-lg font-semibold text-slate-900">Accruals & Reports</h1>
            <p class="text-xs text-slate-500">Income statement, trial balance and balance sheet reports.</p>
        </div>
        <form method="GET" class="flex items-center gap-2">
            <select name="type" class="border border-slate-200 rounded px-3 py-2 text-sm" onchange="this.form.submit()">
                <option value="income" {{ $reportType === 'income' ? 'selected' : '' }}>Income Statement</option>
                <option value="trial_balance" {{ $reportType === 'trial_balance' ? 'selected' : '' }}>Trial Balance</option>
                <option value="balance_sheet" {{ $reportType === 'balance_sheet' ? 'selected' : '' }}>Balance Sheet</option>
            </select>
            <select name="year" class="border border-slate-200 rounded px-3 py-2 text-sm" onchange="this.form.submit()">
                @for($y = now()->year; $y >= now()->year - 5; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
            <select name="month" class="border border-slate-200 rounded px-3 py-2 text-sm" onchange="this.form.submit()">
                <option value="">All Months</option>
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                @endfor
            </select>
            <button type="button" onclick="window.print()" class="bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-semibold px-4 py-2 rounded">
                Export / Print
            </button>
        </form>
    </div>

    @if(isset($data) && !empty($data))
        @if($data['type'] === 'income')
            <div class="bg-white border border-slate-100 rounded-lg overflow-hidden mb-4">
                <div class="px-6 py-4 border-b border-slate-200">
                    <h2 class="text-lg font-semibold text-slate-900">Income Statement</h2>
                    <p class="text-xs text-slate-500">{{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}</p>
                </div>
                <div class="admin-table-scroll overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-slate-700">Revenue Account</th>
                                <th class="px-4 py-2 text-right text-xs font-semibold text-slate-700">Current Period</th>
                                <th class="px-4 py-2 text-right text-xs font-semibold text-slate-700">Previous Period</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($data['revenues'] ?? [] as $revenue)
                                <tr>
                                    <td class="px-4 py-3 text-slate-700">{{ $revenue['name'] }}</td>
                                    <td class="px-4 py-3 text-right text-slate-700">Ksh {{ number_format($revenue['current'], 2) }}</td>
                                    <td class="px-4 py-3 text-right text-slate-700">Ksh {{ number_format($revenue['previous'], 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-3 text-center text-slate-500">No revenue accounts found</td>
                                </tr>
                            @endforelse
                            <tr class="bg-emerald-50 font-semibold">
                                <td class="px-4 py-3 text-slate-900">Total Revenue</td>
                                <td class="px-4 py-3 text-right text-emerald-700">Ksh {{ number_format($data['total_revenue'], 2) }}</td>
                                <td class="px-4 py-3 text-right text-slate-700">Ksh {{ number_format($data['previous_revenue'], 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="admin-table-scroll overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-slate-700">Expense Account</th>
                                <th class="px-4 py-2 text-right text-xs font-semibold text-slate-700">Current Period</th>
                                <th class="px-4 py-2 text-right text-xs font-semibold text-slate-700">Previous Period</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($data['expenses'] ?? [] as $expense)
                                <tr>
                                    <td class="px-4 py-3 text-slate-700">{{ $expense['name'] }}</td>
                                    <td class="px-4 py-3 text-right text-slate-700">Ksh {{ number_format($expense['current'], 2) }}</td>
                                    <td class="px-4 py-3 text-right text-slate-700">Ksh {{ number_format($expense['previous'], 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-3 text-center text-slate-500">No expense accounts found</td>
                                </tr>
                            @endforelse
                            <tr class="bg-red-50 font-semibold">
                                <td class="px-4 py-3 text-slate-900">Total Expenses</td>
                                <td class="px-4 py-3 text-right text-red-700">Ksh {{ number_format($data['total_expenses'], 2) }}</td>
                                <td class="px-4 py-3 text-right text-slate-700">Ksh {{ number_format($data['previous_expenses'], 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-semibold text-slate-900">Net Income</span>
                        <span class="text-lg font-bold {{ $data['net_income'] >= 0 ? 'text-emerald-700' : 'text-red-700' }}">
                            Ksh {{ number_format($data['net_income'], 2) }}
                        </span>
                    </div>
                </div>
            </div>
        @elseif($data['type'] === 'trial_balance')
            <div class="bg-white border border-slate-100 rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200">
                    <h2 class="text-lg font-semibold text-slate-900">Trial Balance</h2>
                    <p class="text-xs text-slate-500">{{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}</p>
                </div>
                <div class="admin-table-scroll overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-slate-700">Account Code</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-slate-700">Account Name</th>
                                <th class="px-4 py-2 text-right text-xs font-semibold text-slate-700">Debits</th>
                                <th class="px-4 py-2 text-right text-xs font-semibold text-slate-700">Credits</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($data['accounts'] ?? [] as $account)
                                <tr>
                                    <td class="px-4 py-3 text-slate-700">{{ $account['code'] }}</td>
                                    <td class="px-4 py-3 text-slate-700">{{ $account['name'] }}</td>
                                    <td class="px-4 py-3 text-right text-slate-700">Ksh {{ number_format($account['debits'], 2) }}</td>
                                    <td class="px-4 py-3 text-right text-slate-700">Ksh {{ number_format($account['credits'], 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-slate-500">No accounts found for this period</td>
                                </tr>
                            @endforelse
                            <tr class="bg-slate-50 font-semibold border-t-2 border-slate-300">
                                <td colspan="2" class="px-4 py-3 text-slate-900">Total</td>
                                <td class="px-4 py-3 text-right text-slate-900">Ksh {{ number_format($data['total_debits'], 2) }}</td>
                                <td class="px-4 py-3 text-right text-slate-900">Ksh {{ number_format($data['total_credits'], 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        @elseif($data['type'] === 'balance_sheet')
            <div class="bg-white border border-slate-100 rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200">
                    <h2 class="text-lg font-semibold text-slate-900">Balance Sheet</h2>
                    <p class="text-xs text-slate-500">As of {{ $endDate->format('M d, Y') }}</p>
                </div>
                <div class="grid md:grid-cols-2 gap-6 p-6">
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900 mb-3">Assets</h3>
                        <div class="space-y-2">
                            @forelse($data['assets'] ?? [] as $asset)
                                <div class="flex justify-between text-sm">
                                    <span class="text-slate-700">{{ $asset['name'] }}</span>
                                    <span class="text-slate-900 font-medium">Ksh {{ number_format($asset['balance'], 2) }}</span>
                                </div>
                            @empty
                                <p class="text-xs text-slate-500">No assets found</p>
                            @endforelse
                            <div class="flex justify-between text-sm font-semibold pt-2 border-t border-slate-200">
                                <span class="text-slate-900">Total Assets</span>
                                <span class="text-slate-900">Ksh {{ number_format($data['total_assets'], 2) }}</span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900 mb-3">Liabilities & Equity</h3>
                        <div class="space-y-2 mb-4">
                            <h4 class="text-xs font-semibold text-slate-600 uppercase">Liabilities</h4>
                            @forelse($data['liabilities'] ?? [] as $liability)
                                <div class="flex justify-between text-sm">
                                    <span class="text-slate-700">{{ $liability['name'] }}</span>
                                    <span class="text-slate-900 font-medium">Ksh {{ number_format($liability['balance'], 2) }}</span>
                                </div>
                            @empty
                                <p class="text-xs text-slate-500">No liabilities found</p>
                            @endforelse
                            <div class="flex justify-between text-sm font-semibold pt-2 border-t border-slate-200">
                                <span class="text-slate-900">Total Liabilities</span>
                                <span class="text-slate-900">Ksh {{ number_format($data['total_liabilities'], 2) }}</span>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <h4 class="text-xs font-semibold text-slate-600 uppercase">Equity</h4>
                            @forelse($data['equity'] ?? [] as $equity)
                                <div class="flex justify-between text-sm">
                                    <span class="text-slate-700">{{ $equity['name'] }}</span>
                                    <span class="text-slate-900 font-medium">Ksh {{ number_format($equity['balance'], 2) }}</span>
                                </div>
                            @empty
                                <p class="text-xs text-slate-500">No equity accounts found</p>
                            @endforelse
                            <div class="flex justify-between text-sm font-semibold pt-2 border-t border-slate-200">
                                <span class="text-slate-900">Total Equity</span>
                                <span class="text-slate-900">Ksh {{ number_format($data['total_equity'], 2) }}</span>
                            </div>
                        </div>
                        <div class="flex justify-between text-sm font-bold pt-4 mt-4 border-t-2 border-slate-300">
                            <span class="text-slate-900">Total Liabilities & Equity</span>
                            <span class="text-slate-900">Ksh {{ number_format($data['total_liabilities'] + $data['total_equity'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @else
        <div class="bg-white border border-slate-100 rounded-lg p-8 text-center">
            <p class="text-slate-500">No data available for the selected period.</p>
        </div>
    @endif
@endsection
