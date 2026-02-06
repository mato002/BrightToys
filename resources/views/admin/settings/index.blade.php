@extends('layouts.admin')

@section('page_title', 'Settings')

@section('content')
    {{-- Page header --}}
    <div class="mb-6 flex flex-col md:flex-row md:items-end md:justify-between gap-4">
        <div>
            <h1 class="text-xl md:text-2xl font-semibold text-slate-900 tracking-tight">Settings</h1>
            <p class="text-xs md:text-sm text-slate-500 mt-1 max-w-2xl">
                Manage security settings, preferences and account security for BrightToys Admin.
            </p>
        </div>
        <div class="flex items-center gap-2 text-[11px] text-slate-500">
            <span>Last login:</span>
            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-1 text-emerald-700 border border-emerald-100">
                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                Active session
            </span>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 text-sm text-emerald-700 bg-emerald-50 border border-emerald-200 px-4 py-2 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid lg:grid-cols-3 gap-6 text-sm">
        {{-- Left column: quick actions --}}
        <div class="space-y-4 lg:col-span-1">
            <div class="bg-white border rounded-2xl p-5">
                <h2 class="text-xs font-semibold tracking-[0.18em] text-slate-500 uppercase mb-3">Quick actions</h2>
                <div class="space-y-2 text-xs">
                    <a href="{{ route('admin.profile') }}"
                       class="w-full inline-flex items-center justify-between rounded-xl border border-slate-200 px-3 py-2 hover:border-emerald-400 hover:bg-emerald-50/40 transition-colors">
                        <span class="font-medium text-slate-800">View profile</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M9 5l7 7-7 7" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                    <a href="{{ route('admin.profile.edit') }}"
                       class="w-full inline-flex items-center justify-between rounded-xl border border-slate-200 px-3 py-2 hover:border-emerald-400 hover:bg-emerald-50/40 transition-colors">
                        <span class="font-medium text-slate-800">Update profile</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M9 5l7 7-7 7" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        {{-- Right column: security settings --}}
        <div class="space-y-4 lg:col-span-2">
            {{-- Security Section --}}
            <div class="bg-white border rounded-2xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">Security</h2>
                        <p class="text-[11px] text-slate-500 mt-0.5">
                            Basic security overview for your admin account.
                        </p>
                    </div>
                </div>
                <div class="grid md:grid-cols-2 gap-4 text-xs">
                    <div class="rounded-xl border border-slate-100 bg-slate-50/60 p-3">
                        <p class="font-semibold text-slate-900 text-sm mb-1">Password</p>
                        <p class="text-slate-500 mb-2">Your password is set. Change it periodically to keep your account secure.</p>
                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-[11px] text-emerald-700 border border-emerald-100">
                            Status: Good
                        </span>
                    </div>
                    <div class="rounded-xl border border-amber-100 bg-amber-50/60 p-3">
                        <p class="font-semibold text-slate-900 text-sm mb-1">Two‑factor authentication</p>
                        <p class="text-slate-500 mb-2">
                            2FA is not configured yet. In a production setup you could require this for all admins.
                        </p>
                        <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-0.5 text-[11px] text-amber-800 border border-amber-200">
                            Recommendation: Enable 2FA
                        </span>
                    </div>
                </div>
            </div>

            {{-- Change Password Form --}}
            <div class="bg-white border rounded-2xl p-5">
                <h2 class="text-base font-semibold text-slate-900 mb-4">Change Password</h2>
                
                @if($errors->any())
                    <div class="mb-4 text-sm text-red-700 bg-red-50 border border-red-200 px-4 py-2 rounded-lg">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.settings.password') }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="current_password" class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Current Password <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="password"
                                   id="current_password"
                                   name="current_password"
                                   required
                                   class="w-full border border-slate-200 rounded-lg px-3 py-2 pr-12 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 password-toggle-input">
                            <button type="button"
                                    class="absolute inset-y-0 right-0 px-3 text-xs font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 focus:outline-none password-toggle-btn transition-colors"
                                    data-target="current_password"
                                    style="z-index: 10;">
                                Show
                            </button>
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-xs font-semibold text-slate-700 mb-1.5">
                            New Password <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="password"
                                   id="password"
                                   name="password"
                                   required
                                   class="w-full border border-slate-200 rounded-lg px-3 py-2 pr-12 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 password-toggle-input">
                            <button type="button"
                                    class="absolute inset-y-0 right-0 px-3 text-xs font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 focus:outline-none password-toggle-btn transition-colors"
                                    data-target="password"
                                    style="z-index: 10;">
                                Show
                            </button>
                        </div>
                        <p class="mt-1 text-[11px] text-slate-500">
                            Password must be at least 8 characters long.
                        </p>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Confirm New Password <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="password"
                                   id="password_confirmation"
                                   name="password_confirmation"
                                   required
                                   class="w-full border border-slate-200 rounded-lg px-3 py-2 pr-12 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 password-toggle-input">
                            <button type="button"
                                    class="absolute inset-y-0 right-0 px-3 text-xs font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 focus:outline-none password-toggle-btn transition-colors"
                                    data-target="password_confirmation"
                                    style="z-index: 10;">
                                Show
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-4 py-2.5 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M5 13l4 4L19 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span>Update Password</span>
                        </button>
                    </div>
                </form>
            </div>

            {{-- Recent Activity --}}
            <div class="bg-white border rounded-2xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-base font-semibold text-slate-900">Recent activity</h2>
                    <span class="text-[11px] text-slate-400">Demo data</span>
                </div>
                <div class="space-y-2 text-xs text-slate-600">
                    <div class="flex items-center justify-between py-1 border-b border-slate-100 last:border-0">
                        <span>Signed in to admin dashboard</span>
                        <span class="text-[11px] text-slate-400">Just now</span>
                    </div>
                    <div class="flex items-center justify-between py-1 border-b border-slate-100 last:border-0">
                        <span>Viewed orders overview</span>
                        <span class="text-[11px] text-slate-400">Today</span>
                    </div>
                    <div class="flex items-center justify-between py-1 border-b border-slate-100 last:border-0">
                        <span>Updated product catalogue</span>
                        <span class="text-[11px] text-slate-400">This week</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
