@extends('layouts.admin')

@section('page_title', 'Penalty Actions')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
        <div>
            <h1 class="text-lg font-semibold">Penalty Actions</h1>
            <p class="text-xs text-slate-500">Manual penalties, waivers, and penalty pauses for members.</p>
        </div>
        <a href="{{ route('admin.penalties.create') }}"
           class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-4 py-2 rounded-lg shadow-sm">
            New Penalty Action
        </a>
    </div>

    <form method="GET" class="mb-4 bg-white border border-slate-100 rounded-lg p-3 text-xs flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-[10px] font-semibold mb-1 text-slate-700">Status</label>
            <select name="status" class="border border-slate-200 rounded px-3 py-2 text-sm">
                <option value="">All</option>
                <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                <option value="approved" @selected(request('status') === 'approved')>Approved</option>
                <option value="rejected" @selected(request('status') === 'rejected')>Rejected</option>
            </select>
        </div>
        <div>
            <label class="block text-[10px] font-semibold mb-1 text-slate-700">Type</label>
            <select name="type" class="border border-slate-200 rounded px-3 py-2 text-sm">
                <option value="">All</option>
                <option value="apply" @selected(request('type') === 'apply')>Apply Penalty</option>
                <option value="waive" @selected(request('type') === 'waive')>Waive Penalty</option>
                <option value="pause" @selected(request('type') === 'pause')>Pause Penalties</option>
            </select>
        </div>
        <div>
            <label class="block text-[10px] font-semibold mb-1 text-slate-700">Partner</label>
            <select name="partner_id" class="border border-slate-200 rounded px-3 py-2 text-sm">
                <option value="">All</option>
                @foreach($partners as $partner)
                    <option value="{{ $partner->id }}" @selected(request('partner_id') == $partner->id)>
                        {{ $partner->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit"
                class="bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2 rounded text-xs">
            Filter
        </button>
    </form>

    <div class="bg-white border border-slate-100 rounded-lg overflow-hidden">
        <div class="overflow-x-auto text-sm">
            <table class="min-w-full">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">Partner</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">Scope</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">Target Period</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">Reason</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($adjustments as $adj)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 text-xs text-slate-600">
                                {{ $adj->created_at->format('d M Y') }}<br>
                                <span class="text-[10px] text-slate-400">{{ $adj->created_at->format('H:i') }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm font-medium text-slate-900">
                                {{ $adj->partner->name }}
                            </td>
                            <td class="px-4 py-3 text-xs capitalize">
                                {{ $adj->type === 'apply' ? 'Apply Penalty' : ($adj->type === 'waive' ? 'Waive Penalty' : 'Pause Penalties') }}
                            </td>
                            <td class="px-4 py-3 text-xs">
                                {{ str_replace('_', ' ', $adj->scope) }}
                            </td>
                            <td class="px-4 py-3 text-xs">
                                @if($adj->type === 'pause')
                                    {{ $adj->paused_from?->format('d M Y') }}
                                    –
                                    {{ $adj->paused_to?->format('d M Y') ?? 'Open ended' }}
                                @else
                                    @if($adj->target_year && $adj->target_month)
                                        {{ \Carbon\Carbon::create($adj->target_year, $adj->target_month, 1)->format('M Y') }}
                                    @else
                                        —
                                    @endif
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if($adj->amount)
                                    Ksh {{ number_format($adj->amount, 2) }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs">
                                <span class="inline-flex items-center rounded-full px-2 py-1 text-[10px] font-semibold
                                    @if($adj->status === 'approved') bg-emerald-100 text-emerald-700
                                    @elseif($adj->status === 'rejected') bg-red-100 text-red-700
                                    @else bg-amber-100 text-amber-700 @endif">
                                    {{ ucfirst($adj->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-600 max-w-xs">
                                {{ \Illuminate\Support\Str::limit($adj->reason, 80) }}
                            </td>
                            <td class="px-4 py-3 text-xs">
                                @if($adj->status === 'pending')
                                    <form action="{{ route('admin.penalties.approve', $adj) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-emerald-600 hover:text-emerald-700 mr-2">Approve</button>
                                    </form>
                                    <form action="{{ route('admin.penalties.reject', $adj) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-red-600 hover:text-red-700">Reject</button>
                                    </form>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-8 text-center text-slate-500 text-sm">
                                No penalty actions found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $adjustments->links() }}
    </div>
@endsection

