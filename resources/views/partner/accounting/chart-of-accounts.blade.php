@extends('layouts.partner')

@section('page_title', 'Chart of Accounts')

@section('partner_content')
    <div class="mb-4">
        <h1 class="text-lg font-semibold">Chart of Accounts</h1>
        <p class="text-xs text-slate-500">
            View all active accounts used in the accounting system.
        </p>
    </div>

    {{-- Accounts List --}}
    <div class="bg-white rounded-lg border border-slate-100 shadow-sm overflow-hidden">
        <div class="space-y-4 p-4">
            @foreach($accountTypes as $type)
                @if(isset($accounts[$type]) && $accounts[$type]->count() > 0)
                    <div>
                        <h3 class="text-xs font-semibold text-slate-700 mb-2 uppercase">{{ $type }}</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-[11px] responsive-table">
                                <thead class="bg-slate-50 border-b border-slate-200">
                                    <tr>
                                        <th class="px-3 py-2 text-left font-semibold text-slate-700">Code</th>
                                        <th class="px-3 py-2 text-left font-semibold text-slate-700">Name</th>
                                        <th class="px-3 py-2 text-left font-semibold text-slate-700">Parent</th>
                                        <th class="px-3 py-2 text-left font-semibold text-slate-700">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($accounts[$type] as $account)
                                        <tr>
                                            <td class="px-3 py-2 font-mono text-slate-900" data-label="Code">{{ $account->code }}</td>
                                            <td class="px-3 py-2 text-slate-700" data-label="Name">{{ $account->name }}</td>
                                            <td class="px-3 py-2 text-slate-600" data-label="Parent">
                                                @if($account->parent)
                                                    {{ $account->parent->code }}
                                                @else
                                                    <span class="text-slate-400">—</span>
                                                @endif
                                            </td>
                                            <td class="px-3 py-2" data-label="Status">
                                                <span class="px-2 py-1 text-[10px] font-semibold rounded {{ $account->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-800' }}">
                                                    {{ $account->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
@endsection
