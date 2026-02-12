@extends('layouts.admin')

@section('title', 'Documents')

@section('content')
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-xl font-semibold text-slate-900">Documents</h1>
                <p class="mt-1 text-sm text-slate-500">
                    Structured storage for constitution, agreements, title deeds, minutes and other key records.
                </p>
            </div>
            @if(auth()->user()->isSuperAdmin() || auth()->user()->hasAdminRole('finance_admin') || auth()->user()->hasAdminRole('chairman'))
                <a href="{{ route('admin.documents.create') }}"
                   class="inline-flex items-center rounded-lg bg-emerald-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                    Upload Document
                </a>
            @endif
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 mb-4">
            <form method="GET" class="p-4 grid grid-cols-1 md:grid-cols-4 gap-3 text-sm">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">Category</label>
                    <select name="category" class="block w-full rounded-md border-slate-300 text-sm">
                        <option value="">All</option>
                        @foreach($categories as $key)
                            <option value="{{ $key }}" @selected(request('category') === $key)>
                                {{ Str::of($key)->replace('_', ' ')->title() }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">Sub-category</label>
                    <input type="text" name="sub_category" value="{{ request('sub_category') }}"
                           class="block w-full rounded-md border-slate-300 text-sm"
                           placeholder="e.g. AGM 2025, Land Project A">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">Visibility</label>
                    <select name="visibility" class="block w-full rounded-md border-slate-300 text-sm">
                        <option value="">All</option>
                        <option value="internal_admin" @selected(request('visibility') === 'internal_admin')>Internal (Management)</option>
                        <option value="partners" @selected(request('visibility') === 'partners')>Partners</option>
                        <option value="public_link" @selected(request('visibility') === 'public_link')>Public Link</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">Search</label>
                    <div class="flex gap-2">
                        <input type="text" name="q" value="{{ request('q') }}"
                               class="block w-full rounded-md border-slate-300 text-sm"
                               placeholder="Title or description">
                        <button type="submit"
                                class="inline-flex items-center rounded-md bg-slate-800 px-3 py-2 text-xs font-semibold text-white hover:bg-slate-900">
                            Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200">
            <div class="admin-table-scroll">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Title</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Category</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Sub-category</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Visibility</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Uploaded By</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Uploaded At</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($documents as $document)
                        <tr>
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.documents.show', $document) }}"
                                   class="font-medium text-slate-800 hover:text-emerald-700">
                                    {{ $document->title }}
                                </a>
                                @if($document->is_archived)
                                    <span class="ml-2 inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-500">
                                        Archived
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-700">
                                {{ $document->category ? Str::of($document->category)->replace('_', ' ')->title() : '—' }}
                            </td>
                            <td class="px-4 py-3 text-slate-700">
                                {{ $document->sub_category ?: '—' }}
                            </td>
                            <td class="px-4 py-3 text-slate-700">
                                {{ Str::of($document->type)->replace('_', ' ')->title() }}
                            </td>
                            <td class="px-4 py-3 text-slate-700">
                                @switch($document->visibility)
                                    @case('partners')
                                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700">
                                            Partners
                                        </span>
                                        @break
                                    @case('public_link')
                                        <span class="inline-flex items-center rounded-full bg-sky-50 px-2 py-0.5 text-xs font-medium text-sky-700">
                                            Public Link
                                        </span>
                                        @break
                                    @default
                                        <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-700">
                                            Internal
                                        </span>
                                @endswitch
                            </td>
                            <td class="px-4 py-3 text-slate-700">
                                {{ $document->uploader?->name ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-slate-700">
                                {{ $document->created_at?->format('Y-m-d H:i') }}
                            </td>
                            <td class="px-4 py-3 text-right text-xs">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('admin.documents.download', $document) }}"
                                       class="text-emerald-600 hover:text-emerald-800 font-medium">
                                        Download
                                    </a>
                                    @if(!$document->is_archived && (auth()->user()->isSuperAdmin() || auth()->user()->hasAdminRole('finance_admin') || auth()->user()->hasAdminRole('chairman')))
                                        <form method="POST" action="{{ route('admin.documents.archive', $document) }}"
                                              onsubmit="return confirm('Archive this document? It will be hidden from standard listings.');">
                                            @csrf
                                            <button type="submit" class="text-slate-500 hover:text-slate-700">
                                                Archive
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-6 text-center text-sm text-slate-500">
                                No documents found yet. Upload constitution, agreements, minutes, or other records to get started.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            @if($documents->hasPages())
                <div class="px-4 py-3 border-t border-slate-100">
                    {{ $documents->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
