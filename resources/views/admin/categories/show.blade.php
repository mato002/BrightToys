@extends('layouts.admin')

@section('page_title', $category->name)

@section('content')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
        <div>
            <h1 class="text-lg font-semibold">{{ $category->name }}</h1>
            <p class="text-xs text-slate-500">Category details and recent products.</p>
        </div>
        <div class="flex items-center gap-2 text-xs">
            <a href="{{ route('admin.categories.edit', $category) }}"
               class="inline-flex items-center px-3 py-1.5 rounded-md border border-slate-200 text-slate-700 hover:bg-slate-50">
                Edit Category
            </a>
            <a href="{{ route('admin.categories.index') }}"
               class="inline-flex items-center px-3 py-1.5 rounded-md border border-slate-200 text-slate-600 hover:bg-slate-50">
                Back to list
            </a>
        </div>
    </div>

    <div class="grid md:grid-cols-3 gap-6 text-sm">
        <div class="bg-white border border-slate-100 rounded-xl p-4 shadow-sm space-y-4">
            <div>
                <span class="text-xs text-slate-500 uppercase tracking-wide block mb-1">Name</span>
                <span class="text-sm font-semibold text-slate-900">{{ $category->name }}</span>
            </div>
            <div>
                <span class="text-xs text-slate-500 uppercase tracking-wide block mb-1">Slug</span>
                <span class="text-xs text-slate-700 font-mono">{{ $category->slug }}</span>
            </div>
            <div>
                <span class="text-xs text-slate-500 uppercase tracking-wide block mb-1">Total Products</span>
                <span class="text-base font-bold text-slate-900">{{ $category->products->count() }}</span>
            </div>
            <div>
                <span class="text-xs text-slate-500 uppercase tracking-wide block mb-1">Description</span>
                <span class="text-xs text-slate-700 leading-relaxed">
                    {{ $category->description ?: 'No description provided.' }}
                </span>
            </div>
            <div class="pt-2 border-t border-slate-100">
                <p class="text-[11px] text-slate-500">
                    <span class="block">Created: {{ $category->created_at->format('M d, Y H:i') }}</span>
                    <span class="block mt-1">Updated: {{ $category->updated_at->format('M d, Y H:i') }}</span>
                </p>
            </div>
        </div>

        <div class="md:col-span-2 bg-white border border-slate-100 rounded-xl p-4 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <h2 class="text-sm font-semibold text-slate-900">Recent products in this category</h2>
                <span class="text-[11px] text-slate-500">
                    Total: {{ $category->products->count() }}
                </span>
            </div>

            @if($category->products->isEmpty())
                <p class="text-xs text-slate-500">No products in this category yet.</p>
            @else
                <div class="overflow-x-auto text-xs">
                    <table class="min-w-full">
                        <thead class="bg-slate-50 text-[11px] text-slate-500 uppercase tracking-wide">
                        <tr>
                            <th class="px-3 py-1.5 text-left">Name</th>
                            <th class="px-3 py-1.5 text-left">Price</th>
                            <th class="px-3 py-1.5 text-left">Stock</th>
                            <th class="px-3 py-1.5 text-right">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($category->products as $product)
                            <tr class="border-t border-slate-100">
                                <td class="px-3 py-1.5 text-slate-800">
                                    {{ $product->name }}
                                </td>
                                <td class="px-3 py-1.5">
                                    Ksh {{ number_format($product->price, 0) }}
                                </td>
                                <td class="px-3 py-1.5">
                                    {{ $product->stock }}
                                </td>
                                <td class="px-3 py-1.5 text-right">
                                    <a href="{{ route('admin.products.show', $product) }}"
                                       class="text-[11px] text-amber-600 hover:underline">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection

