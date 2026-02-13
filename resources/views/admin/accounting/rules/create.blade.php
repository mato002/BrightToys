@extends('layouts.admin')

@section('page_title', 'Create Accounting Rule')

@section('content')
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 mb-4">
        <div>
            <a href="{{ route('admin.accounting.chart-of-accounts.index') }}" class="text-emerald-600 hover:text-emerald-700 text-sm mb-2 inline-flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Chart of Accounts
            </a>
            <h1 class="text-lg font-semibold text-slate-900">Create Accounting Rule</h1>
            <p class="text-xs text-slate-500">Define automatic journal entries for frequent transactions</p>
        </div>
    </div>

    @if($errors->any())
        <div class="mb-4 text-sm text-red-700 bg-red-50 border border-red-200 px-4 py-2 rounded-lg">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white border border-slate-100 rounded-lg p-6">
        <form action="{{ route('admin.accounting.rules.store') }}" method="POST">
            @csrf

            <div class="space-y-4">
                {{-- Rule Name --}}
                <div>
                    <label for="name" class="block text-xs font-semibold mb-1 text-slate-700">
                        Rule Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name') }}"
                           required
                           class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-400"
                           placeholder="e.g., Salary Advances, Loan Ledger">
                    <p class="mt-1 text-xs text-slate-500">A descriptive name for this accounting rule</p>
                </div>

                {{-- Trigger Event --}}
                <div>
                    <label for="trigger_event" class="block text-xs font-semibold mb-1 text-slate-700">
                        Trigger Event <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="trigger_event" 
                           name="trigger_event" 
                           value="{{ old('trigger_event') }}"
                           required
                           list="common-triggers"
                           class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-400"
                           placeholder="e.g., salary_advance, loan_ledger">
                    <datalist id="common-triggers">
                        @foreach($commonTriggers as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </datalist>
                    <p class="mt-1 text-xs text-slate-500">The event code that triggers this rule (used in code: e.g., 'salary_advance')</p>
                </div>

                {{-- Debit Account --}}
                <div>
                    <label for="debit_account_id" class="block text-xs font-semibold mb-1 text-slate-700">
                        Debit Account <span class="text-red-500">*</span>
                    </label>
                    <select id="debit_account_id" 
                            name="debit_account_id" 
                            required
                            class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-400">
                        <option value="">-- Select Debit Account --</option>
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}" {{ old('debit_account_id') == $account->id ? 'selected' : '' }}>
                                {{ $account->code }} - {{ $account->name }} ({{ $account->type }})
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-slate-500">Account to be debited when this rule is triggered</p>
                </div>

                {{-- Credit Account --}}
                <div>
                    <label for="credit_account_id" class="block text-xs font-semibold mb-1 text-slate-700">
                        Credit Account <span class="text-red-500">*</span>
                    </label>
                    <select id="credit_account_id" 
                            name="credit_account_id" 
                            required
                            class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-400">
                        <option value="">-- Select Credit Account --</option>
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}" {{ old('credit_account_id') == $account->id ? 'selected' : '' }}>
                                {{ $account->code }} - {{ $account->name }} ({{ $account->type }})
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-slate-500">Account to be credited when this rule is triggered</p>
                </div>

                {{-- Description --}}
                <div>
                    <label for="description" class="block text-xs font-semibold mb-1 text-slate-700">
                        Description
                    </label>
                    <textarea id="description" 
                              name="description" 
                              rows="3"
                              class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-400"
                              placeholder="Optional description of when and how this rule is used">{{ old('description') }}</textarea>
                </div>

                {{-- Active Status --}}
                <div>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" 
                               name="is_active" 
                               value="1"
                               {{ old('is_active', true) ? 'checked' : '' }}
                               class="w-4 h-4 text-emerald-600 border-slate-300 rounded focus:ring-emerald-500">
                        <span class="text-sm text-slate-700">Active (rule will be applied automatically)</span>
                    </label>
                </div>
            </div>

            {{-- Info Box --}}
            <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <h3 class="text-xs font-semibold text-blue-900 mb-2">How Accounting Rules Work:</h3>
                <ul class="text-xs text-blue-800 space-y-1 list-disc list-inside">
                    <li>When a transaction with the specified trigger event occurs, this rule automatically creates a journal entry</li>
                    <li>The specified amount will be debited to the Debit Account and credited to the Credit Account</li>
                    <li>This ensures consistent double-entry bookkeeping without manual journal entry creation</li>
                    <li>Rules can be activated or deactivated without deletion</li>
                </ul>
            </div>

            <div class="mt-6 flex gap-3">
                <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold px-6 py-2 rounded">
                    Create Rule
                </button>
                <a href="{{ route('admin.accounting.chart-of-accounts.index') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-700 text-sm font-semibold px-6 py-2 rounded">
                    Cancel
                </a>
            </div>
        </form>
    </div>

@endsection
