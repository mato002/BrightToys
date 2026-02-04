@extends('layouts.app')

@section('title', 'Contact Us - BrightToys')

@section('content')
    {{-- Hero section --}}
    <section class="relative bg-gradient-to-br from-pink-100 via-amber-50 to-sky-100 overflow-hidden py-12">
        {{-- Background image with overlay - Colorful toy collection --}}
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-20" 
             style="background-image: url('https://images.pexels.com/photos/160715/pexels-photo-160715.jpeg?auto=compress&cs=tinysrgb&w=1920');">
        </div>
        <div class="absolute inset-0 bg-gradient-to-br from-pink-100/80 via-amber-50/80 to-sky-100/80"></div>
        
        <div class="container mx-auto px-4 lg:px-8 text-center relative z-10">
            <h1 class="text-3xl md:text-4xl font-bold text-slate-900 mb-3">Get in Touch</h1>
            <p class="text-base md:text-lg text-slate-600 max-w-2xl mx-auto">
                Have a question about your order, need help choosing a toy, or want to partner with us? We'd love to hear from you!
            </p>
        </div>
    </section>

    <div class="container mx-auto px-4 lg:px-8 py-10 max-w-6xl">
        <div class="grid md:grid-cols-2 gap-8">
            {{-- Contact Information --}}
            <div class="space-y-6">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900 mb-4">Contact Information</h2>
                    <p class="text-sm text-slate-600 mb-6">
                        Reach out to us through any of these channels. We typically respond within 24 hours.
                    </p>
                </div>

                <div class="space-y-4">
                    <div class="flex gap-4 p-4 bg-white border border-slate-200 rounded-xl shadow-sm">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-amber-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-phone text-xl text-amber-600"></i>
                            </div>
                        </div>
                        <div>
                            <h3 class="font-semibold text-slate-900 mb-1">Phone</h3>
                            <p class="text-sm text-slate-600">(+254) 747900900</p>
                            <p class="text-xs text-slate-500 mt-1">Mon - Sat: 9:00 AM - 6:00 PM</p>
                        </div>
                    </div>

                    <div class="flex gap-4 p-4 bg-white border border-slate-200 rounded-xl shadow-sm">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-pink-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-envelope text-xl text-pink-600"></i>
                            </div>
                        </div>
                        <div>
                            <h3 class="font-semibold text-slate-900 mb-1">Email</h3>
                            <p class="text-sm text-slate-600">hello@brighttoys.com</p>
                            <p class="text-xs text-slate-500 mt-1">We'll respond within 24 hours</p>
                        </div>
                    </div>

                    <div class="flex gap-4 p-4 bg-white border border-slate-200 rounded-xl shadow-sm">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-sky-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-map-marker-alt text-xl text-sky-600"></i>
                            </div>
                        </div>
                        <div>
                            <h3 class="font-semibold text-slate-900 mb-1">Location</h3>
                            <p class="text-sm text-slate-600">Nairobi, Kenya</p>
                            <p class="text-xs text-slate-500 mt-1">Serving customers nationwide</p>
                        </div>
                    </div>
                </div>

                {{-- Social Media --}}
                <div class="pt-4">
                    <h3 class="font-semibold text-slate-900 mb-3">Follow Us</h3>
                    <div class="flex gap-3">
                        <a href="https://facebook.com" target="_blank" rel="noopener" 
                           class="w-10 h-10 bg-blue-500 hover:bg-blue-600 rounded-full flex items-center justify-center text-white transition-colors">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://instagram.com" target="_blank" rel="noopener"
                           class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 rounded-full flex items-center justify-center text-white transition-colors">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="https://twitter.com" target="_blank" rel="noopener"
                           class="w-10 h-10 bg-sky-400 hover:bg-sky-500 rounded-full flex items-center justify-center text-white transition-colors">
                            <i class="fab fa-twitter"></i>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Contact Form --}}
            <div>
                <div class="bg-white border border-slate-200 rounded-2xl p-6 md:p-8 shadow-sm">
                    <h2 class="text-xl font-bold text-slate-900 mb-4">Send us a Message</h2>

                    @if (session('status'))
                        <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form class="space-y-4" method="POST" action="{{ route('pages.contact.submit') }}">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold mb-1.5 text-slate-700">Your Name *</label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                   class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-400 @error('name') border-red-400 @enderror">
                            @error('name')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold mb-1.5 text-slate-700">Email Address *</label>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                   class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-400 @error('email') border-red-400 @enderror">
                            @error('email')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold mb-1.5 text-slate-700">Subject</label>
                            <input type="text" name="subject" value="{{ old('subject') }}"
                                   placeholder="e.g., Order inquiry, Product question"
                                   class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-400 @error('subject') border-red-400 @enderror">
                            @error('subject')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold mb-1.5 text-slate-700">Message *</label>
                            <textarea rows="5" name="message" required
                                      placeholder="Tell us how we can help..."
                                      class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-400 @error('message') border-red-400 @enderror">{{ old('message') }}</textarea>
                            @error('message')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit"
                                class="w-full bg-amber-500 hover:bg-amber-600 text-white font-semibold px-6 py-3 rounded-lg shadow-sm shadow-amber-500/30 transition-colors">
                            Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
