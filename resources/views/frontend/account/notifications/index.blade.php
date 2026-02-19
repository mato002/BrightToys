@extends('layouts.account')

@section('title', 'Notifications')
@section('page_title', 'Notifications')

@section('content')
    <div class="space-y-6">
        {{-- Page Header --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-slate-900 tracking-tight">Notifications</h1>
                <p class="text-xs md:text-sm text-slate-500 mt-1">View important updates about your orders and account</p>
            </div>
            @php
                $unreadCount = $notifications->whereNull('read_at')->count();
            @endphp
            @if($unreadCount > 0)
                <form action="{{ route('account.notifications.read-all') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" 
                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-amber-700 bg-amber-50 border-2 border-amber-200 rounded-lg hover:bg-amber-100 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 6L9 17l-5-5"/>
                        </svg>
                        Mark All as Read
                    </button>
                </form>
            @endif
        </div>

        {{-- Filter tabs: Order updates, Delivery, Promotions, All --}}
        <div class="flex flex-wrap gap-2 mb-4">
            <a href="{{ route('account.notifications', ['filter' => 'all']) }}" class="px-4 py-2 rounded-lg text-sm font-semibold transition-colors {{ request('filter', 'all') === 'all' ? 'bg-amber-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">All</a>
            <a href="{{ route('account.notifications', ['filter' => 'order_updates']) }}" class="px-4 py-2 rounded-lg text-sm font-semibold transition-colors {{ request('filter') === 'order_updates' ? 'bg-amber-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">Order updates</a>
            <a href="{{ route('account.notifications', ['filter' => 'delivery']) }}" class="px-4 py-2 rounded-lg text-sm font-semibold transition-colors {{ request('filter') === 'delivery' ? 'bg-amber-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">Delivery</a>
            <a href="{{ route('account.notifications', ['filter' => 'promotion']) }}" class="px-4 py-2 rounded-lg text-sm font-semibold transition-colors {{ request('filter') === 'promotion' ? 'bg-amber-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">Promotions</a>
        </div>

        {{-- Notifications List --}}
        <div class="bg-white border-2 border-slate-200 rounded-xl shadow-sm overflow-hidden">
            @forelse($notifications as $notification)
                <div class="border-b border-slate-200 last:border-b-0 {{ !$notification->read_at ? 'bg-amber-50/30' : 'bg-white' }} hover:bg-slate-50 transition-colors">
                    <div class="p-5 md:p-6">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 mt-1">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center
                                    @if(str_contains($notification->type, 'order')) bg-blue-100 text-blue-600
                                    @elseif(str_contains($notification->type, 'payment')) bg-emerald-100 text-emerald-600
                                    @elseif(str_contains($notification->type, 'shipping')) bg-purple-100 text-purple-600
                                    @else bg-amber-100 text-amber-600
                                    @endif">
                                    @if(str_contains($notification->type, 'order'))
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                            <circle cx="8.5" cy="7" r="4"/>
                                            <path d="M20 8v6M23 11h-6"/>
                                        </svg>
                                    @elseif(str_contains($notification->type, 'payment'))
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <line x1="12" y1="1" x2="12" y2="23"/>
                                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                                        </svg>
                                    @elseif(str_contains($notification->type, 'shipping'))
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M1 3h15v13H1zM16 8h4l3 3v5h-7V8z"/>
                                            <circle cx="5.5" cy="18.5" r="2.5"/>
                                            <circle cx="18.5" cy="18.5" r="2.5"/>
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                                            <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                                        </svg>
                                    @endif
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <h3 class="text-base font-bold text-slate-900">{{ $notification->title }}</h3>
                                            @if(!$notification->read_at)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-100 text-amber-700 border border-amber-200">
                                                    New
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-sm text-slate-700 mb-2">{{ $notification->message }}</p>
                                        <div class="flex items-center gap-4 text-xs text-slate-500">
                                            <span class="flex items-center gap-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <circle cx="12" cy="12" r="10"/>
                                                    <polyline points="12 6 12 12 16 14"/>
                                                </svg>
                                                {{ $notification->created_at->format('M d, Y') }}
                                            </span>
                                            <span>{{ $notification->created_at->format('g:i A') }}</span>
                                            <span class="text-slate-400">•</span>
                                            <span class="capitalize">{{ str_replace('_', ' ', $notification->type) }}</span>
                                        </div>
                                    </div>
                                    @if(!$notification->read_at)
                                        <form action="{{ route('account.notifications.read', $notification->id) }}" method="POST" class="flex-shrink-0">
                                            @csrf
                                            <button type="submit" 
                                                    class="p-2 text-slate-400 hover:text-amber-600 transition-colors"
                                                    title="Mark as read">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M20 6L9 17l-5-5"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center">
                    <div class="mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 md:h-20 md:w-20 text-slate-300 mx-auto" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">No notifications yet</h3>
                    <p class="text-sm text-slate-500 max-w-md mx-auto">
                        Updates about your orders, payments, and account will appear here.
                    </p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($notifications->hasPages())
            <div class="mt-6">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
@endsection
