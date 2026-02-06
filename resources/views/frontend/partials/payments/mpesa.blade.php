<div class="space-y-3">
    <div class="flex items-center justify-between gap-2">
        <div>
            <p class="font-semibold text-emerald-900 text-xs">Mpesa Payment</p>
            <p class="text-[11px] text-emerald-700">
                We will send an STK push to your phone to authorize the payment.
            </p>
        </div>
        <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-semibold uppercase tracking-wide">
            Secure
        </span>
    </div>

    <div class="grid sm:grid-cols-2 gap-3">
        <div>
            <label class="block text-[11px] font-semibold mb-1 text-emerald-900">
                Mpesa Phone Number *
            </label>
            <input type="tel"
                   name="mpesa_phone"
                   value="{{ old('mpesa_phone', auth()->user()->phone ?? '') }}"
                   placeholder="e.g. 07xx xxx xxx"
                   class="border border-emerald-200 rounded-lg w-full px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-400">
        </div>

        <div>
            <label class="block text-[11px] font-semibold mb-1 text-emerald-900">
                Payer Name (optional)
            </label>
            <input type="text"
                   name="mpesa_name"
                   value="{{ old('mpesa_name', auth()->user()->name ?? '') }}"
                   placeholder="Name on Mpesa account"
                   class="border border-emerald-200 rounded-lg w-full px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-400">
        </div>
    </div>

    <p class="text-[11px] text-emerald-700">
        After you tap <span class="font-semibold">Place Order</span>, you will receive an Mpesa pop‑up on your phone.
        Confirm with your PIN to complete the payment.
    </p>
</div>

