<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $partner = $user->partner;

        if (!$partner) {
            abort(403, 'You are not associated with a partner account.');
        }

        // Load entry contribution and wallets with recent transactions
        $partner->load([
            'entryContribution.paymentPlan.installments',
            'wallets.transactions' => function ($q) {
                $q->latest('occurred_at')->limit(10);
            },
        ]);

        $welfareWallet = $partner->wallets->firstWhere('type', \App\Models\MemberWallet::TYPE_WELFARE);
        $investmentWallet = $partner->wallets->firstWhere('type', \App\Models\MemberWallet::TYPE_INVESTMENT);

        return view('partner.profile.index', compact('user', 'partner', 'welfareWallet', 'investmentWallet'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $partner = $user->partner;

        if (!$partner) {
            abort(403, 'You are not associated with a partner account.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'date_of_birth' => ['nullable', 'date'],
            'national_id_number' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:500'],
            'id_document' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'] ?? $partner->email,
            'phone' => $validated['phone'] ?? $partner->phone,
            'date_of_birth' => $validated['date_of_birth'] ?? $partner->date_of_birth,
            'national_id_number' => $validated['national_id_number'] ?? $partner->national_id_number,
            'address' => $validated['address'] ?? $partner->address,
        ];

        // Handle ID document upload
        if ($request->hasFile('id_document')) {
            if ($partner->id_document_path) {
                Storage::disk('public')->delete($partner->id_document_path);
            }

            $file = $request->file('id_document');
            $path = $file->store('partner-ids', 'public');
            $updateData['id_document_path'] = $path;
        }

        $partner->update($updateData);

        return redirect()->route('partner.profile')
            ->with('success', 'Profile updated successfully.');
    }
}

