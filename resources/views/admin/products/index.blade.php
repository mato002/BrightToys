@extends('layouts.admin')

@section('page_title', 'Products')

@section('breadcrumbs')
    <span class="breadcrumb-separator">/</span>
    <a href="{{ route('admin.products.index') }}" class="text-slate-600 hover:text-emerald-600 transition-colors">Products</a>
@endsection

@section('content')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
        <div>
            <h1 class="text-lg font-semibold">Products</h1>
            <p class="text-xs text-slate-500">Manage all products available in the store.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.products.export') . '?' . http_build_query(request()->query()) }}"
               class="no-print inline-flex items-center justify-center bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-semibold px-4 py-2 rounded-md shadow-sm tooltip"
               data-tooltip="Export products to CSV (Ctrl/Cmd + E)"
               aria-label="Export products">
                Export CSV
            </a>
            <a href="{{ route('admin.products.report') . '?' . http_build_query(request()->query()) }}"
               class="no-print inline-flex items-center justify-center bg-blue-500 hover:bg-blue-600 text-white text-xs font-semibold px-4 py-2 rounded-md shadow-sm tooltip"
               data-tooltip="Generate printable report"
               aria-label="Generate report">
                Generate Report
            </a>
            <button onclick="window.print()"
                    class="no-print inline-flex items-center justify-center bg-slate-500 hover:bg-slate-600 text-white text-xs font-semibold px-4 py-2 rounded-md shadow-sm tooltip"
                    data-tooltip="Print this page (Ctrl/Cmd + P)"
                    aria-label="Print page">
                Print
            </button>
            <a href="{{ route('admin.products.create') }}"
               class="no-print inline-flex items-center justify-center bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold px-4 py-2 rounded-md shadow-sm tooltip"
               data-tooltip="Create new product (Ctrl/Cmd + K)"
               aria-label="Add product">
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

    {{-- Include bulk actions form --}}
    @include('admin.partials.bulk-actions', ['route' => 'admin.products.bulk'])

    <div class="bg-white border border-slate-100 rounded-lg overflow-x-auto admin-table-scroll text-sm shadow-sm">
        <table class="min-w-full">
            <thead class="bg-slate-50 text-xs text-slate-500 uppercase tracking-wide">
            <tr>
                <th class="px-3 py-2 w-12">
                    <input type="checkbox" id="select-all" aria-label="Select all products">
                </th>
                <th class="px-3 py-2 text-left">Image</th>
                <th class="px-3 py-2 text-left">
                    <button type="button" 
                            onclick="sortTable('name')" 
                            class="flex items-center gap-1 hover:text-slate-900 transition-colors sortable-header"
                            data-column="name">
                        Name
                        @if(request('sort') == 'name')
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 {{ request('direction') == 'asc' ? '' : 'rotate-180' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M5 12h14M12 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 opacity-30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M5 12h14M12 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        @endif
                    </button>
                </th>
                <th class="px-3 py-2 text-left">
                    <button type="button" 
                            onclick="sortTable('sku')" 
                            class="flex items-center gap-1 hover:text-slate-900 transition-colors sortable-header"
                            data-column="sku">
                        SKU
                        @if(request('sort') == 'sku')
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 {{ request('direction') == 'asc' ? '' : 'rotate-180' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M5 12h14M12 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 opacity-30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M5 12h14M12 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        @endif
                    </button>
                </th>
                <th class="px-3 py-2 text-left">Category</th>
                <th class="px-3 py-2 text-left">
                    <button type="button" 
                            onclick="sortTable('price')" 
                            class="flex items-center gap-1 hover:text-slate-900 transition-colors sortable-header"
                            data-column="price">
                        Price
                        @if(request('sort') == 'price')
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 {{ request('direction') == 'asc' ? '' : 'rotate-180' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M5 12h14M12 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 opacity-30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M5 12h14M12 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        @endif
                    </button>
                </th>
                <th class="px-3 py-2 text-left">
                    <button type="button" 
                            onclick="sortTable('stock')" 
                            class="flex items-center gap-1 hover:text-slate-900 transition-colors sortable-header"
                            data-column="stock">
                        Stock
                        @if(request('sort') == 'stock')
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 {{ request('direction') == 'asc' ? '' : 'rotate-180' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M5 12h14M12 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 opacity-30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M5 12h14M12 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        @endif
                    </button>
                </th>
                <th class="px-3 py-2 text-left">
                    <button type="button" 
                            onclick="sortTable('status')" 
                            class="flex items-center gap-1 hover:text-slate-900 transition-colors sortable-header"
                            data-column="status">
                        Status
                        @if(request('sort') == 'status')
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 {{ request('direction') == 'asc' ? '' : 'rotate-180' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M5 12h14M12 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 opacity-30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M5 12h14M12 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        @endif
                    </button>
                </th>
                <th class="px-3 py-2 text-left">Featured</th>
                <th class="px-3 py-2 text-right">Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse($products as $product)
                <tr class="border-t border-slate-100 hover:bg-slate-50">
                    <td class="px-3 py-2">
                        <input type="checkbox" class="item-checkbox" value="{{ $product->id }}" aria-label="Select product {{ $product->id }}">
                    </td>
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
                    <td colspan="10" class="px-3 py-12">
                        <div class="empty-state">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M4 7l8-4 8 4-8 4-8-4z" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M4 17l8 4 8-4" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M4 7v10" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M20 7v10" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <h3>No products found</h3>
                            <p>No products match your search criteria. Try adjusting your filters or create a new product.</p>
                            <a href="{{ route('admin.products.create') }}" 
                               class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold px-4 py-2 rounded-lg mt-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 5v14M5 12h14" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                Create First Product
                            </a>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @include('admin.partials.pagination', ['paginator' => $products])

    {{-- Bulk Actions Bar --}}
    <div id="bulk-actions-bar" class="hidden fixed bottom-0 left-0 right-0 bg-emerald-600 text-white px-4 py-3 shadow-lg z-50">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span id="selected-count" class="text-sm font-semibold">0 items selected</span>
            </div>
            <div class="flex gap-2">
                <button type="button" 
                        data-bulk-action="activate" 
                        class="px-3 py-1.5 bg-white text-emerald-600 text-xs font-semibold rounded hover:bg-emerald-50 transition-colors">
                    Activate
                </button>
                <button type="button" 
                        data-bulk-action="deactivate" 
                        class="px-3 py-1.5 bg-white text-emerald-600 text-xs font-semibold rounded hover:bg-emerald-50 transition-colors">
                    Deactivate
                </button>
                <button type="button" 
                        id="bulk-delete" 
                        class="px-3 py-1.5 bg-red-500 text-white text-xs font-semibold rounded hover:bg-red-600 transition-colors">
                    Delete Selected
                </button>
                <button type="button" 
                        onclick="clearSelection()" 
                        class="px-3 py-1.5 bg-white/20 text-white text-xs font-semibold rounded hover:bg-white/30 transition-colors">
                    Cancel
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Sortable table functionality
    function sortTable(column) {
        const currentSort = '{{ request('sort') }}';
        const currentDirection = '{{ request('direction', 'desc') }}';
        const newDirection = (currentSort === column && currentDirection === 'asc') ? 'desc' : 'asc';
        
        const url = new URL(window.location.href);
        url.searchParams.set('sort', column);
        url.searchParams.set('direction', newDirection);
        url.searchParams.delete('page'); // Reset to first page
        
        window.location.href = url.toString();
    }

    // Bulk selection functionality
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('select-all');
        const checkboxes = document.querySelectorAll('.item-checkbox');
        const bulkActionsBar = document.getElementById('bulk-actions-bar');
        const selectedCount = document.getElementById('selected-count');
        const bulkActionForm = document.getElementById('bulk-action-form');

        function updateBulkActionsBar() {
            const checked = Array.from(checkboxes).filter(cb => cb.checked);
            if (checked.length > 0) {
                bulkActionsBar.classList.remove('hidden');
                selectedCount.textContent = `${checked.length} item${checked.length > 1 ? 's' : ''} selected`;
            } else {
                bulkActionsBar.classList.add('hidden');
            }
        }

        // Select all checkbox
        if (selectAll) {
            selectAll.addEventListener('change', function() {
                checkboxes.forEach(cb => cb.checked = this.checked);
                updateBulkActionsBar();
            });
        }

        // Individual checkboxes
        checkboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                if (selectAll) {
                    selectAll.checked = Array.from(checkboxes).every(cb => cb.checked);
                }
                updateBulkActionsBar();
            });
        });

        // Bulk actions
        document.querySelectorAll('[data-bulk-action]').forEach(btn => {
            btn.addEventListener('click', function() {
                const action = this.dataset.bulkAction;
                const checked = Array.from(checkboxes).filter(cb => cb.checked).map(cb => cb.value);
                
                if (checked.length === 0) {
                    Swal.fire('Error', 'Please select at least one item.', 'error');
                    return;
                }

                if (action === 'delete' || this.id === 'bulk-delete') {
                    Swal.fire({
                        title: 'Delete Selected Items?',
                        text: `Are you sure you want to delete ${checked.length} item(s)? This cannot be undone.`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Yes, delete them',
                    }).then((result) => {
                        if (result.isConfirmed && bulkActionForm) {
                            document.getElementById('bulk-action-type').value = 'delete';
                            // Add selected IDs to form
                            checked.forEach(id => {
                                const input = document.createElement('input');
                                input.type = 'hidden';
                                input.name = 'ids[]';
                                input.value = id;
                                bulkActionForm.appendChild(input);
                            });
                            bulkActionForm.submit();
                        }
                    });
                } else if (bulkActionForm) {
                    document.getElementById('bulk-action-type').value = action;
                    checked.forEach(id => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'ids[]';
                        input.value = id;
                        bulkActionForm.appendChild(input);
                    });
                    bulkActionForm.submit();
                }
            });
        });
    });

    function clearSelection() {
        document.querySelectorAll('.item-checkbox, #select-all').forEach(cb => cb.checked = false);
        document.getElementById('bulk-actions-bar').classList.add('hidden');
    }
</script>
@endpush
