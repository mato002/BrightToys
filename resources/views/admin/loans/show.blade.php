@extends('layouts.admin')

@section('page_title', 'Loan Details')

@section('content')
    <div class="mb-4">
        <div class="flex items-center justify-between gap-3 mb-1">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.loans.index') }}" class="text-slate-500 hover:text-slate-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M19 12H5M12 19l-7-7 7-7" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
                <h1 class="text-lg font-semibold">Loan: {{ $loan->lender_name }}</h1>
            </div>
            @if(auth()->user()->hasPermission('loans.create'))
                <a href="{{ route('admin.loans.edit', $loan) }}"
                   class="bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold px-4 py-2 rounded-lg">
                    Edit Loan
                </a>
            @endif
        </div>
        <p class="text-xs text-slate-500">Overview, amortization schedule and repayments.</p>
    </div>

    <div class="grid md:grid-cols-3 gap-4 mb-4">
        <div class="bg-white border border-slate-100 rounded-lg p-4 shadow-sm text-xs">
            <p class="text-[11px] font-semibold text-slate-500 mb-1">Loan Summary</p>
            <p class="flex justify-between mb-1">
                <span class="text-slate-500">Lender</span>
                <span class="font-semibold text-slate-900">{{ $loan->lender_name }}</span>
            </p>
            <p class="flex justify-between mb-1">
                <span class="text-slate-500">Amount</span>
                <span class="font-semibold text-slate-900">Ksh {{ number_format($loan->amount, 0) }}</span>
            </p>
            <p class="flex justify-between mb-1">
                <span class="text-slate-500">Interest</span>
                <span class="font-semibold text-slate-900">{{ number_format($loan->interest_rate * 100, 2) }}% p.a.</span>
            </p>
            <p class="flex justify-between mb-1">
                <span class="text-slate-500">Tenure</span>
                <span class="font-semibold text-slate-900">{{ $loan->tenure_months }} months</span>
            </p>
            <p class="flex justify-between mb-1">
                <span class="text-slate-500">Project</span>
                <span class="font-semibold text-slate-900">{{ optional($loan->project)->name ?? '—' }}</span>
            </p>
            <p class="flex justify-between mb-1">
                <span class="text-slate-500">Outstanding Principal</span>
                <span class="font-semibold text-emerald-700">Ksh {{ number_format($principalOutstanding, 0) }}</span>
            </p>
        </div>

        <div class="bg-white border border-slate-100 rounded-lg p-4 shadow-sm text-xs">
            <p class="text-[11px] font-semibold text-slate-500 mb-1">Status</p>
            <span class="inline-flex items-center px-2 py-1 rounded-full text-[10px] font-medium
                @if($loan->status === 'active') bg-emerald-50 text-emerald-700 border border-emerald-100
                @elseif($loan->status === 'repaid') bg-blue-50 text-blue-700 border border-blue-100
                @else bg-amber-50 text-amber-700 border border-amber-100 @endif">
                {{ ucfirst(str_replace('_', ' ', $loan->status)) }}
            </span>
            <p class="text-[11px] text-slate-500 mt-2">
                This status reflects whether the loan is on track, behind schedule, or fully repaid based on
                scheduled vs actual repayments.
            </p>
        </div>

        <div class="bg-white border border-slate-100 rounded-lg p-4 shadow-sm text-xs">
            <p class="text-[11px] font-semibold text-slate-500 mb-2">Record Repayment</p>
            @if(auth()->user()->hasPermission('loans.repayments.create'))
                <form action="{{ route('admin.loans.repayments.store', $loan) }}" method="POST" enctype="multipart/form-data" class="space-y-2">
                    @csrf
                    <div>
                        <label class="block text-[11px] text-slate-600 mb-1">Schedule Period</label>
                        <select name="loan_schedule_id"
                                class="border border-slate-200 rounded w-full px-2 py-1.5 text-xs focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">Unspecified</option>
                            @foreach($loan->schedules as $s)
                                <option value="{{ $s->id }}">
                                    #{{ $s->period_number }} – {{ $s->due_date->format('M d, Y') }} (Ksh {{ number_format($s->total_due, 0) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[11px] text-slate-600 mb-1">Date</label>
                            <input type="date" name="date_paid"
                                   value="{{ old('date_paid', now()->format('Y-m-d')) }}"
                                   class="border border-slate-200 rounded w-full px-2 py-1.5 text-xs focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500">
                        </div>
                        <div>
                            <label class="block text-[11px] text-slate-600 mb-1">Amount (Ksh)</label>
                            <input type="number" step="0.01" min="0.01" name="amount_paid" value="{{ old('amount_paid') }}"
                                   class="border border-slate-200 rounded w-full px-2 py-1.5 text-xs focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[11px] text-slate-600 mb-1">Bank Reference</label>
                        <input type="text" name="bank_reference" value="{{ old('bank_reference') }}"
                               class="border border-slate-200 rounded w-full px-2 py-1.5 text-xs focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                    <div>
                        <label class="block text-[11px] text-slate-600 mb-1">Supporting Document</label>
                        <input type="file" name="document" accept=".pdf,.jpg,.jpeg,.png"
                               class="border border-slate-200 rounded w-full px-2 py-1.5 text-xs focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500">
                        <p class="text-[10px] text-slate-500 mt-1">Upload bank statement/receipt (PDF/JPG/PNG, max 10MB).</p>
                    </div>
                    <div class="pt-1">
                        <button type="submit"
                                class="w-full bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-3 py-1.5 rounded-lg">
                            Save Repayment
                        </button>
                    </div>
                </form>
            @else
                <p class="text-[11px] text-slate-500">
                    You do not have permission to record repayments.
                </p>
            @endif
        </div>
    </div>

    {{-- Amortization schedule --}}
    <div class="bg-white border border-slate-100 rounded-lg p-4 shadow-sm mb-4">
        <h2 class="text-sm font-semibold text-slate-900 mb-2">Amortization Schedule</h2>
        <div class="overflow-x-auto text-xs">
            <table class="min-w-full">
                <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-3 py-2 text-left">#</th>
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
                        $onTrack = abs($diff) < 1; // simple tolerance
                    @endphp
                    <tr>
                        <td class="px-3 py-2">{{ $schedule->period_number }}</td>
                        <td class="px-3 py-2">{{ $schedule->due_date->format('M d, Y') }}</td>
                        <td class="px-3 py-2 text-right">Ksh {{ number_format($schedule->principal_due, 0) }}</td>
                        <td class="px-3 py-2 text-right">Ksh {{ number_format($schedule->interest_due, 0) }}</td>
                        <td class="px-3 py-2 text-right">Ksh {{ number_format($expected, 0) }}</td>
                        <td class="px-3 py-2 text-right">Ksh {{ number_format($paid, 0) }}</td>
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
            </table>
        </div>
    </div>

    {{-- Repayments & reconciliation --}}
    <div class="bg-white border border-slate-100 rounded-lg p-4 shadow-sm">
        <h2 class="text-sm font-semibold text-slate-900 mb-3">Repayments & Reconciliation</h2>
        <div class="space-y-2 text-xs">
            @forelse($loan->repayments()->latest()->get() as $repayment)
                <div class="flex items-start justify-between p-2 rounded border border-slate-100">
                    <div>
                        <p class="font-semibold text-slate-900">
                            Ksh {{ number_format($repayment->amount_paid, 0) }}
                            <span class="text-[10px] text-slate-500">on {{ $repayment->date_paid->format('M d, Y') }}</span>
                        </p>
                        <p class="text-[11px] text-slate-500">
                            Ref: {{ $repayment->bank_reference ?? '—' }}
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
                    <div class="text-right">
                        <p class="mb-1">
                            @if($repayment->reconciliation_status === 'green')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">
                                    Tallies with bank
                                </span>
                            @elseif($repayment->reconciliation_status === 'red')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-red-50 text-red-700 border border-red-100">
                                    Mismatch – needs reconciliation
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-slate-50 text-slate-700 border border-slate-100">
                                    Pending confirmation
                                </span>
                            @endif
                        </p>
                        @if(auth()->user()->hasPermission('loans.repayments.reconcile'))
                            <form action="{{ route('admin.loans.repayments.reconcile', [$loan, $repayment]) }}" method="POST" class="space-y-1">
                                @csrf
                                <select name="status"
                                        class="border border-slate-200 rounded w-full px-2 py-1 text-[11px] focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500">
                                    <option value="green" {{ $repayment->reconciliation_status === 'green' ? 'selected' : '' }}>Mark Green (tallies)</option>
                                    <option value="red" {{ $repayment->reconciliation_status === 'red' ? 'selected' : '' }}>Mark Red (mismatch)</option>
                                </select>
                                <textarea name="reconciliation_note" rows="2"
                                          class="border border-slate-200 rounded w-full px-2 py-1 text-[11px] focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500"
                                          placeholder="Add reconciliation note (required for mismatches)">{{ old('reconciliation_note', $repayment->reconciliation_note) }}</textarea>
                                <button type="submit"
                                        class="w-full bg-slate-900 hover:bg-slate-800 text-white text-[11px] font-semibold px-2 py-1 rounded-lg">
                                    Update Reconciliation
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-xs text-slate-500">
                    No repayments recorded yet.
                </p>
            @endforelse
        </div>
    </div>
@endsection

