<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Loan;
use App\Models\Cart;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AccountController extends Controller
{
    public function overview()
    {
        $user = auth()->user();

        // Dashboard Statistics
        $totalOrders = $user->orders()->count();
        $totalSpent = $user->orders()->where('status', 'completed')->sum('total');
        $pendingOrders = $user->orders()->where('status', 'pending')->count();
        $processingOrders = $user->orders()->where('status', 'processing')->count();
        $shippedOrders = $user->orders()->where('status', 'shipped')->count();
        $deliveredOrders = $user->orders()->where('status', 'delivered')->count();
        $completedOrders = $user->orders()->where('status', 'completed')->count();
        $cancelledOrders = $user->orders()->where('status', 'cancelled')->count();
        
        $cartItems = Cart::where('user_id', $user->id)->sum('quantity');
        $wishlistItems = Wishlist::where('user_id', $user->id)->count();
        $savedAddresses = $user->addresses()->count();
        
        // Recent orders
        $recentOrders = $user->orders()
            ->with('items.product')
            ->latest()
            ->take(5)
            ->get();
        
        // Orders this month
        $ordersThisMonth = $user->orders()
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();
        
        $spentThisMonth = $user->orders()
            ->where('status', 'completed')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('total');
        
        // Last 7 days orders
        $ordersLast7Days = collect(range(6, 0))->map(function ($daysAgo) use ($user) {
            $date = Carbon::today()->subDays($daysAgo);
            return [
                'date' => $date->format('M d'),
                'count' => $user->orders()->whereDate('created_at', $date)->count(),
                'total' => $user->orders()
                    ->where('status', 'completed')
                    ->whereDate('created_at', $date)
                    ->sum('total'),
            ];
        });

        $stats = [
            'total_orders' => $totalOrders,
            'total_spent' => $totalSpent,
            'pending_orders' => $pendingOrders,
            'processing_orders' => $processingOrders,
            'shipped_orders' => $shippedOrders,
            'delivered_orders' => $deliveredOrders,
            'completed_orders' => $completedOrders,
            'cancelled_orders' => $cancelledOrders,
            'cart_items' => $cartItems,
            'wishlist_items' => $wishlistItems,
            'saved_addresses' => $savedAddresses,
            'orders_this_month' => $ordersThisMonth,
            'spent_this_month' => $spentThisMonth,
        ];

        return view('frontend.account.overview', compact('user', 'stats', 'recentOrders', 'ordersLast7Days'));
    }

    public function profile()
    {
        $user = auth()->user();
        return view('frontend.account.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        
        if (isset($validated['phone'])) {
            $user->phone = $validated['phone'];
        }

        if (!empty($validated['password'])) {
            $user->password = bcrypt($validated['password']);
        }

        $user->save();

        return redirect()->route('account.profile')
            ->with('success', 'Profile updated successfully.');
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

    public function loans()
    {
        $user = auth()->user();
        
        // Members can view all group loans (read-only)
        $query = Loan::with(['project', 'schedules.repayments', 'repayments']);
        
        if ($status = request('status')) {
            $query->where('status', $status);
        }
        
        if ($search = request('search')) {
            $query->where('lender_name', 'like', "%{$search}%");
        }
        
        $loans = $query->latest()->paginate(10)->withQueryString();
        
        return view('frontend.account.loans', compact('user', 'loans'));
    }

    public function showLoan(Loan $loan)
    {
        $user = auth()->user();
        
        // Members can view loans in read-only mode
        $loan->load(['project', 'schedules.repayments', 'repayments']);
        
        // Calculate outstanding balances (same logic as admin)
        $totalPrincipalScheduled = $loan->schedules->sum('principal_due');
        $totalInterestScheduled = $loan->schedules->sum('interest_due');
        $totalScheduled = $totalPrincipalScheduled + $totalInterestScheduled;
        $totalPaid = $loan->repayments->sum('amount_paid');
        $principalPaid = $totalScheduled > 0 
            ? ($totalPaid * ($totalPrincipalScheduled / $totalScheduled))
            : 0;
        $principalOutstanding = max(0, $totalPrincipalScheduled - $principalPaid);
        $interestOutstanding = max(0, $totalInterestScheduled - ($totalPaid - $principalPaid));
        $totalOutstanding = $principalOutstanding + $interestOutstanding;
        
        // Calculate remaining tenure
        $startDate = $loan->start_date ?? $loan->created_at;
        $monthsElapsed = $startDate->diffInMonths(now());
        $remainingTenure = max(0, $loan->tenure_months - $monthsElapsed);
        
        // Calculate status
        $status = 'active';
        $overduePeriods = 0;
        foreach ($loan->schedules as $schedule) {
            $schedulePaid = $schedule->repayments->sum('amount_paid');
            $isDue = $schedule->due_date->isPast();
            if ($isDue && $schedulePaid < $schedule->total_due * 0.99) {
                $overduePeriods++;
            }
        }
        if ($totalOutstanding <= 0.01) {
            $status = 'repaid';
        } elseif ($overduePeriods > 0) {
            $status = 'in_arrears';
        }
        
        return view('frontend.account.loan-show', compact(
            'loan',
            'principalOutstanding',
            'interestOutstanding',
            'totalOutstanding',
            'remainingTenure',
            'status'
        ));
    }

    public function notifications()
    {
        $user = auth()->user();
        
        $notifications = \App\Models\SystemNotification::where('user_id', $user->id)
            ->latest()
            ->paginate(20);
        
        // Mark as read when viewing
        \App\Models\SystemNotification::where('user_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
        
        return view('frontend.account.notifications', compact('user', 'notifications'));
    }
}

