@extends('layouts.admin')

@section('page_title', 'Create Project')

@section('content')
    <div class="mb-4">
        <div class="flex items-center gap-3 mb-2">
            <a href="{{ route('admin.projects.index') }}" class="text-slate-500 hover:text-slate-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M19 12H5M12 19l-7-7 7-7" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>
            <h1 class="text-lg font-semibold">Create New Project</h1>
        </div>
        <p class="text-xs text-slate-500">Add a new project that partners can access from their dashboard.</p>
    </div>

    <div class="bg-white border border-slate-100 rounded-lg p-6">
        <form action="{{ route('admin.projects.store') }}" method="POST" class="space-y-4">
            @csrf

            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="block text-xs font-semibold text-slate-700 mb-1">
                        Project Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                           class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                    @error('name')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="slug" class="block text-xs font-semibold text-slate-700 mb-1">
                        Slug (auto-generated if empty)
                    </label>
                    <input type="text" id="slug" name="slug" value="{{ old('slug') }}"
                           class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                    @error('slug')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="block text-xs font-semibold text-slate-700 mb-1">
                        Description
                    </label>
                    <textarea id="description" name="description" rows="3"
                              class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="type" class="block text-xs font-semibold text-slate-700 mb-1">
                        Project Type <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="type" name="type" value="{{ old('type', 'ecommerce') }}" required
                           placeholder="e.g., ecommerce, service, platform"
                           class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                    @error('type')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="color" class="block text-xs font-semibold text-slate-700 mb-1">
                        Color Theme <span class="text-red-500">*</span>
                    </label>
                    <select id="color" name="color" required
                            class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                        <option value="emerald" {{ old('color', 'emerald') === 'emerald' ? 'selected' : '' }}>Emerald</option>
                        <option value="blue" {{ old('color') === 'blue' ? 'selected' : '' }}>Blue</option>
                        <option value="amber" {{ old('color') === 'amber' ? 'selected' : '' }}>Amber</option>
                        <option value="purple" {{ old('color') === 'purple' ? 'selected' : '' }}>Purple</option>
                        <option value="red" {{ old('color') === 'red' ? 'selected' : '' }}>Red</option>
                        <option value="indigo" {{ old('color') === 'indigo' ? 'selected' : '' }}>Indigo</option>
                    </select>
                    @error('color')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="url" class="block text-xs font-semibold text-slate-700 mb-1">
                        External URL
                    </label>
                    <input type="url" id="url" name="url" value="{{ old('url') }}"
                           placeholder="https://example.com"
                           class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                    <p class="text-[10px] text-slate-500 mt-1">Use this for external projects</p>
                    @error('url')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="route_name" class="block text-xs font-semibold text-slate-700 mb-1">
                        Laravel Route Name
                    </label>
                    <input type="text" id="route_name" name="route_name" value="{{ old('route_name') }}"
                           placeholder="e.g., home, admin.dashboard"
                           class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                    <p class="text-[10px] text-slate-500 mt-1">Use this for internal Laravel routes</p>
                    @error('route_name')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="icon" class="block text-xs font-semibold text-slate-700 mb-1">
                        Icon Class (FontAwesome)
                    </label>
                    <input type="text" id="icon" name="icon" value="{{ old('icon') }}"
                           placeholder="e.g., fas fa-shopping-cart"
                           class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                    @error('icon')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="sort_order" class="block text-xs font-semibold text-slate-700 mb-1">
                        Sort Order
                    </label>
                    <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                           class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                    @error('sort_order')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                               class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                        <span class="text-xs font-semibold text-slate-700">Active</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                <button type="submit"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-4 py-2 rounded-lg">
                    Create Project
                </button>
                <a href="{{ route('admin.projects.index') }}"
                   class="border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-semibold px-4 py-2 rounded-lg">
                    Cancel
                </a>
            </div>
        </form>
    </div>
@endsection
