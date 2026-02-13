@extends('layouts.admin')

@section('page_title', 'Create Chart of Account')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
        <div>
            <a href="{{ route('admin.accounting.chart-of-accounts.index') }}" class="text-emerald-600 hover:text-emerald-700 text-sm mb-2 inline-flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Chart of Accounts
            </a>
            <h1 class="text-lg font-semibold text-slate-900">Create Chart of Account</h1>
            <p class="text-xs text-slate-500">Add a new account to the chart of accounts</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 text-sm text-emerald-700 bg-emerald-50 border border-emerald-200 px-4 py-2 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.accounting.chart-of-accounts.store') }}" method="POST" class="bg-white border border-slate-100 rounded-lg p-6">
        @csrf
        
        @if($errors->any())
            <div class="mb-4 text-sm text-red-700 bg-red-50 border border-red-200 px-4 py-2 rounded-lg">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="space-y-4">
            <div>
                <label class="block text-xs font-semibold mb-1 text-slate-700">
                    Account Code <span class="text-red-500">*</span>
                </label>
                <input type="text" name="code" value="{{ old('code') }}" required
                       class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-400"
                       placeholder="e.g., 1000, 2000">
                <p class="mt-1 text-xs text-slate-500">Unique code for this account</p>
            </div>

            <div>
                <label class="block text-xs font-semibold mb-1 text-slate-700">
                    Account Name <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-400"
                       placeholder="e.g., Cash, Accounts Receivable">
            </div>

            <div>
                <label class="block text-xs font-semibold mb-1 text-slate-700">
                    Account Type <span class="text-red-500">*</span>
                </label>
                <select name="type" required
                        class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-400">
                    <option value="">-- Select Type --</option>
                    @foreach($accountTypes as $type)
                        <option value="{{ $type }}" {{ old('type') == $type ? 'selected' : '' }}>
                            {{ $type }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold mb-1 text-slate-700">
                    Parent Account
                </label>
                <select name="parent_id"
                        class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-400">
                    <option value="">-- None (Top Level) --</option>
                    @foreach($parentAccounts as $parent)
                        <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                            {{ $parent->code }} - {{ $parent->name }}
                        </option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-slate-500">Optional: Select a parent account if this is a sub-account</p>
            </div>

            <div>
                <label class="block text-xs font-semibold mb-1 text-slate-700">
                    Description
                </label>
                <textarea name="description" rows="4"
                          class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-400"
                          placeholder="Optional description for this account">{{ old('description') }}</textarea>
            </div>

            <div>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                           class="w-4 h-4 text-emerald-600 border-slate-300 rounded focus:ring-emerald-500">
                    <span class="text-sm text-slate-700">Active</span>
                </label>
                <p class="mt-1 text-xs text-slate-500">Inactive accounts will not appear in dropdowns</p>
            </div>
        </div>

        <div class="flex items-center gap-3 pt-4 mt-4 border-t-2 border-slate-200">
            <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold px-4 py-2 rounded">
                Create Account
            </button>
            <a href="{{ route('admin.accounting.chart-of-accounts.index') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-700 text-sm font-semibold px-4 py-2 rounded">
                Cancel
            </a>
        </div>
    </form>
@endsection
