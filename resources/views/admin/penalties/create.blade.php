@extends('layouts.admin')

@section('page_title', 'New Penalty Action')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
        <div>
            <h1 class="text-lg font-semibold">New Penalty Action</h1>
            <p class="text-xs text-slate-500">
                Apply a manual penalty, request a waiver, or pause penalties for a member.
            </p>
        </div>
        <a href="{{ route('admin.penalties.index') }}" class="text-sm text-slate-600 hover:text-slate-800">
            Back to list
        </a>
    </div>

    <form action="{{ route('admin.penalties.store') }}" method="POST"
          class="bg-white border border-slate-100 rounded-lg p-6 text-sm space-y-4 shadow-sm max-w-xl">
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
            <label class="block text-xs font-semibold mb-1 text-slate-700">Partner <span class="text-red-500">*</span></label>
            <select name="partner_id" required
                    class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                <option value="">Select partner</option>
                @foreach($partners as $partner)
                    <option value="{{ $partner->id }}" @selected(old('partner_id', $selectedPartnerId) == $partner->id)>
                        {{ $partner->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-semibold mb-1 text-slate-700">Action Type <span class="text-red-500">*</span></label>
            <select name="type" id="penalty_type" required
                    class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                <option value="apply" {{ old('type', $type) === 'apply' ? 'selected' : '' }}>Apply Penalty</option>
                <option value="waive" {{ old('type', $type) === 'waive' ? 'selected' : '' }}>Waive Penalty</option>
                <option value="pause" {{ old('type', $type) === 'pause' ? 'selected' : '' }}>Pause Penalties</option>
            </select>
        </div>

        {{-- Amount & Period (for apply/waive) --}}
        <div id="amount_period_section">
            <div class="grid md:grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-semibold mb-1 text-slate-700">Amount (KES) <span class="text-red-500">*</span></label>
                    <input type="number" name="amount" value="{{ old('amount') }}" step="0.01" min="0.01"
                           class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1 text-slate-700">Year <span class="text-red-500">*</span></label>
                    <input type="number" name="target_year" value="{{ old('target_year', now()->year) }}" min="2000" max="2100"
                           class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1 text-slate-700">Month <span class="text-red-500">*</span></label>
                    <select name="target_month"
                            class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ (int)old('target_month', now()->month) === $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create(null, $m, 1)->format('F') }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <p class="text-[11px] text-slate-500 mt-1">
                Select the month whose penalties you are adjusting.
            </p>
        </div>

        {{-- Pause section --}}
        <div id="pause_section" class="hidden">
            <div class="grid md:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold mb-1 text-slate-700">Pause From <span class="text-red-500">*</span></label>
                    <input type="date" name="paused_from" value="{{ old('paused_from', now()->format('Y-m-d')) }}"
                           class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1 text-slate-700">Pause To</label>
                    <input type="date" name="paused_to" value="{{ old('paused_to') }}"
                           class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                    <p class="text-[11px] text-slate-500 mt-1">Leave empty to pause penalties until further notice.</p>
                </div>
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold mb-1 text-slate-700">Reason / Justification <span class="text-red-500">*</span></label>
            <textarea name="reason" rows="4"
                      class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                      placeholder="Explain why this penalty is being applied, waived, or paused...">{{ old('reason') }}</textarea>
        </div>

        <div class="flex items-center gap-3 pt-4 border-t border-slate-200">
            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-6 py-2 rounded shadow-sm">
                Save Penalty Action
            </button>
            <a href="{{ route('admin.penalties.index') }}" class="text-sm text-slate-600 hover:text-slate-800">
                Cancel
            </a>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const typeSelect = document.getElementById('penalty_type');
            const amountSection = document.getElementById('amount_period_section');
            const pauseSection = document.getElementById('pause_section');

            function syncSections() {
                if (typeSelect.value === 'pause') {
                    amountSection.classList.add('hidden');
                    pauseSection.classList.remove('hidden');
                } else {
                    amountSection.classList.remove('hidden');
                    pauseSection.classList.add('hidden');
                }
            }

            typeSelect.addEventListener('change', syncSections);
            syncSections();
        });
    </script>
@endsection

