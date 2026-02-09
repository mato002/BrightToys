@extends('layouts.admin')

@section('page_title', 'Categories')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
        <div>
            <h1 class="text-lg font-semibold">Categories</h1>
            <p class="text-xs text-slate-500">Organize your products into categories.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.categories.export') . '?' . http_build_query(request()->query()) }}"
               class="inline-flex items-center justify-center bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-semibold px-4 py-2 rounded-md shadow-sm">
                Export CSV
            </a>
            <a href="{{ route('admin.categories.report') . '?' . http_build_query(request()->query()) }}"
               class="inline-flex items-center justify-center bg-blue-500 hover:bg-blue-600 text-white text-xs font-semibold px-4 py-2 rounded-md shadow-sm">
                Generate Report
            </a>
            <a href="{{ route('admin.categories.create') }}"
               class="bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold px-4 py-2 rounded">
                Add Category
            </a>
        </div>
    </div>

    <form method="GET" class="mb-4 bg-white border border-slate-100 rounded-lg p-3 text-xs max-w-md">
        <label class="block text-[11px] font-semibold mb-1 text-slate-600">Search</label>
        <div class="flex gap-2">
            <input type="text" name="q" value="{{ request('q') }}"
                   placeholder="Search by name or slug"
                   class="border border-slate-200 rounded w-full px-3 py-1.5 text-xs">
            <button class="bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold px-4 py-1.5 rounded-md">
                Filter
            </button>
        </div>
        @if(request()->has('q') && request('q') !== '')
            <a href="{{ route('admin.categories.index') }}"
               class="inline-block mt-2 text-xs text-slate-500 hover:text-slate-700">
                Clear
            </a>
        @endif
    </form>

    <div class="bg-white border border-slate-100 rounded-lg overflow-x-auto admin-table-scroll text-sm shadow-sm">
        <table class="min-w-full">
            <thead class="bg-slate-50 text-xs text-slate-500 uppercase tracking-wide">
            <tr>
                <th class="px-3 py-2 text-left">Name</th>
                <th class="px-3 py-2 text-left">Slug</th>
                <th class="px-3 py-2 text-left">Products</th>
                <th class="px-3 py-2 text-right">Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse($categories as $category)
                <tr class="border-t border-slate-100 hover:bg-slate-50">
                    <td class="px-3 py-2">
                        <div class="text-xs font-semibold text-slate-900">{{ $category->name }}</div>
                        @if($category->description)
                            <div class="text-[10px] text-slate-500 mt-0.5 line-clamp-1">{{ \Illuminate\Support\Str::limit($category->description, 50) }}</div>
                        @endif
                    </td>
                    <td class="px-3 py-2 text-[11px] text-slate-500 font-mono">{{ $category->slug }}</td>
                    <td class="px-3 py-2 text-xs">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-100 text-[11px] font-medium">
                            {{ $category->products_count ?? 0 }} products
                        </span>
                    </td>
                    <td class="px-3 py-2 text-right space-x-2">
                        <a href="{{ route('admin.categories.show', $category) }}" class="text-xs text-slate-700 hover:text-slate-900 hover:underline">View</a>
                        <a href="{{ route('admin.categories.edit', $category) }}" class="text-xs text-amber-600 hover:text-amber-700 hover:underline font-medium">Edit</a>
                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline" data-confirm="Delete this category? All products in this category will be uncategorized.">
                            @csrf
                            @method('DELETE')
                            <button class="text-xs text-red-500 hover:text-red-700 hover:underline">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-3 py-4 text-center text-slate-500 text-sm">No categories found.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $categories->links() }}
    </div>
@endsection

