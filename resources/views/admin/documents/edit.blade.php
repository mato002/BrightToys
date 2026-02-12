@extends('layouts.admin')

@section('title', 'Edit Document')

@section('content')
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-semibold text-slate-900">Edit Document</h1>
                <p class="mt-1 text-sm text-slate-500">
                    Update document details, related entity and access rules.
                </p>
            </div>
            <a href="{{ route('admin.documents.show', $document) }}"
               class="text-sm text-slate-600 hover:text-slate-800">
                Back to details
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 max-w-3xl">
            <form method="POST" action="{{ route('admin.documents.update', $document) }}" enctype="multipart/form-data" class="p-6 space-y-5">
                @csrf
                @method('PUT')

                @if($errors->any())
                    <div class="mb-4 rounded-md bg-red-50 border border-red-200 px-4 py-3 text-xs text-red-700">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Category<span class="text-red-500">*</span></label>
                        <select name="category" class="mt-1 block w-full rounded-md border-slate-300 text-sm" required>
                            <option value="">Select category</option>
                            @foreach($categories as $key)
                                <option value="{{ $key }}" @selected(old('category', $document->category) === $key)>
                                    {{ \Illuminate\Support\Str::of($key)->replace('_', ' ')->title() }}
                                </option>
                            @endforeach
                        </select>
                        @error('category')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700">Sub-category</label>
                        <input type="text" name="sub_category" value="{{ old('sub_category', $document->sub_category) }}"
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
                            <option value="agreement" @selected(old('type', $document->type) === 'agreement')>Agreement</option>
                            <option value="report" @selected(old('type', $document->type) === 'report')>Report</option>
                            <option value="policy" @selected(old('type', $document->type) === 'policy')>Policy</option>
                            <option value="minutes" @selected(old('type', $document->type) === 'minutes')>Minutes</option>
                            <option value="resolution" @selected(old('type', $document->type) === 'resolution')>Resolution</option>
                            <option value="other" @selected(old('type', $document->type) === 'other')>Other</option>
                        </select>
                        @error('type')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700">Visibility<span class="text-red-500">*</span></label>
                        <select name="visibility" class="mt-1 block w-full rounded-md border-slate-300 text-sm" required>
                            <option value="">Select visibility</option>
                            <option value="internal_admin" @selected(old('visibility', $document->visibility) === 'internal_admin')>Internal (Management only)</option>
                            <option value="partners" @selected(old('visibility', $document->visibility) === 'partners')>Partners (read-only)</option>
                            <option value="public_link" @selected(old('visibility', $document->visibility) === 'public_link')>Public link</option>
                        </select>
                        @error('visibility')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Title<span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $document->title) }}"
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
                              placeholder="Optional context, e.g. which meeting, which project, or which member this relates to">{{ old('description', $document->description) }}</textarea>
                    @error('description')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Replace File (optional)</label>
                    <input type="file" name="file"
                           class="mt-1 block w-full text-sm text-slate-700 file:mr-4 file:rounded-md file:border-0 file:bg-emerald-600 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-emerald-700">
                    <p class="mt-1 text-xs text-slate-500">
                        Leave empty to keep the current file ({{ $document->original_name }}). Allowed: PDF, Word, Excel, JPG, PNG. Max size 20MB.
                    </p>
                    @error('file')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="border-t border-slate-200 pt-4 mt-4">
                    <h2 class="text-sm font-semibold text-slate-800 mb-2">Advanced Access & Linking</h2>
                    <p class="text-xs text-slate-500 mb-3">
                        Update the related entity and fine-grained access rules for this document.
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold mb-1 text-slate-700">
                                Related To (Entity)
                            </label>
                            @php
                                $subjectType = old('subject_type', $document->subject_type);
                                $subjectId = old('subject_id', $document->subject_id);
                                $viewRoles = old('view_roles.0', is_array($document->view_roles) ? implode(',', $document->view_roles) : '');
                                $blockedUsers = old('blocked_users.0', is_array($document->blocked_users) ? implode(',', $document->blocked_users) : '');
                            @endphp
                            <select name="subject_type"
                                    class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-400">
                                <option value="">None / General</option>
                                <option value="member" {{ $subjectType === 'member' ? 'selected' : '' }}>Member</option>
                                <option value="loan" {{ $subjectType === 'loan' ? 'selected' : '' }}>Loan</option>
                                <option value="project" {{ $subjectType === 'project' ? 'selected' : '' }}>Project</option>
                                <option value="meeting" {{ $subjectType === 'meeting' ? 'selected' : '' }}>Meeting</option>
                            </select>
                            <p class="text-[10px] text-slate-500 mt-1">
                                Choose what this document is primarily linked to.
                            </p>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold mb-1 text-slate-700">
                                Related ID
                            </label>
                            <input type="number" name="subject_id" value="{{ $subjectId }}"
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
                                   value="{{ $viewRoles }}">
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
                                   value="{{ $blockedUsers }}">
                            <p class="text-[10px] text-slate-500 mt-1">
                                Use this to ensure an affected member cannot view their own disciplinary document.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4">
                    <a href="{{ route('admin.documents.show', $document) }}"
                       class="text-sm text-slate-600 hover:text-slate-800">
                        Cancel
                    </a>
                    <button type="submit"
                            class="inline-flex items-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                        Update Document
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

