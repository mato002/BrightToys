@extends('layouts.account')

@section('title', 'Notifications')
@section('page_title', 'Notifications')

@section('content')
    <div class="w-full space-y-6">
        {{-- Header --}}
        <div class="bg-gradient-to-r from-amber-500 to-orange-600 rounded-2xl shadow-lg overflow-hidden">
            <div class="p-6 text-white">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center border-2 border-white/30">
                        <i class="fas fa-bell text-2xl text-white"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold">My Notifications</h2>
                        <p class="text-amber-100 text-sm mt-1">Stay updated with your account activity</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Notifications List --}}
        @if($notifications->count() > 0)
            <div class="space-y-4">
                @foreach($notifications as $notification)
                    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-shadow {{ !$notification->read_at ? 'border-l-4 border-l-amber-500 bg-amber-50/30' : '' }}">
                        <div class="p-6">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        @if(!$notification->read_at)
                                            <span class="inline-flex h-2 w-2 rounded-full bg-amber-500"></span>
                                        @endif
                                        <h3 class="text-lg font-semibold text-slate-900">{{ $notification->title }}</h3>
                                    </div>
                                    <p class="text-sm text-slate-700 mb-3">{{ $notification->message }}</p>
                                    <div class="flex items-center gap-4 text-xs text-slate-500">
                                        <span class="flex items-center gap-1">
                                            <i class="fas fa-clock"></i>
                                            {{ $notification->created_at->diffForHumans() }}
                                        </span>
                                        @if($notification->type)
                                            <span class="inline-flex items-center px-2 py-1 rounded-md bg-slate-100 text-slate-600">
                                                {{ ucfirst(str_replace('_', ' ', $notification->type)) }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-6">
                {{ $notifications->links() }}
            </div>
        @else
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-12 text-center">
                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-bell-slash text-2xl text-slate-400"></i>
                </div>
                <h3 class="text-lg font-semibold text-slate-900 mb-2">No notifications yet</h3>
                <p class="text-sm text-slate-600">You'll see your notifications here when they arrive.</p>
            </div>
        @endif
    </div>
@endsection
