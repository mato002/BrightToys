@extends('layouts.admin')

@section('page_title', 'Create Penalty Rate')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
        <div>
            <h1 class="text-lg font-semibold">Create Penalty Rate</h1>
            <p class="text-xs text-slate-500">Configure a new penalty rate for overdue payments.</p>
        </div>
        <a href="{{ route('admin.penalty-rates.index') }}" class="text-sm text-slate-600 hover:text-slate-800">
            Back to list
        </a>
    </div>

    <form action="{{ route('admin.penalty-rates.store') }}" method="POST" class="card card-body form-full-width">
        @csrf

        @if($errors->any())
            <div class="alert alert-error">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="form-group">
            <label class="form-label">Name <span class="required">*</span></label>
            <input type="text" name="name" value="{{ old('name') }}" required
                   placeholder="e.g. Standard Late Payment">
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Rate <span class="required">*</span></label>
                <input type="number" name="rate" value="{{ old('rate') }}" step="0.01" min="0" required
                       placeholder="2.00">
                <p class="form-help">Percentage or fixed amount depending on calculation method</p>
            </div>

            <div class="form-group">
                <label class="form-label">Calculation Method <span class="required">*</span></label>
                <select name="calculation_method" id="calculation_method" required>
                    <option value="percentage_per_day" {{ old('calculation_method') === 'percentage_per_day' ? 'selected' : '' }}>Percentage per day</option>
                    <option value="percentage_of_installment" {{ old('calculation_method') === 'percentage_of_installment' ? 'selected' : '' }}>Percentage of installment</option>
                    <option value="fixed_amount" {{ old('calculation_method') === 'fixed_amount' ? 'selected' : '' }}>Fixed amount per day</option>
                </select>
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Grace Period (Days) <span class="required">*</span></label>
                <input type="number" name="grace_period_days" value="{{ old('grace_period_days', 7) }}" min="0" required
                       placeholder="7">
                <p class="form-help">Days before penalty applies</p>
            </div>

            <div class="form-group">
                <label class="form-label">Max Penalty Amount (Optional)</label>
                <input type="number" name="max_penalty_amount" value="{{ old('max_penalty_amount') }}" step="0.01" min="0"
                       placeholder="50000.00">
                <p class="form-help">Maximum penalty cap (leave empty for no limit)</p>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Description</label>
            <textarea name="description" rows="3"
                      placeholder="Description of this penalty rate...">{{ old('description') }}</textarea>
        </div>

        <div class="form-group">
            <label class="flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active') ? 'checked' : '' }}>
                <span class="text-sm text-slate-700">Set as active rate (will deactivate current active rate)</span>
            </label>
        </div>

        <div class="flex items-center gap-3 pt-4 border-t-2 border-slate-200">
            <button type="submit" class="btn-primary">
                Create Penalty Rate
            </button>
            <a href="{{ route('admin.penalty-rates.index') }}" class="btn-secondary">
                Cancel
            </a>
        </div>
    </form>
@endsection
