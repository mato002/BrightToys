@extends('layouts.account')

@section('title', 'Profile Details')
@section('page_title', 'Profile Details')

@section('breadcrumbs')
    <span class="breadcrumb-separator">/</span>
    <a href="{{ route('account.profile') }}" class="text-slate-600 hover:text-amber-600 transition-colors">Profile</a>
@endsection

@section('content')
    <div class="w-full space-y-6">
        {{-- Profile Header Card --}}
        <div class="bg-gradient-to-br from-amber-500 via-amber-600 to-orange-600 rounded-2xl shadow-lg overflow-hidden">
            <div class="p-8 text-white">
                <div class="flex items-center gap-6">
                    {{-- Avatar --}}
                    <div class="relative">
                        <div class="w-24 h-24 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center border-4 border-white/30 shadow-xl">
                            <i class="fas fa-user text-4xl text-white"></i>
                        </div>
                        <div class="absolute bottom-0 right-0 w-8 h-8 bg-white rounded-full flex items-center justify-center shadow-lg border-2 border-amber-500">
                            <i class="fas fa-camera text-xs text-amber-600"></i>
                        </div>
                    </div>
                    {{-- User Info --}}
                    <div class="flex-1">
                        <h2 class="text-3xl font-bold mb-1">{{ $user->name }}</h2>
                        <p class="text-amber-100 flex items-center gap-2">
                            <i class="fas fa-envelope text-sm"></i>
                            {{ $user->email }}
                        </p>
                        @if($user->phone)
                            <p class="text-amber-100 flex items-center gap-2 mt-1">
                                <i class="fas fa-phone text-sm"></i>
                                {{ $user->phone }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Personal Information Card --}}
        <div class="bg-white rounded-2xl shadow-lg border border-slate-100 overflow-hidden">
            {{-- Card Header --}}
            <div class="bg-gradient-to-r from-slate-50 to-slate-100 px-8 py-6 border-b border-slate-200">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-amber-500 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user-edit text-white"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-slate-900">Personal Information</h2>
                        <p class="text-sm text-slate-600 mt-0.5">Update your account information and password</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('account.profile.update') }}" method="POST" class="p-8 space-y-6">
                @csrf
                @method('PUT')

                {{-- Success Message --}}
                @if(session('success'))
                    <div class="bg-emerald-50 border-l-4 border-emerald-500 rounded-lg p-4 flex items-center gap-3">
                        <i class="fas fa-check-circle text-emerald-500 text-xl"></i>
                        <p class="text-emerald-800 font-medium">{{ session('success') }}</p>
                    </div>
                @endif

                {{-- Form Grid --}}
                <div class="grid md:grid-cols-2 gap-6">
                    {{-- Full Name --}}
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-semibold text-slate-700 mb-2.5 flex items-center gap-2">
                            <i class="fas fa-user text-amber-500 text-xs"></i>
                            Full Name <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-user text-slate-400"></i>
                            </div>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $user->name) }}"
                                   required
                                   class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border-2 border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 focus:bg-white transition-all @error('name') border-red-300 bg-red-50 @enderror">
                        </div>
                        @error('name')
                            <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                <i class="fas fa-exclamation-circle text-xs"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Email Address --}}
                    <div class="md:col-span-2">
                        <label for="email" class="block text-sm font-semibold text-slate-700 mb-2.5 flex items-center gap-2">
                            <i class="fas fa-envelope text-amber-500 text-xs"></i>
                            Email Address <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-slate-400"></i>
                            </div>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email', $user->email) }}"
                                   required
                                   class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border-2 border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 focus:bg-white transition-all @error('email') border-red-300 bg-red-50 @enderror">
                        </div>
                        @error('email')
                            <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                <i class="fas fa-exclamation-circle text-xs"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Phone Number --}}
                    <div class="md:col-span-2">
                        <label for="phone" class="block text-sm font-semibold text-slate-700 mb-2.5 flex items-center gap-2">
                            <i class="fas fa-phone text-amber-500 text-xs"></i>
                            Phone Number
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-phone text-slate-400"></i>
                            </div>
                            <input type="text" 
                                   id="phone" 
                                   name="phone" 
                                   value="{{ old('phone', $user->phone) }}"
                                   placeholder="Enter your phone number"
                                   class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border-2 border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 focus:bg-white transition-all @error('phone') border-red-300 bg-red-50 @enderror">
                        </div>
                        @error('phone')
                            <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                <i class="fas fa-exclamation-circle text-xs"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                {{-- Password Section Divider --}}
                <div class="relative my-8">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-slate-200"></div>
                    </div>
                    <div class="relative flex justify-center">
                        <div class="bg-white px-4">
                            <div class="flex items-center gap-2 text-slate-500">
                                <i class="fas fa-lock text-sm"></i>
                                <span class="text-sm font-medium">Change Password</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Password Section --}}
                <div id="password" class="bg-slate-50 rounded-xl p-6 border border-slate-200 scroll-mt-4">
                    <p class="text-sm text-slate-600 mb-6 flex items-center gap-2">
                        <i class="fas fa-info-circle text-amber-500"></i>
                        Leave blank if you don't want to change your password
                    </p>

                    <div class="grid md:grid-cols-2 gap-6">
                        {{-- New Password --}}
                        <div>
                            <label for="password" class="block text-sm font-semibold text-slate-700 mb-2.5 flex items-center gap-2">
                                <i class="fas fa-key text-amber-500 text-xs"></i>
                                New Password
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-slate-400"></i>
                                </div>
                                <input type="password" 
                                       id="password" 
                                       name="password" 
                                       placeholder="Enter new password"
                                       class="w-full pl-11 pr-4 py-3.5 bg-white border-2 border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all @error('password') border-red-300 bg-red-50 @enderror">
                            </div>
                            @error('password')
                                <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle text-xs"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                            <p class="mt-2 text-xs text-slate-500 flex items-center gap-1">
                                <i class="fas fa-shield-alt text-xs"></i>
                                Must be at least 8 characters long
                            </p>
                        </div>

                        {{-- Confirm Password --}}
                        <div>
                            <label for="password_confirmation" class="block text-sm font-semibold text-slate-700 mb-2.5 flex items-center gap-2">
                                <i class="fas fa-key text-amber-500 text-xs"></i>
                                Confirm New Password
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-slate-400"></i>
                                </div>
                                <input type="password" 
                                       id="password_confirmation" 
                                       name="password_confirmation" 
                                       placeholder="Confirm new password"
                                       class="w-full pl-11 pr-4 py-3.5 bg-white border-2 border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center justify-end gap-4 pt-6 border-t border-slate-200">
                    <a href="{{ route('account.overview') }}" 
                       class="px-6 py-3 text-sm font-semibold text-slate-700 bg-slate-100 rounded-xl hover:bg-slate-200 transition-all flex items-center gap-2 shadow-sm">
                        <i class="fas fa-times"></i>
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-8 py-3 text-sm font-semibold text-white bg-gradient-to-r from-amber-500 to-amber-600 rounded-xl hover:from-amber-600 hover:to-amber-700 transition-all shadow-lg shadow-amber-500/30 flex items-center gap-2 transform hover:scale-105">
                        <i class="fas fa-save"></i>
                        Update Profile
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
