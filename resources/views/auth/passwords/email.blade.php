@extends('layouts.auth')

@section('title', 'Forgot Password')

@section('content')
    <div class="bg-white border rounded-lg p-6 shadow-sm">
        <h1 class="text-2xl font-semibold mb-6 text-center">Forgot your password?</h1>

        <p class="text-xs text-gray-600 mb-4 text-center">
            Enter the email address associated with your account and we'll send you a password reset link.
        </p>

        @if (session('status'))
            <div class="mb-4 text-sm text-green-700 bg-green-100 border border-green-200 px-4 py-2 rounded">
                {{ session('status') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 text-sm text-red-700 bg-red-100 border border-red-200 px-4 py-2 rounded">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('password.email') }}" method="POST" class="space-y-4 text-sm">
            @csrf
            <div>
                <label for="email" class="block text-xs font-semibold mb-1">Email address</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autocomplete="email"
                    autofocus
                    class="border rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                    placeholder="you@example.com"
                >
            </div>

            <button
                type="submit"
                class="w-full bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold px-4 py-2 rounded transition-colors"
            >
                Send reset link
            </button>
        </form>

        <p class="text-xs text-gray-600 mt-4 text-center">
            Remembered your password?
            <a href="{{ route('login') }}" class="text-amber-600 hover:underline">Back to login</a>
        </p>
    </div>
@endsection

