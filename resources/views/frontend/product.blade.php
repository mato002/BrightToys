@extends('layouts.app')

@section('title', $product->name . ' - BrightToys')

@section('meta_description')
{{ Str::limit($product->description ?? 'Quality toy for children. Available at BrightToys Kenya.', 160) }}
@endsection

@section('meta_keywords')
{{ $product->name }}, {{ $product->category->name ?? 'toys' }}, kids toys, Kenya
@endsection

@section('og_title')
{{ $product->name }} - BrightToys
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
    "name": "BrightToys"
  },
  "offers": {
    "@type": "Offer",
    "url": "{{ route('product.show', $product->slug) }}",
    "priceCurrency": "KES",
    "price": "{{ $product->price }}",
    "availability": "https://schema.org/{{ $product->stock > 0 ? 'InStock' : 'OutOfStock' }}",
    "seller": {
      "@type": "Organization",
      "name": "BrightToys"
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
    </div>
@endsection

