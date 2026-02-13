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

    <form action="{{ route('admin.admins.store') }}" method="POST" class="card card-body form-full-width">
        @csrf
        
        @if($errors->any())
            <div class="alert alert-error">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="form-group">
            <label class="form-label">Full Name <span class="required">*</span></label>
            <input type="text" name="name" value="{{ old('name') }}" required
                   placeholder="Enter full name">
        </div>

        <div class="form-group">
            <label class="form-label">Email Address <span class="required">*</span></label>
            <input type="email" name="email" value="{{ old('email') }}" required
                   placeholder="admin@brighttoys.com">
            <p class="form-help">This email will be used for login.</p>
        </div>

        <div class="form-group">
            <label class="form-label">Password <span class="required">*</span></label>
            <div class="relative">
                <input type="password" name="password" id="password" required
                       class="pr-12"
                       placeholder="Minimum 8 characters">
                <button type="button"
                        class="absolute inset-y-0 right-0 px-3 text-xs font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 focus:outline-none password-toggle-btn transition-colors"
                        data-target="password"
                        style="z-index: 10;">
                    Show
                </button>
            </div>
            <p class="form-help">Password must be at least 8 characters long.</p>
        </div>

        <div class="form-group">
            <label class="form-label">Confirm Password <span class="required">*</span></label>
            <div class="relative">
                <input type="password" name="password_confirmation" id="password_confirmation" required
                       class="pr-12"
                       placeholder="Re-enter password">
                <button type="button"
                        class="absolute inset-y-0 right-0 px-3 text-xs font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 focus:outline-none password-toggle-btn transition-colors"
                        data-target="password_confirmation"
                        style="z-index: 10;">
                    Show
                </button>
            </div>
        </div>

        <div class="form-group border-t-2 border-slate-200 pt-5">
            <label class="form-label">Admin Roles</label>
            <p class="form-help">Select roles for this admin. Super admins have full access.</p>
            <div class="space-y-2 mt-3">
                @foreach($roles ?? [] as $role)
                    <label class="flex items-center">
                        <input type="checkbox" name="roles[]" value="{{ $role->id }}">
                        <span class="ml-2 text-sm text-slate-700">{{ $role->display_name }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="flex items-center gap-3 pt-4 border-t-2 border-slate-200">
            <button type="submit" class="btn-primary">
                Create Admin
            </button>
            <a href="{{ route('admin.admins.index') }}" class="btn-secondary">
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
