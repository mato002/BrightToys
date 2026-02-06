@extends('layouts.admin')

@section('page_title', 'Financial Record Details')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
        <div>
            <h1 class="text-lg font-semibold">Financial Record Details</h1>
            <p class="text-xs text-slate-500">View and manage financial record.</p>
        </div>
        <a href="{{ route('admin.financial.index') }}" class="text-sm text-slate-600 hover:text-slate-800">
            Back to list
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 text-sm text-emerald-700 bg-emerald-50 border border-emerald-200 px-4 py-2 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid md:grid-cols-2 gap-6">
        <div class="bg-white border border-slate-100 rounded-lg p-4">
            <h2 class="text-sm font-semibold text-slate-900 mb-4">Record Information</h2>
            <dl class="space-y-3 text-sm">
                <div>
                    <dt class="text-xs text-slate-500 mb-1">Type</dt>
                    <dd>
                        <span class="inline-flex items-center rounded-full px-2 py-1 text-[10px] font-medium
                            {{ $financial->type === 'expense' ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700' }}">
                            {{ ucfirst(str_replace('_', ' ', $financial->type)) }}
                        </span>
                    </dd>
                </div>
                @if($financial->category)
                <div>
                    <dt class="text-xs text-slate-500 mb-1">Category</dt>
                    <dd class="text-slate-700">{{ $financial->category }}</dd>
                </div>
                @endif
                <div>
                    <dt class="text-xs text-slate-500 mb-1">Amount</dt>
                    <dd class="font-semibold text-lg
                        {{ $financial->type === 'expense' ? 'text-red-600' : 'text-emerald-600' }}">
                        {{ $financial->currency }} {{ number_format($financial->amount, 2) }}
                    </dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-500 mb-1">Date</dt>
                    <dd class="text-slate-700">{{ $financial->occurred_at->format('d M Y, H:i') }}</dd>
                </div>
                @if($financial->description)
                <div>
                    <dt class="text-xs text-slate-500 mb-1">Description</dt>
                    <dd class="text-slate-700">{{ $financial->description }}</dd>
                </div>
                @endif
                @if($financial->partner)
                <div>
                    <dt class="text-xs text-slate-500 mb-1">Partner</dt>
                    <dd class="text-slate-700">{{ $financial->partner->name }}</dd>
                </div>
                @endif
                <div>
                    <dt class="text-xs text-slate-500 mb-1">Status</dt>
                    <dd>
                        <span class="inline-flex items-center rounded-full px-2 py-1 text-[10px] font-medium
                            {{ $financial->status === 'approved' ? 'bg-emerald-100 text-emerald-700' : 
                               ($financial->status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') }}">
                            {{ ucfirst(str_replace('_', ' ', $financial->status)) }}
                        </span>
                    </dd>
                </div>
            </dl>
        </div>

        <div class="bg-white border border-slate-100 rounded-lg p-4">
            <h2 class="text-sm font-semibold text-slate-900 mb-4">Approval Information</h2>
            <dl class="space-y-3 text-sm">
                <div>
                    <dt class="text-xs text-slate-500 mb-1">Created By</dt>
                    <dd class="text-slate-700">{{ $financial->creator->name }}</dd>
                    <dd class="text-xs text-slate-500">{{ $financial->created_at->format('d M Y, H:i') }}</dd>
                </div>
                @if($financial->approver)
                <div>
                    <dt class="text-xs text-slate-500 mb-1">Approved By</dt>
                    <dd class="text-slate-700">{{ $financial->approver->name }}</dd>
                    <dd class="text-xs text-slate-500">{{ $financial->approved_at->format('d M Y, H:i') }}</dd>
                </div>
                @endif
            </dl>

            @if($financial->status === 'pending_approval')
            <div class="mt-4 pt-4 border-t border-slate-200">
                <div class="flex gap-2">
                    <form action="{{ route('admin.financial.approve', $financial) }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-semibold px-4 py-2 rounded">
                            Approve
                        </button>
                    </form>
                    <form action="{{ route('admin.financial.reject', $financial) }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white text-xs font-semibold px-4 py-2 rounded">
                            Reject
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>

    @if($financial->documents->count() > 0)
    <div class="mt-6 bg-white border border-slate-100 rounded-lg p-4">
        <h2 class="text-sm font-semibold text-slate-900 mb-4">Attached Documents</h2>
        <div class="space-y-2">
            @foreach($financial->documents as $document)
                <div class="flex items-center justify-between p-3 border border-slate-200 rounded-lg">
                    <div>
                        <p class="text-sm font-medium text-slate-900">{{ $document->original_name }}</p>
                        <p class="text-xs text-slate-500">Uploaded by {{ $document->uploader->name }} on {{ $document->created_at->format('d M Y') }}</p>
                    </div>
                    <a href="{{ Storage::url($document->file_path) }}" target="_blank"
                       class="text-emerald-600 hover:text-emerald-700 text-xs font-medium">
                        View/Download
                    </a>
                </div>
            @endforeach
        </div>
    </div>
    @endif
@endsection
