@extends('layouts.admin')

@section('page_title', 'Edit Penalty Rate')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
        <div>
            <h1 class="text-lg font-semibold">Edit Penalty Rate</h1>
            <p class="text-xs text-slate-500">Update penalty rate configuration.</p>
        </div>
        <a href="{{ route('admin.penalty-rates.index') }}" class="text-sm text-slate-600 hover:text-slate-800">
            Back to list
        </a>
    </div>

    <form action="{{ route('admin.penalty-rates.update', $penaltyRate) }}" method="POST" class="bg-white border border-slate-100 rounded-lg p-6 shadow-sm max-w-2xl space-y-4">
        @csrf
        @method('PUT')

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
            <label class="block text-xs font-semibold mb-1 text-slate-700">Name <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name', $penaltyRate->name) }}" required
                   class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
        </div>

        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold mb-1 text-slate-700">Rate <span class="text-red-500">*</span></label>
                <input type="number" name="rate" value="{{ old('rate', $penaltyRate->rate) }}" step="0.01" min="0" required
                       class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block text-xs font-semibold mb-1 text-slate-700">Calculation Method <span class="text-red-500">*</span></label>
                <select name="calculation_method" required
                        class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                    <option value="percentage_per_day" {{ old('calculation_method', $penaltyRate->calculation_method) === 'percentage_per_day' ? 'selected' : '' }}>Percentage per day</option>
                    <option value="percentage_of_installment" {{ old('calculation_method', $penaltyRate->calculation_method) === 'percentage_of_installment' ? 'selected' : '' }}>Percentage of installment</option>
                    <option value="fixed_amount" {{ old('calculation_method', $penaltyRate->calculation_method) === 'fixed_amount' ? 'selected' : '' }}>Fixed amount per day</option>
                </select>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold mb-1 text-slate-700">Grace Period (Days) <span class="text-red-500">*</span></label>
                <input type="number" name="grace_period_days" value="{{ old('grace_period_days', $penaltyRate->grace_period_days) }}" min="0" required
                       class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block text-xs font-semibold mb-1 text-slate-700">Max Penalty Amount (Optional)</label>
                <input type="number" name="max_penalty_amount" value="{{ old('max_penalty_amount', $penaltyRate->max_penalty_amount) }}" step="0.01" min="0"
                       class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold mb-1 text-slate-700">Description</label>
            <textarea name="description" rows="3"
                      class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">{{ old('description', $penaltyRate->description) }}</textarea>
        </div>

        <div>
            <label class="flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $penaltyRate->is_active) ? 'checked' : '' }}>
                <span class="text-xs text-slate-700">Set as active rate</span>
            </label>
        </div>

        <div class="flex items-center gap-3 pt-4 border-t border-slate-200">
            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-6 py-2 rounded shadow-sm">
                Update Penalty Rate
            </button>
            <a href="{{ route('admin.penalty-rates.index') }}" class="text-sm text-slate-600 hover:text-slate-800">
                Cancel
            </a>
        </div>
    </form>
@endsection
