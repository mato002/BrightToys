@extends('layouts.app')

@section('title', $product->name . ' - Otto Investments')

@section('meta_description')
{{ Str::limit($product->description ?? 'Product available at Otto Investments.', 160) }}
@endsection

@section('meta_keywords')
{{ $product->name }}, {{ $product->category->name ?? 'toys' }}, kids toys, Kenya
@endsection

@section('og_title')
{{ $product->name }} - Otto Investments
@endsection

@section('og_description')
{{ Str::limit($product->description ?? 'Quality toy for children.', 200) }}
@endsection

@section('og_image')
{{ $product->image_url ? (str_starts_with($product->image_url, 'http') ? $product->image_url : asset('images/toys/' . $product->image_url)) : asset('images/toys/default.jpg') }}
@endsection

@push('structured_data')
<script type="application/ld+json">
{
  "@context": "https://schema.org/",
  "@type": "Product",
  "name": "{{ $product->name }}",
  "description": "{{ $product->description ?? '' }}",
  "image": "{{ $product->image_url ? (str_starts_with($product->image_url, 'http') ? $product->image_url : asset('images/toys/' . $product->image_url)) : asset('images/toys/default.jpg') }}",
  "brand": {
    "@type": "Brand",
    "name": "Otto Investments"
  },
  "offers": {
    "@type": "Offer",
    "url": "{{ route('product.show', $product->slug) }}",
    "priceCurrency": "KES",
    "price": "{{ $product->price }}",
    "availability": "https://schema.org/{{ $product->stock > 0 ? 'InStock' : 'OutOfStock' }}",
    "seller": {
      "@type": "Organization",
      "name": "Otto Investments"
    }
  }
}
</script>
@endpush

@section('content')
    <div class="container mx-auto px-4 lg:px-8 py-8">
        <div class="mb-4 text-xs text-slate-500">
            <a href="{{ route('home') }}" class="hover:underline">Home</a>
            <span class="mx-1">/</span>
            <a href="{{ route('shop.index') }}" class="hover:underline">Shop</a>
            @if($product->category)
                <span class="mx-1">/</span>
                <a href="{{ route('frontend.category', $product->category->slug) }}" class="hover:underline">
                    {{ $product->category->name }}
                </a>
            @endif
        </div>

        <div class="grid md:grid-cols-2 gap-8">
            <div class="bg-gradient-to-br from-pink-100 via-amber-50 to-sky-100 rounded-2xl h-80 md:h-96 overflow-hidden">
                @if($product->image_url)
                    <img
                        src="{{ str_starts_with($product->image_url, 'http') ? $product->image_url : asset('images/toys/' . $product->image_url) }}"
                        alt="{{ $product->name }}"
                        class="w-full h-full object-cover"
                    >
                @else
                    <div class="w-full h-full flex items-center justify-center text-slate-500 text-sm">
                        Product image
                    </div>
                @endif
            </div>

            <div>
                <h1 class="text-2xl font-semibold mb-2 text-slate-900">{{ $product->name }}</h1>
                
                {{-- Rating Display --}}
                @if($product->total_reviews > 0)
                    <div class="flex items-center gap-2 mb-2">
                        <div class="flex items-center">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star text-{{ $i <= round($product->average_rating) ? 'amber' : 'slate' }}-300 text-sm"></i>
                            @endfor
                        </div>
                        <span class="text-sm text-slate-600">
                            {{ number_format($product->average_rating, 1) }} ({{ $product->total_reviews }} {{ Str::plural('review', $product->total_reviews) }})
                        </span>
                    </div>
                @endif
                
                <p class="text-amber-600 text-xl font-bold mb-4">
                    Ksh {{ number_format($product->price, 0) }}
                </p>

                <p class="text-sm text-slate-600 mb-6">
                    {{ $product->description }}
                </p>

                @if($errors->any())
                    <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(session('success'))
                    <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('cart.add', $product->id) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium mb-1 text-slate-700">Quantity</label>
                        <input type="number" name="quantity" value="1" min="1"
                               class="border border-slate-200 rounded-lg px-2 py-1.5 text-sm w-24 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-400">
                    </div>

                    <button type="submit"
                            class="bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold px-6 py-2.5 rounded-lg shadow-sm shadow-amber-500/30 transition-colors">
                        Add to cart
                    </button>
                </form>

                @auth
                    <div class="mt-4">
                        @php
                            $isInWishlist = auth()->user()->wishlist()->where('product_id', $product->id)->exists();
                        @endphp
                        <form action="{{ route('wishlist.toggle', $product->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" 
                                    class="flex items-center gap-2 text-sm text-slate-600 hover:text-amber-600 transition-colors">
                                <i class="fas {{ $isInWishlist ? 'fa-heart' : 'fa-heart' }} {{ $isInWishlist ? 'text-red-500' : '' }}"></i>
                                <span>{{ $isInWishlist ? 'Remove from Wishlist' : 'Add to Wishlist' }}</span>
                            </button>
                        </form>
                    </div>
                @endauth

                @if(isset($related) && $related->count())
                    <div class="mt-10">
                        <h2 class="text-lg font-semibold mb-3 text-slate-900">You may also like</h2>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
                            @foreach($related as $item)
                                <a href="{{ route('product.show', $item->slug) }}"
                                   class="group border border-slate-200 rounded-2xl bg-white overflow-hidden shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all">
                                    <div class="h-32 bg-slate-100 flex items-center justify-center overflow-hidden">
                                        @if($item->image_url)
                                            <img
                                                src="{{ str_starts_with($item->image_url, 'http') ? $item->image_url : asset('images/toys/' . $item->image_url) }}"
                                                alt="{{ $item->name }}"
                                                class="w-full h-full object-cover"
                                            >
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-[11px] text-slate-400">
                                                Product image
                                            </div>
                                        @endif
                                    </div>
                                    <div class="p-2.5">
                                        <p class="font-medium text-[13px] text-slate-900 truncate">{{ $item->name }}</p>
                                        <p class="text-amber-600 font-semibold text-[11px]">
                                            Ksh {{ number_format($item->price, 0) }}
                                        </p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if(isset($recentlyViewed) && $recentlyViewed->count())
                    <div class="mt-10">
                        <h2 class="text-lg font-semibold mb-3 text-slate-900">Recently Viewed</h2>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
                            @foreach($recentlyViewed as $item)
                                <a href="{{ route('product.show', $item->slug) }}"
                                   class="group border border-slate-200 rounded-2xl bg-white overflow-hidden shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all">
                                    <div class="h-32 bg-slate-100 flex items-center justify-center overflow-hidden">
                                        @if($item->image_url)
                                            <img
                                                src="{{ str_starts_with($item->image_url, 'http') ? $item->image_url : asset('images/toys/' . $item->image_url) }}"
                                                alt="{{ $item->name }}"
                                                class="w-full h-full object-cover"
                                            >
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-[11px] text-slate-400">
                                                Product image
                                            </div>
                                        @endif
                                    </div>
                                    <div class="p-2.5">
                                        <p class="font-medium text-[13px] text-slate-900 truncate">{{ $item->name }}</p>
                                        <p class="text-amber-600 font-semibold text-[11px]">
                                            Ksh {{ number_format($item->price, 0) }}
                                        </p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Reviews Section --}}
        <div class="mt-12 border-t border-slate-200 pt-8">
            <h2 class="text-2xl font-semibold mb-6 text-slate-900">Customer Reviews</h2>

            {{-- Review Summary --}}
            @if($product->total_reviews > 0)
                <div class="bg-slate-50 rounded-xl p-6 mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <div class="flex items-center gap-3 mb-2">
                                <span class="text-4xl font-bold text-slate-900">{{ number_format($product->average_rating, 1) }}</span>
                                <div>
                                    <div class="flex items-center mb-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star text-{{ $i <= round($product->average_rating) ? 'amber' : 'slate' }}-300"></i>
                                        @endfor
                                    </div>
                                    <p class="text-sm text-slate-600">{{ $product->total_reviews }} {{ Str::plural('review', $product->total_reviews) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Review Form --}}
            @auth
                @if(!$hasReviewed)
                    <div class="bg-white border border-slate-200 rounded-xl p-6 mb-8">
                        <h3 class="text-lg font-semibold mb-4 text-slate-900">Write a Review</h3>
                        <form action="{{ route('review.store', $product->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-semibold mb-2 text-slate-700">Rating *</label>
                                    <div class="flex gap-2" id="rating-selector">
                                        @for($i = 5; $i >= 1; $i--)
                                            <input type="radio" name="rating" value="{{ $i }}" id="rating-{{ $i }}" class="hidden" required>
                                            <label for="rating-{{ $i }}" class="cursor-pointer text-2xl text-slate-300 hover:text-amber-400 rating-star" data-rating="{{ $i }}">
                                                <i class="far fa-star"></i>
                                            </label>
                                        @endfor
                                    </div>
                                    @error('rating')
                                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold mb-2 text-slate-700">Title (Optional)</label>
                                    <input type="text" name="title" value="{{ old('title') }}" 
                                           class="border border-slate-200 rounded-lg w-full px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-400"
                                           placeholder="Brief summary of your experience">
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold mb-2 text-slate-700">Your Review *</label>
                                    <textarea name="comment" rows="4" required
                                              class="border border-slate-200 rounded-lg w-full px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-400"
                                              placeholder="Share your experience with this product...">{{ old('comment') }}</textarea>
                                    @error('comment')
                                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                    <p class="text-xs text-slate-500 mt-1">Minimum 10 characters</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold mb-2 text-slate-700">Photos (optional)</label>
                                    <input type="file" name="images[]" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp" multiple
                                           class="border border-slate-200 rounded-lg w-full px-4 py-2 text-sm">
                                    <p class="text-xs text-slate-500 mt-1">Max 5 images, 2MB each</p>
                                </div>

                                <button type="submit" 
                                        class="bg-amber-500 hover:bg-amber-600 text-white font-semibold px-6 py-2.5 rounded-lg shadow-sm shadow-amber-500/30 transition-colors">
                                    Submit Review
                                </button>
                            </div>
                        </form>
                    </div>
                @else
                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-8">
                        <p class="text-sm text-amber-800">You have already reviewed this product.</p>
                    </div>
                @endif
            @endauth

            @guest
                <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 mb-8">
                    <p class="text-sm text-slate-600 mb-2">Please <a href="{{ route('login') }}" class="text-amber-600 hover:underline font-semibold">login</a> to write a review.</p>
                </div>
            @endguest

            {{-- Reviews List --}}
            @if($reviews->count() > 0)
                <div class="space-y-6">
                    @foreach($reviews as $review)
                        <div class="bg-white border border-slate-200 rounded-xl p-6">
                            <div class="flex items-start justify-between mb-3">
                                <div>
                                    <div class="flex items-center gap-2 mb-2">
                                        <div class="flex items-center">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star text-{{ $i <= $review->rating ? 'amber' : 'slate' }}-300 text-xs"></i>
                                            @endfor
                                        </div>
                                        <span class="text-sm font-semibold text-slate-900">{{ $review->name ?? $review->user->name ?? 'Anonymous' }}</span>
                                    </div>
                                    @if($review->title)
                                        <h4 class="font-semibold text-slate-900 mb-1">{{ $review->title }}</h4>
                                    @endif
                                </div>
                                <span class="text-xs text-slate-500">{{ $review->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-sm text-slate-700 mb-3">{{ $review->comment }}</p>
                            <div class="flex items-center gap-4">
                                <form action="{{ route('review.helpful', $review->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-xs text-slate-600 hover:text-amber-600 flex items-center gap-1">
                                        <i class="far fa-thumbs-up"></i>
                                        <span>Helpful ({{ $review->helpful_count }})</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $reviews->links() }}
                </div>
            @else
                <div class="bg-slate-50 border border-slate-200 rounded-xl p-8 text-center">
                    <p class="text-slate-600 mb-2">No reviews yet.</p>
                    <p class="text-sm text-slate-500">Be the first to review this product!</p>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Rating selector interaction
    document.addEventListener('DOMContentLoaded', function() {
        const stars = document.querySelectorAll('.rating-star');
        const ratingInputs = document.querySelectorAll('input[name="rating"]');
        
        stars.forEach(star => {
            star.addEventListener('click', function() {
                const rating = this.dataset.rating;
                ratingInputs.forEach(input => {
                    if (input.value <= rating) {
                        input.checked = true;
                    }
                });
                updateStarDisplay(rating);
            });

            star.addEventListener('mouseenter', function() {
                const rating = this.dataset.rating;
                updateStarDisplay(rating);
            });
        });

        const ratingSelector = document.getElementById('rating-selector');
        if (ratingSelector) {
            ratingSelector.addEventListener('mouseleave', function() {
                const checked = document.querySelector('input[name="rating"]:checked');
                if (checked) {
                    updateStarDisplay(checked.value);
                } else {
                    updateStarDisplay(0);
                }
            });
        }

        function updateStarDisplay(rating) {
            stars.forEach((star, index) => {
                const starRating = 5 - index;
                const icon = star.querySelector('i');
                if (starRating <= rating) {
                    icon.classList.remove('far');
                    icon.classList.add('fas');
                    icon.classList.remove('text-slate-300');
                    icon.classList.add('text-amber-400');
                } else {
                    icon.classList.remove('fas');
                    icon.classList.add('far');
                    icon.classList.remove('text-amber-400');
                    icon.classList.add('text-slate-300');
                }
            });
        }
    });
</script>
@endpush

