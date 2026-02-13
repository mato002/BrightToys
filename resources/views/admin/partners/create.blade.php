@extends('layouts.admin')

@section('page_title', 'Add Partner')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
        <div>
            <h1 class="text-lg font-semibold">Add Partner</h1>
            <p class="text-xs text-slate-500">Create a new partner account.</p>
        </div>
        <a href="{{ route('admin.partners.index') }}" class="text-sm text-slate-600 hover:text-slate-800">
            Back to list
        </a>
    </div>

    <form action="{{ route('admin.partners.store') }}" method="POST" class="card card-body form-full-width">
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
            <label class="form-label">Partner Name <span class="required">*</span></label>
            <input type="text" name="name" value="{{ old('name') }}" required
                   placeholder="Enter partner name">
        </div>

        <div class="form-group">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" value="{{ old('email') }}"
                   placeholder="partner@example.com">
        </div>

        <div class="form-group">
            <label class="form-label">Phone</label>
            <input type="text" name="phone" value="{{ old('phone') }}"
                   placeholder="+1234567890">
        </div>

        <div class="form-group">
            <label class="form-label">Status <span class="required">*</span></label>
            <select name="status" required>
                <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Ownership Percentage <span class="required">*</span></label>
            <input type="number" name="ownership_percentage" value="{{ old('ownership_percentage') }}" 
                   step="0.01" min="0" max="100" required
                   placeholder="10.00">
            <p class="form-help">Enter the ownership percentage (e.g., 10.00 for 10%).</p>
        </div>

        <div class="form-group">
            <label class="form-label">Effective From <span class="required">*</span></label>
            <input type="date" name="effective_from" value="{{ old('effective_from', now()->format('Y-m-d')) }}" required>
        </div>

        <div class="form-group">
            <label class="form-label">Notes</label>
            <textarea name="notes" rows="3"
                      placeholder="Additional notes about this partner...">{{ old('notes') }}</textarea>
        </div>

        {{-- Entry Contribution Section --}}
        <div class="form-group border-t-2 border-slate-200 pt-5">
            <h3 class="text-sm font-semibold text-slate-900 mb-2">Entry Contribution</h3>
            <p class="form-help mb-4">The Chairperson sets the expected entry contribution and payment terms for this partner.</p>
            
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

        <div class="flex items-center gap-3 pt-4 border-t-2 border-slate-200">
            <button type="submit" class="btn-primary">
                Create Partner
            </button>
            <a href="{{ route('admin.partners.index') }}" class="btn-secondary">
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
