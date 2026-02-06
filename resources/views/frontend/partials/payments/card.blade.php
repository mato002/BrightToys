<div class="space-y-3">
    <div class="flex items-center justify-between gap-2">
        <div>
            <p class="font-semibold text-indigo-900 text-xs">Card Payment</p>
            <p class="text-[11px] text-indigo-700">
                Pay securely with your Visa, Mastercard or other supported card.
            </p>
        </div>
        <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-700 text-[10px] font-semibold uppercase tracking-wide">
            Encrypted
        </span>
    </div>

    <div class="space-y-2">
        <label class="block text-[11px] font-semibold mb-1 text-indigo-900">
            Name on Card *
        </label>
        <input type="text"
               name="card_name"
               value="{{ old('card_name', auth()->user()->name ?? '') }}"
               placeholder="As printed on the card"
               class="border border-indigo-200 rounded-lg w-full px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-400">
    </div>

    <div class="space-y-2">
        <label class="block text-[11px] font-semibold mb-1 text-indigo-900">
            Card Number *
        </label>
        <input type="text"
               name="card_number"
               value="{{ old('card_number') }}"
               placeholder="1234 5678 9012 3456"
               maxlength="19"
               class="border border-indigo-200 rounded-lg w-full px-3 py-2 text-xs tracking-[0.2em] focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-400">
    </div>

    <div class="grid grid-cols-[2fr,1fr] gap-3">
        <div class="space-y-1">
            <label class="block text-[11px] font-semibold mb-1 text-indigo-900">
                Expiry Date *
            </label>
            <input type="text"
                   name="card_expiry"
                   value="{{ old('card_expiry') }}"
                   placeholder="MM / YY"
                   maxlength="7"
                   class="border border-indigo-200 rounded-lg w-full px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-400">
        </div>

        <div class="space-y-1">
            <label class="block text-[11px] font-semibold mb-1 text-indigo-900">
                CVV *
            </label>
            <input type="password"
                   name="card_cvv"
                   value="{{ old('card_cvv') }}"
                   maxlength="4"
                   class="border border-indigo-200 rounded-lg w-full px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-400">
        </div>
    </div>

    <p class="text-[11px] text-indigo-700">
        We never store your full card details. Payments are processed via your configured gateway.
    </p>
</div>

