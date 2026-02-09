@extends('layouts.partner')

@section('page_title', 'Create Project')

@section('partner_content')
    <div class="mb-4">
        <div class="flex items-center gap-3 mb-2">
            <a href="{{ route('partner.projects.manage') }}" class="text-slate-500 hover:text-slate-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M19 12H5M12 19l-7-7 7-7" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>
            <h1 class="text-lg font-semibold">Create New Project</h1>
        </div>
        <p class="text-xs text-slate-500">Add a new project and assign users to manage it.</p>
    </div>

    <div class="bg-white border border-slate-100 rounded-lg p-6">
        <form action="{{ route('partner.projects.manage.store') }}" method="POST" class="space-y-4">
            @csrf

            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="block text-xs font-semibold text-slate-700 mb-1">
                        Project Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                           class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-amber-500">
                    @error('name')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="type" class="block text-xs font-semibold text-slate-700 mb-1">
                        Project Type <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="type" name="type" value="{{ old('type', 'ecommerce') }}" required
                           placeholder="e.g., ecommerce, service, platform"
                           class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-amber-500">
                    @error('type')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="block text-xs font-semibold text-slate-700 mb-1">
                        Description
                    </label>
                    <textarea id="description" name="description" rows="3"
                              class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-amber-500">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="color" class="block text-xs font-semibold text-slate-700 mb-1">
                        Color Theme <span class="text-red-500">*</span>
                    </label>
                    <select id="color" name="color" required
                            class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-amber-500">
                        <option value="emerald" {{ old('color', 'emerald') === 'emerald' ? 'selected' : '' }}>Emerald</option>
                        <option value="blue" {{ old('color') === 'blue' ? 'selected' : '' }}>Blue</option>
                        <option value="amber" {{ old('color') === 'amber' ? 'selected' : '' }}>Amber</option>
                        <option value="purple" {{ old('color') === 'purple' ? 'selected' : '' }}>Purple</option>
                        <option value="red" {{ old('color') === 'red' ? 'selected' : '' }}>Red</option>
                        <option value="indigo" {{ old('color') === 'indigo' ? 'selected' : '' }}>Indigo</option>
                    </select>
                </div>

                <div>
                    <label for="icon" class="block text-xs font-semibold text-slate-700 mb-1">
                        Icon Class (FontAwesome)
                    </label>
                    <input type="text" id="icon" name="icon" value="{{ old('icon') }}"
                           placeholder="e.g., fas fa-shopping-cart"
                           class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-amber-500">
                </div>

                <div class="md:col-span-2">
                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 mb-4">
                        <p class="text-xs text-amber-800 font-semibold mb-1">💡 Project URL (Optional)</p>
                        <p class="text-[11px] text-amber-700">You can create the project now and add the URL later when it's developed. Leave these fields empty if the project is still in planning.</p>
                    </div>
                </div>

                <div>
                    <label for="url" class="block text-xs font-semibold text-slate-700 mb-1">
                        External URL <span class="text-slate-400">(Optional)</span>
                    </label>
                    <input type="url" id="url" name="url" value="{{ old('url') }}"
                           placeholder="https://example.com"
                           class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-amber-500">
                    <p class="text-[10px] text-slate-500 mt-1">Add later when project is live</p>
                </div>

                <div>
                    <label for="route_name" class="block text-xs font-semibold text-slate-700 mb-1">
                        Laravel Route Name <span class="text-slate-400">(Optional)</span>
                    </label>
                    <input type="text" id="route_name" name="route_name" value="{{ old('route_name') }}"
                           placeholder="e.g., home, admin.dashboard"
                           class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-amber-500">
                    <p class="text-[10px] text-slate-500 mt-1">Add later when route is created</p>
                </div>

                <div>
                    <label for="sort_order" class="block text-xs font-semibold text-slate-700 mb-1">
                        Sort Order
                    </label>
                    <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                           class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-amber-500">
                </div>

                <div>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                               class="rounded border-slate-300 text-amber-600 focus:ring-amber-500">
                        <span class="text-xs font-semibold text-slate-700">Active</span>
                    </label>
                </div>
            </div>

            {{-- User Assignment Section --}}
            <div class="pt-4 border-t border-slate-200">
                <h3 class="text-sm font-semibold text-slate-900 mb-3">Assign Users to Manage This Project</h3>
                <p class="text-xs text-slate-500 mb-4">Select users who will manage this project (e.g., run the e-commerce website).</p>
                
                @if($users->count() > 0)
                    <div id="user-assignments" class="space-y-2">
                        <div class="user-assignment-item flex items-center gap-2 p-3 border border-slate-200 rounded-lg">
                            <select name="assigned_users[]" class="flex-1 border border-slate-200 rounded px-3 py-2 text-sm">
                                <option value="">Select a user...</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                            <select name="user_roles[]" class="border border-slate-200 rounded px-3 py-2 text-sm">
                                <option value="manager">Manager</option>
                                <option value="editor">Editor</option>
                                <option value="viewer">Viewer</option>
                            </select>
                            <button type="button" onclick="this.closest('.user-assignment-item').remove()" class="text-red-500 hover:text-red-600 text-sm">×</button>
                        </div>
                    </div>
                    <button type="button" onclick="addUserAssignment()" class="mt-2 text-xs text-amber-600 hover:text-amber-700 hover:underline">
                        + Add another user
                    </button>
                @else
                    <p class="text-xs text-slate-500 bg-slate-50 p-3 rounded-lg">
                        No users available to assign. Users must be registered (not partners or admins) to be assigned to projects.
                    </p>
                @endif
            </div>

            <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                <button type="submit"
                        class="bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold px-4 py-2 rounded-lg">
                    Create Project
                </button>
                <a href="{{ route('partner.projects.manage') }}"
                   class="border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-semibold px-4 py-2 rounded-lg">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        function addUserAssignment() {
            const container = document.getElementById('user-assignments');
            const template = container.querySelector('.user-assignment-item').cloneNode(true);
            template.querySelector('select[name="assigned_users[]"]').value = '';
            container.appendChild(template);
        }
    </script>
    @endpush
@endsection
