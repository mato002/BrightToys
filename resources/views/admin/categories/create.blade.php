@extends('layouts.admin')

@section('page_title', 'Add Category')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
        <div>
            <h1 class="text-lg font-semibold">Add Category</h1>
            <p class="text-xs text-slate-500">Create a new product category.</p>
        </div>
        <a href="{{ route('admin.categories.index') }}" class="text-sm text-slate-600 hover:text-slate-800">
            Back to list
        </a>
    </div>

    <form action="{{ route('admin.categories.store') }}" method="POST" class="bg-white border border-slate-100 rounded-lg p-4 text-sm space-y-4 shadow-sm">
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
            <label class="block text-xs font-semibold mb-1 text-slate-700">Name <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name') }}" required
                   class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-400"
                   placeholder="e.g., Baby & Toddler (0-3 yrs)">
        </div>
        <div>
            <label class="block text-xs font-semibold mb-1 text-slate-700">Description</label>
            <textarea name="description" rows="4"
                      class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-400"
                      placeholder="Brief description of this category...">{{ old('description') }}</textarea>
            <p class="text-[10px] text-slate-500 mt-1">Slug will be auto-generated from the name.</p>
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold px-6 py-2 rounded shadow-sm">
                Create Category
            </button>
            <a href="{{ route('admin.categories.index') }}" class="text-sm text-slate-600 hover:text-slate-800">
                Cancel
            </a>
        </div>
    </form>
@endsection

