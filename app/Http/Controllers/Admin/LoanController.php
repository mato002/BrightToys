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
        if (! auth()->user()->hasPermission('loans.view')) {
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

        $loans = Loan::with('project')->latest()->paginate(20);

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

        // Compute outstanding balance and simple status flags
        $principalScheduled = $loan->schedules()->sum('principal_due');
        $principalPaid = $loan->repayments()
            ->join('loan_schedules', 'loan_repayments.loan_schedule_id', '=', 'loan_schedules.id')
            ->sum('loan_schedules.principal_due'); // approximation

        $principalOutstanding = max(0, $principalScheduled - $principalPaid);

        return view('admin.loans.show', compact('loan', 'principalOutstanding'));
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

        $loan->update($validated);

        LoanScheduleService::generateForLoan($loan);

        ActivityLogService::log('loan_updated', $loan, $validated);

        return redirect()->route('admin.loans.show', $loan)
            ->with('success', 'Loan updated and schedule regenerated.');
    }

    public function storeRepayment(Request $request, Loan $loan)
    {
        if (! auth()->user()->hasPermission('loans.repayments.create')) {
            abort(403, 'You do not have permission to record loan repayments.');
        }

        $validated = $request->validate([
            'loan_schedule_id' => ['nullable', 'exists:loan_schedules,id'],
            'date_paid' => ['required', 'date'],
            'amount_paid' => ['required', 'numeric', 'min:0.01'],
            'bank_reference' => ['nullable', 'string', 'max:255'],
            'document' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ]);

        $data = [
            'loan_id' => $loan->id,
            'loan_schedule_id' => $validated['loan_schedule_id'] ?? null,
            'date_paid' => $validated['date_paid'],
            'amount_paid' => $validated['amount_paid'],
            'bank_reference' => $validated['bank_reference'] ?? null,
            'created_by' => Auth::id(),
            'reconciliation_status' => 'pending',
        ];

        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $path = $file->store('loan-repayments', 'public');
            $data['document_path'] = $path;
            $data['document_name'] = $file->getClientOriginalName();
        }

        $repayment = LoanRepayment::create($data);

        ActivityLogService::log('loan_repayment_recorded', $repayment, $data);

        return redirect()->route('admin.loans.show', $loan)
            ->with('success', 'Loan repayment recorded. Awaiting reconciliation.');
    }

    public function reconcileRepayment(Request $request, Loan $loan, LoanRepayment $repayment)
    {
        if (! auth()->user()->hasPermission('loans.repayments.reconcile')) {
            abort(403, 'You do not have permission to reconcile loan repayments.');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:green,red'],
            'reconciliation_note' => ['nullable', 'string'],
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

