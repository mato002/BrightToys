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
          class="bg-white border border-slate-100 rounded-lg p-4 text-sm space-y-4 shadow-sm max-w-xl">
        @csrf

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-lg p-3 text-xs text-red-700">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div>
            <label class="block text-xs font-semibold mb-1 text-slate-700">
                Full Name <span class="text-red-500">*</span>
            </label>
            <input type="text" name="name" value="{{ old('name') }}" required
                   class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-400"
                   placeholder="Member's official name as per ID">
        </div>

        <div class="grid md:grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-semibold mb-1 text-slate-700">
                    Email
                </label>
                <input type="email" name="email" value="{{ old('email') }}"
                       class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-400"
                       placeholder="Will be used for onboarding link">
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1 text-slate-700">
                    Phone
                </label>
                <input type="text" name="phone" value="{{ old('phone') }}"
                       class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-400"
                       placeholder="e.g. +2547...">
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold mb-1 text-slate-700">
                Approval Document (Meeting Minutes/Resolution) <span class="text-red-500">*</span>
            </label>
            <select name="approval_document_id" required
                    class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-400">
                <option value="">Select approval document...</option>
                @foreach($approvalDocuments as $doc)
                    <option value="{{ $doc->id }}" {{ old('approval_document_id') == $doc->id ? 'selected' : '' }}>
                        {{ $doc->title }} ({{ ucfirst(str_replace('_', ' ', $doc->type)) }})
                    </option>
                @endforeach
            </select>
            <p class="text-[10px] text-slate-500 mt-1">
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
        <div class="border-t border-slate-200 pt-4 mt-4">
            <h3 class="text-sm font-semibold text-slate-900 mb-3">Entry Contribution</h3>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold mb-1 text-slate-700">
                        Total Entry Contribution Amount (KES) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="entry_total_amount" value="{{ old('entry_total_amount') }}" 
                           step="0.01" min="0" required
                           class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-400"
                           placeholder="e.g. 2000000">
                    <p class="text-[10px] text-slate-500 mt-1">Total amount required for entry (e.g., Ksh 2,000,000)</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold mb-1 text-slate-700">
                        Initial Deposit (KES)
                    </label>
                    <input type="number" name="entry_initial_deposit" value="{{ old('entry_initial_deposit', 0) }}" 
                           step="0.01" min="0"
                           class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-400"
                           placeholder="e.g. 400000">
                    <p class="text-[10px] text-slate-500 mt-1">Initial deposit paid at registration (e.g., Ksh 400,000)</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold mb-1 text-slate-700">
                        Payment Method <span class="text-red-500">*</span>
                    </label>
                    <select name="entry_payment_method" id="entry_payment_method" required
                            class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-400">
                        <option value="full" {{ old('entry_payment_method') == 'full' ? 'selected' : '' }}>Full Payment</option>
                        <option value="installments" {{ old('entry_payment_method') == 'installments' ? 'selected' : '' }}>Payment Plan (Installments)</option>
                    </select>
                </div>

                {{-- Payment Plan Details (shown when installments selected) --}}
                <div id="payment_plan_section" style="display: {{ old('entry_payment_method') == 'installments' ? 'block' : 'none' }};" 
                     class="bg-slate-50 border border-slate-200 rounded-lg p-4 space-y-3">
                    <h4 class="text-xs font-semibold text-slate-700">Payment Plan Details</h4>
                    
                    <div class="grid md:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold mb-1 text-slate-700">
                                Number of Installments
                            </label>
                            <input type="number" name="installment_count" value="{{ old('installment_count', 6) }}" 
                                   min="2" max="60" id="installment_count"
                                   class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold mb-1 text-slate-700">
                                Payment Frequency
                            </label>
                            <select name="installment_frequency" 
                                    class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                                <option value="monthly" {{ old('installment_frequency', 'monthly') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                <option value="weekly" {{ old('installment_frequency') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                <option value="quarterly" {{ old('installment_frequency') == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold mb-1 text-slate-700">
                            Start Date
                        </label>
                        <input type="date" name="installment_start_date" value="{{ old('installment_start_date', date('Y-m-d', strtotime('+1 month'))) }}" 
                               class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold mb-1 text-slate-700">
                            Payment Terms (Optional)
                        </label>
                        <textarea name="installment_terms" rows="2"
                                  class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                                  placeholder="Additional terms and conditions for the payment plan...">{{ old('installment_terms') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 text-[11px] text-blue-800">
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

        <div class="flex items-center gap-3 pt-2 border-t border-slate-100">
            <button type="submit"
                    class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-6 py-2 rounded shadow-sm">
                Register Member
            </button>
            <a href="{{ route('admin.members.index') }}" class="text-sm text-slate-600 hover:text-slate-800">
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

