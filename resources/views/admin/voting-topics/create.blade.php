@extends('layouts.admin')

@section('page_title', 'New Voting Topic')

@section('content')
    <div class="mb-4">
        <div class="flex items-center gap-3 mb-2">
            <a href="{{ route('admin.voting-topics.index') }}" class="text-slate-500 hover:text-slate-700">
                ← Back
            </a>
            <h1 class="text-lg font-semibold text-slate-900">New Voting Topic</h1>
        </div>
    </div>

    <div class="card form-full-width">
        <form method="POST" action="{{ route('admin.voting-topics.store') }}" class="card-body space-y-4">
            @csrf

            <div class="form-group">
                <label class="form-label">
                    Title <span class="required">*</span>
                </label>
                <input type="text" name="title" value="{{ old('title') }}" required>
                @error('title')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">
                    Description / Question
                </label>
                <textarea name="description" rows="4">{{ old('description') }}</textarea>
                @error('description')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">
                        Initial Status <span class="required">*</span>
                    </label>
                    <select name="status">
                        <option value="draft" {{ old('status', 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="open" {{ old('status') === 'open' ? 'selected' : '' }}>Open Immediately</option>
                    </select>
                    @error('status')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Opens At
                    </label>
                    <input type="datetime-local" name="opens_at" value="{{ old('opens_at') }}"
                           class="w-full border border-slate-200 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                    @error('opens_at')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Closes At
                    </label>
                    <input type="datetime-local" name="closes_at" value="{{ old('closes_at') }}"
                           class="w-full border border-slate-200 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                    @error('closes_at')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                <p class="text-[11px] text-slate-500">
                    Voting weight is based on each partner's current ownership percentage at the time of voting.
                </p>
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 rounded-lg bg-emerald-600 text-white text-xs font-semibold hover:bg-emerald-700">
                    Save Topic
                </button>
            </div>
        </form>
    </div>
@endsection

