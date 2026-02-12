@extends('layouts.admin')

@section('page_title', 'Partner Contributions')

@section('content')
    {{-- Success/Error Messages --}}
    @if(session('success'))
        <div class="mb-4 rounded-lg bg-emerald-50 border border-emerald-200 p-3 text-sm text-emerald-800">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 p-3 text-sm text-red-800">
            {{ session('error') }}
        </div>
    @endif

    <div class="mb-6 flex flex-col md:flex-row md:items-end md:justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-emerald-500 mb-2">Financial</p>
            <h2 class="text-xl md:text-2xl font-semibold text-slate-900 tracking-tight">Partner Contributions</h2>
            <p class="text-xs md:text-sm text-slate-500 mt-1 max-w-xl">
                Manage capital contributions, withdrawals, and profit distributions.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="document.getElementById('contribution-form').classList.toggle('hidden')"
                    class="inline-flex items-center justify-center bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold px-4 py-2 rounded-md shadow-sm transition-colors">
                Add Contribution
            </button>
            <a href="{{ route('admin.financial.index') }}" 
               class="inline-flex items-center justify-center text-xs font-medium text-slate-600 hover:text-slate-800">
                Back to Financial
            </a>
        </div>
    </div>

    {{-- Add Contribution Form --}}
    <div id="contribution-form" class="hidden mb-6 bg-white border border-slate-200 rounded-2xl shadow-sm">
        <div class="px-4 py-3 border-b border-slate-100">
            <h2 class="text-sm font-semibold text-slate-900">Record New Contribution</h2>
        </div>
        <form action="{{ route('admin.financial.contributions.store') }}" method="POST" class="p-4 space-y-4">
            @csrf
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold mb-1.5 text-slate-700">Partner <span class="text-red-500">*</span></label>
                    <select name="partner_id" required
                            class="border border-slate-200 rounded-md w-full px-3 py-2 text-xs focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 @error('partner_id') border-red-300 @enderror">
                        <option value="">Select Partner</option>
                        @foreach($partners ?? [] as $partner)
                            <option value="{{ $partner->id }}" {{ old('partner_id') == $partner->id ? 'selected' : '' }}>{{ $partner->name }}</option>
                        @endforeach
                    </select>
                    @error('partner_id')
                        <p class="text-[10px] text-red-600 mt-0.5">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5 text-slate-700">Type <span class="text-red-500">*</span></label>
                    <select name="type" required
                            class="border border-slate-200 rounded-md w-full px-3 py-2 text-xs focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 @error('type') border-red-300 @enderror">
                        <option value="contribution" {{ old('type') == 'contribution' ? 'selected' : '' }}>Contribution</option>
                        <option value="withdrawal" {{ old('type') == 'withdrawal' ? 'selected' : '' }}>Withdrawal</option>
                        <option value="profit_distribution" {{ old('type') == 'profit_distribution' ? 'selected' : '' }}>Profit Distribution</option>
                    </select>
                    @error('type')
                        <p class="text-[10px] text-red-600 mt-0.5">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5 text-slate-700">Amount <span class="text-red-500">*</span></label>
                    <div class="flex gap-2">
                        <select name="currency" required class="border border-slate-200 rounded-md px-3 py-2 text-xs focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="KES" {{ old('currency', 'KES') == 'KES' ? 'selected' : '' }}>KES</option>
                            <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD</option>
                        </select>
                        <input type="number" name="amount" step="0.01" min="0.01" required value="{{ old('amount') }}"
                               class="border border-slate-200 rounded-md flex-1 px-3 py-2 text-xs focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 @error('amount') border-red-300 @enderror">
                    </div>
                    @error('amount')
                        <p class="text-[10px] text-red-600 mt-0.5">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5 text-slate-700">Date <span class="text-red-500">*</span></label>
                    <input type="date" name="contributed_at" value="{{ old('contributed_at', now()->format('Y-m-d')) }}" required
                           class="border border-slate-200 rounded-md w-full px-3 py-2 text-xs focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 @error('contributed_at') border-red-300 @enderror">
                    @error('contributed_at')
                        <p class="text-[10px] text-red-600 mt-0.5">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5 text-slate-700">Fund Type (for contributions)</label>
                    <select name="fund_type"
                            class="border border-slate-200 rounded-md w-full px-3 py-2 text-xs focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="investment" {{ old('fund_type', 'investment') == 'investment' ? 'selected' : '' }}>Investment</option>
                        <option value="welfare" {{ old('fund_type') == 'welfare' ? 'selected' : '' }}>Welfare</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5 text-slate-700">Reference</label>
                    <input type="text" name="reference" value="{{ old('reference') }}"
                           class="border border-slate-200 rounded-md w-full px-3 py-2 text-xs focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500"
                           placeholder="Transaction reference">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1.5 text-slate-700">Notes</label>
                <textarea name="notes" rows="3"
                          class="border border-slate-200 rounded-md w-full px-3 py-2 text-xs focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500">{{ old('notes') }}</textarea>
            </div>
            <div class="flex gap-2 pt-2">
                <button type="submit" class="inline-flex items-center justify-center bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold px-4 py-2 rounded-md shadow-sm transition-colors">
                    Record Contribution
                </button>
                <button type="button" onclick="document.getElementById('contribution-form').classList.add('hidden')"
                        class="inline-flex items-center justify-center bg-slate-500 hover:bg-slate-600 text-white text-xs font-semibold px-4 py-2 rounded-md shadow-sm transition-colors">
                    Cancel
                </button>
            </div>
        </form>
    </div>

    {{-- Filters --}}
    <form method="GET" class="mb-6 bg-white border border-slate-200 rounded-2xl shadow-sm p-4">
        <div class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[150px]">
                <label class="block text-[11px] font-semibold mb-1.5 text-slate-700 uppercase tracking-wide">Status</label>
                <select name="status" class="border border-slate-200 rounded-md w-full px-3 py-2 text-xs focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div class="flex-1 min-w-[150px]">
                <label class="block text-[11px] font-semibold mb-1.5 text-slate-700 uppercase tracking-wide">Fund Type</label>
                <select name="fund_type" class="border border-slate-200 rounded-md w-full px-3 py-2 text-xs focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">All</option>
                    <option value="investment" {{ request('fund_type') === 'investment' ? 'selected' : '' }}>Investment</option>
                    <option value="welfare" {{ request('fund_type') === 'welfare' ? 'selected' : '' }}>Welfare</option>
                </select>
            </div>
            <div class="flex-1 min-w-[150px]">
                <label class="block text-[11px] font-semibold mb-1.5 text-slate-700 uppercase tracking-wide">Partner</label>
                <select name="partner_id" class="border border-slate-200 rounded-md w-full px-3 py-2 text-xs focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">All Partners</option>
                    @foreach($partners ?? [] as $partner)
                        <option value="{{ $partner->id }}" {{ request('partner_id') == $partner->id ? 'selected' : '' }}>{{ $partner->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <button type="submit" class="inline-flex items-center justify-center bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-semibold px-4 py-2 rounded-md shadow-sm transition-colors">
                    Filter
                </button>
            </div>
        </div>
    </form>

    {{-- Contributions Table --}}
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-100">
            <h2 class="text-sm font-semibold text-slate-900">Contributions List</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Partner</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Fund Type</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-700 uppercase tracking-wider">Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-100">
                    @forelse($contributions as $contribution)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3 text-xs text-slate-900">
                                {{ $contribution->contributed_at->format('d M Y') }}
                            </td>
                            <td class="px-4 py-3 text-xs font-medium text-slate-900">
                                {{ $contribution->partner->name ?? 'N/A' }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full px-2 py-1 text-[10px] font-medium border
                                    {{ $contribution->type === 'contribution' ? 'bg-blue-50 text-blue-700 border-blue-200' : 
                                       ($contribution->type === 'withdrawal' ? 'bg-red-50 text-red-700 border-red-200' : 'bg-emerald-50 text-emerald-700 border-emerald-200') }}">
                                    {{ ucfirst(str_replace('_', ' ', $contribution->type)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-600">
                                @if($contribution->fund_type)
                                    <span class="capitalize">{{ $contribution->fund_type }}</span>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right text-xs font-semibold text-slate-900">
                                {{ $contribution->currency }} {{ number_format($contribution->amount, 2) }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full px-2 py-1 text-[10px] font-medium border
                                    {{ $contribution->status === 'approved' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 
                                       ($contribution->status === 'rejected' ? 'bg-red-50 text-red-700 border-red-200' : 'bg-amber-50 text-amber-700 border-amber-200') }}">
                                    {{ ucfirst($contribution->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.financial.contributions.show', $contribution) }}"
                                       class="inline-flex items-center text-xs text-slate-600 hover:text-emerald-700">
                                        View
                                    </a>
                                    @if($contribution->status === 'pending')
                                        <form action="{{ route('admin.financial.contributions.approve', $contribution) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="inline-flex items-center text-xs text-emerald-600 hover:text-emerald-700 font-medium">
                                                Approve
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.financial.contributions.reject', $contribution) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="inline-flex items-center text-xs text-red-600 hover:text-red-700 font-medium"
                                                    onclick="return confirm('Are you sure you want to reject this contribution?')">
                                                Reject
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-slate-300 mb-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2z"/>
                                    </svg>
                                    <p class="text-sm font-medium text-slate-500">No contributions found</p>
                                    <p class="text-xs text-slate-400 mt-1">
                                        @if(request('status') || request('fund_type') || request('partner_id'))
                                            Try adjusting your filters
                                        @else
                                            Start by recording a new contribution
                                        @endif
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if($contributions->hasPages())
        <div class="mt-6">
            {{ $contributions->links() }}
        </div>
    @endif
@endsection
