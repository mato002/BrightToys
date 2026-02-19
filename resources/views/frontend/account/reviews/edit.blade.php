@extends('layouts.account')

@section('title', 'Edit Review')
@section('page_title', 'Edit Review')

@section('breadcrumbs')
    <span class="breadcrumb-separator">/</span>
    <a href="{{ route('account.reviews') }}" class="text-slate-600 hover:text-amber-600">My Reviews</a>
    <span class="breadcrumb-separator">/</span>
    <span class="text-slate-700">Edit</span>
@endsection

@section('content')
    <div class="max-w-2xl">
        <div class="bg-white border-2 border-slate-200 rounded-xl p-6 shadow-sm">
            <p class="text-sm text-slate-600 mb-4">Review for <a href="{{ route('product.show', $review->product->slug) }}" class="font-semibold text-amber-600 hover:underline">{{ $review->product->name }}</a></p>
            <form action="{{ route('account.reviews.update', $review) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Rating *</label>
                        <select name="rating" required class="w-full border-2 border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                            @foreach([5,4,3,2,1] as $r)
                                <option value="{{ $r }}" @selected((int)old('rating', $review->rating) === $r)>{{ $r }} star{{ $r > 1 ? 's' : '' }}</option>
                            @endforeach
                        </select>
                        @error('rating')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Title (optional)</label>
                        <input type="text" name="title" value="{{ old('title', $review->title) }}"
                               class="w-full border-2 border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                               maxlength="255" placeholder="Short summary">
                        @error('title')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Comment *</label>
                        <textarea name="comment" rows="4" required minlength="10" maxlength="1000"
                                  class="w-full border-2 border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                                  placeholder="Your review (min 10 characters)">{{ old('comment', $review->comment) }}</textarea>
                        @error('comment')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Images (optional)</label>
                        <input type="file" name="images[]" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp" multiple
                               class="w-full border-2 border-slate-300 rounded-lg px-3 py-2 text-sm">
                        <p class="text-xs text-slate-500 mt-1">Max 5 images, 2MB each. Uploading new images replaces existing ones.</p>
                        @if($review->images->count() > 0)
                            <p class="text-xs text-amber-600 mt-1">Current: {{ $review->images->count() }} image(s). Add new to replace.</p>
                        @endif
                        @error('images.*')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="mt-6 flex gap-3">
                    <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white font-semibold px-5 py-2.5 rounded-lg">
                        Update review
                    </button>
                    <a href="{{ route('account.reviews') }}" class="px-5 py-2.5 border-2 border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50 font-semibold">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
