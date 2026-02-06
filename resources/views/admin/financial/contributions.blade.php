@extends('layouts.admin')

@section('page_title', 'Partner Contributions')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
        <div>
            <h1 class="text-lg font-semibold">Partner Contributions</h1>
            <p class="text-xs text-slate-500">Manage capital contributions, withdrawals, and profit distributions.</p>
        </div>
        <div class="flex gap-2">
            <button onclick="document.getElementById('contribution-form').classList.toggle('hidden')"
                    class="bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold px-4 py-2 rounded">
                Add Contribution
            </button>
            <a href="{{ route('admin.financial.index') }}" class="text-sm text-slate-600 hover:text-slate-800">
                Back to Financial
            </a>
        </div>
    </div>

    {{-- Add Contribution Form --}}
    <div id="contribution-form" class="hidden mb-4 bg-white border border-slate-100 rounded-lg p-4 shadow-sm">
        <h2 class="text-sm font-semibold text-slate-900 mb-4">Record New Contribution</h2>
        <form action="{{ route('admin.financial.contributions.store') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold mb-1 text-slate-700">Partner <span class="text-red-500">*</span></label>
                    <select name="partner_id" required
                            class="border border-slate-200 rounded w-full px-3 py-2 text-sm">
                        <option value="">Select Partner</option>
                        @foreach($partners ?? [] as $partner)
                            <option value="{{ $partner->id }}">{{ $partner->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1 text-slate-700">Type <span class="text-red-500">*</span></label>
                    <select name="type" required
                            class="border border-slate-200 rounded w-full px-3 py-2 text-sm">
                        <option value="contribution">Contribution</option>
                        <option value="withdrawal">Withdrawal</option>
                        <option value="profit_distribution">Profit Distribution</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1 text-slate-700">Amount <span class="text-red-500">*</span></label>
                    <div class="flex gap-2">
                        <select name="currency" required class="border border-slate-200 rounded px-3 py-2 text-sm">
                            <option value="USD">USD</option>
                            <option value="KES">KES</option>
                        </select>
                        <input type="number" name="amount" step="0.01" min="0" required
                               class="border border-slate-200 rounded flex-1 px-3 py-2 text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1 text-slate-700">Date <span class="text-red-500">*</span></label>
                    <input type="date" name="contributed_at" value="{{ now()->format('Y-m-d') }}" required
                           class="border border-slate-200 rounded w-full px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1 text-slate-700">Reference</label>
                    <input type="text" name="reference"
                           class="border border-slate-200 rounded w-full px-3 py-2 text-sm"
                           placeholder="Transaction reference">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1 text-slate-700">Notes</label>
                    <textarea name="notes" rows="2"
                              class="border border-slate-200 rounded w-full px-3 py-2 text-sm"></textarea>
                </div>
            </div>
            <div class="flex gap-2 pt-2">
                <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold px-4 py-2 rounded">
                    Record Contribution
                </button>
                <button type="button" onclick="document.getElementById('contribution-form').classList.add('hidden')"
                        class="bg-slate-500 hover:bg-slate-600 text-white text-xs font-semibold px-4 py-2 rounded">
                    Cancel
                </button>
            </div>
        </form>
    </div>

    <form method="GET" class="mb-4 bg-white border border-slate-100 rounded-lg p-3 text-xs">
        <select name="status" class="border border-slate-200 rounded px-3 py-2 text-sm">
            <option value="">All Statuses</option>
            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
        </select>
        <button type="submit" class="ml-2 bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2 rounded text-xs">
            Filter
        </button>
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
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">Partner</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($contributions as $contribution)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3">{{ $contribution->contributed_at->format('d M Y') }}</td>
                            <td class="px-4 py-3 font-medium">{{ $contribution->partner->name }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full px-2 py-1 text-[10px] font-medium
                                    {{ $contribution->type === 'contribution' ? 'bg-blue-100 text-blue-700' : 
                                       ($contribution->type === 'withdrawal' ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700') }}">
                                    {{ ucfirst(str_replace('_', ' ', $contribution->type)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 font-semibold">{{ $contribution->currency }} {{ number_format($contribution->amount, 2) }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full px-2 py-1 text-[10px] font-medium
                                    {{ $contribution->status === 'approved' ? 'bg-emerald-100 text-emerald-700' : 
                                       ($contribution->status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') }}">
                                    {{ ucfirst($contribution->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if($contribution->status === 'pending')
                                    <form action="{{ route('admin.financial.contributions.approve', $contribution) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-emerald-600 hover:text-emerald-700 text-xs">Approve</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-slate-500 text-sm">
                                No contributions found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $contributions->links() }}
    </div>
@endsection
