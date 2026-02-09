<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
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

    public function trackOrder(Order $order)
    {
        $user = auth()->user();
        
        // Ensure user owns this order
        if ($order->user_id !== $user->id) {
            abort(403, 'You do not have permission to view this order.');
        }

        $order->load('items.product');
        
        return view('frontend.account.track-order', compact('order'));
    }

    public function invoice(Order $order)
    {
        $user = auth()->user();
        
        // Ensure user owns this order
        if ($order->user_id !== $user->id) {
            abort(403, 'You do not have permission to view this invoice.');
        }

        $order->load('items.product', 'user');
        
        try {
            if (ob_get_level()) {
                ob_end_clean();
            }

            $html = view('frontend.account.invoice', compact('order'))->render();
            
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->getOptions()->set('isRemoteEnabled', true);
            $dompdf->getOptions()->set('isHtml5ParserEnabled', true);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            
            return $dompdf->stream('invoice_' . $order->order_number . '.pdf', ['Attachment' => false]);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to generate invoice: ' . $e->getMessage()]);
        }
    }

    public function cancelOrder(Request $request, Order $order)
    {
        $user = auth()->user();
        
        // Ensure user owns this order
        if ($order->user_id !== $user->id) {
            abort(403, 'You do not have permission to cancel this order.');
        }

        // Only allow cancellation if order is pending or processing
        if (!in_array($order->status, ['pending', 'processing'])) {
            return back()->withErrors(['error' => 'This order cannot be cancelled.']);
        }

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            // Update order status
            $order->status = 'cancelled';
            $order->save();

            // Restore stock
            foreach ($order->items as $item) {
                if ($item->product) {
                    $item->product->stock += $item->quantity;
                    $item->product->save();
                }
            }

            \Illuminate\Support\Facades\DB::commit();

            // Send email notification
            try {
                \Illuminate\Support\Facades\Mail::to($user->email)
                    ->send(new \App\Mail\OrderStatusUpdateMail($order, 'processing', 'cancelled'));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('Failed to send cancellation email: ' . $e->getMessage());
            }

            return redirect()
                ->route('account.orders')
                ->with('success', 'Order cancelled successfully. Stock has been restored.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Order cancellation error: ' . $e->getMessage());

            return back()->withErrors(['error' => 'Failed to cancel order. Please contact support.']);
        }
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

