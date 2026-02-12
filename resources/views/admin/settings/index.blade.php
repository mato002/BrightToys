@extends('layouts.admin')

@section('title', 'Settings & Permissions')

@section('content')
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <h1 class="text-xl font-semibold text-slate-900">Settings & Permissions</h1>
            <p class="mt-1 text-sm text-slate-500">
                Manage admin roles, permissions, and user access controls.
            </p>
        </div>

        @if(session('success'))
            <div class="mb-4 bg-emerald-50 border border-emerald-200 rounded-lg p-3 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 bg-red-50 border border-red-200 rounded-lg p-3 text-sm text-red-700">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Change Password Section --}}
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h2 class="text-sm font-semibold text-slate-900 mb-4">Change Password</h2>
                <form method="POST" action="{{ route('admin.settings.password') }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Current Password</label>
                        <input type="password" name="current_password" required
                               class="block w-full rounded-md border-slate-300 text-sm">
                        @error('current_password')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">New Password</label>
                        <input type="password" name="password" required
                               class="block w-full rounded-md border-slate-300 text-sm">
                        @error('password')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Confirm New Password</label>
                        <input type="password" name="password_confirmation" required
                               class="block w-full rounded-md border-slate-300 text-sm">
                    </div>
                    <button type="submit"
                            class="w-full bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-4 py-2 rounded-lg">
                        Update Password
                    </button>
                </form>
            </div>

            {{-- Role Management Section --}}
            @if($canManageRoles)
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h2 class="text-sm font-semibold text-slate-900 mb-4">Role Management</h2>
                <p class="text-xs text-slate-500 mb-4">
                    Assign roles to admin users. Roles determine what permissions users have across the system.
                </p>

                @if($adminUsers && $adminUsers->count() > 0)
                    <div class="space-y-4">
                        @foreach($adminUsers as $adminUser)
                            <div class="border border-slate-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <div>
                                        <h3 class="text-sm font-semibold text-slate-900">{{ $adminUser->name }}</h3>
                                        <p class="text-xs text-slate-500">{{ $adminUser->email }}</p>
                                    </div>
                                    @if($adminUser->isSuperAdmin())
                                        <span class="inline-flex items-center rounded-full bg-purple-100 px-2 py-1 text-xs font-medium text-purple-700">
                                            Super Admin
                                        </span>
                                    @endif
                                </div>

                                <form method="POST" action="{{ route('admin.settings.assign-roles', $adminUser) }}" class="space-y-2">
                                    @csrf
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($roles as $role)
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="roles[]" value="{{ $role->id }}"
                                                       {{ $adminUser->adminRoles->contains($role->id) ? 'checked' : '' }}
                                                       class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                                <span class="ml-2 text-xs text-slate-700">{{ $role->display_name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    <button type="submit"
                                            class="mt-2 text-xs bg-slate-100 hover:bg-slate-200 text-slate-700 px-3 py-1.5 rounded">
                                        Update Roles
                                    </button>
                                </form>

                                @if($adminUser->adminRoles->count() > 0)
                                    <div class="mt-2 pt-2 border-t border-slate-100">
                                        <p class="text-xs text-slate-500 mb-1">Current Roles:</p>
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($adminUser->adminRoles as $role)
                                                <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-medium text-emerald-700">
                                                    {{ $role->display_name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-slate-500">No admin users found.</p>
                @endif
            </div>
            @else
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-6">
                <h2 class="text-sm font-semibold text-slate-900 mb-2">Role Management</h2>
                <p class="text-xs text-slate-500">
                    You do not have permission to manage roles. Only Super Admin and Finance Admin can manage roles.
                </p>
            </div>
            @endif
        </div>

        {{-- Permissions Overview (if user can manage roles) --}}
        @if($canManageRoles && $roles)
        <div class="mt-6 bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h2 class="text-sm font-semibold text-slate-900 mb-4">Available Roles</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($roles as $role)
                    <div class="border border-slate-200 rounded-lg p-4">
                        <h3 class="text-sm font-semibold text-slate-900 mb-2">{{ $role->display_name }}</h3>
                        <p class="text-xs text-slate-500 mb-3">Role: <code class="text-slate-600">{{ $role->name }}</code></p>
                        @if($role->permissions && $role->permissions->count() > 0)
                            <div class="space-y-1">
                                <p class="text-xs font-medium text-slate-700 mb-1">Permissions:</p>
                                @foreach($role->permissions as $permission)
                                    <div class="text-xs text-slate-600 bg-slate-50 px-2 py-1 rounded">
                                        {{ $permission->display_name ?? $permission->name }}
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-xs text-slate-400">No permissions assigned</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
@endsection
