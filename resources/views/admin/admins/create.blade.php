@extends('layouts.admin')

@section('page_title', 'Add Admin')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
        <div>
            <h1 class="text-lg font-semibold">Add Admin</h1>
            <p class="text-xs text-slate-500">Create a new admin account for Otto Investments.</p>
        </div>
        <a href="{{ route('admin.admins.index') }}" class="text-sm text-slate-600 hover:text-slate-800">
            Back to list
        </a>
    </div>

    <form action="{{ route('admin.admins.store') }}" method="POST" class="bg-white border border-slate-100 rounded-lg p-4 text-sm space-y-4 shadow-sm">
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
            <label class="block text-xs font-semibold mb-1 text-slate-700">Full Name <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name') }}" required
                   class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-400"
                   placeholder="Enter full name">
        </div>

        <div>
            <label class="block text-xs font-semibold mb-1 text-slate-700">Email Address <span class="text-red-500">*</span></label>
            <input type="email" name="email" value="{{ old('email') }}" required
                   class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-400"
                   placeholder="admin@brighttoys.com">
            <p class="text-[10px] text-slate-500 mt-1">This email will be used for login.</p>
        </div>

        <div>
            <label class="block text-xs font-semibold mb-1 text-slate-700">Password <span class="text-red-500">*</span></label>
            <div class="relative">
                <input type="password" name="password" id="password" required
                       class="border border-slate-200 rounded w-full px-3 py-2 pr-12 text-sm focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-400 password-toggle-input"
                       placeholder="Minimum 8 characters">
                <button type="button"
                        class="absolute inset-y-0 right-0 px-3 text-xs font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 focus:outline-none password-toggle-btn transition-colors"
                        data-target="password"
                        style="z-index: 10;">
                    Show
                </button>
            </div>
            <p class="text-[10px] text-slate-500 mt-1">Password must be at least 8 characters long.</p>
        </div>

        <div>
            <label class="block text-xs font-semibold mb-1 text-slate-700">Confirm Password <span class="text-red-500">*</span></label>
            <div class="relative">
                <input type="password" name="password_confirmation" id="password_confirmation" required
                       class="border border-slate-200 rounded w-full px-3 py-2 pr-12 text-sm focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-400 password-toggle-input"
                       placeholder="Re-enter password">
                <button type="button"
                        class="absolute inset-y-0 right-0 px-3 text-xs font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 focus:outline-none password-toggle-btn transition-colors"
                        data-target="password_confirmation"
                        style="z-index: 10;">
                    Show
                </button>
            </div>
        </div>

        <div class="border-t border-slate-100 pt-4">
            <label class="block text-xs font-semibold mb-2 text-slate-700">Admin Roles</label>
            <p class="text-[10px] text-slate-500 mb-3">Select roles for this admin. Super admins have full access.</p>
            <div class="space-y-2">
                @foreach($roles ?? [] as $role)
                    <label class="flex items-center">
                        <input type="checkbox" name="roles[]" value="{{ $role->id }}" 
                               class="rounded border-slate-300 text-amber-500 focus:ring-amber-500">
                        <span class="ml-2 text-xs text-slate-700">{{ $role->display_name }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="flex items-center gap-3 pt-2 border-t border-slate-100">
            <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold px-6 py-2 rounded shadow-sm">
                Create Admin
            </button>
            <a href="{{ route('admin.admins.index') }}" class="text-sm text-slate-600 hover:text-slate-800">
                Cancel
            </a>
        </div>
    </form>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const buttons = document.querySelectorAll('.password-toggle-btn');

            buttons.forEach((btn) => {
                btn.addEventListener('click', () => {
                    const targetId = btn.getAttribute('data-target');
                    const input = document.getElementById(targetId);

                    if (!input) return;

                    const isHidden = input.type === 'password';
                    input.type = isHidden ? 'text' : 'password';
                    btn.textContent = isHidden ? 'Hide' : 'Show';
                });
            });
        });
    </script>
    @endpush
@endsection
