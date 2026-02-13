@extends('layouts.admin')

@section('page_title', 'Chart of Account Details')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
        <div>
            <a href="{{ route('admin.accounting.chart-of-accounts.index') }}" class="text-emerald-600 hover:text-emerald-700 text-sm mb-2 inline-flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Chart of Accounts
            </a>
            <h1 class="text-lg font-semibold text-slate-900">Chart of Account Details</h1>
            <p class="text-xs text-slate-500">View account information and related records</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.accounting.chart-of-accounts.edit', $chartOfAccount) }}" class="bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-semibold px-4 py-2 rounded">
                Edit
            </a>
        </div>
    </div>

    <div class="grid md:grid-cols-2 gap-6">
        {{-- Account Information --}}
        <div class="bg-white border border-slate-100 rounded-lg p-6">
            <h2 class="text-sm font-semibold text-slate-900 mb-4">Account Information</h2>
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Account Code</label>
                    <p class="text-sm text-slate-900 font-mono">{{ $chartOfAccount->code }}</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Account Name</label>
                    <p class="text-sm text-slate-900">{{ $chartOfAccount->name }}</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Account Type</label>
                    <span class="inline-block px-2 py-1 text-xs font-semibold rounded
                        {{ $chartOfAccount->type == 'ASSET' ? 'bg-blue-100 text-blue-800' : '' }}
                        {{ $chartOfAccount->type == 'LIABILITY' ? 'bg-red-100 text-red-800' : '' }}
                        {{ $chartOfAccount->type == 'EQUITY' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $chartOfAccount->type == 'INCOME' ? 'bg-purple-100 text-purple-800' : '' }}
                        {{ $chartOfAccount->type == 'EXPENSE' ? 'bg-orange-100 text-orange-800' : '' }}">
                        {{ $chartOfAccount->type }}
                    </span>
                </div>
                @if($chartOfAccount->parent)
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Parent Account</label>
                    <a href="{{ route('admin.accounting.chart-of-accounts.show', $chartOfAccount->parent) }}" class="text-sm text-emerald-600 hover:text-emerald-700">
                        {{ $chartOfAccount->parent->code }} - {{ $chartOfAccount->parent->name }}
                    </a>
                </div>
                @endif
                @if($chartOfAccount->description)
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Description</label>
                    <p class="text-sm text-slate-700">{{ $chartOfAccount->description }}</p>
                </div>
                @endif
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Status</label>
                    <span class="inline-block px-2 py-1 text-xs font-semibold rounded {{ $chartOfAccount->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-800' }}">
                        {{ $chartOfAccount->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Related Information --}}
        <div class="space-y-6">
            {{-- Child Accounts --}}
            @if($chartOfAccount->children->count() > 0)
            <div class="bg-white border border-slate-100 rounded-lg p-6">
                <h2 class="text-sm font-semibold text-slate-900 mb-4">Child Accounts ({{ $chartOfAccount->children->count() }})</h2>
                <div class="space-y-2">
                    @foreach($chartOfAccount->children as $child)
                        <a href="{{ route('admin.accounting.chart-of-accounts.show', $child) }}" class="block p-2 hover:bg-slate-50 rounded text-sm text-slate-700">
                            <span class="font-mono">{{ $child->code }}</span> - {{ $child->name }}
                        </a>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Journal Entries --}}
            @if($chartOfAccount->journalEntryLines->count() > 0)
            <div class="bg-white border border-slate-100 rounded-lg p-6">
                <h2 class="text-sm font-semibold text-slate-900 mb-4">Journal Entries ({{ $chartOfAccount->journalEntryLines->count() }})</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700">Date</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700">Type</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($chartOfAccount->journalEntryLines->take(10) as $line)
                                <tr class="{{ $loop->even ? 'bg-slate-50' : 'bg-white' }}">
                                    <td class="px-3 py-2 text-slate-700">
                                        {{ $line->journalEntry->transaction_date->format('M d, Y') }}
                                    </td>
                                    <td class="px-3 py-2">
                                        <span class="px-2 py-1 text-xs font-semibold rounded
                                            {{ $line->entry_type == 'debit' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                            {{ strtoupper($line->entry_type) }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2 text-slate-900 font-medium">
                                        {{ number_format($line->amount, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($chartOfAccount->journalEntryLines->count() > 10)
                    <p class="mt-2 text-xs text-slate-500 text-center">Showing first 10 of {{ $chartOfAccount->journalEntryLines->count() }} entries</p>
                @endif
            </div>
            @endif
        </div>
    </div>

    {{-- Actions --}}
    <div class="mt-6 bg-white border border-slate-100 rounded-lg p-6">
        <h2 class="text-sm font-semibold text-slate-900 mb-4">Actions</h2>
        <div class="flex gap-2">
            <a href="{{ route('admin.accounting.chart-of-accounts.edit', $chartOfAccount) }}" class="bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-semibold px-4 py-2 rounded">
                Edit Account
            </a>
            @if($chartOfAccount->journalEntryLines->count() == 0 && $chartOfAccount->children->count() == 0)
            <form action="{{ route('admin.accounting.chart-of-accounts.destroy', $chartOfAccount) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this account? This action cannot be undone.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white text-xs font-semibold px-4 py-2 rounded">
                    Delete Account
                </button>
            </form>
            @else
            <button disabled class="bg-slate-300 text-slate-500 text-xs font-semibold px-4 py-2 rounded cursor-not-allowed" title="Cannot delete account with journal entries or child accounts">
                Delete Account
            </button>
            @endif
        </div>
    </div>
@endsection
