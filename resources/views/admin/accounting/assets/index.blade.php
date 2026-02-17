@extends('layouts.admin')

@section('page_title', 'Company Assets')

@section('content')
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 mb-4">
        <div>
            <a href="{{ route('admin.accounting.dashboard') }}" class="text-emerald-600 hover:text-emerald-700 text-sm mb-2 inline-flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Books of Account
            </a>
            <h1 class="text-lg font-semibold text-slate-900">Company Assets</h1>
            <p class="text-xs text-slate-500">Register and manage company assets.</p>
        </div>
    </div>

    <div class="bg-white border border-slate-100 rounded-lg overflow-hidden">
        <div class="admin-table-scroll overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-slate-700">Asset Code</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-slate-700">Name</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-slate-700">Type</th>
                        <th class="px-4 py-2 text-right text-xs font-semibold text-slate-700">Purchase Value</th>
                        <th class="px-4 py-2 text-right text-xs font-semibold text-slate-700">Current Value</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-slate-700">Location</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-slate-700">Date Acquired</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($assets ?? [] as $asset)
                        <tr>
                            <td class="px-4 py-3 text-slate-700">{{ $asset['code'] }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $asset['name'] }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $asset['type'] }}</td>
                            <td class="px-4 py-3 text-right text-slate-700">Ksh {{ number_format($asset['purchase_value'], 2) }}</td>
                            <td class="px-4 py-3 text-right text-slate-700">Ksh {{ number_format($asset['current_value'], 2) }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $asset['location'] }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $asset['date_acquired'] ? \Carbon\Carbon::parse($asset['date_acquired'])->format('M d, Y') : 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-slate-500 text-sm">
                                No assets registered yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
