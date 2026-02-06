@extends('layouts.admin')

@section('page_title', 'Document Details')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
        <div>
            <h1 class="text-lg font-semibold">{{ $document->title }}</h1>
            <p class="text-xs text-slate-500">Document details and information.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.documents.download', $document) }}"
               class="bg-blue-500 hover:bg-blue-600 text-white text-xs font-semibold px-4 py-2 rounded">
                Download
            </a>
            <a href="{{ route('admin.documents.index') }}" class="text-sm text-slate-600 hover:text-slate-800">
                Back to list
            </a>
        </div>
    </div>

    <div class="grid md:grid-cols-2 gap-6">
        <div class="bg-white border border-slate-100 rounded-lg p-4">
            <h2 class="text-sm font-semibold text-slate-900 mb-4">Document Information</h2>
            <dl class="space-y-3 text-sm">
                <div>
                    <dt class="text-xs text-slate-500 mb-1">Title</dt>
                    <dd class="font-medium text-slate-900">{{ $document->title }}</dd>
                </div>
                @if($document->description)
                <div>
                    <dt class="text-xs text-slate-500 mb-1">Description</dt>
                    <dd class="text-slate-700">{{ $document->description }}</dd>
                </div>
                @endif
                <div>
                    <dt class="text-xs text-slate-500 mb-1">Type</dt>
                    <dd class="text-slate-700">{{ ucfirst($document->type) }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-500 mb-1">Visibility</dt>
                    <dd>
                        <span class="inline-flex items-center rounded-full px-2 py-1 text-[10px] font-medium
                            {{ $document->visibility === 'internal_admin' ? 'bg-slate-100 text-slate-700' : 
                               ($document->visibility === 'partners' ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700') }}">
                            {{ ucfirst(str_replace('_', ' ', $document->visibility)) }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-500 mb-1">File Name</dt>
                    <dd class="text-slate-700">{{ $document->original_name }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-500 mb-1">File Size</dt>
                    <dd class="text-slate-700">{{ $document->size ? number_format($document->size / 1024, 2) . ' KB' : '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-500 mb-1">MIME Type</dt>
                    <dd class="text-slate-700">{{ $document->mime_type ?? '—' }}</dd>
                </div>
            </dl>
        </div>

        <div class="bg-white border border-slate-100 rounded-lg p-4">
            <h2 class="text-sm font-semibold text-slate-900 mb-4">Upload Information</h2>
            <dl class="space-y-3 text-sm">
                <div>
                    <dt class="text-xs text-slate-500 mb-1">Uploaded By</dt>
                    <dd class="text-slate-700">{{ $document->uploader->name }}</dd>
                    <dd class="text-xs text-slate-500">{{ $document->uploader->email }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-500 mb-1">Uploaded On</dt>
                    <dd class="text-slate-700">{{ $document->created_at->format('d M Y, H:i') }}</dd>
                </div>
                @if($document->is_archived)
                <div>
                    <dt class="text-xs text-slate-500 mb-1">Archived</dt>
                    <dd class="text-slate-700">
                        <span class="inline-flex items-center rounded-full px-2 py-1 text-[10px] font-medium bg-slate-100 text-slate-700">
                            Yes
                        </span>
                    </dd>
                    @if($document->archiver)
                    <dd class="text-xs text-slate-500">By {{ $document->archiver->name }} on {{ $document->archived_at->format('d M Y') }}</dd>
                    @endif
                </div>
                @endif
            </dl>

            @if(!$document->is_archived)
            <div class="mt-4 pt-4 border-t border-slate-200">
                <form action="{{ route('admin.documents.archive', $document) }}" method="POST" 
                      onsubmit="return confirm('Are you sure you want to archive this document?');">
                    @csrf
                    <button type="submit" class="w-full bg-slate-500 hover:bg-slate-600 text-white text-xs font-semibold px-4 py-2 rounded">
                        Archive Document
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>
@endsection
