<div class="space-y-3">
    <div class="flex items-center justify-between gap-2">
        <div>
            <p class="font-semibold text-sky-900 text-xs">Paybill Payment</p>
            <p class="text-[11px] text-sky-700">
                Use the paybill option in your Mpesa menu and enter the details below.
            </p>
        </div>
        <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full bg-sky-100 text-sky-700 text-[10px] font-semibold uppercase tracking-wide">
            Manual
        </span>
    </div>

    <div class="grid sm:grid-cols-2 gap-3">
        <div>
            <label class="block text-[11px] font-semibold mb-1 text-sky-900">
                Business Number
            </label>
            <input type="text"
                   value="{{ config('services.payments.paybill_number', '123456') }}"
                   readonly
                   class="border border-sky-200 bg-sky-50 rounded-lg w-full px-3 py-2 text-xs text-sky-900 font-semibold">
            <p class="text-[10px] text-sky-500 mt-1">Use this paybill number in Mpesa.</p>
        </div>

        <div>
            <label class="block text-[11px] font-semibold mb-1 text-sky-900">
                Account / Reference *
            </label>
            <input type="text"
                   name="paybill_account"
                   value="{{ old('paybill_account') }}"
                   placeholder="e.g. ORDER123 or your phone number"
                   class="border border-sky-200 rounded-lg w-full px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-400">
        </div>
    </div>

    <div class="grid sm:grid-cols-2 gap-3">
        <div>
            <label class="block text-[11px] font-semibold mb-1 text-sky-900">
                Phone Number Used *
            </label>
            <input type="tel"
                   name="paybill_phone"
                   value="{{ old('paybill_phone', auth()->user()->phone ?? '') }}"
                   placeholder="07xx xxx xxx"
                   class="border border-sky-200 rounded-lg w-full px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-400">
        </div>
        <div>
            <label class="block text-[11px] font-semibold mb-1 text-sky-900">
                Amount Paid (Ksh) *
            </label>
            <input type="number"
                   step="0.01"
                   name="paybill_amount"
                   value="{{ old('paybill_amount') }}"
                   placeholder="Exact amount sent"
                   class="border border-sky-200 rounded-lg w-full px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-400">
        </div>
    </div>

    <p class="text-[11px] text-sky-700">
        Complete the payment on your phone, then tap <span class="font-semibold">Place Order</span>.
        We’ll verify your transaction against the provided reference.
    </p>
</div>

