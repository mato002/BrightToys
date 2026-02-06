@extends('layouts.admin')

@section('page_title', 'Financial Records')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
        <div>
            <h1 class="text-lg font-semibold">Financial Records</h1>
            <p class="text-xs text-slate-500">Manage expenses, income, and financial transactions.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.financial.contributions') }}"
               class="bg-blue-500 hover:bg-blue-600 text-white text-xs font-semibold px-4 py-2 rounded">
                Contributions
            </a>
            <a href="{{ route('admin.financial.create') }}"
               class="bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold px-4 py-2 rounded">
                Add Expense
            </a>
        </div>
    </div>

    {{-- Financial Summary Cards --}}
    <div class="grid md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white border border-slate-100 rounded-lg p-4">
            <p class="text-xs text-slate-500 mb-1">Total Revenue</p>
            <p class="text-lg font-semibold text-emerald-600">${{ number_format($totalRevenue ?? 0, 2) }}</p>
        </div>
        <div class="bg-white border border-slate-100 rounded-lg p-4">
            <p class="text-xs text-slate-500 mb-1">Total Contributions</p>
            <p class="text-lg font-semibold text-blue-600">${{ number_format($totalContributions ?? 0, 2) }}</p>
        </div>
        <div class="bg-white border border-slate-100 rounded-lg p-4">
            <p class="text-xs text-slate-500 mb-1">Total Expenses</p>
            <p class="text-lg font-semibold text-red-600">${{ number_format($totalExpenses ?? 0, 2) }}</p>
        </div>
        <div class="bg-white border border-slate-100 rounded-lg p-4">
            <p class="text-xs text-slate-500 mb-1">Pending Approvals</p>
            <p class="text-lg font-semibold text-amber-600">{{ $pendingApprovals ?? 0 }}</p>
        </div>
    </div>

    <form method="GET" class="mb-4 bg-white border border-slate-100 rounded-lg p-3 text-xs grid md:grid-cols-4 gap-3">
        <select name="type" class="border border-slate-200 rounded px-3 py-2 text-sm">
            <option value="">All Types</option>
            <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>Expense</option>
            <option value="adjustment" {{ request('type') === 'adjustment' ? 'selected' : '' }}>Adjustment</option>
            <option value="other_income" {{ request('type') === 'other_income' ? 'selected' : '' }}>Other Income</option>
        </select>
        <select name="status" class="border border-slate-200 rounded px-3 py-2 text-sm">
            <option value="">All Statuses</option>
            <option value="pending_approval" {{ request('status') === 'pending_approval' ? 'selected' : '' }}>Pending</option>
            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
        </select>
        <input type="date" name="from" value="{{ request('from') }}" placeholder="From date"
               class="border border-slate-200 rounded px-3 py-2 text-sm">
        <div class="flex gap-2">
            <input type="date" name="to" value="{{ request('to') }}" placeholder="To date"
                   class="border border-slate-200 rounded px-3 py-2 text-sm flex-1">
            <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2 rounded text-xs">
                Filter
            </button>
        </div>
    </form>

    @if(session('success'))
        <div class="mb-4 text-sm text-emerald-700 bg-emerald-50 border border-emerald-200 px-4 py-2 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white border border-slate-100 rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">Description</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($records as $record)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3">{{ $record->occurred_at->format('d M Y') }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full px-2 py-1 text-[10px] font-medium
                                    {{ $record->type === 'expense' ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700' }}">
                                    {{ ucfirst(str_replace('_', ' ', $record->type)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-slate-900">{{ Str::limit($record->description ?? '—', 40) }}</div>
                                @if($record->category)
                                    <div class="text-xs text-slate-500">{{ $record->category }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 font-semibold
                                {{ $record->type === 'expense' ? 'text-red-600' : 'text-emerald-600' }}">
                                {{ $record->currency }} {{ number_format($record->amount, 2) }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full px-2 py-1 text-[10px] font-medium
                                    {{ $record->status === 'approved' ? 'bg-emerald-100 text-emerald-700' : 
                                       ($record->status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') }}">
                                    {{ ucfirst(str_replace('_', ' ', $record->status)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.financial.show', $record) }}"
                                       class="text-emerald-600 hover:text-emerald-700 text-xs">View</a>
                                    @if($record->status === 'pending_approval')
                                        <form action="{{ route('admin.financial.approve', $record) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-emerald-600 hover:text-emerald-700 text-xs">Approve</button>
                                        </form>
                                        <form action="{{ route('admin.financial.reject', $record) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-red-600 hover:text-red-700 text-xs">Reject</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-slate-500 text-sm">
                                No financial records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $records->links() }}
    </div>
@endsection
