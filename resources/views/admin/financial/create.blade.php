@extends('layouts.admin')

@section('page_title', 'Add Financial Record')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
        <div>
            <h1 class="text-lg font-semibold">Add Financial Record</h1>
            <p class="text-xs text-slate-500">Record an expense, adjustment, or other income.</p>
        </div>
        <a href="{{ route('admin.financial.index') }}" class="text-sm text-slate-600 hover:text-slate-800">
            Back to list
        </a>
    </div>

    <form action="{{ route('admin.financial.store') }}" method="POST" enctype="multipart/form-data" 
          class="bg-white border border-slate-100 rounded-lg p-4 text-sm space-y-4 shadow-sm max-w-2xl">
        @csrf
        
        @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-lg p-3 text-xs text-red-700">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div>
            <label class="block text-xs font-semibold mb-1 text-slate-700">Type <span class="text-red-500">*</span></label>
            <select name="type" required
                    class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-400">
                <option value="expense" {{ old('type') === 'expense' ? 'selected' : '' }}>Expense</option>
                <option value="adjustment" {{ old('type') === 'adjustment' ? 'selected' : '' }}>Adjustment</option>
                <option value="other_income" {{ old('type') === 'other_income' ? 'selected' : '' }}>Other Income</option>
            </select>
        </div>

        <div>
            <label class="block text-xs font-semibold mb-1 text-slate-700">Category</label>
            <input type="text" name="category" value="{{ old('category') }}"
                   class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-400"
                   placeholder="e.g., Purchase, Construction, Logistics, Tax, Operations">
            <p class="text-[10px] text-slate-500 mt-1">
                Use clear categories like <strong>purchase</strong>, <strong>construction</strong>, <strong>logistics</strong>, <strong>tax</strong>, <strong>operations</strong>.
            </p>
        </div>

        <div>
            <label class="block text-xs font-semibold mb-1 text-slate-700">Amount <span class="text-red-500">*</span></label>
            <div class="flex flex-col gap-2">
                <div class="flex gap-2">
                    <select name="currency" required
                            class="border border-slate-200 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-amber-500">
                        <option value="USD" {{ old('currency', 'USD') === 'USD' ? 'selected' : '' }}>USD</option>
                        <option value="KES" {{ old('currency') === 'KES' ? 'selected' : '' }}>KES</option>
                    </select>
                    <input type="number" name="amount" value="{{ old('amount') }}" step="0.01" min="0" required
                           class="border border-slate-200 rounded flex-1 px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-400"
                           placeholder="0.00">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1 text-slate-700">Paid From (Bank / Loan / Account)</label>
                    <input type="text" name="paid_from" value="{{ old('paid_from') }}"
                           class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-400"
                           placeholder="e.g., Co-op Bank Main A/C, ABC SACCO Loan">
                    <p class="text-[10px] text-slate-500 mt-1">
                        Track whether this was paid from a bank account, SACCO loan, cash, etc.
                    </p>
                </div>
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold mb-1 text-slate-700">Date <span class="text-red-500">*</span></label>
            <input type="datetime-local" name="occurred_at" value="{{ old('occurred_at', now()->format('Y-m-d\TH:i')) }}" required
                   class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-400">
        </div>

        <div>
            <label class="block text-xs font-semibold mb-1 text-slate-700">Description</label>
            <textarea name="description" rows="3"
                      class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-400"
                      placeholder="Describe this financial record...">{{ old('description') }}</textarea>
        </div>

        <div class="grid md:grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-semibold mb-1 text-slate-700">Partner (Optional)</label>
                <select name="partner_id"
                        class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-400">
                    <option value="">None</option>
                    @foreach($partners as $partner)
                        <option value="{{ $partner->id }}" {{ old('partner_id') == $partner->id ? 'selected' : '' }}>
                            {{ $partner->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold mb-1 text-slate-700">Project (Optional)</label>
                <select name="project_id"
                        class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-400">
                    <option value="">Not linked to a project</option>
                    @foreach($projects as $proj)
                        <option value="{{ $proj->id }}" {{ old('project_id') == $proj->id ? 'selected' : '' }}>
                            {{ $proj->name }}
                        </option>
                    @endforeach
                </select>
                <p class="text-[10px] text-slate-500 mt-1">
                    Link this expense/income to a specific investment project.
                </p>
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold mb-1 text-slate-700">Receipts/Documents</label>
            <input type="file" name="documents[]" multiple accept=".pdf,.jpg,.jpeg,.png"
                   class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-400">
            <p class="text-[10px] text-slate-500 mt-1">Upload receipts or supporting documents (PDF, JPG, PNG - Max 10MB each)</p>
        </div>

        <div class="bg-amber-50 border border-amber-200 rounded-lg p-3">
            <p class="text-xs text-amber-700">
                <strong>Note:</strong> This record will be created with "Pending Approval" status and will require approval before being finalized.
            </p>
        </div>

        <div class="flex items-center gap-3 pt-2 border-t border-slate-100">
            <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold px-6 py-2 rounded shadow-sm">
                Create Record
            </button>
            <a href="{{ route('admin.financial.index') }}" class="text-sm text-slate-600 hover:text-slate-800">
                Cancel
            </a>
        </div>
    </form>
@endsection
