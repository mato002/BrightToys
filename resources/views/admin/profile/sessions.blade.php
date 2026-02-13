@extends('layouts.admin')

@section('page_title', 'Active Sessions')

@section('content')
    {{-- Success/Error messages --}}
    @if(session('success'))
        <div class="mb-4 sm:mb-6 text-xs sm:text-sm text-emerald-700 bg-emerald-50 border border-emerald-200 px-3 sm:px-4 py-2.5 sm:py-3 rounded-xl shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 sm:mb-6 text-xs sm:text-sm text-red-700 bg-red-50 border border-red-200 px-3 sm:px-4 py-2.5 sm:py-3 rounded-xl shadow-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- Page header --}}
    <div class="mb-6 flex flex-col gap-4">
        <div>
            <h1 class="text-lg sm:text-xl md:text-2xl font-semibold text-slate-900 tracking-tight">Active Sessions</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-2xl">
                Manage your active login sessions across different devices and browsers. You can revoke any session except your current one.
            </p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2">
            <a href="{{ route('admin.profile') }}"
               class="inline-flex items-center justify-center gap-1 rounded-full border border-slate-200 bg-white px-3 py-2 text-xs font-medium text-slate-700 hover:border-emerald-400 hover:bg-emerald-50 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M19 12H5M12 19l-7-7 7-7" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span>Back to profile</span>
            </a>
            @if($sessions->count() > 1)
                <form action="{{ route('admin.profile.sessions.revoke-all') }}" method="POST" onsubmit="return confirm('Are you sure you want to revoke all other sessions? You will be logged out from all other devices.');" class="w-full sm:w-auto">
                    @csrf
                    <button type="submit"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-1 rounded-full border border-red-200 bg-white px-3 py-2 text-xs font-medium text-red-700 hover:border-red-400 hover:bg-red-50 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M18 6L6 18M6 6l12 12"/>
                        </svg>
                        <span>Revoke All Others</span>
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- Sessions List --}}
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        @if($sessions->isEmpty())
            <div class="p-8 sm:p-12 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 sm:h-12 sm:w-12 text-slate-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                <p class="text-slate-500 text-xs sm:text-sm">No active sessions found.</p>
            </div>
        @else
            <div class="divide-y divide-slate-100">
                @foreach($sessions as $session)
                    <div class="p-4 sm:p-6 hover:bg-slate-50 transition-colors {{ $session['is_current'] ? 'bg-emerald-50/50' : '' }}">
                        <div class="flex flex-col gap-4">
                            <div class="flex items-start gap-3 sm:gap-4 flex-1">
                                {{-- Device Icon --}}
                                <div class="flex-shrink-0">
                                    @if($session['device']['device'] === 'Mobile')
                                        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg bg-blue-100 flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    @elseif($session['device']['device'] === 'Tablet')
                                        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg bg-purple-100 flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    @else
                                        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg bg-slate-100 flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    @endif
                                </div>

                                {{-- Session Info --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-col sm:flex-row sm:items-center gap-2 mb-1.5">
                                        <h3 class="text-xs sm:text-sm font-semibold text-slate-900 break-words">
                                            {{ $session['device']['browser'] }} on {{ $session['device']['platform'] }}
                                        </h3>
                                        @if($session['is_current'])
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-700 border border-emerald-200 w-fit">
                                                Current Session
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-slate-600 mb-1.5 break-words">
                                        <span class="inline-block">{{ $session['device']['device'] }}</span>
                                        <span class="mx-1.5">•</span>
                                        <span class="inline-block break-all">{{ $session['ip_address'] }}</span>
                                    </p>
                                    <p class="text-[11px] text-slate-500 leading-relaxed">
                                        <span class="inline-block">Last active: {{ \Carbon\Carbon::createFromTimestamp($session['last_activity'])->diffForHumans() }}</span>
                                        <span class="hidden sm:inline mx-1">•</span>
                                        <br class="sm:hidden">
                                        <span class="inline-block sm:ml-0">{{ \Carbon\Carbon::createFromTimestamp($session['last_activity'])->format('M d, Y g:i A') }}</span>
                                    </p>
                                    @if($session['device']['full'])
                                        <p class="text-[10px] text-slate-400 mt-1.5 font-mono break-all" title="{{ $session['device']['full'] }}">
                                            {{ Str::limit($session['device']['full'], 60) }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="flex items-center justify-end sm:justify-start pt-2 border-t border-slate-100 sm:border-t-0 sm:pt-0">
                                @if(!$session['is_current'])
                                    <form action="{{ route('admin.profile.sessions.revoke', $session['id']) }}" method="POST" onsubmit="return confirm('Are you sure you want to revoke this session?');" class="w-full sm:w-auto">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-4 py-2 text-xs font-medium text-red-700 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 hover:border-red-300 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M18 6L6 18M6 6l12 12"/>
                                            </svg>
                                            Revoke
                                        </button>
                                    </form>
                                @else
                                    <span class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-4 py-2 text-xs font-medium text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M20 6L9 17l-5-5"/>
                                        </svg>
                                        Active
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Security Notice --}}
    <div class="mt-6 bg-amber-50 border border-amber-200 rounded-xl p-4 sm:p-5">
        <div class="flex gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div class="flex-1 min-w-0">
                <h4 class="text-sm font-semibold text-amber-900 mb-2">Security Tips</h4>
                <ul class="text-xs text-amber-800 space-y-1.5 list-disc list-inside">
                    <li>If you notice any suspicious sessions, revoke them immediately</li>
                    <li>Always log out from shared or public computers</li>
                    <li>Use strong, unique passwords for your account</li>
                    <li>Consider enabling two-factor authentication if available</li>
                </ul>
            </div>
        </div>
    </div>
@endsection
