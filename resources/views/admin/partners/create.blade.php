@extends('layouts.admin')

@section('page_title', 'Add Partner')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
        <div>
            <h1 class="text-lg font-semibold">Add Partner</h1>
            <p class="text-xs text-slate-500">Create a new partner account.</p>
        </div>
        <a href="{{ route('admin.partners.index') }}" class="text-sm text-slate-600 hover:text-slate-800">
            Back to list
        </a>
    </div>

    <form action="{{ route('admin.partners.store') }}" method="POST" class="bg-white border border-slate-100 rounded-lg p-4 text-sm space-y-4 shadow-sm max-w-2xl">
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
            <label class="block text-xs font-semibold mb-1 text-slate-700">Partner Name <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name') }}" required
                   class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-400"
                   placeholder="Enter partner name">
        </div>

        <div>
            <label class="block text-xs font-semibold mb-1 text-slate-700">Email Address</label>
            <input type="email" name="email" value="{{ old('email') }}"
                   class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-400"
                   placeholder="partner@example.com">
        </div>

        <div>
            <label class="block text-xs font-semibold mb-1 text-slate-700">Phone</label>
            <input type="text" name="phone" value="{{ old('phone') }}"
                   class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-400"
                   placeholder="+1234567890">
        </div>

        <div>
            <label class="block text-xs font-semibold mb-1 text-slate-700">Status <span class="text-red-500">*</span></label>
            <select name="status" required
                    class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-400">
                <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>

        <div>
            <label class="block text-xs font-semibold mb-1 text-slate-700">Ownership Percentage <span class="text-red-500">*</span></label>
            <input type="number" name="ownership_percentage" value="{{ old('ownership_percentage') }}" 
                   step="0.01" min="0" max="100" required
                   class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-400"
                   placeholder="10.00">
            <p class="text-[10px] text-slate-500 mt-1">Enter the ownership percentage (e.g., 10.00 for 10%).</p>
        </div>

        <div>
            <label class="block text-xs font-semibold mb-1 text-slate-700">Effective From <span class="text-red-500">*</span></label>
            <input type="date" name="effective_from" value="{{ old('effective_from', now()->format('Y-m-d')) }}" required
                   class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-400">
        </div>

        <div>
            <label class="block text-xs font-semibold mb-1 text-slate-700">Notes</label>
            <textarea name="notes" rows="3"
                      class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-400"
                      placeholder="Additional notes about this partner...">{{ old('notes') }}</textarea>
        </div>

        <div class="flex items-center gap-3 pt-2 border-t border-slate-100">
            <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold px-6 py-2 rounded shadow-sm">
                Create Partner
            </button>
            <a href="{{ route('admin.partners.index') }}" class="text-sm text-slate-600 hover:text-slate-800">
                Cancel
            </a>
        </div>
    </form>
@endsection
