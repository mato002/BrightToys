@extends('layouts.admin')

@section('page_title', 'Chart of Accounts')

@section('content')
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 mb-4">
        <div>
            <a href="{{ route('admin.accounting.dashboard') }}" class="text-emerald-600 hover:text-emerald-700 text-sm mb-2 inline-flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Books of Account
            </a>
            <h1 class="text-lg font-semibold text-slate-900">Chart of Accounts</h1>
            <p class="text-xs text-slate-500">List of Accounts used by the Company & accounting rules</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.accounting.chart-of-accounts.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white text-xs font-semibold px-4 py-2 rounded">
                Create Account
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 text-sm text-emerald-700 bg-emerald-50 border border-emerald-200 px-4 py-2 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 text-sm text-red-700 bg-red-50 border border-red-200 px-4 py-2 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    {{-- Accounts List --}}
    <div class="bg-white border border-slate-100 rounded-lg p-4 mb-6">
        <h2 class="text-sm font-semibold text-slate-900 mb-4">Accounts List</h2>
        <div class="space-y-4">
            @foreach($accountTypes as $type)
                @if(isset($accounts[$type]) && $accounts[$type]->count() > 0)
                    <div>
                        <h3 class="text-xs font-semibold text-slate-700 mb-2">{{ $type }}</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-slate-50 border-b border-slate-200">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700">Code</th>
                                        <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700">Name</th>
                                        <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700">Parent</th>
                                        <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700">Status</th>
                                        <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($accounts[$type] as $account)
                                        <tr class="{{ $loop->even ? 'bg-slate-50' : 'bg-white' }}">
                                            <td class="px-3 py-2 font-mono text-slate-900">{{ $account->code }}</td>
                                            <td class="px-3 py-2 text-slate-700">{{ $account->name }}</td>
                                            <td class="px-3 py-2 text-slate-600">
                                                @if($account->parent)
                                                    <a href="{{ route('admin.accounting.chart-of-accounts.show', $account->parent) }}" class="text-emerald-600 hover:text-emerald-700">
                                                        {{ $account->parent->code }}
                                                    </a>
                                                @else
                                                    <span class="text-slate-400">—</span>
                                                @endif
                                            </td>
                                            <td class="px-3 py-2">
                                                <span class="px-2 py-1 text-xs font-semibold rounded {{ $account->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-800' }}">
                                                    {{ $account->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td class="px-3 py-2">
                                                <div class="flex items-center gap-2">
                                                    <a href="{{ route('admin.accounting.chart-of-accounts.show', $account) }}" class="text-blue-600 hover:text-blue-700" title="View">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                                            <circle cx="12" cy="12" r="3" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                                        </svg>
                                                    </a>
                                                    <a href="{{ route('admin.accounting.chart-of-accounts.edit', $account) }}" class="text-emerald-600 hover:text-emerald-700" title="Edit">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                                        </svg>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>

    <div class="grid md:grid-cols-2 gap-6">
        {{-- Left Column: Account Types & Wallet Mappings --}}
        <div class="space-y-6">
            {{-- Account Types --}}
            <div class="bg-white border border-slate-100 rounded-lg p-4">
                <h2 class="text-sm font-semibold text-slate-900 mb-3">Account Types</h2>
                <div class="space-y-2">
                    @foreach($accountTypes as $type)
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="account_types[]" value="{{ $type }}"
                                   class="w-4 h-4 text-emerald-600 border-slate-300 rounded focus:ring-emerald-500">
                            <span class="text-sm text-slate-700">{{ $type }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Set Wallet Accounts --}}
            <div class="bg-white border border-slate-100 rounded-lg p-4">
                <h2 class="text-sm font-semibold text-slate-900 mb-3">Set Wallet Accounts</h2>
                <form action="{{ route('admin.accounting.chart-of-accounts.update-mappings') }}" method="POST">
                    @csrf
                    <div class="space-y-3">
                        @foreach($walletTypes as $walletType => $label)
                            <div>
                                <label class="block text-xs font-semibold mb-1 text-slate-700">{{ $loop->iteration }}. {{ $label }}</label>
                                <select name="mappings[{{ $walletType }}]" required
                                        class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-400">
                                    <option value="">-- Select Account --</option>
                                    @foreach($accounts->flatten() as $account)
                                        <option value="{{ $account->id }}"
                                                {{ ($walletMappings[$walletType]->account_id ?? null) == $account->id ? 'selected' : '' }}>
                                            {{ $account->code }} - {{ $account->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endforeach
                    </div>
                    <button type="submit" class="mt-4 w-full bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold px-4 py-2 rounded">
                        Update
                    </button>
                </form>
            </div>
        </div>

        {{-- Right Column: Accounting Rules --}}
        <div class="bg-white border border-slate-100 rounded-lg p-4">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-semibold text-slate-900">Accounting Rules</h2>
                <a href="{{ route('admin.accounting.rules.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white text-xs font-semibold px-3 py-1.5 rounded">
                    + Create Rule
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700">Rule</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700">Trigger</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700">Debit</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700">Credit</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700">Status</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($rules as $rule)
                            <tr class="{{ $loop->even ? 'bg-slate-50' : 'bg-white' }}">
                                <td class="px-3 py-2 text-slate-900 font-medium">{{ $rule->name }}</td>
                                <td class="px-3 py-2">
                                    <span class="text-xs font-mono text-slate-600 bg-slate-100 px-2 py-1 rounded">{{ $rule->trigger_event }}</span>
                                </td>
                                <td class="px-3 py-2">
                                    <div class="flex items-center gap-2">
                                        <span class="text-slate-700">{{ $rule->debitAccount->name }}</span>
                                        <a href="{{ route('admin.accounting.chart-of-accounts.edit', $rule->debit_account_id) }}" class="text-emerald-600 hover:text-emerald-700" title="Edit Account">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                                <td class="px-3 py-2 text-slate-700">{{ $rule->creditAccount->name }}</td>
                                <td class="px-3 py-2">
                                    <span class="px-2 py-1 text-xs font-semibold rounded {{ $rule->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-800' }}">
                                        {{ $rule->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-3 py-2">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('admin.accounting.rules.edit', $rule) }}" class="text-emerald-600 hover:text-emerald-700" title="Edit Rule">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </a>
                                        <form action="{{ route('admin.accounting.rules.destroy', $rule) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this rule?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-700" title="Delete Rule">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                    <path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-3 py-4 text-center text-slate-500 text-xs">
                                    No accounting rules configured yet.
                                    <a href="{{ route('admin.accounting.rules.create') }}" class="text-blue-600 hover:text-blue-700 ml-1">Create one</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection
