@extends('layouts.admin')

@section('page_title', 'Add Project Asset')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
        <div>
            <h1 class="text-lg font-semibold">Add Project Asset</h1>
            <p class="text-xs text-slate-500">Record land, stock, equipment or other assets acquired for a project.</p>
        </div>
        <a href="{{ route('admin.projects.index') }}" class="text-sm text-slate-600 hover:text-slate-800">
            Back to projects
        </a>
    </div>

    <form action="{{ route('admin.project-assets.store') }}" method="POST" enctype="multipart/form-data"
          class="bg-white border border-slate-100 rounded-lg p-4 text-sm space-y-4 shadow-sm max-w-2xl">
        @csrf

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-lg p-3 text-xs text-red-700">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div>
            <label class="block text-xs font-semibold mb-1 text-slate-700">Project <span class="text-red-500">*</span></label>
            <select name="project_id" required
                    class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-400">
                <option value="">Select project</option>
                @foreach($projects as $project)
                    <option value="{{ $project->id }}"
                        {{ old('project_id', $selectedProjectId) == $project->id ? 'selected' : '' }}>
                        {{ $project->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-semibold mb-1 text-slate-700">Asset Name <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name') }}" required
                   class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-400"
                   placeholder="e.g., Land Parcel Kisumu/Block 3/124, Warehouse Racking, Delivery Van">
        </div>

        <div class="grid md:grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-semibold mb-1 text-slate-700">Category</label>
                <input type="text" name="category" value="{{ old('category') }}"
                       class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-400"
                       placeholder="e.g., land, stock, equipment">
            </div>

            <div>
                <label class="block text-xs font-semibold mb-1 text-slate-700">Date Acquired</label>
                <input type="date" name="date_acquired" value="{{ old('date_acquired') }}"
                       class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-400">
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-semibold mb-1 text-slate-700">Acquisition Cost (Ksh) <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" name="acquisition_cost" value="{{ old('acquisition_cost') }}" min="0" required
                       class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-400"
                       placeholder="0.00">
            </div>

            <div>
                <label class="block text-xs font-semibold mb-1 text-slate-700">Current Estimated Value (Ksh)</label>
                <input type="number" step="0.01" name="current_value" value="{{ old('current_value') }}" min="0"
                       class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-400"
                       placeholder="Leave blank to use acquisition cost">
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold mb-1 text-slate-700">Notes (Optional)</label>
            <textarea name="notes" rows="3"
                      class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-400"
                      placeholder="Details about this asset, condition, location, etc.">{{ old('notes') }}</textarea>
        </div>

        <div>
            <label class="block text-xs font-semibold mb-1 text-slate-700">Supporting Document</label>
            <input type="file" name="supporting_document" accept=".pdf,.jpg,.jpeg,.png"
                   class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-400">
            <p class="text-[10px] text-slate-500 mt-1">
                Upload title deed, purchase invoice or any proof document (PDF, JPG, PNG - Max 10MB).
            </p>
        </div>

        <div class="flex items-center gap-3 pt-2 border-t border-slate-100">
            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-6 py-2 rounded shadow-sm">
                Save Asset
            </button>
            <a href="{{ route('admin.projects.index') }}" class="text-sm text-slate-600 hover:text-slate-800">
                Cancel
            </a>
        </div>
    </form>
@endsection

