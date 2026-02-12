@extends('layouts.app')

@section('title', 'Page Not Found - Otto Investments')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-pink-100 via-amber-50 to-sky-100 py-12 px-4">
    <div class="max-w-md w-full text-center">
        <div class="mb-8">
            <h1 class="text-9xl font-bold text-amber-500 mb-4">404</h1>
            <h2 class="text-3xl font-bold text-slate-900 mb-4">Oops! Page Not Found</h2>
            <p class="text-slate-600 mb-8">
                The page you're looking for doesn't exist or has been moved.
            </p>
        </div>
        
        <div class="space-y-4">
            <a href="{{ route('home') }}" 
               class="inline-block bg-amber-500 hover:bg-amber-600 text-white font-semibold px-8 py-3 rounded-lg shadow-sm shadow-amber-500/30 transition-colors">
                Go to Homepage
            </a>
            <div>
                <a href="{{ route('shop.index') }}" 
                   class="text-amber-600 hover:text-amber-700 font-medium">
                    Browse Our Toys →
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
