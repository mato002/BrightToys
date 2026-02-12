@extends('layouts.admin')

@section('title', 'Upload Document')

@section('content')
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <h1 class="text-xl font-semibold text-slate-900">Upload Document</h1>
            <p class="mt-1 text-sm text-slate-500">
                Store constitution, agreements, title deeds, minutes and other documents in structured categories.
            </p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 max-w-3xl">
            <form method="POST" action="{{ route('admin.documents.store') }}" enctype="multipart/form-data" class="p-6 space-y-5">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Category<span class="text-red-500">*</span></label>
                        <select name="category" class="mt-1 block w-full rounded-md border-slate-300 text-sm" required>
                            <option value="">Select category</option>
                            @foreach($categories as $key)
                                <option value="{{ $key }}" @selected(old('category') === $key)>
                                    {{ Str::of($key)->replace('_', ' ')->title() }}
                                </option>
                            @endforeach
                        </select>
                        @error('category')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700">Sub-category</label>
                        <input type="text" name="sub_category" value="{{ old('sub_category') }}"
                               class="mt-1 block w-full rounded-md border-slate-300 text-sm"
                               placeholder="e.g. AGM 2025, Land Project A">
                        @error('sub_category')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Document Type<span class="text-red-500">*</span></label>
                        <select name="type" class="mt-1 block w-full rounded-md border-slate-300 text-sm" required>
                            <option value="">Select type</option>
                            <option value="agreement" @selected(old('type') === 'agreement')>Agreement</option>
                            <option value="report" @selected(old('type') === 'report')>Report</option>
                            <option value="policy" @selected(old('type') === 'policy')>Policy</option>
                            <option value="minutes" @selected(old('type') === 'minutes')>Minutes</option>
                            <option value="resolution" @selected(old('type') === 'resolution')>Resolution</option>
                            <option value="other" @selected(old('type') === 'other')>Other</option>
                        </select>
                        @error('type')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700">Visibility<span class="text-red-500">*</span></label>
                        <select name="visibility" class="mt-1 block w-full rounded-md border-slate-300 text-sm" required>
                            <option value="">Select visibility</option>
                            <option value="internal_admin" @selected(old('visibility') === 'internal_admin')>Internal (Management only)</option>
                            <option value="partners" @selected(old('visibility') === 'partners')>Partners (read-only)</option>
                            <option value="public_link" @selected(old('visibility') === 'public_link')>Public link</option>
                        </select>
                        @error('visibility')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Title<span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}"
                           class="mt-1 block w-full rounded-md border-slate-300 text-sm"
                           required>
                    @error('title')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Description</label>
                    <textarea name="description" rows="3"
                              class="mt-1 block w-full rounded-md border-slate-300 text-sm"
                              placeholder="Optional context, e.g. which meeting, which project, or which member this relates to">{{ old('description') }}</textarea>
                    @error('description')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">File<span class="text-red-500">*</span></label>
                    <input type="file" name="file"
                           class="mt-1 block w-full text-sm text-slate-700 file:mr-4 file:rounded-md file:border-0 file:bg-emerald-600 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-emerald-700"
                           required>
                    <p class="mt-1 text-xs text-slate-500">
                        Allowed: PDF, Word, Excel, JPG, PNG. Max size 20MB.
                    </p>
                    @error('file')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="border-t border-slate-200 pt-4 mt-4">
                    <h2 class="text-sm font-semibold text-slate-800 mb-2">Advanced Access Control</h2>
                    <p class="text-xs text-slate-500 mb-3">
                        Optionally restrict this document to specific roles or individual users. Use this for
                        sensitive files such as disciplinary letters where the affected member should not see
                        their own document.
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold mb-1 text-slate-700">
                                Related To (Entity)
                            </label>
                            <select name="subject_type"
                                    class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-400">
                                <option value="">None / General</option>
                                <option value="member" {{ old('subject_type') === 'member' ? 'selected' : '' }}>Member</option>
                                <option value="loan" {{ old('subject_type') === 'loan' ? 'selected' : '' }}>Loan</option>
                                <option value="project" {{ old('subject_type') === 'project' ? 'selected' : '' }}>Project</option>
                                <option value="meeting" {{ old('subject_type') === 'meeting' ? 'selected' : '' }}>Meeting</option>
                            </select>
                            <p class="text-[10px] text-slate-500 mt-1">
                                Choose what this document is primarily linked to.
                            </p>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold mb-1 text-slate-700">
                                Related ID
                            </label>
                            <input type="number" name="subject_id" value="{{ old('subject_id') }}"
                                   class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-400"
                                   placeholder="e.g. Member / Loan / Project ID">
                            <p class="text-[10px] text-slate-500 mt-1">
                                Internal reference ID for the selected entity (optional).
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label class="block text-xs font-semibold mb-1 text-slate-700">
                                Allow Specific Admin Roles
                            </label>
                            <input type="text" name="view_roles[]"
                                   class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-400"
                                   placeholder="e.g. finance_admin,chairman"
                                   value="{{ old('view_roles.0') }}">
                            <p class="text-[10px] text-slate-500 mt-1">
                                Comma-separated role names. Leave empty to use only the visibility setting.
                            </p>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold mb-1 text-slate-700">
                                Block Specific Users
                            </label>
                            <input type="text" name="blocked_users[]"
                                   class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-400"
                                   placeholder="User IDs to block, comma-separated"
                                   value="{{ old('blocked_users.0') }}">
                            <p class="text-[10px] text-slate-500 mt-1">
                                Use this to ensure an affected member cannot view their own disciplinary document.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4">
                    <a href="{{ route('admin.documents.index') }}"
                       class="text-sm text-slate-600 hover:text-slate-800">
                        Cancel
                    </a>
                    <button type="submit"
                            class="inline-flex items-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                        Save Document
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
