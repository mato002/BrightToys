@extends('layouts.admin')

@section('page_title', 'Upload Document')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
        <div>
            <h1 class="text-lg font-semibold">Upload Document</h1>
            <p class="text-xs text-slate-500">Add a new document to the vault.</p>
        </div>
        <a href="{{ route('admin.documents.index') }}" class="text-sm text-slate-600 hover:text-slate-800">
            Back to list
        </a>
    </div>

    <form action="{{ route('admin.documents.store') }}" method="POST" enctype="multipart/form-data" 
          class="bg-white border border-slate-100 rounded-lg p-4 text-sm space-y-4 shadow-sm max-w-2xl">
        @csrf
        
        @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-lg p-3 text-xs text-red-700">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div>
            <label class="block text-xs font-semibold mb-1 text-slate-700">Document Type <span class="text-red-500">*</span></label>
            <select name="type" required
                    class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-400">
                <option value="agreement" {{ old('type') === 'agreement' ? 'selected' : '' }}>Agreement</option>
                <option value="report" {{ old('type') === 'report' ? 'selected' : '' }}>Report</option>
                <option value="policy" {{ old('type') === 'policy' ? 'selected' : '' }}>Policy</option>
                <option value="other" {{ old('type') === 'other' ? 'selected' : '' }}>Other</option>
            </select>
        </div>

        <div>
            <label class="block text-xs font-semibold mb-1 text-slate-700">Title <span class="text-red-500">*</span></label>
            <input type="text" name="title" value="{{ old('title') }}" required
                   class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-400"
                   placeholder="Enter document title">
        </div>

        <div>
            <label class="block text-xs font-semibold mb-1 text-slate-700">Description</label>
            <textarea name="description" rows="3"
                      class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-400"
                      placeholder="Describe the document...">{{ old('description') }}</textarea>
        </div>

        <div>
            <label class="block text-xs font-semibold mb-1 text-slate-700">Visibility <span class="text-red-500">*</span></label>
            <select name="visibility" required
                    class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-400">
                <option value="internal_admin" {{ old('visibility', 'internal_admin') === 'internal_admin' ? 'selected' : '' }}>Internal Admin Only</option>
                <option value="partners" {{ old('visibility') === 'partners' ? 'selected' : '' }}>Partners</option>
                <option value="public_link" {{ old('visibility') === 'public_link' ? 'selected' : '' }}>Public Link</option>
            </select>
            <p class="text-[10px] text-slate-500 mt-1">Choose who can access this document.</p>
        </div>

        <div>
            <label class="block text-xs font-semibold mb-1 text-slate-700">File <span class="text-red-500">*</span></label>
            <input type="file" name="file" required accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                   class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-400">
            <p class="text-[10px] text-slate-500 mt-1">Accepted formats: PDF, DOC, DOCX, JPG, PNG (Max 10MB)</p>
        </div>

        <div class="flex items-center gap-3 pt-2 border-t border-slate-100">
            <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold px-6 py-2 rounded shadow-sm">
                Upload Document
            </button>
            <a href="{{ route('admin.documents.index') }}" class="text-sm text-slate-600 hover:text-slate-800">
                Cancel
            </a>
        </div>
    </form>
@endsection
