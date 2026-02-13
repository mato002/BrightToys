@extends('layouts.partner')

@section('page_title', 'Company Assets')

@section('partner_content')
    <div class="mb-4">
        <h1 class="text-lg font-semibold">Company Assets</h1>
        <p class="text-xs text-slate-500">
            View company assets and fixed asset records.
        </p>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
        <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm">
            <p class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold mb-1">Total Assets</p>
            <p class="text-2xl font-bold text-slate-900">{{ number_format($totalAssets ?? 0, 0) }}</p>
            <p class="text-[11px] text-slate-500 mt-1">Active assets</p>
        </div>

        <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm">
            <p class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold mb-1">Purchase Value</p>
            <p class="text-2xl font-bold text-amber-600">KES {{ number_format($totalPurchaseValue ?? 0, 2) }}</p>
            <p class="text-[11px] text-slate-500 mt-1">Total purchase cost</p>
        </div>

        <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm">
            <p class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold mb-1">Current Value</p>
            <p class="text-2xl font-bold text-emerald-600">KES {{ number_format($totalCurrentValue ?? 0, 2) }}</p>
            <p class="text-[11px] text-slate-500 mt-1">Depreciated value</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm mb-4">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Asset Type</label>
                <select name="asset_type" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <option value="">All Types</option>
                    @foreach($assetTypes ?? [] as $type)
                        <option value="{{ $type }}" @selected(request('asset_type') == $type)>{{ ucfirst($type) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Status</label>
                <select name="is_active" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <option value="">All</option>
                    <option value="1" @selected(request('is_active') == '1')>Active</option>
                    <option value="0" @selected(request('is_active') == '0')>Inactive</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, code, description..." class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Actions</label>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold px-4 py-2 rounded-lg">
                        Filter
                    </button>
                    <a href="{{ route('partner.accounting.assets') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-semibold px-4 py-2 rounded-lg">
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Assets Table --}}
    <div class="bg-white rounded-lg border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-[11px]">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold text-slate-700">Asset Code</th>
                        <th class="px-3 py-2 text-left font-semibold text-slate-700">Name</th>
                        <th class="px-3 py-2 text-left font-semibold text-slate-700">Type</th>
                        <th class="px-3 py-2 text-left font-semibold text-slate-700">Account</th>
                        <th class="px-3 py-2 text-right font-semibold text-slate-700">Purchase Value</th>
                        <th class="px-3 py-2 text-right font-semibold text-slate-700">Current Value</th>
                        <th class="px-3 py-2 text-left font-semibold text-slate-700">Purchase Date</th>
                        <th class="px-3 py-2 text-left font-semibold text-slate-700">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($assets ?? [] as $asset)
                        <tr class="hover:bg-slate-50">
                            <td class="px-3 py-2 font-mono text-slate-900">{{ $asset->asset_code }}</td>
                            <td class="px-3 py-2 text-slate-700">{{ $asset->name }}</td>
                            <td class="px-3 py-2">
                                <span class="px-2 py-1 text-[10px] font-semibold rounded bg-slate-100 text-slate-800">
                                    {{ ucfirst($asset->asset_type) }}
                                </span>
                            </td>
                            <td class="px-3 py-2 text-slate-600">
                                {{ $asset->account->code ?? 'N/A' }} - {{ $asset->account->name ?? 'N/A' }}
                            </td>
                            <td class="px-3 py-2 text-right text-slate-900">KES {{ number_format($asset->purchase_value, 2) }}</td>
                            <td class="px-3 py-2 text-right text-emerald-600">KES {{ number_format($asset->current_value ?? 0, 2) }}</td>
                            <td class="px-3 py-2 text-slate-600">{{ $asset->purchase_date->format('M d, Y') }}</td>
                            <td class="px-3 py-2">
                                <span class="px-2 py-1 text-[10px] font-semibold rounded {{ $asset->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-800' }}">
                                    {{ $asset->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-3 py-6 text-center text-slate-400 text-xs">
                                No assets found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($assets) && $assets->hasPages())
            <div class="px-4 py-3 border-t border-slate-100">
                {{ $assets->links() }}
            </div>
        @endif
    </div>
@endsection
