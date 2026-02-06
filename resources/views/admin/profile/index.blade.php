@extends('layouts.admin')

@section('page_title', 'My Profile')

@section('content')
    {{-- Page header --}}
    <div class="mb-6 flex flex-col md:flex-row md:items-end md:justify-between gap-4">
        <div>
            <h1 class="text-xl md:text-2xl font-semibold text-slate-900 tracking-tight">My Profile</h1>
            <p class="text-xs md:text-sm text-slate-500 mt-1 max-w-2xl">
                View and manage your admin account profile information.
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
        {{-- Left column: profile summary & role --}}
        <div class="space-y-4 lg:col-span-1">
            <div class="bg-white border rounded-2xl p-5 shadow-sm shadow-emerald-50">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 rounded-full bg-emerald-500 text-white flex items-center justify-center text-lg font-semibold">
                        {{ strtoupper(substr($user->name ?? 'A', 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-semibold text-slate-900">{{ $user->name }}</p>
                        <p class="text-xs text-slate-500">{{ $user->email }}</p>
                        <div class="mt-1 inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-0.5 text-[11px] text-emerald-700 border border-emerald-100">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                            Super admin
                        </div>
                    </div>
                </div>
                <p class="text-xs text-slate-500 leading-relaxed">
                    This account has access to manage products, orders, customers and other admins.
                    Keep your login details secure and enable additional security where possible.
                </p>
            </div>

            <div class="bg-white border rounded-2xl p-5">
                <h2 class="text-xs font-semibold tracking-[0.18em] text-slate-500 uppercase mb-3">Quick actions</h2>
                <div class="space-y-2 text-xs">
                    <a href="{{ route('admin.profile.edit') }}"
                       class="w-full inline-flex items-center justify-between rounded-xl border border-slate-200 px-3 py-2 hover:border-emerald-400 hover:bg-emerald-50/40 transition-colors">
                        <span class="font-medium text-slate-800">Update profile details</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M9 5l7 7-7 7" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                    <a href="{{ route('admin.settings') }}"
                       class="w-full inline-flex items-center justify-between rounded-xl border border-slate-200 px-3 py-2 hover:border-emerald-400 hover:bg-emerald-50/40 transition-colors">
                        <span class="font-medium text-slate-800">Security & Settings</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M9 5l7 7-7 7" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        {{-- Right column: account overview & security --}}
        <div class="space-y-4 lg:col-span-2">
            <div class="bg-white border rounded-2xl p-5">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-base font-semibold text-slate-900">Account overview</h2>
                    <span class="text-[11px] text-slate-400">
                        Joined {{ $user->created_at?->format('d M Y') ?? '—' }}
                    </span>
                </div>
                <div class="grid md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <p class="text-[11px] text-slate-500 mb-1">Full name</p>
                        <p class="font-medium text-slate-900">{{ $user->name }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] text-slate-500 mb-1">Email address</p>
                        <p class="font-medium text-slate-900 break-all">{{ $user->email }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] text-slate-500 mb-1">Role</p>
                        <p class="font-medium text-slate-900">Administrator</p>
                    </div>
                </div>
            </div>

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
@endsection

