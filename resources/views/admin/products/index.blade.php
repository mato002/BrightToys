@extends('layouts.admin')

@section('page_title', 'Products')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
        <div>
            <h1 class="text-lg font-semibold">Products</h1>
            <p class="text-xs text-slate-500">Manage all products available in the store.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.products.export') . '?' . http_build_query(request()->query()) }}"
               class="inline-flex items-center justify-center bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-semibold px-4 py-2 rounded-md shadow-sm">
                Export CSV
            </a>
            <a href="{{ route('admin.products.report') . '?' . http_build_query(request()->query()) }}"
               class="inline-flex items-center justify-center bg-blue-500 hover:bg-blue-600 text-white text-xs font-semibold px-4 py-2 rounded-md shadow-sm">
                Generate Report
            </a>
            <a href="{{ route('admin.products.create') }}"
               class="inline-flex items-center justify-center bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold px-4 py-2 rounded-md shadow-sm">
                Add Product
            </a>
        </div>
    </div>

    <form method="GET" class="mb-4 bg-white border border-slate-100 rounded-lg p-3 text-xs grid md:grid-cols-3 gap-3">
        <div>
            <label class="block text-[11px] font-semibold mb-1 text-slate-600">Search</label>
            <input type="text" name="q" value="{{ request('q') }}"
                   placeholder="Search by name or SKU"
                   class="border border-slate-200 rounded w-full px-3 py-1.5 text-xs">
        </div>
        <div>
            <label class="block text-[11px] font-semibold mb-1 text-slate-600">Category</label>
            <select name="category_id" class="border border-slate-200 rounded w-full px-3 py-1.5 text-xs">
                <option value="">All categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end gap-2">
            <button class="bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold px-4 py-1.5 rounded-md">
                Filter
            </button>
            @if(request()->hasAny(['q', 'category_id']))
                <a href="{{ route('admin.products.index') }}"
                   class="text-xs text-slate-500 hover:text-slate-700">
                    Clear
                </a>
            @endif
        </div>
    </form>

    <div class="bg-white border border-slate-100 rounded-lg overflow-x-auto admin-table-scroll text-sm shadow-sm">
        <table class="min-w-full">
            <thead class="bg-slate-50 text-xs text-slate-500 uppercase tracking-wide">
            <tr>
                <th class="px-3 py-2 text-left">Image</th>
                <th class="px-3 py-2 text-left">Name</th>
                <th class="px-3 py-2 text-left">SKU</th>
                <th class="px-3 py-2 text-left">Category</th>
                <th class="px-3 py-2 text-left">Price</th>
                <th class="px-3 py-2 text-left">Stock</th>
                <th class="px-3 py-2 text-left">Status</th>
                <th class="px-3 py-2 text-left">Featured</th>
                <th class="px-3 py-2 text-right">Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse($products as $product)
                <tr class="border-t border-slate-100 hover:bg-slate-50">
                    <td class="px-3 py-2">
                        @if($product->image_url)
                            <div class="w-12 h-12 rounded overflow-hidden bg-slate-100 flex items-center justify-center">
                                <img src="{{ str_starts_with($product->image_url, 'http') ? $product->image_url : asset('images/toys/' . $product->image_url) }}" 
                                     alt="{{ $product->name }}"
                                     class="w-full h-full object-cover"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="w-full h-full items-center justify-center text-[10px] text-slate-400" style="display:none;">
                                    No image
                                </div>
                            </div>
                        @else
                            <div class="w-12 h-12 rounded bg-slate-100 flex items-center justify-center text-[10px] text-slate-400">
                                No image
                            </div>
                        @endif
                    </td>
                    <td class="px-3 py-2">
                        <div class="text-xs font-semibold text-slate-900">{{ $product->name }}</div>
                        @if($product->description)
                            <div class="text-[10px] text-slate-500 mt-0.5 line-clamp-1">{{ \Illuminate\Support\Str::limit($product->description, 50) }}</div>
                        @endif
                    </td>
                    <td class="px-3 py-2 text-xs text-slate-500">
                        {{ $product->sku ?? '-' }}
                    </td>
                    <td class="px-3 py-2 text-xs text-slate-700">{{ $product->category->name ?? '-' }}</td>
                    <td class="px-3 py-2 text-xs font-semibold">Ksh {{ number_format($product->price, 0) }}</td>
                    <td class="px-3 py-2 text-xs">
                        <span class="{{ $product->stock > 10 ? 'text-emerald-600' : ($product->stock > 0 ? 'text-amber-600' : 'text-red-600') }}">
                            {{ $product->stock }}
                        </span>
                    </td>
                    <td class="px-3 py-2 text-xs">
                        @if($product->status === 'active')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100 text-[11px]">
                                Active
                            </span>
                        @elseif($product->status === 'inactive')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-slate-50 text-slate-700 border border-slate-100 text-[11px]">
                                Inactive
                            </span>
                        @else
                            <span class="text-[11px] text-slate-400">-</span>
                        @endif
                    </td>
                    <td class="px-3 py-2 text-xs">
                        @if($product->featured)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-100 text-[11px]">
                                Featured
                            </span>
                        @else
                            <span class="text-[11px] text-slate-400">-</span>
                        @endif
                    </td>
                    <td class="px-3 py-2 text-right space-x-2">
                        <a href="{{ route('admin.products.show', $product) }}" class="text-xs text-slate-700 hover:underline">View</a>
                        <a href="{{ route('admin.products.edit', $product) }}" class="text-xs text-amber-600 hover:underline">Edit</a>
                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline" data-confirm="Delete this product?">
                            @csrf
                            @method('DELETE')
                            <button class="text-xs text-red-500 hover:underline">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="px-3 py-4 text-center text-gray-500">No products found.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $products->links() }}
    </div>
@endsection

