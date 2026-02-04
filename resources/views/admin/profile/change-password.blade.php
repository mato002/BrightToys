@extends('layouts.admin')

@section('page_title', 'Change Password')

@section('content')
    {{-- Page header --}}
    <div class="mb-6 flex flex-col md:flex-row md:items-end md:justify-between gap-4">
        <div>
            <h1 class="text-xl md:text-2xl font-semibold text-slate-900 tracking-tight">Change password</h1>
            <p class="text-xs md:text-sm text-slate-500 mt-1 max-w-2xl">
                Update your account password. Choose a strong password to keep your account secure.
            </p>
        </div>
        <a href="{{ route('admin.profile') }}"
           class="inline-flex items-center gap-1 rounded-full border border-slate-200 bg-white px-3 py-1.5 text-[11px] font-medium text-slate-700 hover:border-emerald-400 hover:bg-emerald-50">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path d="M19 12H5M12 19l-7-7 7-7" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span>Back to profile</span>
        </a>
    </div>

    <div class="max-w-2xl">
        <div class="bg-white border rounded-2xl p-6 shadow-sm">
            @if(session('success'))
                <div class="mb-4 text-sm text-emerald-700 bg-emerald-50 border border-emerald-200 px-4 py-2 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 text-sm text-red-700 bg-red-50 border border-red-200 px-4 py-2 rounded-lg">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.profile.change-password.post') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label for="current_password" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Current password
                    </label>
                    <input type="password"
                           id="current_password"
                           name="current_password"
                           required
                           class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    <p class="mt-1 text-[11px] text-slate-500">
                        Enter your current password to confirm the change.
                    </p>
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        New password
                    </label>
                    <input type="password"
                           id="password"
                           name="password"
                           required
                           class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    <p class="mt-1 text-[11px] text-slate-500">
                        Use at least 8 characters with a mix of letters, numbers and symbols.
                    </p>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Confirm new password
                    </label>
                    <input type="password"
                           id="password_confirmation"
                           name="password_confirmation"
                           required
                           class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-4 py-2.5 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span>Update password</span>
                    </button>
                    <a href="{{ route('admin.profile') }}"
                       class="inline-flex items-center rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-medium px-4 py-2.5 transition-colors">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
