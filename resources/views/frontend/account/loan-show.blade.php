@extends('layouts.account')

@section('title', 'Loan Details')
@section('page_title', 'Loan Details')

@section('content')
    <div class="space-y-4">
        <div class="bg-white border rounded-lg p-5">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <a href="{{ route('account.loans') }}" class="text-slate-500 hover:text-slate-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M19 12H5M12 19l-7-7 7-7" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-lg font-semibold">{{ $loan->lender_name }}</h1>
                        <p class="text-xs text-slate-500 mt-1">Read-only view of loan details, schedule, and repayments</p>
                    </div>
                </div>
                <span class="inline-flex items-center px-3 py-1.5 rounded-md border border-slate-300 bg-slate-50 text-xs text-slate-600">
                    Read-Only View
                </span>
            </div>

            <div class="grid md:grid-cols-3 gap-4 mb-4">
                <div class="bg-slate-50 border border-slate-200 rounded-lg p-4 text-xs">
                    <p class="text-[11px] font-semibold text-slate-500 mb-2">Loan Summary</p>
                    <div class="space-y-1.5">
                        <p class="flex justify-between">
                            <span class="text-slate-500">Lender</span>
                            <span class="font-semibold text-slate-900">{{ $loan->lender_name }}</span>
                        </p>
                        <p class="flex justify-between">
                            <span class="text-slate-500">Amount</span>
                            <span class="font-semibold text-slate-900">Ksh {{ number_format($loan->amount, 0) }}</span>
                        </p>
                        <p class="flex justify-between">
                            <span class="text-slate-500">Interest</span>
                            <span class="font-semibold text-slate-900">{{ number_format($loan->interest_rate * 100, 2) }}% p.a.</span>
                        </p>
                        <p class="flex justify-between">
                            <span class="text-slate-500">Tenure</span>
                            <span class="font-semibold text-slate-900">{{ $loan->tenure_months }} months</span>
                        </p>
                        <p class="flex justify-between">
                            <span class="text-slate-500">Remaining</span>
                            <span class="font-semibold {{ $remainingTenure <= 0 ? 'text-red-600' : 'text-slate-900' }}">
                                {{ $remainingTenure }} months
                            </span>
                        </p>
                        @if($loan->project)
                            <p class="flex justify-between">
                                <span class="text-slate-500">Project</span>
                                <span class="font-semibold text-slate-900">{{ $loan->project->name }}</span>
                            </p>
                        @endif
                        <div class="pt-2 mt-2 border-t border-slate-200">
                            <p class="flex justify-between mb-1">
                                <span class="text-slate-500">Outstanding Principal</span>
                                <span class="font-semibold text-emerald-700">Ksh {{ number_format($principalOutstanding, 0) }}</span>
                            </p>
                            <p class="flex justify-between mb-1">
                                <span class="text-slate-500">Outstanding Interest</span>
                                <span class="font-semibold text-amber-700">Ksh {{ number_format($interestOutstanding, 0) }}</span>
                            </p>
                            <p class="flex justify-between mt-1 pt-1 border-t border-slate-200">
                                <span class="text-slate-600 font-semibold">Total Outstanding</span>
                                <span class="font-bold text-red-600">Ksh {{ number_format($totalOutstanding, 0) }}</span>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-50 border border-slate-200 rounded-lg p-4 text-xs">
                    <p class="text-[11px] font-semibold text-slate-500 mb-2">Loan Status</p>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-[10px] font-medium mb-3
                        @if($status === 'repaid') bg-blue-50 text-blue-700 border border-blue-100
                        @elseif($status === 'in_arrears') bg-red-50 text-red-700 border border-red-100
                        @else bg-emerald-50 text-emerald-700 border border-emerald-100 @endif">
                        @if($status === 'repaid')
                            Fully Repaid
                        @elseif($status === 'in_arrears')
                            Behind Schedule
                        @else
                            On Track
                        @endif
                    </span>
                    <p class="text-[11px] text-slate-500 mt-2">
                        Status is automatically calculated based on scheduled vs actual repayments.
                    </p>
                    @php
                        $totalScheduled = $loan->schedules->sum('total_due');
                        $totalPaid = $loan->repayments->sum('amount_paid');
                        $progress = $totalScheduled > 0 ? ($totalPaid / $totalScheduled) * 100 : 0;
                    @endphp
                    <div class="mt-3 pt-3 border-t border-slate-200">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-[10px] text-slate-600">Repayment Progress</span>
                            <span class="text-[10px] font-semibold text-slate-900">{{ number_format($progress, 1) }}%</span>
                        </div>
                        <div class="w-full bg-slate-200 rounded-full h-2">
                            <div class="bg-emerald-600 h-2 rounded-full transition-all" style="width: {{ $progress }}%"></div>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-50 border border-slate-200 rounded-lg p-4 text-xs">
                    <p class="text-[11px] font-semibold text-slate-500 mb-2">Quick Info</p>
                    <div class="space-y-1.5 text-[11px] text-slate-600">
                        <p>Created: {{ $loan->created_at->format('M d, Y') }}</p>
                        <p>Last Updated: {{ $loan->updated_at->format('M d, Y') }}</p>
                        @if($loan->start_date)
                            <p>Start Date: {{ $loan->start_date->format('M d, Y') }}</p>
                        @endif
                        <p>Repayment Frequency: {{ ucfirst($loan->repayment_frequency) }}</p>
                    </div>
                </div>
            </div>

            {{-- Amortization Schedule --}}
            <div class="bg-white border border-slate-200 rounded-lg p-4 shadow-sm mb-4">
                <h2 class="text-sm font-semibold text-slate-900 mb-3">Amortization Schedule</h2>
                @if($loan->schedules->isEmpty())
                    <div class="text-center py-8 text-sm text-slate-500">
                        <p class="mb-2">No amortization schedule has been generated for this loan yet.</p>
                        <p class="text-xs">Please contact management to generate the schedule.</p>
                    </div>
                @else
                    <div class="overflow-x-auto text-xs">
                        <table class="min-w-full">
                            <thead class="bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th class="px-3 py-2 text-left">Period #</th>
                                <th class="px-3 py-2 text-left">Due Date</th>
                                <th class="px-3 py-2 text-right">Principal</th>
                                <th class="px-3 py-2 text-right">Interest</th>
                                <th class="px-3 py-2 text-right">Total Due</th>
                                <th class="px-3 py-2 text-right">Paid</th>
                                <th class="px-3 py-2 text-left">Status</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                            @foreach($loan->schedules as $schedule)
                                @php
                                    $paid = $schedule->repayments->sum('amount_paid');
                                    $expected = $schedule->total_due;
                                    $diff = $expected - $paid;
                                    $onTrack = abs($diff) < 1;
                                @endphp
                                <tr class="hover:bg-slate-50">
                                    <td class="px-3 py-2 font-medium">{{ $schedule->period_number }}</td>
                                    <td class="px-3 py-2">{{ $schedule->due_date->format('M d, Y') }}</td>
                                    <td class="px-3 py-2 text-right font-semibold text-slate-900">Ksh {{ number_format($schedule->principal_due, 0) }}</td>
                                    <td class="px-3 py-2 text-right text-slate-700">Ksh {{ number_format($schedule->interest_due, 0) }}</td>
                                    <td class="px-3 py-2 text-right font-semibold text-slate-900">Ksh {{ number_format($expected, 0) }}</td>
                                    <td class="px-3 py-2 text-right {{ $paid > 0 ? 'text-emerald-700 font-medium' : 'text-slate-500' }}">Ksh {{ number_format($paid, 0) }}</td>
                                    <td class="px-3 py-2">
                                        @if($onTrack)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">
                                                On track
                                            </span>
                                        @elseif($paid > 0)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-amber-50 text-amber-700 border border-amber-100">
                                                Partially paid
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-red-50 text-red-700 border border-red-100">
                                                Unpaid
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot class="bg-slate-50 border-t-2 border-slate-200">
                            <tr>
                                <td colspan="2" class="px-3 py-2 font-semibold text-slate-900">Total</td>
                                <td class="px-3 py-2 text-right font-semibold text-slate-900">Ksh {{ number_format($loan->schedules->sum('principal_due'), 0) }}</td>
                                <td class="px-3 py-2 text-right font-semibold text-slate-900">Ksh {{ number_format($loan->schedules->sum('interest_due'), 0) }}</td>
                                <td class="px-3 py-2 text-right font-semibold text-slate-900">Ksh {{ number_format($loan->schedules->sum('total_due'), 0) }}</td>
                                <td class="px-3 py-2 text-right font-semibold text-emerald-700">Ksh {{ number_format($loan->schedules->sum(function($s) { return $s->repayments->sum('amount_paid'); }), 0) }}</td>
                                <td class="px-3 py-2"></td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                @endif
            </div>

            {{-- Repayments --}}
            <div class="bg-white border border-slate-200 rounded-lg p-4 shadow-sm">
                <h2 class="text-sm font-semibold text-slate-900 mb-3">Repayments & Reconciliation Status</h2>
                <div class="space-y-2 text-xs">
                    @forelse($loan->repayments()->latest()->get() as $repayment)
                        <div class="flex items-start justify-between p-3 rounded-lg border border-slate-200 hover:bg-slate-50">
                            <div class="flex-1">
                                <p class="font-semibold text-slate-900">
                                    Ksh {{ number_format($repayment->amount_paid, 0) }}
                                    <span class="text-[10px] text-slate-500 font-normal">on {{ $repayment->date_paid->format('M d, Y') }}</span>
                                </p>
                                <p class="text-[11px] text-slate-500 mt-1">
                                    @if($repayment->bank_reference)
                                        Reference: <span class="font-medium">{{ $repayment->bank_reference }}</span>
                                    @else
                                        No reference provided
                                    @endif
                                    @if($repayment->document_path)
                                        · <a href="{{ asset('storage/' . $repayment->document_path) }}" target="_blank"
                                             class="text-emerald-600 hover:text-emerald-700 underline">View document</a>
                                    @endif
                                </p>
                                @if($repayment->reconciliation_note)
                                    <p class="text-[10px] text-slate-500 mt-1">
                                        Note: {{ $repayment->reconciliation_note }}
                                    </p>
                                @endif
                            </div>
                            <div class="text-right ml-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium
                                    @if($repayment->reconciliation_status === 'green') bg-emerald-50 text-emerald-700 border border-emerald-100
                                    @elseif($repayment->reconciliation_status === 'red') bg-red-50 text-red-700 border border-red-100
                                    @else bg-slate-50 text-slate-700 border border-slate-100
                                    @endif">
                                    @if($repayment->reconciliation_status === 'green')
                                        Tallies with bank
                                    @elseif($repayment->reconciliation_status === 'red')
                                        Mismatch – needs reconciliation
                                    @else
                                        Pending confirmation
                                    @endif
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-500 text-center py-4">
                            No repayments recorded yet.
                        </p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
