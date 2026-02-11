<?php

namespace App\Http\Controllers;

use App\Models\Partner; // Members and Partners are the same
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MemberOnboardingController extends Controller
{
    /**
     * Show onboarding form for a member given a secure token.
     */
    public function show(string $token)
    {
        $member = Partner::where('onboarding_token', $token)
            ->where(function ($q) {
                $q->whereNull('onboarding_token_expires_at')
                  ->orWhere('onboarding_token_expires_at', '>=', now());
            })
            ->firstOrFail();

        return view('onboarding.member', compact('member', 'token'));
    }

    /**
     * Handle onboarding submission (biodata + ID).
     */
    public function submit(Request $request, string $token)
    {
        $member = Partner::where('onboarding_token', $token)
            ->where(function ($q) {
                $q->whereNull('onboarding_token_expires_at')
                  ->orWhere('onboarding_token_expires_at', '>=', now());
            })
            ->firstOrFail();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'date_of_birth' => ['nullable', 'date'],
            'national_id_number' => ['required', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:255'],
            'id_document' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'] ?? $member->email,
            'phone' => $validated['phone'] ?? $member->phone,
            'date_of_birth' => $validated['date_of_birth'] ?? $member->date_of_birth,
            'national_id_number' => $validated['national_id_number'],
            'address' => $validated['address'] ?? $member->address,
            'biodata_completed_at' => now(),
        ];

        if ($request->hasFile('id_document')) {
            if ($member->id_document_path) {
                Storage::disk('public')->delete($member->id_document_path);
            }

            $file = $request->file('id_document');
            $path = $file->store('member-ids', 'public');
            $data['id_document_path'] = $path;
        }

        // Keep token but optionally shorten expiry, so Chairperson can still see it
        $data['onboarding_token_expires_at'] = now()->addDays(1);

        $member->update($data);

        return view('onboarding.complete', compact('member'));
    }
}

