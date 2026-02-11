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

    <div class="bg-white border border-slate-100 rounded-lg p-6 max-w-2xl">
        <form method="POST" action="{{ route('admin.voting-topics.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">
                    Title <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" value="{{ old('title') }}"
                       class="w-full border border-slate-200 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                       required>
                @error('title')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">
                    Description / Question
                </label>
                <textarea name="description" rows="4"
                          class="w-full border border-slate-200 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Initial Status <span class="text-red-500">*</span>
                    </label>
                    <select name="status"
                            class="w-full border border-slate-200 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                        <option value="draft" {{ old('status', 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="open" {{ old('status') === 'open' ? 'selected' : '' }}>Open Immediately</option>
                    </select>
                    @error('status')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
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

