<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\LoanRepayment;
use App\Models\LoanSchedule;
use App\Models\Project;
use App\Services\ActivityLogService;
use App\Services\LoanScheduleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LoanController extends Controller
{
    protected function ensureCanView(): void
    {
        $user = auth()->user();
        // Allow viewing if user has permission, is a member, or is admin/partner
        if (! $user->hasPermission('loans.view') 
            && ! ($user->member ?? false)
            && ! ($user->is_admin ?? false)
            && ! ($user->is_partner ?? false)) {
            abort(403, 'You do not have permission to view loans.');
        }
    }

    protected function ensureCanManage(): void
    {
        if (! auth()->user()->hasPermission('loans.create')) {
            abort(403, 'You do not have permission to manage loans.');
        }
    }

    public function index()
    {
        $this->ensureCanView();

        $query = Loan::with('project');

        // Optional status filter
        if ($status = request('status')) {
            $query->where('status', $status);
        }

        // Simple search by lender or project name
        if ($search = request('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('lender_name', 'like', "%{$search}%")
                  ->orWhereHas('project', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $loans = $query->latest()->paginate(20)->withQueryString();

        return view('admin.loans.index', compact('loans'));
    }

    public function create()
    {
        $this->ensureCanManage();

        $projects = Project::orderBy('name')->get(['id', 'name']);

        return view('admin.loans.create', compact('projects'));
    }

    public function store(Request $request)
    {
        $this->ensureCanManage();

        $validated = $request->validate([
            'lender_name' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'interest_rate' => ['required', 'numeric', 'min:0'],
            'tenure_months' => ['required', 'integer', 'min:1'],
            'repayment_frequency' => ['required', 'string', 'max:50'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'start_date' => ['nullable', 'date'],
        ]);

        $loan = Loan::create($validated);

        LoanScheduleService::generateForLoan($loan);

        ActivityLogService::log('loan_created', $loan, $validated);

        return redirect()->route('admin.loans.show', $loan)
            ->with('success', 'Loan registered and schedule generated successfully.');
    }

    public function show(Loan $loan)
    {
        $this->ensureCanView();

        $loan->load(['project', 'schedules.repayments', 'repayments']);

        // Calculate outstanding balances (principal and interest)
        $totalPrincipalScheduled = $loan->schedules->sum('principal_due');
        $totalInterestScheduled = $loan->schedules->sum('interest_due');
        $totalScheduled = $totalPrincipalScheduled + $totalInterestScheduled;
        
        // Calculate total paid (from all repayments, not just linked to schedules)
        $totalPaid = $loan->repayments->sum('amount_paid');
        
        // Calculate principal paid (approximation: proportional to scheduled principal)
        $principalPaid = $totalScheduled > 0 
            ? ($totalPaid * ($totalPrincipalScheduled / $totalScheduled))
            : 0;
        
        $principalOutstanding = max(0, $totalPrincipalScheduled - $principalPaid);
        $interestOutstanding = max(0, $totalInterestScheduled - ($totalPaid - $principalPaid));
        $totalOutstanding = $principalOutstanding + $interestOutstanding;

        // Calculate remaining tenure
        $startDate = $loan->start_date ?? $loan->created_at;
        $endDate = $startDate->copy()->addMonths($loan->tenure_months);
        $today = now();
        $monthsElapsed = $startDate->diffInMonths($today);
        $remainingMonths = max(0, $loan->tenure_months - $monthsElapsed);
        $remainingTenure = $remainingMonths;

        // Calculate automatic status based on repayments vs schedule
        $status = 'active'; // default
        $overduePeriods = 0;
        $paidPeriods = 0;
        
        foreach ($loan->schedules as $schedule) {
            $schedulePaid = $schedule->repayments->sum('amount_paid');
            $isDue = $schedule->due_date->isPast();
            
            if ($schedulePaid >= $schedule->total_due * 0.99) { // 99% tolerance
                $paidPeriods++;
            } elseif ($isDue && $schedulePaid < $schedule->total_due * 0.99) {
                $overduePeriods++;
            }
        }

        // Determine status
        if ($totalOutstanding <= 0.01) {
            $status = 'repaid';
        } elseif ($overduePeriods > 0) {
            $status = 'in_arrears';
        } elseif ($paidPeriods >= $loan->schedules->count() * 0.8) {
            $status = 'active'; // On track
        } else {
            $status = 'active';
        }

        // Load activity logs - include both Loan and LoanRepayment activities
        $repaymentIds = $loan->repayments->pluck('id')->toArray();
        $activityLogs = \App\Models\ActivityLog::where(function($query) use ($loan, $repaymentIds) {
                // Direct loan activities
                $query->where(function($q) use ($loan) {
                    $q->where('subject_type', Loan::class)
                      ->where('subject_id', $loan->id);
                });
                // Loan repayment activities (if any repayments exist)
                if (!empty($repaymentIds)) {
                    $query->orWhere(function($q) use ($repaymentIds) {
                        $q->where('subject_type', LoanRepayment::class)
                          ->whereIn('subject_id', $repaymentIds);
                    });
                }
            })
            ->with('user')
            ->latest()
            ->take(50)
            ->get();

        return view('admin.loans.show', compact(
            'loan', 
            'principalOutstanding',
            'interestOutstanding',
            'totalOutstanding',
            'remainingTenure',
            'status',
            'activityLogs'
        ));
    }

    public function edit(Loan $loan)
    {
        $this->ensureCanManage();
        $projects = Project::orderBy('name')->get(['id', 'name']);

        return view('admin.loans.edit', compact('loan', 'projects'));
    }

    public function update(Request $request, Loan $loan)
    {
        $this->ensureCanManage();

        $validated = $request->validate([
            'lender_name' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'interest_rate' => ['required', 'numeric', 'min:0'],
            'tenure_months' => ['required', 'integer', 'min:1'],
            'repayment_frequency' => ['required', 'string', 'max:50'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'start_date' => ['nullable', 'date'],
            'status' => ['required', 'in:active,repaid,in_arrears'],
        ]);

        // Prevent loan closure if there are unresolved red entries
        if ($validated['status'] === 'repaid') {
            $unresolvedRedEntries = $loan->repayments()
                ->where('reconciliation_status', 'red')
                ->where(function($query) {
                    $query->whereNull('reconciliation_note')
                          ->orWhere('reconciliation_note', '');
                })
                ->count();

            if ($unresolvedRedEntries > 0) {
                return redirect()->route('admin.loans.show', $loan)
                    ->with('error', "Cannot close loan. There are {$unresolvedRedEntries} red repayment entries that require reconciliation notes before the loan can be marked as repaid.");
            }
        }

        $loan->update($validated);

        LoanScheduleService::generateForLoan($loan);

        ActivityLogService::log('loan_updated', $loan, $validated);

        return redirect()->route('admin.loans.show', $loan)
            ->with('success', 'Loan updated and schedule regenerated.');
    }

    public function storeRepayment(Request $request, Loan $loan)
    {
        $user = auth()->user();
        // Allow Treasurer, Super Admin, or users with loans.repayments.create permission
        if (! $user->hasPermission('loans.repayments.create') 
            && ! $user->hasAdminRole('treasurer') 
            && ! $user->isSuperAdmin()) {
            abort(403, 'You do not have permission to record loan repayments.');
        }

        // If the loan has an amortization schedule, require repayments to be linked
        // to a specific schedule period so that expected vs actual can always be compared.
        $hasSchedule = $loan->schedules()->exists();

        $rules = [
            'loan_schedule_id' => [$hasSchedule ? 'required' : 'nullable', 'exists:loan_schedules,id'],
            'date_paid' => ['required', 'date'],
            'amount_paid' => ['required', 'numeric', 'min:0.01'],
            'bank_reference' => ['nullable', 'string', 'max:255'],
            'document' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ];

        $messages = [
            'loan_schedule_id.required' => 'Please select the schedule period this repayment belongs to.',
            'loan_schedule_id.exists' => 'The selected schedule period does not exist.',
            'date_paid.required' => 'The payment date is required.',
            'date_paid.date' => 'The payment date must be a valid date.',
            'amount_paid.required' => 'The amount paid is required.',
            'amount_paid.numeric' => 'The amount paid must be a number.',
            'amount_paid.min' => 'The amount paid must be at least 0.01.',
            'bank_reference.max' => 'The bank reference must not exceed 255 characters.',
            'document.file' => 'The document must be a valid file.',
            'document.mimes' => 'The document must be a PDF, JPG, JPEG, or PNG file.',
            'document.max' => 'The document must not exceed 10MB.',
        ];

        $validated = $request->validate($rules, $messages);

        // Calculate expected amount if linked to a schedule period
        $expectedAmount = null;
        $comparisonNote = null;
        $suggestedStatus = 'pending';
        
        if ($validated['loan_schedule_id']) {
            $schedule = \App\Models\LoanSchedule::find($validated['loan_schedule_id']);
            if ($schedule) {
                $expectedAmount = $schedule->total_due;
                $actualAmount = $validated['amount_paid'];
                $difference = abs($expectedAmount - $actualAmount);
                $tolerance = 0.01; // Allow 1 cent tolerance for rounding
                
                // Auto-suggest reconciliation status based on comparison
                if ($difference <= $tolerance) {
                    $suggestedStatus = 'green';
                    $comparisonNote = "Amount matches expected payment (Expected: Ksh " . number_format($expectedAmount, 2) . ", Actual: Ksh " . number_format($actualAmount, 2) . ")";
                } else {
                    $suggestedStatus = 'red';
                    $comparisonNote = "Amount discrepancy detected. Expected: Ksh " . number_format($expectedAmount, 2) . ", Actual: Ksh " . number_format($actualAmount, 2) . ", Difference: Ksh " . number_format($difference, 2);
                }
            }
        }

        $data = [
            'loan_id' => $loan->id,
            'loan_schedule_id' => $validated['loan_schedule_id'] ?? null,
            'date_paid' => $validated['date_paid'],
            'amount_paid' => $validated['amount_paid'],
            'bank_reference' => $validated['bank_reference'] ?? null,
            'created_by' => Auth::id(),
            'reconciliation_status' => $suggestedStatus, // Auto-suggest based on comparison
            'reconciliation_note' => $comparisonNote, // Store comparison result
        ];

        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $path = $file->store('loan-repayments', 'public');
            $data['document_path'] = $path;
            $data['document_name'] = $file->getClientOriginalName();
        }

        $repayment = LoanRepayment::create($data);

        // Log with comparison details
        $logData = array_merge($data, [
            'expected_amount' => $expectedAmount,
            'suggested_status' => $suggestedStatus,
        ]);
        ActivityLogService::log('loan_repayment_recorded', $repayment, $logData);

        $message = 'Loan repayment recorded. ';
        if ($suggestedStatus === 'green') {
            $message .= 'Amount matches expected payment - marked as Green.';
        } elseif ($suggestedStatus === 'red') {
            $message .= 'Amount discrepancy detected - marked as Red. Please add reconciliation notes.';
        } else {
            $message .= 'Awaiting reconciliation.';
        }

        return redirect()->route('admin.loans.show', $loan)
            ->with('success', $message);
    }

    public function reconcileRepayment(Request $request, Loan $loan, LoanRepayment $repayment)
    {
        if (! auth()->user()->hasPermission('loans.repayments.reconcile')) {
            abort(403, 'You do not have permission to reconcile loan repayments.');
        }

        // Require notes for red entries
        $validated = $request->validate([
            'status' => ['required', 'in:green,red'],
            'reconciliation_note' => [
                'required_if:status,red',
                'nullable',
                'string',
                'min:10',
            ],
        ], [
            'reconciliation_note.required_if' => 'A reconciliation note is required when marking an entry as Red (mismatch).',
            'reconciliation_note.min' => 'Reconciliation note must be at least 10 characters when marking as Red.',
        ]);

        $repayment->update([
            'reconciliation_status' => $validated['status'],
            'reconciliation_note' => $validated['reconciliation_note'] ?? null,
            'confirmed_by' => Auth::id(),
        ]);

        ActivityLogService::log('loan_repayment_reconciled', $repayment, $validated);

        return redirect()->route('admin.loans.show', $loan)
            ->with('success', 'Repayment reconciliation updated.');
    }
}

