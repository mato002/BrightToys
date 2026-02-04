@extends('layouts.auth')

@section('title', 'Login')

@section('content')
    <div>
        <h1 class="text-2xl font-semibold mb-2 text-slate-900 text-center">Welcome back</h1>
        <p class="text-xs text-slate-500 mb-6 text-center">
            Sign in to access your account and continue shopping.
        </p>

        @if($errors->any())
            <div class="mb-4 text-sm text-red-700 bg-red-100 border border-red-200 px-4 py-2 rounded">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST" class="space-y-4 text-sm">
            @csrf
            <div class="space-y-1">
                <label for="email" class="block text-[11px] font-semibold tracking-wide text-slate-700 uppercase">Email address</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autocomplete="email"
                    autofocus
                    class="border border-slate-200 rounded-lg w-full px-3 py-2.5 text-sm bg-slate-50/60 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-400 transition-colors"
                    placeholder="you@example.com"
                >
            </div>
            <div class="space-y-1">
                <label for="password" class="block text-[11px] font-semibold tracking-wide text-slate-700 uppercase">Password</label>
                <div class="relative group">
                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        class="border border-slate-200 rounded-lg w-full px-3 py-2.5 pr-12 text-sm bg-slate-50/60 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-400 transition-colors password-toggle-input"
                        placeholder="Enter your password"
                    >
                    <button
                        type="button"
                        class="absolute inset-y-0 right-0 px-3 text-[11px] text-slate-500 hover:text-slate-700 focus:outline-none password-toggle-btn"
                        data-target="password"
                    >
                        Show
                    </button>
                </div>
            </div>
            <div class="flex items-center justify-between text-[11px]">
                <label class="flex items-center gap-2 text-slate-600">
                    <input type="checkbox" name="remember" class="h-3.5 w-3.5 rounded border-slate-300 text-amber-500 focus:ring-amber-500">
                    <span>Remember me</span>
                </label>
                <a href="{{ route('password.request') }}" class="font-medium text-amber-600 hover:text-amber-700 hover:underline">
                    Forgot password?
                </a>
            </div>
            <button type="submit"
                    class="w-full bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold px-4 py-2.5 rounded-lg shadow-sm shadow-amber-500/30 transition-colors">
                Login
            </button>
        </form>

        <p class="text-xs text-slate-500 mt-4 text-center">
            Don't have an account yet?
            <a href="{{ route('register') }}" class="text-amber-600 hover:text-amber-700 hover:underline font-semibold">Create one</a>
        </p>
    </div>
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
@endsection

