<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Account')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 flex items-center justify-center">
    <div class="w-full max-w-md mx-auto px-4 py-10">
        <div class="flex flex-col items-center mb-6">
            <div class="inline-flex items-center gap-2 mb-2">
                <div class="h-9 w-9 rounded-full bg-amber-500 flex items-center justify-center text-white text-lg font-semibold">
                    OI
                </div>
                <span class="text-xl font-semibold tracking-wide">Otto Investments</span>
            </div>
            <p class="text-[11px] text-slate-500 text-center max-w-sm">
                Sign in or create an account to access your workspace, manage projects and view your activity.
            </p>
        </div>

        <div class="bg-white/95 backdrop-blur border border-slate-200 shadow-[0_18px_60px_rgba(15,23,42,0.08)] rounded-2xl p-6 sm:p-8">
            @yield('content')
        </div>

        <p class="mt-6 text-center text-[11px] text-slate-500">
            © {{ date('Y') }} Otto Investments. All rights reserved.
        </p>
    </div>
</body>
</html>

