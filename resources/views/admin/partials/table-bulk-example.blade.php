{{-- Example: How to add bulk actions to a table --}}
{{-- Copy this pattern to your table views --}}

<div class="bg-white border border-slate-100 rounded-lg overflow-x-auto admin-table-scroll text-sm shadow-sm">
    <table class="min-w-full">
        <thead class="bg-slate-50 text-xs text-slate-500 uppercase tracking-wide">
        <tr>
            {{-- Select all checkbox --}}
            <th class="px-3 py-2 w-12">
                <input type="checkbox" id="select-all" aria-label="Select all items">
            </th>
            <th class="px-3 py-2 text-left" data-sortable data-column="name">Name</th>
            <th class="px-3 py-2 text-left" data-sortable data-column="created_at">Date</th>
            <th class="px-3 py-2 text-left">Status</th>
            <th class="px-3 py-2 text-right">Actions</th>
        </tr>
        </thead>
        <tbody>
        @forelse($items as $item)
            <tr class="border-t border-slate-100 hover:bg-slate-50">
                {{-- Item checkbox --}}
                <td class="px-3 py-2">
                    <input type="checkbox" class="item-checkbox" value="{{ $item->id }}" aria-label="Select item {{ $item->id }}">
                </td>
                <td class="px-3 py-2 text-slate-900">{{ $item->name }}</td>
                <td class="px-3 py-2 text-slate-600">{{ $item->created_at->format('M d, Y') }}</td>
                <td class="px-3 py-2">
                    <span class="px-2 py-1 text-xs rounded-full {{ $item->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700' }}">
                        {{ ucfirst($item->status) }}
                    </span>
                </td>
                <td class="px-3 py-2 text-right space-x-2">
                    <a href="{{ route('admin.items.show', $item) }}" class="text-xs text-slate-700 hover:underline">View</a>
                    <a href="{{ route('admin.items.edit', $item) }}" class="text-xs text-amber-600 hover:underline">Edit</a>
                    <form action="{{ route('admin.items.destroy', $item) }}" method="POST" class="inline" data-confirm="Delete this item?">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-xs text-red-500 hover:underline">Delete</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="px-3 py-4 text-center text-slate-500">No items found.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

{{-- Include bulk actions component if you have a bulk action route --}}
{{-- @include('admin.partials.bulk-actions', ['route' => 'admin.items.bulk']) --}}
