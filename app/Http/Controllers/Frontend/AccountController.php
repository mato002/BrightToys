<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function profile()
    {
        $user = auth()->user();

        return view('frontend.account.profile', compact('user'));
    }

    public function orders()
    {
        $user = auth()->user();
        $query = $user->orders()->with('items.product');

        if ($status = request('status')) {
            $query->where('status', $status);
        }

        if ($from = request('from_date')) {
            $query->whereDate('created_at', '>=', $from);
        }

        if ($to = request('to_date')) {
            $query->whereDate('created_at', '<=', $to);
        }

        $orders = $query->latest()->paginate(10)->withQueryString();

        return view('frontend.account.orders', compact('user', 'orders'));
    }

    public function addresses()
    {
        $user = auth()->user();
        $addresses = $user->addresses()->latest()->get();

        return view('frontend.account.addresses', compact('user', 'addresses'));
    }

    public function storeAddress(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address_line_1' => ['required', 'string', 'max:500'],
            'address_line_2' => ['nullable', 'string', 'max:500'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'country' => ['required', 'string', 'max:100'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        // If this is set as default, unset other defaults
        if ($request->has('is_default') && $request->is_default) {
            $user->addresses()->update(['is_default' => false]);
        }

        $user->addresses()->create($validated);

        return redirect()->route('account.addresses')
            ->with('success', 'Address added successfully.');
    }

    public function destroyAddress($id)
    {
        $user = auth()->user();
        $address = $user->addresses()->findOrFail($id);
        $address->delete();

        return redirect()->route('account.addresses')
            ->with('success', 'Address deleted successfully.');
    }
}

