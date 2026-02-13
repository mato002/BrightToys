@extends('layouts.partner')

@section('page_title', 'Budget Reports')

@section('partner_content')
    <div class="mb-4">
        <h1 class="text-lg font-semibold">Budget Reports</h1>
        <p class="text-xs text-slate-500">
            View budget reports and financial planning information.
        </p>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
        <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm">
            <p class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold mb-1">Total Budget</p>
            <p class="text-2xl font-bold text-slate-900">KES {{ number_format($totalBudget ?? 0, 2) }}</p>
            <p class="text-[11px] text-slate-500 mt-1">Allocated budget</p>
        </div>

        <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm">
            <p class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold mb-1">Total Spent</p>
            <p class="text-2xl font-bold text-amber-600">KES {{ number_format($totalSpent ?? 0, 2) }}</p>
            <p class="text-[11px] text-slate-500 mt-1">Actual expenditure</p>
        </div>

        <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm">
            <p class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold mb-1">Remaining</p>
            <p class="text-2xl font-bold text-emerald-600">KES {{ number_format($totalBalance ?? 0, 2) }}</p>
            <p class="text-[11px] text-slate-500 mt-1">Available balance</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm mb-4">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-3">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Year</label>
                <select name="year" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <option value="">All Years</option>
                    @foreach($years ?? [] as $year)
                        <option value="{{ $year }}" @selected(request('year') == $year)>{{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Month</label>
                <select name="month" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <option value="">All Months</option>
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" @selected(request('month') == $i)>{{ Carbon\Carbon::create()->month($i)->format('F') }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Account</label>
                <select name="account_id" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <option value="">All Accounts</option>
                    @foreach($accounts ?? [] as $account)
                        <option value="{{ $account->id }}" @selected(request('account_id') == $account->id)>{{ $account->code }} - {{ $account->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Branch</label>
                <select name="branch" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <option value="">All Branches</option>
                    @foreach($branches ?? [] as $branch)
                        <option value="{{ $branch }}" @selected(request('branch') == $branch)>{{ $branch }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Actions</label>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold px-4 py-2 rounded-lg">
                        Filter
                    </button>
                    <a href="{{ route('partner.accounting.budget') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-semibold px-4 py-2 rounded-lg">
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Budget Table --}}
    <div class="bg-white rounded-lg border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-[11px]">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold text-slate-700">Period</th>
                        <th class="px-3 py-2 text-left font-semibold text-slate-700">Account</th>
                        <th class="px-3 py-2 text-left font-semibold text-slate-700">Branch</th>
                        <th class="px-3 py-2 text-right font-semibold text-slate-700">Budget</th>
                        <th class="px-3 py-2 text-right font-semibold text-slate-700">Spent</th>
                        <th class="px-3 py-2 text-right font-semibold text-slate-700">Balance</th>
                        <th class="px-3 py-2 text-right font-semibold text-slate-700">Variance</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($budgets ?? [] as $budget)
                        <tr class="hover:bg-slate-50">
                            <td class="px-3 py-2 text-slate-700">
                                {{ Carbon\Carbon::create($budget->year, $budget->month, 1)->format('M Y') }}
                            </td>
                            <td class="px-3 py-2 text-slate-700">
                                {{ $budget->account->code ?? 'N/A' }} - {{ $budget->account->name ?? 'N/A' }}
                            </td>
                            <td class="px-3 py-2 text-slate-600">{{ $budget->branch_name }}</td>
                            <td class="px-3 py-2 text-right text-slate-900">KES {{ number_format($budget->budget_amount, 2) }}</td>
                            <td class="px-3 py-2 text-right text-amber-600">KES {{ number_format($budget->spent_amount, 2) }}</td>
                            <td class="px-3 py-2 text-right {{ $budget->balance >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                KES {{ number_format($budget->balance, 2) }}
                            </td>
                            <td class="px-3 py-2 text-right {{ abs($budget->variance) <= 10 ? 'text-emerald-600' : (abs($budget->variance) <= 20 ? 'text-amber-600' : 'text-red-600') }}">
                                {{ number_format($budget->variance, 1) }}%
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-3 py-6 text-center text-slate-400 text-xs">
                                No budget records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($budgets) && $budgets->hasPages())
            <div class="px-4 py-3 border-t border-slate-100">
                {{ $budgets->links() }}
            </div>
        @endif
    </div>
@endsection
