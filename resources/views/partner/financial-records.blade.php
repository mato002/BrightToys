@extends('layouts.app')

@section('title', 'Financial Records')

@section('content')
<div class="min-h-screen bg-slate-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-900">Financial Records</h1>
            <p class="text-sm text-slate-600 mt-1">Read-only view of approved financial records.</p>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">Type</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">Description</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($records as $record)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3">{{ $record->occurred_at->format('d M Y') }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-full px-2 py-1 text-[10px] font-medium
                                        {{ $record->type === 'expense' ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700' }}">
                                        {{ ucfirst(str_replace('_', ' ', $record->type)) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">{{ Str::limit($record->description ?? '—', 50) }}</td>
                                <td class="px-4 py-3 font-semibold
                                    {{ $record->type === 'expense' ? 'text-red-600' : 'text-emerald-600' }}">
                                    {{ $record->currency }} {{ number_format($record->amount, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-slate-500">
                                    No financial records available.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            {{ $records->links() }}
        </div>

        <div class="mt-6">
            <a href="{{ route('partner.dashboard') }}" class="text-sm text-emerald-600 hover:text-emerald-700">
                ← Back to Dashboard
            </a>
        </div>
    </div>
</div>
@endsection
