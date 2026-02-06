@extends('layouts.admin')

@section('page_title', 'Partner Details')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
        <div>
            <h1 class="text-lg font-semibold">{{ $partner->name }}</h1>
            <p class="text-xs text-slate-500">Partner details and ownership information.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.partners.edit', $partner) }}"
               class="bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold px-4 py-2 rounded">
                Edit Partner
            </a>
            <a href="{{ route('admin.partners.index') }}" class="text-sm text-slate-600 hover:text-slate-800">
                Back to list
            </a>
        </div>
    </div>

    <div class="grid md:grid-cols-2 gap-6">
        <div class="bg-white border border-slate-100 rounded-lg p-4">
            <h2 class="text-sm font-semibold text-slate-900 mb-4">Partner Information</h2>
            <dl class="space-y-3 text-sm">
                <div>
                    <dt class="text-xs text-slate-500 mb-1">Name</dt>
                    <dd class="font-medium text-slate-900">{{ $partner->name }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-500 mb-1">Email</dt>
                    <dd class="text-slate-700">{{ $partner->email ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-500 mb-1">Phone</dt>
                    <dd class="text-slate-700">{{ $partner->phone ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-500 mb-1">Status</dt>
                    <dd>
                        <span class="inline-flex items-center rounded-full px-2 py-1 text-[10px] font-medium
                            {{ $partner->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                            {{ ucfirst($partner->status) }}
                        </span>
                    </dd>
                </div>
                @if($partner->notes)
                <div>
                    <dt class="text-xs text-slate-500 mb-1">Notes</dt>
                    <dd class="text-slate-700">{{ $partner->notes }}</dd>
                </div>
                @endif
            </dl>
        </div>

        <div class="bg-white border border-slate-100 rounded-lg p-4">
            <h2 class="text-sm font-semibold text-slate-900 mb-4">Current Ownership</h2>
            @if($currentOwnership)
                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="text-xs text-slate-500 mb-1">Ownership Percentage</dt>
                        <dd class="font-semibold text-emerald-600 text-lg">{{ number_format($currentOwnership->percentage, 2) }}%</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-500 mb-1">Effective From</dt>
                        <dd class="text-slate-700">{{ $currentOwnership->effective_from->format('d M Y') }}</dd>
                    </div>
                    @if($currentOwnership->effective_to)
                    <div>
                        <dt class="text-xs text-slate-500 mb-1">Effective To</dt>
                        <dd class="text-slate-700">{{ $currentOwnership->effective_to->format('d M Y') }}</dd>
                    </div>
                    @endif
                </dl>
            @else
                <p class="text-sm text-slate-500">No ownership information available.</p>
            @endif
        </div>
    </div>

    @if($partner->contributions->count() > 0)
    <div class="mt-6 bg-white border border-slate-100 rounded-lg p-4">
        <h2 class="text-sm font-semibold text-slate-900 mb-4">Recent Contributions</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-slate-700">Date</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-slate-700">Type</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-slate-700">Amount</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-slate-700">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($partner->contributions as $contribution)
                        <tr>
                            <td class="px-4 py-2">{{ $contribution->contributed_at->format('d M Y') }}</td>
                            <td class="px-4 py-2">{{ ucfirst(str_replace('_', ' ', $contribution->type)) }}</td>
                            <td class="px-4 py-2 font-medium">{{ $contribution->currency }} {{ number_format($contribution->amount, 2) }}</td>
                            <td class="px-4 py-2">
                                <span class="inline-flex items-center rounded-full px-2 py-1 text-[10px] font-medium
                                    {{ $contribution->status === 'approved' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                    {{ ucfirst($contribution->status) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
@endsection
