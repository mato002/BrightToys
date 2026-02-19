@extends('layouts.account')

@section('title', 'Contact Us')
@section('page_title', 'Contact Us')

@section('breadcrumbs')
    <span class="breadcrumb-separator">/</span>
    <span class="text-slate-700">Contact</span>
@endsection

@section('content')
    <div class="max-w-2xl">
        <div class="bg-white border-2 border-slate-200 rounded-xl p-6 shadow-sm">
            <h2 class="text-xl font-bold text-slate-900 mb-4">Send us a Message</h2>
            <p class="text-sm text-slate-600 mb-6">Have a question about your order, need help, or want to report an issue? We'll get back to you shortly.</p>

            @if (session('status'))
                <div class="mb-4 rounded-lg border-2 border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            <form class="space-y-4" method="POST" action="{{ route('pages.contact.submit') }}">
                @csrf
                <div>
                    <label class="block text-xs font-semibold mb-1.5 text-slate-700">Your Name *</label>
                    <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}" required
                           class="w-full border-2 border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 @error('name') border-red-400 @enderror">
                    @error('name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5 text-slate-700">Email Address *</label>
                    <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" required
                           class="w-full border-2 border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 @error('email') border-red-400 @enderror">
                    @error('email')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5 text-slate-700">Subject</label>
                    <input type="text" name="subject" value="{{ old('subject', request('subject')) }}"
                           placeholder="e.g., Order inquiry, Returns, Refund"
                           class="w-full border-2 border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5 text-slate-700">Message *</label>
                    <textarea rows="5" name="message" required placeholder="Tell us how we can help..."
                              class="w-full border-2 border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">{{ old('message') }}</textarea>
                    @error('message')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <button type="submit" class="w-full bg-amber-600 hover:bg-amber-700 text-white font-semibold px-6 py-3 rounded-lg">
                    Send Message
                </button>
            </form>
        </div>
        <p class="mt-4 text-sm text-slate-500">You can also <a href="{{ route('account.support.create') }}" class="text-amber-600 hover:underline font-medium">open a support ticket</a> for tracked requests and replies.</p>
    </div>
@endsection
