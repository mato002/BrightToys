@extends('layouts.admin')

@section('page_title', 'Post Journal Transaction')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
        <div>
            <a href="{{ route('admin.accounting.dashboard') }}" class="text-emerald-600 hover:text-emerald-700 text-sm mb-2 inline-flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Books of Account
            </a>
            <h1 class="text-lg font-semibold">Post Journal Transaction</h1>
            <p class="text-xs text-slate-500">Manual Post an Entry to Journal Record</p>
        </div>
    </div>

    <form action="{{ route('admin.accounting.journal.store') }}" method="POST" id="journal-form"
          class="bg-white border border-slate-100 rounded-lg p-6 text-sm space-y-4 shadow-sm max-w-3xl">
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

        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold mb-1 text-slate-700">Branch <span class="text-red-500">*</span></label>
                <select name="branch_name" required
                        class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-400">
                    <option value="Corporate (HQ)" {{ old('branch_name', 'Corporate (HQ)') === 'Corporate (HQ)' ? 'selected' : '' }}>Corporate (HQ)</option>
                    <option value="Head Office" {{ old('branch_name') === 'Head Office' ? 'selected' : '' }}>Head Office</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold mb-1 text-slate-700">Date <span class="text-red-500">*</span></label>
                <input type="date" name="transaction_date" value="{{ old('transaction_date', now()->format('Y-m-d')) }}" required
                       class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-400">
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold mb-1 text-slate-700">Ref No</label>
            <input type="text" name="reference_number" value="{{ old('reference_number') }}"
                   class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-400"
                   placeholder="Reference number (optional)">
        </div>

        {{-- Debit Account --}}
        <div class="border border-slate-200 rounded-lg p-4 bg-slate-50">
            <div class="flex items-center justify-between mb-3">
                <label class="block text-xs font-semibold text-slate-700">Debit Account <span class="text-red-500">*</span></label>
                <button type="button" onclick="addDebitLine()" class="text-xs text-emerald-600 hover:text-emerald-700 flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <circle cx="12" cy="12" r="10" stroke-width="1.6"/>
                        <path d="M12 8v8M8 12h8" stroke-width="1.6" stroke-linecap="round"/>
                    </svg>
                    Add Line
                </button>
            </div>
            <div id="debit-lines">
                <div class="debit-line flex gap-2 items-end">
                    <div class="flex-1">
                        <select name="debit_accounts[]" required
                                class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-400">
                            <option value="">-- Select Account --</option>
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}">{{ $account->code }} - {{ $account->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-32">
                        <label class="block text-xs font-semibold mb-1 text-slate-700">Amount</label>
                        <input type="number" name="debit_amounts[]" step="0.01" min="0" required
                               class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-400"
                               placeholder="0.00">
                    </div>
                    <button type="button" onclick="removeLine(this)" class="text-red-500 hover:text-red-700 hidden remove-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <circle cx="12" cy="12" r="10" stroke-width="1.6"/>
                            <path d="M8 12h8" stroke-width="1.6" stroke-linecap="round"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Credit Account --}}
        <div class="border border-slate-200 rounded-lg p-4 bg-slate-50">
            <div class="flex items-center justify-between mb-3">
                <label class="block text-xs font-semibold text-slate-700">Credit Account <span class="text-red-500">*</span></label>
                <button type="button" onclick="addCreditLine()" class="text-xs text-emerald-600 hover:text-emerald-700 flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <circle cx="12" cy="12" r="10" stroke-width="1.6"/>
                        <path d="M12 8v8M8 12h8" stroke-width="1.6" stroke-linecap="round"/>
                    </svg>
                    Add Line
                </button>
            </div>
            <div id="credit-lines">
                <div class="credit-line flex gap-2 items-end">
                    <div class="flex-1">
                        <select name="credit_accounts[]" required
                                class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-400">
                            <option value="">-- Select Account --</option>
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}">{{ $account->code }} - {{ $account->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-32">
                        <label class="block text-xs font-semibold mb-1 text-slate-700">Amount</label>
                        <input type="number" name="credit_amounts[]" step="0.01" min="0" required
                               class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-400"
                               placeholder="0.00">
                    </div>
                    <button type="button" onclick="removeLine(this)" class="text-red-500 hover:text-red-700 hidden remove-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <circle cx="12" cy="12" r="10" stroke-width="1.6"/>
                            <path d="M8 12h8" stroke-width="1.6" stroke-linecap="round"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 text-xs">
            <p class="font-semibold text-amber-800 mb-1">Balance Check:</p>
            <p id="balance-status" class="text-amber-700">Total Debit: <span id="total-debit">0.00</span> | Total Credit: <span id="total-credit">0.00</span></p>
            <p id="balance-error" class="text-red-600 font-semibold mt-1 hidden">Debit and Credit amounts must be equal!</p>
        </div>

        <div>
            <label class="block text-xs font-semibold mb-1 text-slate-700">Transaction Details</label>
            <textarea name="transaction_details" rows="3"
                      class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-400"
                      placeholder="Enter transaction details...">{{ old('transaction_details') }}</textarea>
        </div>

        <div>
            <label class="block text-xs font-semibold mb-1 text-slate-700">Comments</label>
            <textarea name="comments" rows="2"
                      class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-400"
                      placeholder="Additional comments (optional)...">{{ old('comments') }}</textarea>
        </div>

        <div class="flex gap-3 pt-4 border-t border-slate-200">
            <button type="submit"
                    class="bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold px-6 py-2.5 rounded-md shadow-sm">
                Post
            </button>
            <a href="{{ route('admin.accounting.dashboard') }}"
               class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold px-6 py-2.5 rounded-md">
                Cancel
            </a>
        </div>
    </form>

    @push('scripts')
    <script>
        function addDebitLine() {
            const container = document.getElementById('debit-lines');
            const firstLine = container.querySelector('.debit-line');
            const newLine = firstLine.cloneNode(true);
            newLine.querySelector('select').value = '';
            newLine.querySelector('input').value = '';
            newLine.querySelector('.remove-btn').classList.remove('hidden');
            container.appendChild(newLine);
            updateBalance();
        }

        function addCreditLine() {
            const container = document.getElementById('credit-lines');
            const firstLine = container.querySelector('.credit-line');
            const newLine = firstLine.cloneNode(true);
            newLine.querySelector('select').value = '';
            newLine.querySelector('input').value = '';
            newLine.querySelector('.remove-btn').classList.remove('hidden');
            container.appendChild(newLine);
            updateBalance();
        }

        function removeLine(btn) {
            btn.closest('.debit-line, .credit-line').remove();
            updateBalance();
        }

        function updateBalance() {
            let totalDebit = 0;
            let totalCredit = 0;

            document.querySelectorAll('input[name="debit_amounts[]"]').forEach(input => {
                totalDebit += parseFloat(input.value) || 0;
            });

            document.querySelectorAll('input[name="credit_amounts[]"]').forEach(input => {
                totalCredit += parseFloat(input.value) || 0;
            });

            document.getElementById('total-debit').textContent = totalDebit.toFixed(2);
            document.getElementById('total-credit').textContent = totalCredit.toFixed(2);

            const errorDiv = document.getElementById('balance-error');
            if (Math.abs(totalDebit - totalCredit) > 0.01) {
                errorDiv.classList.remove('hidden');
            } else {
                errorDiv.classList.add('hidden');
            }
        }

        // Add event listeners to all amount inputs
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('input[name="debit_amounts[]"], input[name="credit_amounts[]"]').forEach(input => {
                input.addEventListener('input', updateBalance);
            });

            // Form validation
            document.getElementById('journal-form').addEventListener('submit', function(e) {
                updateBalance();
                const errorDiv = document.getElementById('balance-error');
                if (!errorDiv.classList.contains('hidden')) {
                    e.preventDefault();
                    alert('Please ensure Debit and Credit amounts are equal before posting.');
                }
            });
        });
    </script>
    @endpush
@endsection
