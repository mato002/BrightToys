@extends('layouts.admin')

@section('page_title', 'Add Product')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-6">
        <div>
            <h1 class="text-lg font-semibold text-slate-900">Add New Product</h1>
            <p class="text-xs text-slate-500 mt-0.5">Create a new product for your store</p>
        </div>
        <a href="{{ route('admin.products.index') }}"
           class="inline-flex items-center justify-center text-xs font-semibold text-slate-600 hover:text-slate-900 px-4 py-2 border border-slate-200 rounded-lg hover:bg-slate-50">
            ← Back to Products
        </a>
    </div>

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" 
          class="bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden">
        @csrf
        
        @if($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-4 m-6 rounded">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Please correct the following errors:</h3>
                        <ul class="mt-2 text-sm text-red-700 list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <div class="p-6 space-y-6">
            {{-- Basic Information --}}
            <div>
                <h2 class="text-sm font-semibold text-slate-900 mb-4 pb-2 border-b border-slate-200">Basic Information</h2>
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold mb-1.5 text-slate-700">
                            Product Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="name" 
                               value="{{ old('name') }}" 
                               required
                               class="w-full px-3 py-2.5 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold mb-1.5 text-slate-700">SKU</label>
                        <input type="text" 
                               name="sku" 
                               value="{{ old('sku') }}"
                               placeholder="Auto-generated if empty"
                               class="w-full px-3 py-2.5 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition">
                    </div>
                </div>
            </div>

            {{-- Slug and Category --}}
            <div>
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold mb-1.5 text-slate-700">Slug</label>
                        <input type="text" 
                               name="slug" 
                               value="{{ old('slug') }}"
                               placeholder="Auto-generated from name"
                               class="w-full px-3 py-2.5 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition">
                        <p class="text-[10px] text-slate-500 mt-1">URL-friendly version of the name</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold mb-1.5 text-slate-700">Category</label>
                        <select name="category_id" 
                                class="w-full px-3 py-2.5 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition bg-white">
                            <option value="">-- Select Category --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Product Image --}}
            <div>
                <label class="block text-xs font-semibold mb-1.5 text-slate-700">Product Image</label>
                <div class="p-4 bg-slate-50 rounded-lg border-2 border-dashed border-slate-300">
                    <label class="flex flex-col items-center justify-center cursor-pointer">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <svg class="w-10 h-10 mb-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            <p class="mb-2 text-xs text-slate-500">
                                <span class="font-semibold">Click to upload</span> or drag and drop
                            </p>
                            <p class="text-[10px] text-slate-500">PNG, JPG, GIF, WEBP (MAX. 2MB)</p>
                        </div>
                        <input type="file" 
                               name="image" 
                               id="image-input"
                               accept="image/jpeg,image/jpg,image/png,image/gif,image/webp"
                               class="hidden"
                               onchange="previewImage(this)">
                    </label>
                    <div id="image-preview-container" class="hidden mt-4">
                        <img id="image-preview" 
                             src="" 
                             alt="Preview"
                             class="w-32 h-32 object-cover rounded-lg border-2 border-slate-300 shadow-sm mx-auto">
                    </div>
                    <div class="mt-3 text-center">
                        <span class="text-[10px] text-slate-500">or</span>
                        <input type="text" 
                               name="image_url" 
                               value="{{ old('image_url') }}"
                               placeholder="Enter existing filename (e.g., toy-name.jpg)"
                               class="mt-2 w-full px-3 py-2 text-xs border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                    </div>
                </div>
            </div>

            {{-- Pricing & Stock --}}
            <div>
                <h2 class="text-sm font-semibold text-slate-900 mb-4 pb-2 border-b border-slate-200">Pricing & Inventory</h2>
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold mb-1.5 text-slate-700">
                            Price (Ksh) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-500">Ksh</span>
                            <input type="number" 
                                   step="0.01" 
                                   name="price" 
                                   value="{{ old('price') }}" 
                                   required 
                                   min="0"
                                   class="w-full pl-12 pr-3 py-2.5 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold mb-1.5 text-slate-700">Stock Quantity</label>
                        <input type="number" 
                               name="stock" 
                               value="{{ old('stock', 0) }}" 
                               min="0"
                               class="w-full px-3 py-2.5 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition">
                    </div>
                </div>
            </div>

            {{-- Description --}}
            <div>
                <label class="block text-xs font-semibold mb-1.5 text-slate-700">Description</label>
                <textarea name="description" 
                          rows="5"
                          placeholder="Enter product description..."
                          class="w-full px-3 py-2.5 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition resize-none">{{ old('description') }}</textarea>
            </div>

            {{-- Status & Settings --}}
            <div>
                <h2 class="text-sm font-semibold text-slate-900 mb-4 pb-2 border-b border-slate-200">Status & Settings</h2>
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold mb-1.5 text-slate-700">Status</label>
                        <select name="status" 
                                class="w-full px-3 py-2.5 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition bg-white">
                            <option value="active" @selected(old('status', 'active') == 'active')>Active</option>
                            <option value="inactive" @selected(old('status') == 'inactive')>Inactive</option>
                        </select>
                    </div>
                    <div class="flex items-center">
                        <div class="flex items-center h-10">
                            <input type="checkbox" 
                                   name="featured" 
                                   value="1" 
                                   id="featured" 
                                   class="w-4 h-4 text-amber-600 border-slate-300 rounded focus:ring-amber-500"
                                   @checked(old('featured'))>
                            <label for="featured" class="ml-2 text-xs font-medium text-slate-700">
                                Featured Product
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Form Actions --}}
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between">
            <a href="{{ route('admin.products.index') }}" 
               class="text-sm text-slate-600 hover:text-slate-900 font-medium">
                Cancel
            </a>
            <button type="submit" 
                    class="inline-flex items-center justify-center px-6 py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold rounded-lg shadow-sm hover:shadow-md transition-all">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Create Product
            </button>
        </div>
    </form>

    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    const previewContainer = document.getElementById('image-preview-container');
                    const preview = document.getElementById('image-preview');
                    if (previewContainer && preview) {
                        preview.src = e.target.result;
                        previewContainer.classList.remove('hidden');
                    }
                };
                
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Handle drag and drop
        const dropZone = document.querySelector('label.cursor-pointer');
        if (dropZone) {
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            ['dragenter', 'dragover'].forEach(eventName => {
                dropZone.addEventListener(eventName, highlight, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, unhighlight, false);
            });

            function highlight(e) {
                dropZone.classList.add('border-amber-500', 'bg-amber-50');
            }

            function unhighlight(e) {
                dropZone.classList.remove('border-amber-500', 'bg-amber-50');
            }

            dropZone.addEventListener('drop', handleDrop, false);

            function handleDrop(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                const input = document.getElementById('image-input');
                if (input && files.length > 0) {
                    input.files = files;
                    previewImage(input);
                }
            }
        }
    </script>
@endsection
