@extends('layouts.admin')

@section('title', 'Settings & Permissions')
@section('page_title', 'Settings & Permissions')

@section('content')
    <div class="space-y-6">
        {{-- Page Header --}}
        <div class="mb-6">
            <h1 class="text-xl md:text-2xl font-semibold text-slate-900 tracking-tight">Settings & Permissions</h1>
            <p class="mt-1 text-sm text-slate-500 max-w-2xl">
                Manage your account settings, security preferences, roles, and permissions.
            </p>
        </div>

        {{-- Success/Error Messages --}}
        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-sm text-red-700">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Left Column - Main Settings --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Profile Information --}}
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="text-base font-semibold text-slate-900">Profile Information</h2>
                            <p class="text-xs text-slate-500 mt-1">Update your name and email address</p>
                        </div>
                        <a href="{{ route('admin.profile') }}" class="text-xs text-emerald-600 hover:text-emerald-700 font-medium">
                            View Full Profile →
                        </a>
                    </div>
                    <form method="POST" action="{{ route('admin.settings.profile') }}" class="space-y-4">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-slate-700 mb-1.5">Full Name</label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                       class="block w-full rounded-lg border-slate-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                                @error('name')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-700 mb-1.5">Email Address</label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                       class="block w-full rounded-lg border-slate-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                                @error('email')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <button type="submit"
                                class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
                            Update Profile
                        </button>
                    </form>
                </div>

                {{-- Security Settings --}}
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                    <h2 class="text-base font-semibold text-slate-900 mb-4">Security Settings</h2>
                    
                    {{-- Change Password --}}
                    <div class="mb-6 pb-6 border-b border-slate-200">
                        <h3 class="text-sm font-medium text-slate-900 mb-3">Change Password</h3>
                        <form method="POST" action="{{ route('admin.settings.password') }}" class="space-y-4">
                            @csrf
                            @method('PUT')
                            <div>
                                <label class="block text-xs font-medium text-slate-700 mb-1.5">Current Password</label>
                                <input type="password" name="current_password" required
                                       class="block w-full rounded-lg border-slate-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                                @error('current_password')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1.5">New Password</label>
                                    <input type="password" name="password" required
                                           class="block w-full rounded-lg border-slate-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                                    @error('password')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1.5">Confirm New Password</label>
                                    <input type="password" name="password_confirmation" required
                                           class="block w-full rounded-lg border-slate-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                                </div>
                            </div>
                            <button type="submit"
                                    class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
                                Update Password
                            </button>
                        </form>
                    </div>

                    {{-- Active Sessions --}}
                    <div>
                        <h3 class="text-sm font-medium text-slate-900 mb-2">Active Sessions</h3>
                        <p class="text-xs text-slate-500 mb-3">Manage your active login sessions across different devices</p>
                        <a href="{{ route('admin.profile.sessions') }}"
                           class="inline-flex items-center gap-2 text-sm text-emerald-600 hover:text-emerald-700 font-medium">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            View Active Sessions
                        </a>
                    </div>
                </div>

                {{-- Role Management --}}
                @if($canManageRoles)
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                    <h2 class="text-base font-semibold text-slate-900 mb-4">Role Management</h2>
                    <p class="text-xs text-slate-500 mb-4">
                        Assign roles to admin users. Roles determine what permissions users have across the system.
                    </p>

                    @if($adminUsers && $adminUsers->count() > 0)
                        <div class="space-y-4">
                            @foreach($adminUsers as $adminUser)
                                <div class="border border-slate-200 rounded-lg p-4 hover:bg-slate-50 transition-colors">
                                    <div class="flex items-center justify-between mb-3">
                                        <div>
                                            <h3 class="text-sm font-semibold text-slate-900">{{ $adminUser->name }}</h3>
                                            <p class="text-xs text-slate-500">{{ $adminUser->email }}</p>
                                        </div>
                                        @if($adminUser->isSuperAdmin())
                                            <span class="inline-flex items-center rounded-full bg-purple-100 px-2.5 py-1 text-xs font-medium text-purple-700">
                                                Super Admin
                                            </span>
                                        @endif
                                    </div>

                                    <form method="POST" action="{{ route('admin.settings.assign-roles', $adminUser) }}" class="space-y-3">
                                        @csrf
                                        <div class="flex flex-wrap gap-3">
                                            @foreach($roles as $role)
                                                <label class="inline-flex items-center cursor-pointer">
                                                    <input type="checkbox" name="roles[]" value="{{ $role->id }}"
                                                           {{ $adminUser->adminRoles->contains($role->id) ? 'checked' : '' }}
                                                           class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                                    <span class="ml-2 text-xs text-slate-700">{{ $role->display_name }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                        <button type="submit"
                                                class="text-xs bg-slate-100 hover:bg-slate-200 text-slate-700 px-3 py-1.5 rounded-lg transition-colors">
                                            Update Roles
                                        </button>
                                    </form>

                                    @if($adminUser->adminRoles->count() > 0)
                                        <div class="mt-3 pt-3 border-t border-slate-100">
                                            <p class="text-xs text-slate-500 mb-2">Current Roles:</p>
                                            <div class="flex flex-wrap gap-1.5">
                                                @foreach($adminUser->adminRoles as $role)
                                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-medium text-emerald-700 border border-emerald-200">
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
                @endif
            </div>

            {{-- Right Column - Sidebar Info --}}
            <div class="space-y-6">
                {{-- Account Information --}}
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                    <h2 class="text-base font-semibold text-slate-900 mb-4">Account Information</h2>
                    <div class="space-y-3">
                        <div>
                            <p class="text-xs text-slate-500 mb-1">Member Since</p>
                            <p class="text-sm font-medium text-slate-900">{{ $accountStats['member_since'] }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 mb-1">Last Activity</p>
                            <p class="text-sm font-medium text-slate-900">{{ $accountStats['last_login'] }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 mb-1">Total Activity Logs</p>
                            <p class="text-sm font-medium text-slate-900">{{ number_format($accountStats['total_activity']) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 mb-1">Email Status</p>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $accountStats['email_verified'] ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                {{ $accountStats['email_verified'] ? 'Verified' : 'Not Verified' }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Recent Activity --}}
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                    <h2 class="text-base font-semibold text-slate-900 mb-4">Recent Activity</h2>
                    @if($recentActivity->count() > 0)
                        <div class="space-y-3">
                            @foreach($recentActivity as $activity)
                                <div class="border-l-2 border-emerald-200 pl-3 py-1">
                                    <p class="text-xs font-medium text-slate-900">{{ $activity->action ?? 'Activity' }}</p>
                                    <p class="text-[11px] text-slate-500 mt-0.5">{{ $activity->created_at->diffForHumans() }}</p>
                                </div>
                            @endforeach
                        </div>
                        <a href="{{ route('admin.profile') }}" class="mt-4 inline-block text-xs text-emerald-600 hover:text-emerald-700 font-medium">
                            View All Activity →
                        </a>
                    @else
                        <p class="text-xs text-slate-500">No recent activity</p>
                    @endif
                </div>

                {{-- Quick Links --}}
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                    <h2 class="text-base font-semibold text-slate-900 mb-4">Quick Links</h2>
                    <div class="space-y-2">
                        <a href="{{ route('admin.profile') }}" class="block text-sm text-slate-700 hover:text-emerald-600 transition-colors">
                            My Profile
                        </a>
                        <a href="{{ route('admin.profile.sessions') }}" class="block text-sm text-slate-700 hover:text-emerald-600 transition-colors">
                            Active Sessions
                        </a>
                        <a href="{{ route('admin.profile.edit') }}" class="block text-sm text-slate-700 hover:text-emerald-600 transition-colors">
                            Edit Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Available Roles Overview (if user can manage roles) --}}
        @if($canManageRoles && $roles)
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h2 class="text-base font-semibold text-slate-900 mb-4">Available Roles & Permissions</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($roles as $role)
                    <div class="border border-slate-200 rounded-lg p-4 hover:bg-slate-50 transition-colors">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-sm font-semibold text-slate-900">{{ $role->display_name }}</h3>
                            <code class="text-[10px] text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded">{{ $role->name }}</code>
                        </div>
                        @if($role->permissions && $role->permissions->count() > 0)
                            <div class="space-y-1.5">
                                <p class="text-xs font-medium text-slate-700 mb-1">Permissions:</p>
                                <div class="flex flex-wrap gap-1">
                                    @foreach($role->permissions->take(5) as $permission)
                                        <span class="text-[10px] text-slate-600 bg-slate-50 px-2 py-0.5 rounded border border-slate-200">
                                            {{ $permission->display_name ?? $permission->name }}
                                        </span>
                                    @endforeach
                                    @if($role->permissions->count() > 5)
                                        <span class="text-[10px] text-slate-500">+{{ $role->permissions->count() - 5 }} more</span>
                                    @endif
                                </div>
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
