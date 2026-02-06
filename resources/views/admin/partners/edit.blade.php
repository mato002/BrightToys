@extends('layouts.admin')

@section('page_title', 'Edit Partner')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
        <div>
            <h1 class="text-lg font-semibold">Edit Partner</h1>
            <p class="text-xs text-slate-500">Update partner information.</p>
        </div>
        <a href="{{ route('admin.partners.index') }}" class="text-sm text-slate-600 hover:text-slate-800">
            Back to list
        </a>
    </div>

    <form action="{{ route('admin.partners.update', $partner) }}" method="POST" class="bg-white border border-slate-100 rounded-lg p-4 text-sm space-y-4 shadow-sm max-w-2xl">
        @csrf
        @method('PUT')
        
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
            <input type="text" name="name" value="{{ old('name', $partner->name) }}" required
                   class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-400"
                   placeholder="Enter partner name">
        </div>

        <div>
            <label class="block text-xs font-semibold mb-1 text-slate-700">Email Address</label>
            <input type="email" name="email" value="{{ old('email', $partner->email) }}"
                   class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-400"
                   placeholder="partner@example.com">
        </div>

        <div>
            <label class="block text-xs font-semibold mb-1 text-slate-700">Phone</label>
            <input type="text" name="phone" value="{{ old('phone', $partner->phone) }}"
                   class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-400"
                   placeholder="+1234567890">
        </div>

        <div>
            <label class="block text-xs font-semibold mb-1 text-slate-700">Status <span class="text-red-500">*</span></label>
            <select name="status" required
                    class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-400">
                <option value="active" {{ old('status', $partner->status) === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ old('status', $partner->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>

        <div>
            <label class="block text-xs font-semibold mb-1 text-slate-700">Notes</label>
            <textarea name="notes" rows="3"
                      class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-400"
                      placeholder="Additional notes about this partner...">{{ old('notes', $partner->notes) }}</textarea>
        </div>

        @if($currentOwnership)
        <div class="bg-slate-50 border border-slate-200 rounded-lg p-3">
            <p class="text-xs text-slate-600 mb-2">Current Ownership: <span class="font-semibold">{{ number_format($currentOwnership->percentage, 2) }}%</span></p>
            <p class="text-[10px] text-slate-500">To change ownership, create a new ownership record with a different effective date.</p>
        </div>
        @endif

        <div class="flex items-center gap-3 pt-2 border-t border-slate-100">
            <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold px-6 py-2 rounded shadow-sm">
                Update Partner
            </button>
            <a href="{{ route('admin.partners.index') }}" class="text-sm text-slate-600 hover:text-slate-800">
                Cancel
            </a>
        </div>
    </form>
@endsection
