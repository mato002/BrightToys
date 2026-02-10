@extends('layouts.admin')

@section('page_title', 'Edit Project Asset')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
        <div>
            <h1 class="text-lg font-semibold">Edit Project Asset</h1>
            <p class="text-xs text-slate-500">Update details for this project asset.</p>
        </div>
        <a href="{{ route('partner.projects.finances', $asset->project_id) }}" class="text-sm text-slate-600 hover:text-slate-800">
            Back to project finances
        </a>
    </div>

    <form action="{{ route('admin.project-assets.update', $asset) }}" method="POST" enctype="multipart/form-data"
          class="bg-white border border-slate-100 rounded-lg p-4 text-sm space-y-4 shadow-sm max-w-2xl">
        @csrf
        @method('PUT')

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
                @foreach($projects as $project)
                    <option value="{{ $project->id }}"
                        {{ old('project_id', $asset->project_id) == $project->id ? 'selected' : '' }}>
                        {{ $project->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-semibold mb-1 text-slate-700">Asset Name <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name', $asset->name) }}" required
                   class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-400">
        </div>

        <div class="grid md:grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-semibold mb-1 text-slate-700">Category</label>
                <input type="text" name="category" value="{{ old('category', $asset->category) }}"
                       class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-400">
            </div>

            <div>
                <label class="block text-xs font-semibold mb-1 text-slate-700">Date Acquired</label>
                <input type="date" name="date_acquired"
                       value="{{ old('date_acquired', optional($asset->date_acquired)->format('Y-m-d')) }}"
                       class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-400">
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-semibold mb-1 text-slate-700">Acquisition Cost (Ksh) <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" name="acquisition_cost"
                       value="{{ old('acquisition_cost', $asset->acquisition_cost) }}" min="0" required
                       class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-400">
            </div>

            <div>
                <label class="block text-xs font-semibold mb-1 text-slate-700">Current Estimated Value (Ksh)</label>
                <input type="number" step="0.01" name="current_value"
                       value="{{ old('current_value', $asset->current_value) }}" min="0"
                       class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-400">
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold mb-1 text-slate-700">Notes (Optional)</label>
            <textarea name="notes" rows="3"
                      class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-400">{{ old('notes', $asset->notes) }}</textarea>
        </div>

        <div>
            <label class="block text-xs font-semibold mb-1 text-slate-700">Supporting Document</label>
            @if($asset->supporting_document_path)
                <p class="text-[11px] text-slate-600 mb-1">
                    Current: 
                    <a href="{{ asset('storage/' . $asset->supporting_document_path) }}" target="_blank"
                       class="text-emerald-600 hover:text-emerald-700 underline">
                        {{ $asset->supporting_document_name ?? 'View document' }}
                    </a>
                </p>
            @endif
            <input type="file" name="supporting_document" accept=".pdf,.jpg,.jpeg,.png"
                   class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-400">
            <p class="text-[10px] text-slate-500 mt-1">Upload to replace the existing document (PDF, JPG, PNG - Max 10MB).</p>
        </div>

        <div class="flex items-center gap-3 pt-2 border-t border-slate-100">
            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-6 py-2 rounded shadow-sm">
                Update Asset
            </button>
            <a href="{{ route('partner.projects.finances', $asset->project_id) }}" class="text-sm text-slate-600 hover:text-slate-800">
                Cancel
            </a>
        </div>
    </form>
@endsection

@extends('layouts.admin')

@section('page_title', 'Edit Project Asset')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
        <div>
            <h1 class="text-lg font-semibold">Edit Project Asset</h1>
            <p class="text-xs text-slate-500">
                Update details for this asset so partners see accurate project value.
            </p>
        </div>
        <a href="{{ route('admin.projects.show', $asset->project_id) }}" class="text-sm text-slate-600 hover:text-slate-800">
            Back to project
        </a>
    </div>

    <form action="{{ route('admin.project-assets.update', $asset) }}" method="POST" enctype="multipart/form-data"
          class="bg-white border border-slate-100 rounded-lg p-4 text-sm space-y-4 shadow-sm max-w-2xl">
        @csrf
        @method('PUT')

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-lg p-3 text-xs text-red-700 mb-2">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div>
            <label class="block text-xs font-semibold mb-1 text-slate-700">
                Project <span class="text-red-500">*</span>
            </label>
            <select name="project_id" required
                    class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-400">
                @foreach($projects as $proj)
                    <option value="{{ $proj->id }}"
                        {{ old('project_id', $asset->project_id) == $proj->id ? 'selected' : '' }}>
                        {{ $proj->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-semibold mb-1 text-slate-700">
                Asset Name <span class="text-red-500">*</span>
            </label>
            <input type="text" name="name" value="{{ old('name', $asset->name) }}" required
                   class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-400">
        </div>

        <div>
            <label class="block text-xs font-semibold mb-1 text-slate-700">
                Category
            </label>
            <input type="text" name="category" value="{{ old('category', $asset->category) }}"
                   class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-400">
        </div>

        <div class="grid md:grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-semibold mb-1 text-slate-700">
                    Acquisition Cost (KES) <span class="text-red-500">*</span>
                </label>
                <input type="number" step="0.01" min="0" name="acquisition_cost"
                       value="{{ old('acquisition_cost', $asset->acquisition_cost) }}" required
                       class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-400">
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1 text-slate-700">
                    Date Acquired <span class="text-red-500">*</span>
                </label>
                <input type="date" name="date_acquired"
                       value="{{ old('date_acquired', optional($asset->date_acquired)->format('Y-m-d')) }}" required
                       class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-400">
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold mb-1 text-slate-700">
                Current Estimated Value (KES)
            </label>
            <input type="number" step="0.01" min="0" name="current_value"
                   value="{{ old('current_value', $asset->current_value) }}"
                   class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-400">
        </div>

        <div>
            <label class="block text-xs font-semibold mb-1 text-slate-700">
                Notes
            </label>
            <textarea name="notes" rows="3"
                      class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-400">{{ old('notes', $asset->notes) }}</textarea>
        </div>

        <div>
            <label class="block text-xs font-semibold mb-1 text-slate-700">
                Supporting Document
            </label>
            @if($asset->supporting_document_path)
                <p class="text-[11px] text-slate-600 mb-1">
                    Current:
                    <a href="{{ asset('storage/' . $asset->supporting_document_path) }}" target="_blank"
                       class="text-emerald-600 hover:text-emerald-700 underline">
                        {{ $asset->supporting_document_name ?? 'View document' }}
                    </a>
                </p>
            @endif
            <input type="file" name="supporting_document" accept=".pdf,.jpg,.jpeg,.png"
                   class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-400">
            <p class="text-[10px] text-slate-500 mt-1">
                Upload a new file to replace the existing document (max 10MB).
            </p>
        </div>

        <div class="flex items-center gap-3 pt-2 border-t border-slate-100">
            <button type="submit"
                    class="bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold px-6 py-2 rounded shadow-sm">
                Update Asset
            </button>
            <a href="{{ route('admin.projects.show', $asset->project_id) }}" class="text-sm text-slate-600 hover:text-slate-800">
                Cancel
            </a>
        </div>
    </form>
@endsection

