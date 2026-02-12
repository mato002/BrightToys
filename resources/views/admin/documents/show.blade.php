@extends('layouts.admin')

@section('title', $document->title)

@section('content')
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="mb-4 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-semibold text-slate-900">{{ $document->title }}</h1>
                <p class="mt-1 text-sm text-slate-500">
                    {{ Str::of($document->category ?? 'Other')->replace('_', ' ')->title() }}
                    @if($document->sub_category)
                        · {{ $document->sub_category }}
                    @endif
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.documents.download', $document) }}"
                   class="inline-flex items-center rounded-lg bg-slate-800 px-3 py-2 text-sm font-semibold text-white hover:bg-slate-900">
                    Download
                </a>
                @if(!$document->is_archived && (auth()->user()->isSuperAdmin() || auth()->user()->hasAdminRole('finance_admin') || auth()->user()->hasAdminRole('chairman')))
                    <form method="POST" action="{{ route('admin.documents.archive', $document) }}"
                          onsubmit="return confirm('Archive this document? It will be hidden from standard listings.');">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center rounded-lg bg-slate-100 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200">
                            Archive
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <div class="lg:col-span-2 space-y-4">
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
                    <h2 class="text-sm font-semibold text-slate-800 mb-3">Document Details</h2>
                    <dl class="divide-y divide-slate-100 text-sm">
                        <div class="py-2 flex justify-between">
                            <dt class="text-slate-500">Category</dt>
                            <dd class="text-slate-900">
                                {{ $document->category ? Str::of($document->category)->replace('_', ' ')->title() : 'Other' }}
                            </dd>
                        </div>
                        <div class="py-2 flex justify-between">
                            <dt class="text-slate-500">Sub-category</dt>
                            <dd class="text-slate-900">
                                {{ $document->sub_category ?: '—' }}
                            </dd>
                        </div>
                        <div class="py-2 flex justify-between">
                            <dt class="text-slate-500">Type</dt>
                            <dd class="text-slate-900">
                                {{ Str::of($document->type)->replace('_', ' ')->title() }}
                            </dd>
                        </div>
                        <div class="py-2 flex justify-between">
                            <dt class="text-slate-500">Visibility</dt>
                            <dd class="text-slate-900">
                                @switch($document->visibility)
                                    @case('partners') Partners (read-only) @break
                                    @case('public_link') Public Link @break
                                    @default Internal (Management)
                                @endswitch
                            </dd>
                        </div>
                        <div class="py-2 flex justify-between">
                            <dt class="text-slate-500">Uploaded By</dt>
                            <dd class="text-slate-900">
                                {{ $document->uploader?->name ?? '—' }}
                            </dd>
                        </div>
                        <div class="py-2 flex justify-between">
                            <dt class="text-slate-500">Uploaded At</dt>
                            <dd class="text-slate-900">
                                {{ $document->created_at?->format('Y-m-d H:i') }}
                            </dd>
                        </div>
                        <div class="py-2 flex justify-between">
                            <dt class="text-slate-500">File</dt>
                            <dd class="text-slate-900 text-right">
                                <div>{{ $document->original_name }}</div>
                                <div class="text-xs text-slate-500">
                                    {{ $document->mime_type ?: 'Unknown type' }}
                                    @if($document->size)
                                        · {{ number_format($document->size / 1024, 1) }} KB
                                    @endif
                                </div>
                            </dd>
                        </div>
                    </dl>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
                    <h2 class="text-sm font-semibold text-slate-800 mb-2">Description</h2>
                    <p class="text-sm text-slate-700 whitespace-pre-line">
                        {{ $document->description ?: 'No description provided.' }}
                    </p>
                </div>
            </div>

            <div class="space-y-4">
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
                    <h2 class="text-sm font-semibold text-slate-800 mb-3">Audit</h2>
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-slate-500">Status</dt>
                            <dd class="text-slate-900">
                                @if($document->is_archived)
                                    <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-700">
                                        Archived
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700">
                                        Active
                                    </span>
                                @endif
                            </dd>
                        </div>
                        @if($document->is_archived)
                            <div class="flex justify-between">
                                <dt class="text-slate-500">Archived At</dt>
                                <dd class="text-slate-900">
                                    {{ $document->archived_at?->format('Y-m-d H:i') }}
                                </dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-slate-500">Archived By</dt>
                                <dd class="text-slate-900">
                                    {{ $document->archiver?->name ?? '—' }}
                                </dd>
                            </div>
                        @endif
                    </dl>
                    <p class="mt-3 text-xs text-slate-500">
                        All document actions (upload, archive) are logged in the global activity log for audit and accountability.
                    </p>
                </div>

                @if($document->versions->isNotEmpty())
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
                        <h2 class="text-sm font-semibold text-slate-800 mb-3">Version History</h2>
                        <p class="text-xs text-slate-500 mb-3">
                            Every time this document is uploaded or updated, a version snapshot is stored so that no
                            change happens without a trace.
                        </p>
                        <div class="max-h-56 overflow-y-auto text-sm">
                            <ul class="divide-y divide-slate-100">
                                @foreach($document->versions->take(5) as $version)
                                    <li class="py-2 flex items-start justify-between gap-3">
                                        <div>
                                            <p class="text-slate-800">
                                                Version {{ $version->version }}
                                                <span class="text-xs text-slate-500">
                                                    · {{ $version->created_at?->format('Y-m-d H:i') }}
                                                </span>
                                            </p>
                                            <p class="text-xs text-slate-500">
                                                File: {{ $version->original_name }} ({{ $version->size ? number_format($version->size / 1024, 1) . ' KB' : 'size unknown' }})
                                            </p>
                                        </div>
                                        <span class="text-[11px] text-slate-500">
                                            {{ $version->creator?->name ?? 'System' }}
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <div class="bg-slate-50 border border-dashed border-slate-200 rounded-xl p-4 text-xs text-slate-600">
                    <p class="font-semibold mb-1">Visibility Rules</p>
                    <p>
                        Members only see documents that are shared with <span class="font-medium">partners</span> or via
                        explicit <span class="font-medium">public links</span>. Internal documents remain restricted to
                        authorised management roles.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
