@extends('layouts.admin')

@section('page_title', 'Add Member')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
        <div>
            <h1 class="text-lg font-semibold">Register New Member</h1>
            <p class="text-xs text-slate-500">
                Chairperson registers members after group approval. Onboarding is completed by the member via a secure link.
            </p>
        </div>
        <a href="{{ route('admin.members.index') }}" class="text-sm text-slate-600 hover:text-slate-800">
            Back to members
        </a>
    </div>

    <form action="{{ route('admin.members.store') }}" method="POST"
          class="card card-body form-full-width">
        @csrf

        @if($errors->any())
            <div class="alert alert-error">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="form-group">
            <label class="form-label">
                Full Name <span class="required">*</span>
            </label>
            <input type="text" name="name" value="{{ old('name') }}" required
                   placeholder="Member's official name as per ID">
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">
                    Email
                </label>
                <input type="email" name="email" value="{{ old('email') }}"
                       placeholder="Will be used for onboarding link">
            </div>
            <div class="form-group">
                <label class="form-label">
                    Phone
                </label>
                <input type="text" name="phone" value="{{ old('phone') }}"
                       placeholder="e.g. +2547...">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">
                Approval Document (Meeting Minutes/Resolution) <span class="required">*</span>
            </label>
            <select name="approval_document_id" required>
                <option value="">Select approval document...</option>
                @foreach($approvalDocuments as $doc)
                    <option value="{{ $doc->id }}" {{ old('approval_document_id') == $doc->id ? 'selected' : '' }}>
                        {{ $doc->title }} ({{ ucfirst(str_replace('_', ' ', $doc->type)) }})
                    </option>
                @endforeach
            </select>
            <p class="form-help">
                Link to the meeting minutes or resolution document that approved this member's registration.
                @if($approvalDocuments->isEmpty())
                    <span class="text-amber-600 font-semibold">No meeting minutes or resolutions found. 
                    <a href="{{ route('admin.documents.create') }}" class="underline">Upload one first</a>.</span>
                @else
                    <a href="{{ route('admin.documents.create') }}" class="text-emerald-600 hover:underline">Upload new document</a>
                @endif
            </p>
        </div>

        {{-- Entry Contribution Section --}}
        <div class="form-group border-t-2 border-slate-200 pt-5">
            <h3 class="text-sm font-semibold text-slate-900 mb-4">Entry Contribution</h3>
            
            <div class="space-y-4">
                <div class="form-group">
                    <label class="form-label">
                        Total Entry Contribution Amount (KES) <span class="required">*</span>
                    </label>
                    <input type="number" name="entry_total_amount" value="{{ old('entry_total_amount') }}" 
                           step="0.01" min="0" required
                           placeholder="e.g. 2000000">
                    <p class="form-help">Total amount required for entry (e.g., Ksh 2,000,000)</p>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        Initial Deposit (KES)
                    </label>
                    <input type="number" name="entry_initial_deposit" value="{{ old('entry_initial_deposit', 0) }}" 
                           step="0.01" min="0"
                           placeholder="e.g. 400000">
                    <p class="form-help">Initial deposit paid at registration (e.g., Ksh 400,000)</p>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        Payment Method <span class="required">*</span>
                    </label>
                    <select name="entry_payment_method" id="entry_payment_method" required>
                        <option value="full" {{ old('entry_payment_method') == 'full' ? 'selected' : '' }}>Full Payment</option>
                        <option value="installments" {{ old('entry_payment_method') == 'installments' ? 'selected' : '' }}>Payment Plan (Installments)</option>
                    </select>
                </div>

                {{-- Payment Plan Details (shown when installments selected) --}}
                <div id="payment_plan_section" style="display: {{ old('entry_payment_method') == 'installments' ? 'block' : 'none' }};" 
                     class="bg-slate-50 border border-slate-200 rounded-lg p-4 space-y-3">
                    <h4 class="text-xs font-semibold text-slate-700">Payment Plan Details</h4>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">
                                Number of Installments
                            </label>
                            <input type="number" name="installment_count" value="{{ old('installment_count', 6) }}" 
                                   min="2" max="60" id="installment_count">
                        </div>
                        <div class="form-group">
                            <label class="form-label">
                                Payment Frequency
                            </label>
                            <select name="installment_frequency">
                                <option value="monthly" {{ old('installment_frequency', 'monthly') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                <option value="weekly" {{ old('installment_frequency') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                <option value="quarterly" {{ old('installment_frequency') == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Start Date
                        </label>
                        <input type="date" name="installment_start_date" value="{{ old('installment_start_date', date('Y-m-d', strtotime('+1 month'))) }}">
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Payment Terms (Optional)
                        </label>
                        <textarea name="installment_terms" rows="2"
                                  placeholder="Additional terms and conditions for the payment plan...">{{ old('installment_terms') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="alert alert-info">
            <p class="font-semibold mb-1 flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/>
                </svg>
                Workflow Reminder
            </p>
            <ol class="list-decimal list-inside space-y-1 mt-2">
                <li>Group meeting decides to add member (outside system)</li>
                <li>Meeting minutes/resolution uploaded to Documents module</li>
                <li>Chairperson registers member here and links to the approval document</li>
                <li>System sends secure onboarding link to member</li>
                <li>Member completes biodata and identification details</li>
            </ol>
        </div>

        <div class="flex items-center gap-3 pt-4 border-t-2 border-slate-200">
            <button type="submit" class="btn-primary">
                Register Member
            </button>
            <a href="{{ route('admin.members.index') }}" class="btn-secondary">
                Cancel
            </a>
        </div>
    </form>

    <script>
        document.getElementById('entry_payment_method').addEventListener('change', function() {
            const planSection = document.getElementById('payment_plan_section');
            if (this.value === 'installments') {
                planSection.style.display = 'block';
            } else {
                planSection.style.display = 'none';
            }
        });
    </script>
@endsection

