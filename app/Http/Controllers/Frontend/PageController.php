<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function about()
    {
        return view('frontend.pages.about');
    }

    public function contact()
    {
        return view('frontend.pages.contact');
    }

    public function submitContact(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
            'ticket_type' => ['nullable', 'in:general,complaint,return,refund'],
            'order_number' => ['nullable', 'string', 'max:50'],
        ]);

        SupportTicket::create([
            'user_id' => auth()->id(),
            'name' => $data['name'],
            'email' => $data['email'],
            'subject' => $data['subject'] ?? 'General enquiry',
            'message' => $data['message'],
            'status' => 'open',
            'ticket_type' => $data['ticket_type'] ?? 'general',
            'order_number' => $data['order_number'] ?? null,
        ]);

        $redirectRoute = auth()->check() ? 'account.contact' : 'pages.contact';
        return redirect()
            ->route($redirectRoute)
            ->with('status', 'Thank you for contacting us. Our support team will get back to you shortly.');
    }

    public function policies()
    {
        return view('frontend.pages.policies');
    }
}

