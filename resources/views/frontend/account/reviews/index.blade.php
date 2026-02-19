@extends('layouts.account')

@section('title', 'My Reviews')
@section('page_title', 'My Reviews')

@section('breadcrumbs')
    <span class="breadcrumb-separator">/</span>
    <span class="text-slate-700">My Reviews</span>
@endsection

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-slate-900 tracking-tight">My Reviews</h1>
                <p class="text-xs md:text-sm text-slate-500 mt-1">View and edit your product reviews</p>
            </div>
        </div>

        @forelse($reviews as $review)
            <div class="bg-white border-2 border-slate-200 rounded-xl p-5 md:p-6 shadow-sm">
                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <a href="{{ route('product.show', $review->product->slug) }}" class="text-base font-bold text-slate-900 hover:text-amber-600">
                                {{ $review->product->name }}
                            </a>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                @if($review->status === 'approved') bg-emerald-100 text-emerald-700
                                @elseif($review->status === 'rejected') bg-red-100 text-red-700
                                @else bg-amber-100 text-amber-700
                                @endif">
                                {{ ucfirst($review->status) }}
                            </span>
                        </div>
                        <div class="flex items-center gap-2 mb-2">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star text-amber-500 {{ $i <= $review->rating ? '' : 'opacity-30' }}"></i>
                            @endfor
                            <span class="text-xs text-slate-500">{{ $review->created_at->format('M d, Y') }}</span>
                        </div>
                        @if($review->title)
                            <p class="text-sm font-semibold text-slate-800 mb-1">{{ $review->title }}</p>
                        @endif
                        <p class="text-sm text-slate-600">{{ Str::limit($review->comment, 150) }}</p>
                        @if($review->images->count() > 0)
                            <div class="flex flex-wrap gap-2 mt-2">
                                @foreach($review->images->take(3) as $img)
                                    <img src="{{ Storage::url($img->path) }}" alt="" class="w-14 h-14 rounded-lg object-cover border border-slate-200">
                                @endforeach
                                @if($review->images->count() > 3)
                                    <span class="text-xs text-slate-500 self-center">+{{ $review->images->count() - 3 }} more</span>
                                @endif
                            </div>
                        @endif
                    </div>
                    <a href="{{ route('account.reviews.edit', $review) }}"
                       class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-amber-700 bg-amber-50 border-2 border-amber-200 rounded-lg hover:bg-amber-100">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                </div>
            </div>
        @empty
            <div class="bg-white border-2 border-dashed border-slate-300 rounded-xl p-12 text-center">
                <i class="fas fa-star text-4xl text-slate-300 mb-4"></i>
                <h3 class="text-xl font-bold text-slate-900 mb-2">No reviews yet</h3>
                <p class="text-sm text-slate-500 mb-6">Leave reviews on products you've purchased from the product page.</p>
                <a href="{{ route('shop.index') }}" class="inline-flex items-center gap-2 bg-amber-600 hover:bg-amber-700 text-white font-semibold px-6 py-3 rounded-lg">
                    Browse products
                </a>
            </div>
        @endforelse

        @if($reviews->hasPages())
            <div class="mt-6">{{ $reviews->links() }}</div>
        @endif
    </div>
@endsection
