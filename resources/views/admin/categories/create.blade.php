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

    <form action="{{ route('admin.categories.store') }}" method="POST" class="card card-body form-full-width">
        @csrf
        
        @if($errors->any())
            <div class="alert alert-error">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="form-group">
            <label class="form-label">Name <span class="required">*</span></label>
            <input type="text" name="name" value="{{ old('name') }}" required
                   placeholder="e.g., Baby & Toddler (0-3 yrs)">
        </div>
        <div class="form-group">
            <label class="form-label">Description</label>
            <textarea name="description" rows="4"
                      placeholder="Brief description of this category...">{{ old('description') }}</textarea>
            <p class="form-help">Slug will be auto-generated from the name.</p>
        </div>

        <div class="flex items-center gap-3 pt-4 border-t-2 border-slate-200">
            <button type="submit" class="btn-primary">
                Create Category
            </button>
            <a href="{{ route('admin.categories.index') }}" class="btn-secondary">
                Cancel
            </a>
        </div>
    </form>
@endsection

