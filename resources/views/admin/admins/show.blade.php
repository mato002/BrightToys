@extends('layouts.admin')

@section('page_title', 'Admin Details')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
        <div>
            <h1 class="text-lg font-semibold">Admin Details</h1>
            <p class="text-xs text-slate-500">View admin account information.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.admins.edit', $admin) }}"
               class="inline-flex items-center justify-center bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold px-4 py-2 rounded-md shadow-sm">
                Edit Admin
            </a>
            <a href="{{ route('admin.admins.index') }}" class="text-sm text-slate-600 hover:text-slate-800">
                Back to list
            </a>
        </div>
    </div>

    <div class="grid md:grid-cols-2 gap-4">
        <div class="bg-white border border-slate-100 rounded-lg p-4 shadow-sm">
            <h2 class="text-sm font-semibold text-slate-900 mb-4 pb-2 border-b border-slate-200">Account Information</h2>
            <div class="space-y-3 text-sm">
                <div>
                    <label class="text-xs text-slate-500 uppercase tracking-wide block mb-1">Name</label>
                    <p class="text-slate-900 font-medium">{{ $admin->name }}</p>
                </div>
                <div>
                    <label class="text-xs text-slate-500 uppercase tracking-wide block mb-1">Email</label>
                    <p class="text-slate-900 font-medium">{{ $admin->email }}</p>
                </div>
                <div>
                    <label class="text-xs text-slate-500 uppercase tracking-wide block mb-1">Account Type</label>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                        Admin
                    </span>
                </div>
                <div>
                    <label class="text-xs text-slate-500 uppercase tracking-wide block mb-1">Joined</label>
                    <p class="text-slate-900">{{ $admin->created_at->format('F d, Y') }}</p>
                </div>
                <div>
                    <label class="text-xs text-slate-500 uppercase tracking-wide block mb-1">Last Updated</label>
                    <p class="text-slate-900">{{ $admin->updated_at->format('F d, Y') }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white border border-slate-100 rounded-lg p-4 shadow-sm">
            <h2 class="text-sm font-semibold text-slate-900 mb-4 pb-2 border-b border-slate-200">Statistics</h2>
            <div class="space-y-3 text-sm">
                <div>
                    <label class="text-xs text-slate-500 uppercase tracking-wide block mb-1">Total Orders</label>
                    <p class="text-slate-900 font-semibold text-lg">{{ $admin->orders_count ?? 0 }}</p>
                </div>
                @if($admin->id === auth()->id())
                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 text-xs text-amber-700">
                        <p class="font-medium">This is your account</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if($admin->id !== auth()->id())
        <div class="mt-4 bg-white border border-slate-100 rounded-lg p-4 shadow-sm">
            <h2 class="text-sm font-semibold text-slate-900 mb-4 pb-2 border-b border-slate-200">Danger Zone</h2>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-900 font-medium mb-1">Delete Admin</p>
                    <p class="text-xs text-slate-500">Permanently delete this admin account. This action cannot be undone.</p>
                </div>
                <form action="{{ route('admin.admins.destroy', $admin) }}" method="POST"
                      data-confirm="Are you sure you want to delete this admin? This action cannot be undone.">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white text-xs font-semibold px-4 py-2 rounded shadow-sm">
                        Delete Admin
                    </button>
                </form>
            </div>
        </div>
    @endif
@endsection
