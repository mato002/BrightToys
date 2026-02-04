@extends('layouts.app')

@section('title', 'About Us - BrightToys')

@section('content')
    {{-- Hero section --}}
    <section class="relative bg-gradient-to-br from-pink-100 via-amber-50 to-sky-100 overflow-hidden py-12">
        {{-- Background image with overlay - Happy kids with toys --}}
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-20" 
             style="background-image: url('https://images.pexels.com/photos/159711/books-bookstore-book-reading-159711.jpeg?auto=compress&cs=tinysrgb&w=1920');">
        </div>
        <div class="absolute inset-0 bg-gradient-to-br from-pink-100/80 via-amber-50/80 to-sky-100/80"></div>
        
        <div class="container mx-auto px-4 lg:px-8 text-center relative z-10">
            <h1 class="text-3xl md:text-4xl font-bold text-slate-900 mb-3">Welcome to BrightToys</h1>
            <p class="text-base md:text-lg text-slate-600 max-w-2xl mx-auto">
                Kenya's trusted destination for safe, fun, and educational toys that spark imagination and support every child's growth journey.
            </p>
        </div>
    </section>

    {{-- Main content --}}
    <div class="container mx-auto px-4 lg:px-8 py-10 max-w-5xl">
        {{-- Our Story --}}
        <section class="mb-12">
            <div class="grid md:grid-cols-2 gap-8 items-center">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900 mb-4">Our Story</h2>
                    <div class="space-y-4 text-sm md:text-base text-slate-700 leading-relaxed">
                        <p>
                            BrightToys was born from a simple belief: every child deserves access to high-quality toys that are not just fun, but also safe, age-appropriate, and designed to support their development.
                        </p>
                        <p>
                            Founded in Kenya, we understand the unique needs of families across the country. We carefully curate our collection, working with trusted brands and suppliers to bring you toys that meet international safety standards while celebrating the joy of play.
                        </p>
                        <p>
                            From baby's first sensory toys to complex building sets for older kids, every product in our store is hand-picked with care, ensuring it meets our strict standards for quality, safety, and educational value.
                        </p>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-pink-200 to-amber-200 rounded-2xl h-64 md:h-80 flex items-center justify-center shadow-lg">
                    <div class="text-center text-slate-600">
                        <i class="fas fa-bullseye text-5xl mb-2 text-amber-600"></i>
                        <p class="text-sm font-medium">Our Mission</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- Our Values --}}
        <section class="mb-12">
            <h2 class="text-2xl font-bold text-slate-900 mb-6 text-center">What We Stand For</h2>
            <div class="grid md:grid-cols-3 gap-6">
                <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow">
                    <div class="mb-3"><i class="fas fa-shield-alt text-4xl text-amber-600"></i></div>
                    <h3 class="font-bold text-slate-900 mb-2">Safety First</h3>
                    <p class="text-sm text-slate-600">
                        All toys meet or exceed international safety standards. We test for age-appropriateness, material quality, and choking hazards.
                    </p>
                </div>
                <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow">
                    <div class="mb-3"><i class="fas fa-graduation-cap text-4xl text-pink-600"></i></div>
                    <h3 class="font-bold text-slate-900 mb-2">Learning Through Play</h3>
                    <p class="text-sm text-slate-600">
                        Every toy is chosen to support development: fine motor skills, problem-solving, creativity, and social interaction.
                    </p>
                </div>
                <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow">
                    <div class="mb-3"><i class="fas fa-heart text-4xl text-red-500"></i></div>
                    <h3 class="font-bold text-slate-900 mb-2">Family Trust</h3>
                    <p class="text-sm text-slate-600">
                        We're parents too. We understand the importance of finding toys that are both engaging for kids and trusted by parents.
                    </p>
                </div>
            </div>
        </section>

        {{-- Why Choose Us --}}
        <section class="mb-12 bg-gradient-to-br from-sky-50 to-pink-50 rounded-2xl p-8 md:p-10">
            <h2 class="text-2xl font-bold text-slate-900 mb-6 text-center">Why Shop With Us?</h2>
            <div class="grid md:grid-cols-2 gap-6">
                <div class="flex gap-4">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-amber-500 rounded-full flex items-center justify-center text-white">
                            <i class="fas fa-check text-lg"></i>
                        </div>
                    </div>
                    <div>
                        <h3 class="font-semibold text-slate-900 mb-1">Curated Selection</h3>
                        <p class="text-sm text-slate-600">We don't stock everything – only the best toys that we'd give to our own children.</p>
                    </div>
                </div>
                <div class="flex gap-4">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-pink-500 rounded-full flex items-center justify-center text-white">
                            <i class="fas fa-check text-lg"></i>
                        </div>
                    </div>
                    <div>
                        <h3 class="font-semibold text-slate-900 mb-1">Age-Appropriate Guidance</h3>
                        <p class="text-sm text-slate-600">Clear age recommendations and detailed product descriptions help you choose the perfect toy.</p>
                    </div>
                </div>
                <div class="flex gap-4">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-sky-500 rounded-full flex items-center justify-center text-white">
                            <i class="fas fa-check text-lg"></i>
                        </div>
                    </div>
                    <div>
                        <h3 class="font-semibold text-slate-900 mb-1">Fast & Reliable Delivery</h3>
                        <p class="text-sm text-slate-600">Quick shipping across Kenya with secure packaging to keep toys safe in transit.</p>
                    </div>
                </div>
                <div class="flex gap-4">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-emerald-500 rounded-full flex items-center justify-center text-white">
                            <i class="fas fa-check text-lg"></i>
                        </div>
                    </div>
                    <div>
                        <h3 class="font-semibold text-slate-900 mb-1">Easy Returns</h3>
                        <p class="text-sm text-slate-600">Not satisfied? We offer hassle-free returns within 14 days for unopened items.</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- Contact CTA --}}
        <section class="text-center bg-white border border-slate-200 rounded-2xl p-8 shadow-sm">
            <h2 class="text-2xl font-bold text-slate-900 mb-3">Have Questions?</h2>
            <p class="text-sm text-slate-600 mb-6 max-w-xl mx-auto">
                We're here to help! Whether you need advice on choosing the right toy or have questions about your order, our friendly team is ready to assist.
            </p>
            <a href="{{ route('pages.contact') }}" 
               class="inline-flex items-center justify-center bg-amber-500 hover:bg-amber-600 text-white font-semibold px-6 py-3 rounded-lg shadow-sm shadow-amber-500/30 transition-colors">
                Get in Touch
            </a>
        </section>
    </div>
@endsection
