@extends('layouts.auth')

@section('title', 'Register')

@section('content')
    <div class="bg-white border rounded-lg p-6 shadow-sm">
        <h1 class="text-2xl font-semibold mb-2 text-center">Create your account</h1>
        <p class="text-xs text-gray-600 mb-6 text-center">
            Join BrightToys to track your orders, save your favourites and enjoy a smoother checkout.
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

        <form action="{{ route('register.post') }}" method="POST" class="space-y-4 text-sm">
            @csrf
            <div>
                <label for="name" class="block text-xs font-semibold mb-1">Full name</label>
                <input
                    id="name"
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    required
                    autocomplete="name"
                    class="border rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                    placeholder="John Doe"
                >
            </div>
            <div>
                <label for="email" class="block text-xs font-semibold mb-1">Email address</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autocomplete="email"
                    class="border rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                    placeholder="you@example.com"
                >
            </div>
            <div>
                <label for="password" class="block text-xs font-semibold mb-1">Password</label>
                <div class="relative">
                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        autocomplete="new-password"
                        class="border rounded w-full px-3 py-2 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 password-toggle-input"
                        placeholder="At least 8 characters"
                    >
                    <button
                        type="button"
                        class="absolute inset-y-0 right-0 px-3 text-xs text-gray-500 hover:text-gray-700 focus:outline-none password-toggle-btn"
                        data-target="password"
                    >
                        Show
                    </button>
                </div>
            </div>
            <div>
                <label for="password_confirmation" class="block text-xs font-semibold mb-1">Confirm password</label>
                <div class="relative">
                    <input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        required
                        autocomplete="new-password"
                        class="border rounded w-full px-3 py-2 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 password-toggle-input"
                        placeholder="Repeat your password"
                    >
                    <button
                        type="button"
                        class="absolute inset-y-0 right-0 px-3 text-xs text-gray-500 hover:text-gray-700 focus:outline-none password-toggle-btn"
                        data-target="password_confirmation"
                    >
                        Show
                    </button>
                </div>
                <p class="mt-1 text-[11px] text-gray-500">
                    Use at least 8 characters. Combine letters, numbers and symbols for better security.
                </p>
            </div>
            <button type="submit"
                    class="w-full bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold px-4 py-2 rounded transition-colors">
                Register
            </button>
        </form>

        <p class="text-xs text-gray-600 mt-4 text-center">
            Already have an account?
            <a href="{{ route('login') }}" class="text-amber-600 hover:underline font-semibold">Sign in</a>
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

