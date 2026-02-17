<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Services\MonthlyContributionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
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

        // Get monthly contribution status
        $monthlyContribution = MonthlyContributionService::forPartner($partner);

        return view('partner.profile.index', compact('user', 'partner', 'welfareWallet', 'investmentWallet', 'monthlyContribution'));
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

    /**
     * Show active sessions for the authenticated user
     */
    public function sessions()
    {
        $user = Auth::user();
        $currentSessionId = Session::getId();
        
        // Get all sessions for this user
        $sessions = DB::table('sessions')
            ->where('user_id', $user->id)
            ->orderBy('last_activity', 'desc')
            ->get()
            ->map(function ($session) use ($currentSessionId) {
                return [
                    'id' => $session->id,
                    'ip_address' => $session->ip_address,
                    'user_agent' => $session->user_agent,
                    'last_activity' => $session->last_activity,
                    'is_current' => $session->id === $currentSessionId,
                    'device' => $this->parseUserAgent($session->user_agent),
                ];
            });

        return view('partner.profile.sessions', compact('sessions', 'currentSessionId'));
    }

    /**
     * Revoke a specific session
     */
    public function revokeSession(Request $request, $sessionId)
    {
        $user = Auth::user();
        $currentSessionId = Session::getId();

        // Prevent revoking current session
        if ($sessionId === $currentSessionId) {
            return redirect()->route('partner.profile.sessions')
                ->with('error', 'You cannot revoke your current session.');
        }

        // Verify the session belongs to the user
        $session = DB::table('sessions')
            ->where('id', $sessionId)
            ->where('user_id', $user->id)
            ->first();

        if (!$session) {
            return redirect()->route('partner.profile.sessions')
                ->with('error', 'Session not found or does not belong to you.');
        }

        // Delete the session
        DB::table('sessions')->where('id', $sessionId)->delete();

        return redirect()->route('partner.profile.sessions')
            ->with('success', 'Session revoked successfully.');
    }

    /**
     * Revoke all other sessions (except current)
     */
    public function revokeAllOtherSessions()
    {
        $user = Auth::user();
        $currentSessionId = Session::getId();

        // Delete all sessions except current
        DB::table('sessions')
            ->where('user_id', $user->id)
            ->where('id', '!=', $currentSessionId)
            ->delete();

        return redirect()->route('partner.profile.sessions')
            ->with('success', 'All other sessions have been revoked.');
    }

    /**
     * Parse user agent string to extract device and browser info
     */
    private function parseUserAgent($userAgent)
    {
        if (!$userAgent) {
            return [
                'device' => 'Unknown Device',
                'browser' => 'Unknown Browser',
                'platform' => 'Unknown',
            ];
        }

        $device = 'Desktop';
        $browser = 'Unknown Browser';
        $platform = 'Unknown';

        // Detect device type
        if (preg_match('/mobile|android|iphone|ipad|ipod|blackberry|iemobile|opera mini/i', strtolower($userAgent))) {
            $device = 'Mobile';
            if (preg_match('/tablet|ipad|playbook|silk/i', strtolower($userAgent))) {
                $device = 'Tablet';
            }
        }

        // Detect browser
        if (preg_match('/chrome/i', $userAgent) && !preg_match('/edg/i', $userAgent)) {
            $browser = 'Chrome';
        } elseif (preg_match('/firefox/i', $userAgent)) {
            $browser = 'Firefox';
        } elseif (preg_match('/safari/i', $userAgent) && !preg_match('/chrome/i', $userAgent)) {
            $browser = 'Safari';
        } elseif (preg_match('/edg/i', $userAgent)) {
            $browser = 'Edge';
        } elseif (preg_match('/opera|opr/i', $userAgent)) {
            $browser = 'Opera';
        }

        // Detect platform
        if (preg_match('/windows/i', $userAgent)) {
            $platform = 'Windows';
        } elseif (preg_match('/macintosh|mac os x/i', $userAgent)) {
            $platform = 'macOS';
        } elseif (preg_match('/linux/i', $userAgent)) {
            $platform = 'Linux';
        } elseif (preg_match('/android/i', $userAgent)) {
            $platform = 'Android';
        } elseif (preg_match('/iphone|ipad|ipod/i', $userAgent)) {
            $platform = 'iOS';
        }

        return [
            'device' => $device,
            'browser' => $browser,
            'platform' => $platform,
            'full' => $userAgent,
        ];
    }
}

