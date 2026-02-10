@extends('layouts.admin')

@section('page_title', 'Members')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
        <div>
            <h1 class="text-lg font-semibold">Members</h1>
            <p class="text-xs text-slate-500">Registered members of the group, managed by the Chairperson.</p>
        </div>
        <a href="{{ route('admin.members.create') }}"
           class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-4 py-2 rounded-lg shadow-sm">
            Add Member
        </a>
    </div>

    {{-- Filters & search --}}
    <form method="GET" class="mb-4 bg-white border border-slate-100 rounded-lg p-3 text-xs">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div class="md:col-span-2">
                <label class="block text-[11px] font-medium text-slate-600 mb-1">Search</label>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search by name, email or phone..."
                       class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block text-[11px] font-medium text-slate-600 mb-1">Status</label>
                <select name="status"
                        class="border border-slate-200 rounded w-full px-2 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                    <option value="">All</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-medium text-slate-600 mb-1">Linked user</label>
                <select name="linked"
                        class="border border-slate-200 rounded w-full px-2 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                    <option value="">All</option>
                    <option value="with_user" {{ request('linked') === 'with_user' ? 'selected' : '' }}>Has login</option>
                    <option value="without_user" {{ request('linked') === 'without_user' ? 'selected' : '' }}>No login</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-medium text-slate-600 mb-1">Onboarding state</label>
                <select name="onboarding"
                        class="border border-slate-200 rounded w-full px-2 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
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
            <a href="{{ route('admin.members.index') }}"
               class="text-xs text-slate-500 hover:text-slate-700">
                Clear
            </a>
        </div>
    </form>

    <div class="bg-white border border-slate-100 rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-slate-700">Name</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-slate-700">Email</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-slate-700">Phone</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-slate-700">Status</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-slate-700">Linked Partner</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-slate-700">Onboarding</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-slate-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($members as $member)
                        <tr>
                            <td class="px-4 py-2 text-slate-900">{{ $member->name }}</td>
                            <td class="px-4 py-2 text-slate-700">{{ $member->email ?? '—' }}</td>
                            <td class="px-4 py-2 text-slate-700">{{ $member->phone ?? '—' }}</td>
                            <td class="px-4 py-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px]
                                    @if($member->status === 'active') bg-emerald-50 text-emerald-700 border border-emerald-100
                                    @elseif($member->status === 'pending') bg-amber-50 text-amber-700 border border-amber-100
                                    @else bg-slate-50 text-slate-700 border border-slate-100 @endif">
                                    {{ ucfirst($member->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-slate-700">
                                {{ optional($member->partner)->name ?? '—' }}
                            </td>
                            <td class="px-4 py-2 text-xs text-slate-600">
                                @if($member->biodata_completed_at)
                                    Biodata completed {{ $member->biodata_completed_at->diffForHumans() }}
                                @elseif($member->onboarding_token)
                                    Link sent,
                                    @if($member->onboarding_token_expires_at && $member->onboarding_token_expires_at->isFuture())
                                        expires {{ $member->onboarding_token_expires_at->diffForHumans() }}
                                    @else
                                        <span class="text-amber-600">link expired</span>
                                    @endif
                                @else
                                    Not started
                                @endif
                            </td>
                            <td class="px-4 py-2 text-xs">
                                @if($member->onboarding_token)
                                    <button type="button"
                                            class="text-emerald-600 hover:text-emerald-700 underline"
                                            onclick="navigator.clipboard.writeText('{{ url('/onboarding/'.$member->onboarding_token) }}');">
                                        Copy onboarding link
                                    </button>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-slate-500 text-sm">
                                No members have been registered yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $members->links() }}
    </div>
@endsection

