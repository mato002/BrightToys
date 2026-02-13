@extends('layouts.app')

@section('title', 'Access Denied - Otto Investments')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-pink-100 via-amber-50 to-sky-100 py-12 px-4">
    <div class="max-w-md w-full text-center">
        <div class="mb-8">
            <h1 class="text-9xl font-bold text-orange-500 mb-4">403</h1>
            <h2 class="text-3xl font-bold text-slate-900 mb-4">Access Denied</h2>
            <p class="text-slate-600 mb-8">
                You don't have permission to access this page.
            </p>
        </div>
        
        <div class="space-y-4">
            <a href="{{ route('home') }}" 
               class="inline-block bg-amber-500 hover:bg-amber-600 text-white font-semibold px-8 py-3 rounded-lg shadow-sm shadow-amber-500/30 transition-colors">
                Go to Homepage
            </a>
            @auth
                <div>
                    <a href="{{ route('account.overview') }}" 
                       class="text-amber-600 hover:text-amber-700 font-medium">
                        Go to My Account →
                    </a>
                </div>
            @else
                <div>
                    <a href="{{ route('login') }}" 
                       class="text-amber-600 hover:text-amber-700 font-medium">
                        Login →
                    </a>
                </div>
            @endauth
        </div>
    </div>
</div>
@endsection
