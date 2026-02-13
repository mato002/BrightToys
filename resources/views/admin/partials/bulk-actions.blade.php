{{-- Bulk Actions Component --}}
{{-- Usage: Include this in table views with @include('admin.partials.bulk-actions', ['route' => 'admin.products.bulk']) --}}

@if(isset($route))
<form id="bulk-action-form" action="{{ route($route) }}" method="POST" class="hidden">
    @csrf
    @method('POST')
    <input type="hidden" name="action" id="bulk-action-type" value="">
    <input type="hidden" name="status" id="bulk-status" value="">
</form>
@endif

<script>
    // Enhanced bulk action handler
    document.addEventListener('DOMContentLoaded', function() {
        const bulkActionForm = document.getElementById('bulk-action-form');
        const bulkActionsBar = document.getElementById('bulk-actions-bar');
        
        if (!bulkActionForm || !bulkActionsBar) return;

        // Add action buttons to bulk actions bar
        const actionsContainer = bulkActionsBar.querySelector('.flex.gap-2');
        if (actionsContainer && !actionsContainer.querySelector('[data-bulk-action]')) {
            // Products-specific actions
            if (bulkActionForm.action.includes('products')) {
                actionsContainer.innerHTML = `
                    <select id="bulk-status-select" class="px-3 py-1.5 border border-slate-300 rounded text-xs">
                        <option value="">Select Status...</option>
                        <option value="active">Activate</option>
                        <option value="inactive">Deactivate</option>
                    </select>
                    <button type="button" data-bulk-action="activate" class="px-3 py-1.5 bg-emerald-600 text-white text-xs font-semibold rounded hover:bg-emerald-700">
                        Activate
                    </button>
                    <button type="button" data-bulk-action="deactivate" class="px-3 py-1.5 bg-amber-600 text-white text-xs font-semibold rounded hover:bg-amber-700">
                        Deactivate
                    </button>
                    <button type="button" data-bulk-action="feature" class="px-3 py-1.5 bg-blue-600 text-white text-xs font-semibold rounded hover:bg-blue-700">
                        Feature
                    </button>
                    <button type="button" data-bulk-action="unfeature" class="px-3 py-1.5 bg-slate-600 text-white text-xs font-semibold rounded hover:bg-slate-700">
                        Unfeature
                    </button>
                    <button type="button" id="bulk-delete" class="px-3 py-1.5 bg-red-600 text-white text-xs font-semibold rounded hover:bg-red-700">
                        Delete Selected
                    </button>
                    <button type="button" onclick="document.getElementById('bulk-actions-bar').classList.add('hidden'); document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = false); document.getElementById('select-all').checked = false;" 
                            class="px-3 py-1.5 bg-slate-200 text-slate-700 text-xs font-semibold rounded hover:bg-slate-300">
                        Cancel
                    </button>
                `;
            }
            // Orders-specific actions
            else if (bulkActionForm.action.includes('orders')) {
                actionsContainer.innerHTML = `
                    <select id="bulk-status-select" class="px-3 py-1.5 border border-slate-300 rounded text-xs">
                        <option value="">Select Status...</option>
                        <option value="pending">Pending</option>
                        <option value="processing">Processing</option>
                        <option value="shipped">Shipped</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                    <button type="button" id="bulk-status-update" class="px-3 py-1.5 bg-emerald-600 text-white text-xs font-semibold rounded hover:bg-emerald-700">
                        Update Status
                    </button>
                    <button type="button" id="bulk-delete" class="px-3 py-1.5 bg-red-600 text-white text-xs font-semibold rounded hover:bg-red-700">
                        Delete Selected
                    </button>
                    <button type="button" onclick="document.getElementById('bulk-actions-bar').classList.add('hidden'); document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = false); document.getElementById('select-all').checked = false;" 
                            class="px-3 py-1.5 bg-slate-200 text-slate-700 text-xs font-semibold rounded hover:bg-slate-300">
                        Cancel
                    </button>
                `;
            }
        }

        // Handle bulk status update for orders
        const bulkStatusUpdate = document.getElementById('bulk-status-update');
        if (bulkStatusUpdate) {
            bulkStatusUpdate.addEventListener('click', function(e) {
                e.preventDefault();
                const statusSelect = document.getElementById('bulk-status-select');
                if (!statusSelect || !statusSelect.value) {
                    Swal.fire('Error', 'Please select a status first.', 'error');
                    return;
                }
                
                const checked = Array.from(document.querySelectorAll('.item-checkbox:checked'))
                    .map(cb => cb.value);
                if (checked.length === 0) return;

                document.getElementById('bulk-action-type').value = 'status_update';
                document.getElementById('bulk-status').value = statusSelect.value;
                bulkActionForm.submit();
            });
        }

        // Handle other bulk actions
        document.querySelectorAll('[data-bulk-action]').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const action = this.dataset.bulkAction;
                const checked = Array.from(document.querySelectorAll('.item-checkbox:checked'))
                    .map(cb => cb.value);
                
                if (checked.length === 0) {
                    Swal.fire('Error', 'Please select at least one item.', 'error');
                    return;
                }

                if (action === 'delete') {
                    Swal.fire({
                        title: 'Delete Selected Items?',
                        text: `Are you sure you want to delete ${checked.length} item(s)? This cannot be undone.`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Yes, delete them',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById('bulk-action-type').value = action;
                            bulkActionForm.submit();
                        }
                    });
                } else {
                    document.getElementById('bulk-action-type').value = action;
                    bulkActionForm.submit();
                }
            });
        });
    });
</script>
