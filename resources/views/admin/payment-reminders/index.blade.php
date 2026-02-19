@extends('layouts.admin')

@section('page_title', 'Payment Reminders')

@section('breadcrumbs')
    <span class="breadcrumb-separator">/</span>
    <a href="{{ route('admin.payment-reminders.index') }}" class="text-slate-600 hover:text-emerald-600 transition-colors">Payment Reminders</a>
@endsection

@section('content')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
        <div>
            <h1 class="text-lg font-semibold">Payment Reminders</h1>
            <p class="text-xs text-slate-500">Track overdue and upcoming entry contribution payments.</p>
        </div>
        <div class="flex gap-2 no-print">
            <a href="{{ route('admin.payment-reminders.export') . '?' . http_build_query(request()->query()) }}"
               class="inline-flex items-center justify-center bg-blue-500 hover:bg-blue-600 text-white text-xs font-semibold px-4 py-2 rounded-md shadow-sm tooltip"
               data-tooltip="Export reminders to CSV"
               aria-label="Export CSV">
                Export CSV
            </a>
            <a href="{{ route('admin.financial.contributions') }}"
               class="inline-flex items-center justify-center bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-semibold px-4 py-2 rounded-md shadow-sm tooltip"
               data-tooltip="Review and approve contributions"
               aria-label="Review contributions">
                Review Contributions
            </a>
            <a href="{{ route('admin.partners.index') }}"
               class="inline-flex items-center justify-center bg-slate-600 hover:bg-slate-700 text-white text-xs font-semibold px-4 py-2 rounded-md shadow-sm"
               aria-label="View partners">
                View Partners
            </a>
            <button onclick="window.print()"
                    class="inline-flex items-center justify-center bg-slate-500 hover:bg-slate-600 text-white text-xs font-semibold px-4 py-2 rounded-md shadow-sm tooltip"
                    data-tooltip="Print this page (Ctrl/Cmd + P)"
                    aria-label="Print page">
                Print
            </button>
        </div>
    </div>

    {{-- Enhanced Summary stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
        <div class="bg-white border rounded-lg p-4 border-red-200 bg-red-50/50">
            <p class="text-xs font-semibold text-red-700 uppercase tracking-wide">Overdue</p>
            <p class="text-2xl font-bold text-red-800 mt-1">{{ $stats['overdue_count'] }}</p>
            <p class="text-xs text-red-600 mt-0.5">KES {{ number_format($stats['total_overdue_amount'], 2) }}</p>
            @if($stats['average_days_overdue'] > 0)
                <p class="text-[10px] text-red-500 mt-1">Avg: {{ $stats['average_days_overdue'] }} days</p>
            @endif
        </div>
        <div class="bg-white border rounded-lg p-4 border-amber-200 bg-amber-50/50">
            <p class="text-xs font-semibold text-amber-700 uppercase tracking-wide">Upcoming (30 days)</p>
            <p class="text-2xl font-bold text-amber-800 mt-1">{{ $stats['upcoming_count'] }}</p>
            <p class="text-xs text-amber-600 mt-0.5">KES {{ number_format($stats['total_upcoming_amount'], 2) }}</p>
        </div>
        <div class="bg-white border rounded-lg p-4 border-orange-200 bg-orange-50/50">
            <p class="text-xs font-semibold text-orange-700 uppercase tracking-wide">Total Penalties</p>
            <p class="text-2xl font-bold text-orange-800 mt-1">KES {{ number_format($stats['total_penalties'], 2) }}</p>
            <p class="text-xs text-orange-600 mt-0.5">accrued penalties</p>
        </div>
        <div class="bg-white border rounded-lg p-4 border-slate-200 bg-slate-50/50">
            <p class="text-xs font-semibold text-slate-700 uppercase tracking-wide">Partners with balance</p>
            <p class="text-2xl font-bold text-slate-800 mt-1">{{ $stats['partners_with_balance'] }}</p>
            <p class="text-xs text-slate-600 mt-0.5">KES {{ number_format($stats['total_outstanding'], 2) }} total</p>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.payment-reminders.index') }}" class="mb-4 bg-white border border-slate-100 rounded-lg p-3 text-xs">
        {{-- Quick Date Presets --}}
        <div class="mb-3">
            <label class="block text-[11px] font-semibold mb-2 text-slate-600">Quick Filters</label>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.payment-reminders.index', array_merge(request()->except('date_preset', 'date_from', 'date_to'), ['date_preset' => 'today'])) }}"
                   class="px-3 py-1.5 rounded-md text-xs font-medium transition-colors {{ $datePreset === 'today' ? 'bg-emerald-500 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    Today
                </a>
                <a href="{{ route('admin.payment-reminders.index', array_merge(request()->except('date_preset', 'date_from', 'date_to'), ['date_preset' => 'this_week'])) }}"
                   class="px-3 py-1.5 rounded-md text-xs font-medium transition-colors {{ $datePreset === 'this_week' ? 'bg-emerald-500 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    This Week
                </a>
                <a href="{{ route('admin.payment-reminders.index', array_merge(request()->except('date_preset', 'date_from', 'date_to'), ['date_preset' => 'this_month'])) }}"
                   class="px-3 py-1.5 rounded-md text-xs font-medium transition-colors {{ $datePreset === 'this_month' ? 'bg-emerald-500 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    This Month
                </a>
                <a href="{{ route('admin.payment-reminders.index', array_merge(request()->except('date_preset', 'date_from', 'date_to'), ['date_preset' => 'next_30_days'])) }}"
                   class="px-3 py-1.5 rounded-md text-xs font-medium transition-colors {{ $datePreset === 'next_30_days' ? 'bg-emerald-500 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    Next 30 Days
                </a>
                <a href="{{ route('admin.payment-reminders.index', array_merge(request()->except('date_preset', 'date_from', 'date_to'), ['date_preset' => 'last_30_days'])) }}"
                   class="px-3 py-1.5 rounded-md text-xs font-medium transition-colors {{ $datePreset === 'last_30_days' ? 'bg-emerald-500 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    Last 30 Days
                </a>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div>
                <label class="block text-[11px] font-semibold mb-1 text-slate-600">Search Partner</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Name or email"
                       class="border border-slate-200 rounded w-full px-3 py-1.5 text-xs">
            </div>
            <div>
                <label class="block text-[11px] font-semibold mb-1 text-slate-600">Status</label>
                <select name="status" class="border border-slate-200 rounded w-full px-3 py-1.5 text-xs">
                    <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>All</option>
                    <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Overdue</option>
                    <option value="upcoming" {{ request('status') === 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                </select>
            </div>
            <div>
                <label class="block text-[11px] font-semibold mb-1 text-slate-600">From Date</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       class="border border-slate-200 rounded w-full px-3 py-1.5 text-xs">
            </div>
            <div>
                <label class="block text-[11px] font-semibold mb-1 text-slate-600">To Date</label>
                <div class="flex gap-2">
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                           class="border border-slate-200 rounded w-full px-3 py-1.5 text-xs">
                    <button type="submit" class="bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold px-4 py-1.5 rounded-md whitespace-nowrap">
                        Filter
                    </button>
                    @if(request()->hasAny(['search', 'status', 'date_from', 'date_to', 'date_preset']))
                        <a href="{{ route('admin.payment-reminders.index') }}"
                           class="bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-semibold px-4 py-1.5 rounded-md whitespace-nowrap">
                            Clear
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </form>

    {{-- Bulk Actions Bar --}}
    @if($overdueInstallments->count() > 0 || $upcomingInstallments->count() > 0)
    <div id="bulkActionsBar" class="hidden mb-4 bg-emerald-50 border border-emerald-200 rounded-lg p-3 no-print">
        <div class="flex items-center justify-between">
            <span class="text-xs font-semibold text-emerald-800">
                <span id="selectedCount">0</span> installment(s) selected
            </span>
            <button onclick="sendBulkReminders()"
                    class="bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-semibold px-4 py-1.5 rounded-md">
                Send Reminders
            </button>
        </div>
    </div>
    @endif

    {{-- Overdue Installments --}}
    @if($overdueInstallments->count() > 0)
    <div class="mb-6 bg-white border border-red-200 rounded-lg p-4">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
                <span class="text-red-600 text-lg">⚠️</span>
                <h2 class="text-sm font-semibold text-red-900">Overdue Installments ({{ $overdueInstallments->count() }})</h2>
            </div>
            <div class="no-print">
                <label class="flex items-center gap-2 text-xs text-slate-600 cursor-pointer">
                    <input type="checkbox" id="selectAllOverdue" onchange="toggleSelectAll('overdue', this.checked)" class="rounded border-slate-300">
                    Select All
                </label>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-red-50 border-b border-red-200">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold text-red-700 no-print">
                            <input type="checkbox" id="checkAllOverdue" onchange="toggleSelectAll('overdue', this.checked)" class="rounded border-slate-300">
                        </th>
                        <th class="px-3 py-2 text-left font-semibold text-red-700">Partner</th>
                        <th class="px-3 py-2 text-left font-semibold text-red-700">Installment #</th>
                        <th class="px-3 py-2 text-left font-semibold text-red-700">Due Date</th>
                        <th class="px-3 py-2 text-left font-semibold text-red-700">Amount</th>
                        <th class="px-3 py-2 text-left font-semibold text-red-700">Paid</th>
                        <th class="px-3 py-2 text-left font-semibold text-red-700">Penalty</th>
                        <th class="px-3 py-2 text-left font-semibold text-red-700">Days Overdue</th>
                        <th class="px-3 py-2 text-left font-semibold text-red-700">Reminder Status</th>
                        <th class="px-3 py-2 text-left font-semibold text-red-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-red-100">
                    @foreach($overdueInstallments as $installment)
                    <tr>
                        <td class="px-3 py-2 no-print">
                            <input type="checkbox" class="installment-checkbox rounded border-slate-300" 
                                   data-installment-id="{{ $installment->id }}" 
                                   data-section="overdue"
                                   onchange="updateBulkActionsBar()">
                        </td>
                        <td class="px-3 py-2">
                            <a href="{{ route('admin.partners.show', $installment->paymentPlan->entryContribution->partner) }}" 
                               class="text-emerald-600 hover:text-emerald-700 underline">
                                {{ $installment->paymentPlan->entryContribution->partner->name }}
                            </a>
                        </td>
                        <td class="px-3 py-2 font-medium">#{{ $installment->installment_number }}</td>
                        <td class="px-3 py-2">{{ $installment->due_date->format('d M Y') }}</td>
                        <td class="px-3 py-2 font-medium">KES {{ number_format($installment->amount, 2) }}</td>
                        <td class="px-3 py-2">KES {{ number_format($installment->paid_amount, 2) }}</td>
                        <td class="px-3 py-2">
                            @if($installment->penalty_amount > 0)
                                <span class="font-semibold text-red-600">KES {{ number_format($installment->penalty_amount, 2) }}</span>
                            @else
                                <span class="text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="px-3 py-2">
                            <span class="font-semibold text-red-600">{{ $installment->days_overdue ?? now()->diffInDays($installment->due_date) }}</span>
                        </td>
                        <td class="px-3 py-2">
                            @if($installment->last_reminder_sent_at)
                                <div class="flex flex-col gap-0.5">
                                    <span class="text-xs text-slate-600">
                                        Sent {{ $installment->last_reminder_sent_at->diffForHumans() }}
                                    </span>
                                    @if($installment->reminder_count > 0)
                                        <span class="text-[10px] text-slate-500">
                                            ({{ $installment->reminder_count }}x)
                                        </span>
                                    @endif
                                    @if($installment->last_reminder_sent_at->isAfter(now()->subDays(7)))
                                        <span class="inline-block w-2 h-2 bg-emerald-500 rounded-full" title="Recently reminded"></span>
                                    @endif
                                </div>
                            @else
                                <span class="text-xs text-slate-400">Not sent</span>
                            @endif
                        </td>
                        <td class="px-3 py-2 no-print">
                            @if($installment->last_reminder_sent_at && $installment->last_reminder_sent_at->isAfter(now()->subDays(7)))
                                <button onclick="sendReminder({{ $installment->id }})"
                                        class="text-slate-400 text-xs underline mr-2 cursor-not-allowed"
                                        title="Reminder sent recently. Wait 7 days between reminders.">
                                    Send Reminder
                                </button>
                            @else
                                <button onclick="sendReminder({{ $installment->id }})"
                                        class="text-emerald-600 hover:text-emerald-700 text-xs underline mr-2">
                                    Send Reminder
                                </button>
                            @endif
                            <a href="{{ route('admin.partners.show', $installment->paymentPlan->entryContribution->partner) }}" 
                               class="text-slate-600 hover:text-slate-700 text-xs underline">
                                View Partner
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Upcoming Installments --}}
    @if($upcomingInstallments->count() > 0)
    <div class="mb-6 bg-white border border-amber-200 rounded-lg p-4">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
                <span class="text-amber-600 text-lg">📅</span>
                <h2 class="text-sm font-semibold text-amber-900">Upcoming Installments (Next 30 Days) - {{ $upcomingInstallments->count() }}</h2>
            </div>
            <div class="no-print">
                <label class="flex items-center gap-2 text-xs text-slate-600 cursor-pointer">
                    <input type="checkbox" id="selectAllUpcoming" onchange="toggleSelectAll('upcoming', this.checked)" class="rounded border-slate-300">
                    Select All
                </label>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-amber-50 border-b border-amber-200">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold text-amber-700 no-print">
                            <input type="checkbox" id="checkAllUpcoming" onchange="toggleSelectAll('upcoming', this.checked)" class="rounded border-slate-300">
                        </th>
                        <th class="px-3 py-2 text-left font-semibold text-amber-700">Partner</th>
                        <th class="px-3 py-2 text-left font-semibold text-amber-700">Installment #</th>
                        <th class="px-3 py-2 text-left font-semibold text-amber-700">Due Date</th>
                        <th class="px-3 py-2 text-left font-semibold text-amber-700">Amount</th>
                        <th class="px-3 py-2 text-left font-semibold text-amber-700">Days Until Due</th>
                        <th class="px-3 py-2 text-left font-semibold text-amber-700">Reminder Status</th>
                        <th class="px-3 py-2 text-left font-semibold text-amber-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-amber-100">
                    @foreach($upcomingInstallments as $installment)
                    <tr>
                        <td class="px-3 py-2 no-print">
                            <input type="checkbox" class="installment-checkbox rounded border-slate-300" 
                                   data-installment-id="{{ $installment->id }}" 
                                   data-section="upcoming"
                                   onchange="updateBulkActionsBar()">
                        </td>
                        <td class="px-3 py-2">
                            <a href="{{ route('admin.partners.show', $installment->paymentPlan->entryContribution->partner) }}" 
                               class="text-emerald-600 hover:text-emerald-700 underline">
                                {{ $installment->paymentPlan->entryContribution->partner->name }}
                            </a>
                        </td>
                        <td class="px-3 py-2 font-medium">#{{ $installment->installment_number }}</td>
                        <td class="px-3 py-2">{{ $installment->due_date->format('d M Y') }}</td>
                        <td class="px-3 py-2 font-medium">KES {{ number_format($installment->amount, 2) }}</td>
                        <td class="px-3 py-2">
                            <span class="font-medium text-amber-600">{{ now()->diffInDays($installment->due_date) }}</span>
                        </td>
                        <td class="px-3 py-2">
                            @if($installment->last_reminder_sent_at)
                                <div class="flex flex-col gap-0.5">
                                    <span class="text-xs text-slate-600">
                                        Sent {{ $installment->last_reminder_sent_at->diffForHumans() }}
                                    </span>
                                    @if($installment->reminder_count > 0)
                                        <span class="text-[10px] text-slate-500">
                                            ({{ $installment->reminder_count }}x)
                                        </span>
                                    @endif
                                    @if($installment->last_reminder_sent_at->isAfter(now()->subDays(7)))
                                        <span class="inline-block w-2 h-2 bg-emerald-500 rounded-full" title="Recently reminded"></span>
                                    @endif
                                </div>
                            @else
                                <span class="text-xs text-slate-400">Not sent</span>
                            @endif
                        </td>
                        <td class="px-3 py-2 no-print">
                            @if($installment->last_reminder_sent_at && $installment->last_reminder_sent_at->isAfter(now()->subDays(7)))
                                <button onclick="sendReminder({{ $installment->id }})"
                                        class="text-slate-400 text-xs underline mr-2 cursor-not-allowed"
                                        title="Reminder sent recently. Wait 7 days between reminders.">
                                    Send Reminder
                                </button>
                            @else
                                <button onclick="sendReminder({{ $installment->id }})"
                                        class="text-emerald-600 hover:text-emerald-700 text-xs underline mr-2">
                                    Send Reminder
                                </button>
                            @endif
                            <a href="{{ route('admin.partners.show', $installment->paymentPlan->entryContribution->partner) }}" 
                               class="text-slate-600 hover:text-slate-700 text-xs underline">
                                View Partner
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Partners Summary --}}
    @if($partnersWithContributions->count() > 0)
    <div class="bg-white border border-slate-100 rounded-lg p-4">
        <h2 class="text-sm font-semibold text-slate-900 mb-4">Partners with Outstanding Balances</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold text-slate-700">Partner</th>
                        <th class="px-3 py-2 text-left font-semibold text-slate-700">Total Amount</th>
                        <th class="px-3 py-2 text-left font-semibold text-slate-700">Paid</th>
                        <th class="px-3 py-2 text-left font-semibold text-slate-700">Outstanding</th>
                        <th class="px-3 py-2 text-left font-semibold text-slate-700">Overdue</th>
                        <th class="px-3 py-2 text-left font-semibold text-slate-700">Upcoming</th>
                        <th class="px-3 py-2 text-left font-semibold text-slate-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($partnersWithContributions as $item)
                    <tr>
                        <td class="px-3 py-2">
                            <a href="{{ route('admin.partners.show', $item['partner']) }}" 
                               class="text-emerald-600 hover:text-emerald-700 underline font-medium">
                                {{ $item['partner']->name }}
                            </a>
                        </td>
                        <td class="px-3 py-2">KES {{ number_format($item['entry_contribution']->total_amount, 2) }}</td>
                        <td class="px-3 py-2 text-emerald-600">KES {{ number_format($item['entry_contribution']->paid_amount, 2) }}</td>
                        <td class="px-3 py-2 font-semibold text-amber-600">KES {{ number_format($item['outstanding_balance'], 2) }}</td>
                        <td class="px-3 py-2">
                            @if($item['overdue_count'] > 0)
                                <span class="text-red-600 font-semibold">{{ $item['overdue_count'] }}</span>
                            @else
                                <span class="text-slate-400">0</span>
                            @endif
                        </td>
                        <td class="px-3 py-2">
                            @if($item['upcoming_count'] > 0)
                                <span class="text-amber-600 font-semibold">{{ $item['upcoming_count'] }}</span>
                            @else
                                <span class="text-slate-400">0</span>
                            @endif
                        </td>
                        <td class="px-3 py-2">
                            <a href="{{ route('admin.partners.show', $item['partner']) }}" 
                               class="text-emerald-600 hover:text-emerald-700 text-xs underline">
                                View Details
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @elseif($overdueInstallments->count() === 0 && $upcomingInstallments->count() === 0)
    <div class="empty-state bg-white border border-slate-100 rounded-lg p-8 text-center max-w-md mx-auto">
        <div class="empty-state-icon mb-4 text-slate-300">
            <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <h3 class="text-sm font-semibold text-slate-700 mb-1">No payment reminders at this time</h3>
        <p class="text-xs text-slate-500 mb-4">
            Overdue and upcoming installments from entry contribution payment plans will appear here. Add partners and set up entry contributions with payment plans to see reminders.
        </p>
        <div class="flex flex-wrap justify-center gap-2">
            <a href="{{ route('admin.financial.contributions') }}"
               class="inline-flex items-center justify-center bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-semibold px-4 py-2 rounded-md shadow-sm">
                Review Contributions
            </a>
            <a href="{{ route('admin.partners.index') }}"
               class="inline-flex items-center justify-center border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-semibold px-4 py-2 rounded-md">
                View Partners
            </a>
        </div>
    </div>
    @endif

    <script>
        function toggleSelectAll(section, checked) {
            const checkboxes = document.querySelectorAll(`.installment-checkbox[data-section="${section}"]`);
            checkboxes.forEach(cb => cb.checked = checked);
            updateBulkActionsBar();
        }

        function updateBulkActionsBar() {
            const checkboxes = document.querySelectorAll('.installment-checkbox:checked');
            const count = checkboxes.length;
            const bulkBar = document.getElementById('bulkActionsBar');
            
            if (count > 0) {
                bulkBar.classList.remove('hidden');
                document.getElementById('selectedCount').textContent = count;
            } else {
                bulkBar.classList.add('hidden');
            }
        }

        function sendReminder(installmentId) {
            if (!confirm('Send payment reminder email for this installment?')) {
                return;
            }

            const url = '{{ route("admin.payment-reminders.send-reminder", ":id") }}'.replace(':id', installmentId);
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (typeof showToast === 'function') {
                        showToast('success', data.message);
                    } else {
                        alert(data.message);
                    }
                } else {
                    if (typeof showToast === 'function') {
                        showToast('error', data.message);
                    } else {
                        alert('Error: ' + data.message);
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (typeof showToast === 'function') {
                    showToast('error', 'Failed to send reminder. Please try again.');
                } else {
                    alert('Failed to send reminder. Please try again.');
                }
            });
        }

        function sendBulkReminders() {
            const checkboxes = document.querySelectorAll('.installment-checkbox:checked');
            const ids = Array.from(checkboxes).map(cb => cb.dataset.installmentId);

            if (ids.length === 0) {
                alert('Please select at least one installment.');
                return;
            }

            if (!confirm(`Send payment reminder emails to ${ids.length} partner(s)?`)) {
                return;
            }

            fetch('{{ route('admin.payment-reminders.bulk-send') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ ids: ids })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const message = `${data.message}\nSent: ${data.sent}, Failed: ${data.failed}`;
                    if (typeof showToast === 'function') {
                        showToast('success', message);
                    } else {
                        alert(message);
                    }
                    
                    // Uncheck all checkboxes
                    document.querySelectorAll('.installment-checkbox').forEach(cb => cb.checked = false);
                    document.querySelectorAll('#selectAllOverdue, #checkAllOverdue, #selectAllUpcoming, #checkAllUpcoming').forEach(cb => cb.checked = false);
                    updateBulkActionsBar();
                    
                    if (data.errors && data.errors.length > 0) {
                        console.warn('Errors:', data.errors);
                    }
                } else {
                    if (typeof showToast === 'function') {
                        showToast('error', data.message || 'Failed to send reminders.');
                    } else {
                        alert('Error: ' + (data.message || 'Failed to send reminders.'));
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (typeof showToast === 'function') {
                    showToast('error', 'Failed to send reminders. Please try again.');
                } else {
                    alert('Failed to send reminders. Please try again.');
                }
            });
        }
    </script>
@endsection
