@extends('layouts.admin')

@section('page_title', 'Partners')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
        <div>
            <h1 class="text-lg font-semibold">Partners</h1>
            <p class="text-xs text-slate-500">View and manage partnership ownership and details. Partners create and manage their own projects.</p>
        </div>
        <a href="{{ route('admin.partners.create') }}"
           class="bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold px-4 py-2 rounded">
            Add Partner
        </a>
    </div>

    <form method="GET" class="mb-4 bg-white border border-slate-100 rounded-lg p-3 text-xs max-w-md">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search partners..."
               class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-amber-500">
    </form>

    @if(session('success'))
        <div class="mb-4 text-sm text-emerald-700 bg-emerald-50 border border-emerald-200 px-4 py-2 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white border border-slate-100 rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">Name</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">Email</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">Ownership</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($partners as $partner)
                        @php
                            $currentOwnership = $partner->ownerships()
                                ->where('effective_from', '<=', now())
                                ->where(function($q) {
                                    $q->whereNull('effective_to')->orWhere('effective_to', '>=', now());
                                })
                                ->first();
                        @endphp
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3">
                                <div class="font-medium text-slate-900">{{ $partner->name }}</div>
                                @if($partner->phone)
                                    <div class="text-xs text-slate-500">{{ $partner->phone }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-600">{{ $partner->email ?? '—' }}</td>
                            <td class="px-4 py-3">
                                @if($currentOwnership)
                                    <span class="font-semibold text-emerald-600">{{ number_format($currentOwnership->percentage, 2) }}%</span>
                                @else
                                    <span class="text-slate-400">Not set</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full px-2 py-1 text-[10px] font-medium
                                    {{ $partner->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                    {{ ucfirst($partner->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.partners.show', $partner) }}"
                                       class="text-emerald-600 hover:text-emerald-700 text-xs">View</a>
                                    <a href="{{ route('admin.partners.edit', $partner) }}"
                                       class="text-amber-600 hover:text-amber-700 text-xs">Edit</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-slate-500 text-sm">
                                No partners found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $partners->links() }}
    </div>
@endsection
