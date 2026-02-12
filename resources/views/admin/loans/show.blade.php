@extends('layouts.admin')

@section('page_title', 'Loan Details')

@section('content')
    {{-- Success/Error Messages --}}
    @if(session('success'))
        <div class="mb-4 rounded-lg bg-emerald-50 border border-emerald-200 p-3 text-sm text-emerald-800">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 p-3 text-sm text-red-800">
            {{ session('error') }}
        </div>
    @endif

    {{-- Warning for unresolved red entries --}}
    @php
        $unresolvedRedEntries = $loan->repayments()->where('reconciliation_status', 'red')
            ->where(function($q) {
                $q->whereNull('reconciliation_note')->orWhere('reconciliation_note', '');
            })->count();
    @endphp
    @if($unresolvedRedEntries > 0 && $loan->status !== 'repaid')
        <div class="mb-4 rounded-lg bg-amber-50 border border-amber-200 p-4">
            <div class="flex items-start gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-600 flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div class="flex-1">
                    <h3 class="text-sm font-semibold text-amber-900 mb-1">Unresolved Reconciliation Issues</h3>
                    <p class="text-xs text-amber-800">
                        There {{ $unresolvedRedEntries === 1 ? 'is' : 'are' }} <strong>{{ $unresolvedRedEntries }}</strong> red repayment {{ $unresolvedRedEntries === 1 ? 'entry' : 'entries' }} that require reconciliation notes before this loan can be marked as repaid.
                    </p>
                    <p class="text-xs text-amber-700 mt-1">
                        Please review the repayments below and add reconciliation notes for all red entries.
                    </p>
                </div>
            </div>
        </div>
    @endif

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
            @php
                $canManage = auth()->user()->hasPermission('loans.create') 
                    || auth()->user()->isSuperAdmin();
                $isReadOnly = !$canManage;
            @endphp
            @if($canManage)
                <a href="{{ route('admin.loans.edit', $loan) }}"
                   class="bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold px-4 py-2 rounded-lg">
                    Edit Loan
                </a>
            @endif
            @if($isReadOnly)
                <span class="inline-flex items-center px-3 py-1.5 rounded-md border border-slate-300 bg-slate-50 text-xs text-slate-600">
                    Read-Only View
                </span>
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
                <span class="text-slate-500">Remaining Tenure</span>
                <span class="font-semibold {{ $remainingTenure <= 0 ? 'text-red-600' : 'text-slate-900' }}">
                    {{ $remainingTenure }} months
                </span>
            </p>
            <p class="flex justify-between mb-1">
                <span class="text-slate-500">Project</span>
                <span class="font-semibold text-slate-900">{{ optional($loan->project)->name ?? '—' }}</span>
            </p>
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

        <div class="bg-white border border-slate-100 rounded-lg p-4 shadow-sm text-xs">
            <p class="text-[11px] font-semibold text-slate-500 mb-1">Loan Status</p>
            <span class="inline-flex items-center px-2 py-1 rounded-full text-[10px] font-medium
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
            <div class="mt-2 pt-2 border-t border-slate-200 text-[10px] text-slate-500">
                <p>Progress: {{ $loan->schedules->count() > 0 ? round(($loan->schedules->sum(function($s) { return $s->repayments->sum('amount_paid'); }) / $loan->schedules->sum('total_due')) * 100, 1) : 0 }}%</p>
            </div>
        </div>

        <div class="bg-white border border-slate-100 rounded-lg p-4 shadow-sm text-xs">
            <p class="text-[11px] font-semibold text-slate-500 mb-2">Record Repayment</p>
            @php
                $canRecordRepayment = !$isReadOnly && (
                    auth()->user()->hasPermission('loans.repayments.create') 
                    || auth()->user()->hasAdminRole('treasurer')
                    || auth()->user()->isSuperAdmin()
                );
            @endphp
            @if($canRecordRepayment)
                <form action="{{ route('admin.loans.repayments.store', $loan) }}" method="POST" enctype="multipart/form-data" class="space-y-2">
                    @csrf
                    <div>
                        @if($loan->schedules->isNotEmpty())
                            <label class="block text-[11px] text-slate-600 mb-1">Schedule Period <span class="text-red-500">*</span></label>
                            <select name="loan_schedule_id"
                                    class="border border-slate-200 rounded w-full px-2 py-1.5 text-xs focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 @error('loan_schedule_id') border-red-300 @enderror">
                                @foreach($loan->schedules as $s)
                                    <option value="{{ $s->id }}" {{ old('loan_schedule_id') == $s->id ? 'selected' : '' }}>
                                        Period #{{ $s->period_number }} – {{ $s->due_date->format('M d, Y') }} (Ksh {{ number_format($s->total_due, 0) }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-[10px] text-slate-500 mt-0.5">
                                All repayments must be linked to a schedule period so the system can compare expected vs actual amounts.
                            </p>
                        @else
                            <label class="block text-[11px] text-slate-600 mb-1">Schedule Period</label>
                            <select name="loan_schedule_id"
                                    class="border border-slate-200 rounded w-full px-2 py-1.5 text-xs focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 @error('loan_schedule_id') border-red-300 @enderror">
                                <option value="">Unspecified (no schedule available)</option>
                            </select>
                            <p class="text-[10px] text-amber-600 mt-0.5">
                                No amortization schedule exists yet. You can record repayments but they will not be auto-compared to an expected amount.
                            </p>
                        @endif
                        @error('loan_schedule_id')
                            <p class="text-[10px] text-red-600 mt-0.5">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[11px] text-slate-600 mb-1">Date Paid <span class="text-red-500">*</span></label>
                            <input type="date" name="date_paid"
                                   value="{{ old('date_paid', now()->format('Y-m-d')) }}"
                                   class="border border-slate-200 rounded w-full px-2 py-1.5 text-xs focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 @error('date_paid') border-red-300 @enderror">
                            @error('date_paid')
                                <p class="text-[10px] text-red-600 mt-0.5">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-[11px] text-slate-600 mb-1">Amount Paid (Ksh) <span class="text-red-500">*</span></label>
                            <input type="number" step="0.01" min="0.01" name="amount_paid" value="{{ old('amount_paid') }}"
                                   placeholder="0.00"
                                   class="border border-slate-200 rounded w-full px-2 py-1.5 text-xs focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 @error('amount_paid') border-red-300 @enderror">
                            @error('amount_paid')
                                <p class="text-[10px] text-red-600 mt-0.5">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div>
                        <label class="block text-[11px] text-slate-600 mb-1">Bank Reference</label>
                        <input type="text" name="bank_reference" value="{{ old('bank_reference') }}"
                               placeholder="e.g., M-Pesa Ref: ABC123XYZ"
                               class="border border-slate-200 rounded w-full px-2 py-1.5 text-xs focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 @error('bank_reference') border-red-300 @enderror">
                        @error('bank_reference')
                            <p class="text-[10px] text-red-600 mt-0.5">{{ $message }}</p>
                        @enderror
                        <p class="text-[10px] text-slate-500 mt-0.5">Transaction reference from bank statement or payment receipt.</p>
                    </div>
                    <div>
                        <label class="block text-[11px] text-slate-600 mb-1">Supporting Document</label>
                        <input type="file" name="document" accept=".pdf,.jpg,.jpeg,.png"
                               class="border border-slate-200 rounded w-full px-2 py-1.5 text-xs focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 @error('document') border-red-300 @enderror">
                        @error('document')
                            <p class="text-[10px] text-red-600 mt-0.5">{{ $message }}</p>
                        @enderror
                        <p class="text-[10px] text-slate-500 mt-1">Upload bank statement or receipt (PDF/JPG/PNG, max 10MB).</p>
                    </div>
                    <div class="pt-1">
                        <button type="submit"
                                class="w-full bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition-colors">
                            <span class="flex items-center justify-center gap-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M5 13l4 4L19 7"/>
                                </svg>
                                Record Repayment
                            </span>
                        </button>
                    </div>
                </form>
            @else
                <div class="text-center py-4">
                    <p class="text-[11px] text-slate-500 mb-2">
                        You do not have permission to record repayments.
                    </p>
                    <p class="text-[10px] text-slate-400">
                        Only Treasurer and authorized management can record loan repayments.
                    </p>
                </div>
            @endif
        </div>
    </div>

    {{-- Amortization schedule --}}
    <div class="bg-white border border-slate-100 rounded-lg p-4 shadow-sm mb-4">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-sm font-semibold text-slate-900">Amortization Schedule</h2>
            @if($loan->schedules->isEmpty() && auth()->user()->hasPermission('loans.create'))
                <form action="{{ route('admin.loans.update', $loan) }}" method="POST" class="inline">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="lender_name" value="{{ $loan->lender_name }}">
                    <input type="hidden" name="amount" value="{{ $loan->amount }}">
                    <input type="hidden" name="interest_rate" value="{{ $loan->interest_rate }}">
                    <input type="hidden" name="tenure_months" value="{{ $loan->tenure_months }}">
                    <input type="hidden" name="repayment_frequency" value="{{ $loan->repayment_frequency }}">
                    <input type="hidden" name="project_id" value="{{ $loan->project_id }}">
                    <input type="hidden" name="start_date" value="{{ $loan->start_date?->format('Y-m-d') }}">
                    <input type="hidden" name="status" value="{{ $loan->status }}">
                    <button type="submit" class="text-xs bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-3 py-1.5 rounded-lg">
                        Generate Schedule
                    </button>
                </form>
            @endif
        </div>
        @if($loan->schedules->isEmpty())
            <div class="text-center py-8 text-sm text-slate-500">
                <p class="mb-2">No amortization schedule has been generated for this loan yet.</p>
                @if(auth()->user()->hasPermission('loans.create'))
                    <p class="text-xs">Click "Generate Schedule" above or edit the loan to regenerate the schedule.</p>
                @else
                    <p class="text-xs">Please contact management to generate the schedule.</p>
                @endif
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
                            $onTrack = abs($diff) < 1; // simple tolerance
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

    {{-- Repayments & reconciliation --}}
    <div class="bg-white border border-slate-100 rounded-lg p-4 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-sm font-semibold text-slate-900">Repayments & Reconciliation</h2>
            @php
                $unresolvedRed = $loan->repayments()->where('reconciliation_status', 'red')
                    ->where(function($q) {
                        $q->whereNull('reconciliation_note')->orWhere('reconciliation_note', '');
                    })->count();
            @endphp
            @if($unresolvedRed > 0)
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium bg-red-50 text-red-700 border border-red-200">
                    {{ $unresolvedRed }} unresolved red {{ $unresolvedRed === 1 ? 'entry' : 'entries' }}
                </span>
            @endif
        </div>
        <div class="space-y-3 text-xs">
            @forelse($loan->repayments()->latest()->get() as $repayment)
                @php
                    $schedule = $repayment->schedule;
                    $expectedAmount = $schedule ? $schedule->total_due : null;
                    $actualAmount = $repayment->amount_paid;
                    $difference = $expectedAmount ? abs($expectedAmount - $actualAmount) : null;
                @endphp
                <div class="p-3 rounded-lg border {{ $repayment->reconciliation_status === 'red' ? 'border-red-200 bg-red-50/30' : ($repayment->reconciliation_status === 'green' ? 'border-emerald-200 bg-emerald-50/30' : 'border-slate-200') }}">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <p class="font-semibold text-slate-900">
                                    Ksh {{ number_format($repayment->amount_paid, 2) }}
                                </p>
                                <span class="text-[10px] text-slate-500">on {{ $repayment->date_paid->format('M d, Y') }}</span>
                            </div>
                            
                            {{-- Expected vs Actual Comparison --}}
                            @if($expectedAmount !== null)
                                <div class="mt-2 p-2 bg-slate-50 rounded border border-slate-200">
                                    <div class="grid grid-cols-2 gap-2 text-[11px]">
                                        <div>
                                            <span class="text-slate-500">Expected:</span>
                                            <span class="font-semibold text-slate-900 ml-1">Ksh {{ number_format($expectedAmount, 2) }}</span>
                                        </div>
                                        <div>
                                            <span class="text-slate-500">Actual:</span>
                                            <span class="font-semibold text-slate-900 ml-1">Ksh {{ number_format($actualAmount, 2) }}</span>
                                        </div>
                                        @if($difference !== null && $difference > 0.01)
                                            <div class="col-span-2">
                                                <span class="text-red-600 font-medium">Difference: Ksh {{ number_format($difference, 2) }}</span>
                                            </div>
                                        @elseif($difference !== null && $difference <= 0.01)
                                            <div class="col-span-2">
                                                <span class="text-emerald-600 font-medium">✓ Amounts match</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <div class="mt-2 space-y-1">
                                <p class="text-[11px] text-slate-500">
                                    <span class="font-medium">Ref:</span> {{ $repayment->bank_reference ?? '—' }}
                                    @if($repayment->document_path)
                                        · <a href="{{ asset('storage/' . $repayment->document_path) }}" target="_blank"
                                             class="text-emerald-600 hover:text-emerald-700 underline">View document</a>
                                    @endif
                                </p>
                                @if($repayment->schedule)
                                    <p class="text-[11px] text-slate-500">
                                        <span class="font-medium">Period:</span> #{{ $repayment->schedule->period_number }} (Due: {{ $repayment->schedule->due_date->format('M d, Y') }})
                                    </p>
                                @endif
                            </div>
                            
                            @if($repayment->reconciliation_note)
                                <div class="mt-2 p-2 bg-slate-100 rounded border border-slate-200">
                                    <p class="text-[10px] font-medium text-slate-600 mb-0.5">Reconciliation Note:</p>
                                    <p class="text-[11px] text-slate-700">{{ $repayment->reconciliation_note }}</p>
                                </div>
                            @endif
                        </div>
                        <div class="flex-shrink-0 text-right">
                            <p class="mb-2">
                                @if($repayment->reconciliation_status === 'green')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        ✓ Tallies with bank
                                    </span>
                                @elseif($repayment->reconciliation_status === 'red')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium bg-red-50 text-red-700 border border-red-200">
                                        ⚠ Mismatch – needs reconciliation
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                        ⏳ Pending confirmation
                                    </span>
                                @endif
                            </p>
                            @if(!$isReadOnly && auth()->user()->hasPermission('loans.repayments.reconcile'))
                                <form action="{{ route('admin.loans.repayments.reconcile', [$loan, $repayment]) }}" method="POST" class="space-y-2 min-w-[200px]">
                                    @csrf
                                    <select name="status" id="status_{{ $repayment->id }}"
                                            class="border border-slate-200 rounded-md w-full px-2 py-1.5 text-[11px] focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500"
                                            onchange="toggleNoteRequired({{ $repayment->id }})">
                                        <option value="green" {{ $repayment->reconciliation_status === 'green' ? 'selected' : '' }}>Mark Green (tallies)</option>
                                        <option value="red" {{ $repayment->reconciliation_status === 'red' ? 'selected' : '' }}>Mark Red (mismatch)</option>
                                    </select>
                                    <textarea name="reconciliation_note" id="note_{{ $repayment->id }}" rows="3"
                                              class="border border-slate-200 rounded-md w-full px-2 py-1.5 text-[11px] focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 @error('reconciliation_note') border-red-300 @enderror"
                                              placeholder="{{ $repayment->reconciliation_status === 'red' ? 'Reconciliation note required for red entries...' : 'Add reconciliation note (required for mismatches)...' }}">{{ old('reconciliation_note', $repayment->reconciliation_note) }}</textarea>
                                    @error('reconciliation_note')
                                        <p class="text-[10px] text-red-600">{{ $message }}</p>
                                    @enderror
                                    <button type="submit"
                                            class="w-full bg-slate-900 hover:bg-slate-800 text-white text-[11px] font-semibold px-3 py-1.5 rounded-md transition-colors">
                                        Update Reconciliation
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-8">
                    <p class="text-xs text-slate-500">
                        No repayments recorded yet.
                    </p>
                </div>
            @endforelse
        </div>
    </div>

    <script>
        function toggleNoteRequired(repaymentId) {
            const statusSelect = document.getElementById('status_' + repaymentId);
            const noteTextarea = document.getElementById('note_' + repaymentId);
            
            if (statusSelect.value === 'red') {
                noteTextarea.required = true;
                noteTextarea.placeholder = 'Reconciliation note is REQUIRED for red entries. Please explain the mismatch.';
                noteTextarea.classList.add('border-amber-300', 'bg-amber-50');
            } else {
                noteTextarea.required = false;
                noteTextarea.placeholder = 'Add reconciliation note (optional for green entries)...';
                noteTextarea.classList.remove('border-amber-300', 'bg-amber-50');
            }
        }
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            @foreach($loan->repayments()->latest()->get() as $repayment)
                toggleNoteRequired({{ $repayment->id }});
            @endforeach
        });
    </script>

    {{-- Activity Log / Audit Trail --}}
    <div class="bg-white border border-slate-100 rounded-lg p-4 shadow-sm mt-4">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-sm font-semibold text-slate-900">Activity Log & Audit Trail</h2>
            <span class="text-[11px] text-slate-500">{{ $activityLogs->count() }} entries</span>
        </div>
        <div class="space-y-2 text-xs">
            @forelse($activityLogs as $log)
                <div class="flex items-start gap-3 p-3 rounded-lg border border-slate-200 hover:border-slate-300 hover:bg-slate-50 transition-colors">
                    <div class="flex-shrink-0 mt-0.5">
                        @php
                            $iconColor = match(true) {
                                str_contains($log->action, 'created') => 'text-emerald-600 bg-emerald-50',
                                str_contains($log->action, 'updated') => 'text-blue-600 bg-blue-50',
                                str_contains($log->action, 'recorded') => 'text-amber-600 bg-amber-50',
                                str_contains($log->action, 'reconciled') => 'text-purple-600 bg-purple-50',
                                default => 'text-slate-600 bg-slate-50'
                            };
                        @endphp
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg {{ $iconColor }}">
                            @if(str_contains($log->action, 'created'))
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 5v14M5 12h14"/>
                                </svg>
                            @elseif(str_contains($log->action, 'updated'))
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                </svg>
                            @elseif(str_contains($log->action, 'recorded'))
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M9 12l2 2 4-4"/>
                                    <path d="M21 12c0 4.97-4.03 9-9 9s-9-4.03-9-9 4.03-9 9-9 9 4.03 9 9z"/>
                                </svg>
                            @elseif(str_contains($log->action, 'reconciled'))
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M9 12l2 2 4-4"/>
                                    <path d="M21 12c0 4.97-4.03 9-9 9s-9-4.03-9-9 4.03-9 9-9 9 4.03 9 9z"/>
                                </svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2z"/>
                                    <path d="M12 6v6l4 2"/>
                                </svg>
                            @endif
                        </span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex-1">
                                <p class="font-semibold text-slate-900">
                                    {{ ucfirst(str_replace(['loan_', '_'], ['', ' '], $log->action)) }}
                                </p>
                                <p class="text-[11px] text-slate-500 mt-0.5">
                                    @if($log->user)
                                        <span class="font-medium">{{ $log->user->name }}</span>
                                        @if($log->user->email)
                                            <span class="text-slate-400">({{ $log->user->email }})</span>
                                        @endif
                                    @else
                                        <span class="text-slate-400">System</span>
                                    @endif
                                </p>
                                @if(!empty($log->details))
                                    @php
                                        $details = is_array($log->details) ? $log->details : json_decode($log->details, true);
                                    @endphp
                                    @if(isset($details['amount_paid']))
                                        <p class="text-[11px] text-slate-600 mt-1">
                                            Amount: <span class="font-semibold">Ksh {{ number_format($details['amount_paid'], 0) }}</span>
                                            @if(isset($details['date_paid']))
                                                · Date: {{ \Carbon\Carbon::parse($details['date_paid'])->format('M d, Y') }}
                                            @endif
                                        </p>
                                    @endif
                                    @if(isset($details['bank_reference']))
                                        <p class="text-[11px] text-slate-600">
                                            Reference: <span class="font-medium">{{ $details['bank_reference'] }}</span>
                                        </p>
                                    @endif
                                    @if(isset($details['reconciliation_status']))
                                        <p class="text-[11px] mt-1">
                                            Status: 
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium
                                                @if($details['reconciliation_status'] === 'green') bg-emerald-50 text-emerald-700 border border-emerald-200
                                                @elseif($details['reconciliation_status'] === 'red') bg-red-50 text-red-700 border border-red-200
                                                @else bg-slate-50 text-slate-700 border border-slate-200
                                                @endif">
                                                {{ $details['reconciliation_status'] === 'green' ? 'Tallies' : ($details['reconciliation_status'] === 'red' ? 'Mismatch' : 'Pending') }}
                                            </span>
                                        </p>
                                    @endif
                                @endif
                            </div>
                            <div class="flex-shrink-0 text-right">
                                <p class="text-[10px] text-slate-400">
                                    {{ $log->created_at->format('M d, Y') }}
                                </p>
                                <p class="text-[10px] text-slate-400">
                                    {{ $log->created_at->format('H:i:s') }}
                                </p>
                                @if($log->ip_address)
                                    <p class="text-[10px] text-slate-400 mt-1">
                                        IP: {{ $log->ip_address }}
                                    </p>
                                @endif
                            </div>
                        </div>
                        @if(!empty($log->details) && (!isset($details['amount_paid']) && !isset($details['bank_reference']) && !isset($details['reconciliation_status'])))
                            <details class="mt-2">
                                <summary class="text-[10px] text-slate-500 cursor-pointer hover:text-slate-700 font-medium">
                                    View Full Details
                                </summary>
                                <pre class="mt-2 p-2 bg-slate-50 rounded text-[10px] text-slate-600 overflow-x-auto border border-slate-200">{{ json_encode($log->details, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                            </details>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-8">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-slate-300 mx-auto mb-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2z"/>
                        <path d="M12 6v6l4 2"/>
                    </svg>
                    <p class="text-xs text-slate-500 font-medium">No activity logs available</p>
                    <p class="text-[10px] text-slate-400 mt-1">Activity will appear here as actions are performed on this loan.</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection

