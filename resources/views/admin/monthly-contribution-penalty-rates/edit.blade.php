@extends('layouts.admin')

@section('page_title', 'Edit Monthly Contribution Penalty Rate')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
        <div>
            <h1 class="text-lg font-semibold">Edit Monthly Contribution Penalty Rate</h1>
            <p class="text-xs text-slate-500">Update the penalty rate configuration.</p>
        </div>
        <a href="{{ route('admin.monthly-contribution-penalty-rates.index') }}" class="text-sm text-slate-600 hover:text-slate-800">
            Back to list
        </a>
    </div>

    <form action="{{ route('admin.monthly-contribution-penalty-rates.update', $penalty_rate) }}" method="POST" class="card card-body form-full-width">
        @csrf
        @method('PUT')

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
            <input type="text" name="name" value="{{ old('name', $penalty_rate->name) }}" required
                   placeholder="e.g. Standard Monthly Contribution Penalty">
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Penalty Rate (%) <span class="required">*</span></label>
                <input type="number" name="rate" value="{{ old('rate', $penalty_rate->rate) }}" step="0.0001" min="0" max="1" required
                       placeholder="0.10">
                <p class="form-help">Enter as decimal (e.g., 0.10 for 10%, 0.15 for 15%)</p>
            </div>

            <div class="form-group">
                <label class="form-label">Effective From <span class="required">*</span></label>
                <input type="date" name="effective_from" value="{{ old('effective_from', $penalty_rate->effective_from->format('Y-m-d')) }}" required>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Effective To (Optional)</label>
            <input type="date" name="effective_to" value="{{ old('effective_to', $penalty_rate->effective_to ? $penalty_rate->effective_to->format('Y-m-d') : '') }}">
            <p class="form-help">Leave empty for current/ongoing rate. Set a date to close this rate.</p>
        </div>

        <div class="form-group">
            <label class="form-label">Description</label>
            <textarea name="description" rows="3"
                      placeholder="Description of this penalty rate...">{{ old('description', $penalty_rate->description) }}</textarea>
        </div>

        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-4">
            <p class="text-xs text-amber-800">
                <strong>Warning:</strong> Changing the effective date may affect how penalties are calculated for past months. Ensure the date is correct before saving.
            </p>
        </div>

        <div class="flex items-center gap-3 pt-4 border-t-2 border-slate-200">
            <button type="submit" class="btn-primary">
                Update Penalty Rate
            </button>
            <a href="{{ route('admin.monthly-contribution-penalty-rates.index') }}" class="btn-secondary">
                Cancel
            </a>
        </div>
    </form>
@endsection
