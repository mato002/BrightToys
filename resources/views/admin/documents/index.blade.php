@extends('layouts.admin')

@section('page_title', 'Documents')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
        <div>
            <h1 class="text-lg font-semibold">Document Vault</h1>
            <p class="text-xs text-slate-500">Manage receipts, agreements, reports, and other documents.</p>
        </div>
        <a href="{{ route('admin.documents.create') }}"
           class="bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold px-4 py-2 rounded">
            Upload Document
        </a>
    </div>

    <form method="GET" class="mb-4 bg-white border border-slate-100 rounded-lg p-3 text-xs grid md:grid-cols-3 gap-3">
        <select name="type" class="border border-slate-200 rounded px-3 py-2 text-sm">
            <option value="">All Types</option>
            <option value="agreement" {{ request('type') === 'agreement' ? 'selected' : '' }}>Agreement</option>
            <option value="report" {{ request('type') === 'report' ? 'selected' : '' }}>Report</option>
            <option value="policy" {{ request('type') === 'policy' ? 'selected' : '' }}>Policy</option>
            <option value="meeting_minutes" {{ request('type') === 'meeting_minutes' ? 'selected' : '' }}>Meeting Minutes</option>
            <option value="resolution" {{ request('type') === 'resolution' ? 'selected' : '' }}>Resolution</option>
            <option value="other" {{ request('type') === 'other' ? 'selected' : '' }}>Other</option>
        </select>
        <select name="visibility" class="border border-slate-200 rounded px-3 py-2 text-sm">
            <option value="">All Visibility</option>
            <option value="internal_admin" {{ request('visibility') === 'internal_admin' ? 'selected' : '' }}>Internal Admin</option>
            <option value="partners" {{ request('visibility') === 'partners' ? 'selected' : '' }}>Partners</option>
            <option value="public_link" {{ request('visibility') === 'public_link' ? 'selected' : '' }}>Public Link</option>
        </select>
        <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2 rounded text-xs">
            Filter
        </button>
    </form>

    @if(session('success'))
        <div class="mb-4 text-sm text-emerald-700 bg-emerald-50 border border-emerald-200 px-4 py-2 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($documents as $document)
            <div class="bg-white border border-slate-100 rounded-lg p-4 hover:shadow-md transition-shadow">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex-1">
                        <h3 class="text-sm font-semibold text-slate-900 mb-1">{{ $document->title }}</h3>
                        <p class="text-xs text-slate-500">{{ Str::limit($document->description ?? 'No description', 60) }}</p>
                    </div>
                </div>
                
                <div class="space-y-2 mb-3">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500">Type:</span>
                        <span class="font-medium text-slate-700">{{ ucfirst($document->type) }}</span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500">Visibility:</span>
                        <span class="font-medium text-slate-700">{{ ucfirst(str_replace('_', ' ', $document->visibility)) }}</span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500">Uploaded:</span>
                        <span class="text-slate-700">{{ $document->created_at->format('d M Y') }}</span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500">By:</span>
                        <span class="text-slate-700">{{ $document->uploader->name }}</span>
                    </div>
                </div>

                <div class="flex items-center gap-2 pt-3 border-t border-slate-100">
                    <a href="{{ route('admin.documents.show', $document) }}"
                       class="flex-1 text-center text-xs text-emerald-600 hover:text-emerald-700 font-medium py-1.5">
                        View
                    </a>
                    <a href="{{ route('admin.documents.download', $document) }}"
                       class="flex-1 text-center text-xs text-blue-600 hover:text-blue-700 font-medium py-1.5">
                        Download
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white border border-slate-100 rounded-lg p-8 text-center">
                <p class="text-slate-500 text-sm">No documents found.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $documents->links() }}
    </div>
@endsection
