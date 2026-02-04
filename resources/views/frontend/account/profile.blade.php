@extends('layouts.account')

@section('title', 'My Profile')
@section('page_title', 'Profile Overview')

@section('content')
    <div class="space-y-6">
            <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
                <h1 class="text-xl font-bold text-slate-900 mb-6">Profile Overview</h1>
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="p-4 bg-slate-50 rounded-lg">
                        <p class="text-xs text-slate-500 mb-1 uppercase tracking-wide">Full Name</p>
                        <p class="font-semibold text-slate-900 text-base">{{ $user->name }}</p>
                    </div>
                    <div class="p-4 bg-slate-50 rounded-lg">
                        <p class="text-xs text-slate-500 mb-1 uppercase tracking-wide">Email Address</p>
                        <p class="font-semibold text-slate-900 text-base">{{ $user->email }}</p>
                    </div>
                    <div class="p-4 bg-slate-50 rounded-lg">
                        <p class="text-xs text-slate-500 mb-1 uppercase tracking-wide">Member Since</p>
                        <p class="font-semibold text-slate-900 text-base">{{ $user->created_at->format('F d, Y') }}</p>
                    </div>
                    <div class="p-4 bg-slate-50 rounded-lg">
                        <p class="text-xs text-slate-500 mb-1 uppercase tracking-wide">Account Status</p>
                        <p class="font-semibold text-emerald-600 text-base">Active</p>
                    </div>
                </div>
            </div>
    </div>
@endsection

