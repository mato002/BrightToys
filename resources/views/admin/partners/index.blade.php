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

    <form method="GET" class="mb-4 bg-white border border-slate-100 rounded-lg p-3 text-xs">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div class="md:col-span-2">
                <label class="block text-[11px] font-medium text-slate-600 mb-1">Search</label>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search by name, email or phone..."
                       class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-amber-500">
            </div>

            <div>
                <label class="block text-[11px] font-medium text-slate-600 mb-1">Status</label>
                <select name="status"
                        class="border border-slate-200 rounded w-full px-2 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-amber-500">
                    <option value="">All</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-medium text-slate-600 mb-1">Ownership</label>
                <select name="ownership"
                        class="border border-slate-200 rounded w-full px-2 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-amber-500">
                    <option value="">All</option>
                    <option value="with_ownership" {{ request('ownership') === 'with_ownership' ? 'selected' : '' }}>With ownership set</option>
                    <option value="without_ownership" {{ request('ownership') === 'without_ownership' ? 'selected' : '' }}>Without ownership</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-medium text-slate-600 mb-1">Linked user</label>
                <select name="linked"
                        class="border border-slate-200 rounded w-full px-2 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-amber-500">
                    <option value="">All</option>
                    <option value="with_user" {{ request('linked') === 'with_user' ? 'selected' : '' }}>Has login</option>
                    <option value="without_user" {{ request('linked') === 'without_user' ? 'selected' : '' }}>No login yet</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mt-3">
            <div>
                <label class="block text-[11px] font-medium text-slate-600 mb-1">Onboarding state</label>
                <select name="onboarding"
                        class="border border-slate-200 rounded w-full px-2 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-amber-500">
                    <option value="">All</option>
                    <option value="link_active" {{ request('onboarding') === 'link_active' ? 'selected' : '' }}>Link active</option>
                    <option value="expired" {{ request('onboarding') === 'expired' ? 'selected' : '' }}>Link expired</option>
                    <option value="completed" {{ request('onboarding') === 'completed' ? 'selected' : '' }}>Onboarding completed</option>
                </select>
            </div>
        </div>

        <div class="mt-3 flex items-center gap-2">
            <button type="submit"
                    class="inline-flex items-center px-3 py-2 rounded bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold">
                Apply filters
            </button>
            <a href="{{ route('admin.partners.index') }}"
               class="text-xs text-slate-500 hover:text-slate-700">
                Clear
            </a>
        </div>
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
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">Linked User</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">Onboarding</th>
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
                                @if($partner->user)
                                    <span class="inline-flex items-center rounded-full px-2 py-1 text-[10px] font-medium bg-blue-50 text-blue-700">
                                        {{ $partner->user->email }}
                                    </span>
                                @else
                                    <span class="text-xs text-slate-400">No login</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full px-2 py-1 text-[10px] font-medium
                                    {{ $partner->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                    {{ ucfirst($partner->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-600">
                                @if($partner->biodata_completed_at)
                                    <span class="text-emerald-600">Biodata completed {{ $partner->biodata_completed_at->diffForHumans() }}</span>
                                @elseif($partner->onboarding_token && $partner->onboarding_token_expires_at && $partner->onboarding_token_expires_at->isFuture())
                                    <button type="button"
                                            class="text-emerald-600 hover:text-emerald-700 underline"
                                            onclick="navigator.clipboard.writeText('{{ url('/onboarding/'.$partner->onboarding_token) }}'); alert('Onboarding link copied to clipboard!');">
                                        Copy link
                                    </button>
                                    <div class="text-[10px] text-slate-500 mt-1">Expires {{ $partner->onboarding_token_expires_at->diffForHumans() }}</div>
                                @elseif($partner->onboarding_token && $partner->onboarding_token_expires_at && $partner->onboarding_token_expires_at->isPast())
                                    <span class="text-red-600">Link expired</span>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
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
                            <td colspan="6" class="px-4 py-8 text-center text-slate-500 text-sm">
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
