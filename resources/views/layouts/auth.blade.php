<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Account')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-b from-slate-50 via-slate-100 to-slate-200 text-gray-900 flex items-center justify-center">
    <div class="w-full max-w-5xl mx-auto px-4 py-8">
        <div class="grid gap-10 lg:grid-cols-[1.2fr,1fr] items-center">
            <div class="hidden lg:block">
                <div class="inline-flex items-center gap-2 mb-6">
                    <div class="h-9 w-9 rounded-full bg-amber-500 flex items-center justify-center text-white text-lg font-semibold">
                        B
                    </div>
                    <span class="text-xl font-semibold tracking-wide">BrightToys</span>
                </div>

                <h2 class="text-3xl font-semibold text-slate-900 mb-3">
                    Your style, delivered.
                </h2>
                <p class="text-sm text-slate-600 mb-6 max-w-md">
                    Sign in to track your orders, manage your account and pick up where you left off.
                </p>

                <div class="space-y-3 text-sm text-slate-700">
                    <div class="flex items-start gap-3">
                        <span class="mt-0.5 h-5 w-5 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center text-xs font-semibold">1</span>
                        <div>
                            <p class="font-medium">Fast, secure checkout</p>
                            <p class="text-xs text-slate-500">Save your details and check out in just a few clicks.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="mt-0.5 h-5 w-5 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center text-xs font-semibold">2</span>
                        <div>
                            <p class="font-medium">Order tracking</p>
                            <p class="text-xs text-slate-500">Follow every step of your delivery from your account.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="mt-0.5 h-5 w-5 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center text-xs font-semibold">3</span>
                        <div>
                            <p class="font-medium">Personalised experience</p>
                            <p class="text-xs text-slate-500">Save favourites and get recommendations tailored to you.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="w-full max-w-md mx-auto">
                <div class="mb-6 text-center lg:hidden">
                    <a href="{{ route('home') }}" class="inline-flex items-center justify-center text-2xl font-bold tracking-wide">
                        BrightToys
                    </a>
                </div>

                <div class="bg-white/90 backdrop-blur border border-slate-200 shadow-[0_18px_60px_rgba(15,23,42,0.08)] rounded-2xl p-6 sm:p-8">
                    @yield('content')
                </div>

                <p class="mt-6 text-center text-[11px] text-slate-500">
                    © {{ date('Y') }} BrightToys. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</body>
</html>

