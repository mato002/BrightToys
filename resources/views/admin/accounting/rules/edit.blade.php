@extends('layouts.admin')

@section('page_title', 'Edit Accounting Rule')

@section('content')
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 mb-4">
        <div>
            <a href="{{ route('admin.accounting.chart-of-accounts.index') }}" class="text-emerald-600 hover:text-emerald-700 text-sm mb-2 inline-flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Chart of Accounts
            </a>
            <h1 class="text-lg font-semibold text-slate-900">Edit Accounting Rule</h1>
            <p class="text-xs text-slate-500">Update automatic journal entry rule</p>
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
        <form action="{{ route('admin.accounting.rules.update', $accountingRule) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                {{-- Rule Name --}}
                <div>
                    <label for="name" class="block text-xs font-semibold mb-1 text-slate-700">
                        Rule Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name', $accountingRule->name) }}"
                           required
                           class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-400">
                </div>

                {{-- Trigger Event --}}
                <div>
                    <label for="trigger_event" class="block text-xs font-semibold mb-1 text-slate-700">
                        Trigger Event <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="trigger_event" 
                           name="trigger_event" 
                           value="{{ old('trigger_event', $accountingRule->trigger_event) }}"
                           required
                           list="common-triggers"
                           class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-400">
                    <datalist id="common-triggers">
                        @foreach($commonTriggers as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </datalist>
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
                            <option value="{{ $account->id }}" {{ old('debit_account_id', $accountingRule->debit_account_id) == $account->id ? 'selected' : '' }}>
                                {{ $account->code }} - {{ $account->name }} ({{ $account->type }})
                            </option>
                        @endforeach
                    </select>
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
                            <option value="{{ $account->id }}" {{ old('credit_account_id', $accountingRule->credit_account_id) == $account->id ? 'selected' : '' }}>
                                {{ $account->code }} - {{ $account->name }} ({{ $account->type }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Description --}}
                <div>
                    <label for="description" class="block text-xs font-semibold mb-1 text-slate-700">
                        Description
                    </label>
                    <textarea id="description" 
                              name="description" 
                              rows="3"
                              class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-400">{{ old('description', $accountingRule->description) }}</textarea>
                </div>

                {{-- Active Status --}}
                <div>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" 
                               name="is_active" 
                               value="1"
                               {{ old('is_active', $accountingRule->is_active) ? 'checked' : '' }}
                               class="w-4 h-4 text-emerald-600 border-slate-300 rounded focus:ring-emerald-500">
                        <span class="text-sm text-slate-700">Active (rule will be applied automatically)</span>
                    </label>
                </div>
            </div>

            <div class="mt-6 flex gap-3">
                <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold px-6 py-2 rounded">
                    Update Rule
                </button>
                <a href="{{ route('admin.accounting.chart-of-accounts.index') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-700 text-sm font-semibold px-6 py-2 rounded">
                    Cancel
                </a>
            </div>
        </form>
    </div>

@endsection
