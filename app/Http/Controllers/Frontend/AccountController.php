<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Loan;
use App\Models\Cart;
use App\Models\Wishlist;
use App\Models\Review;
use App\Models\ReviewImage;
use App\Models\SupportTicket;
use App\Models\SupportTicketReply;
use App\Models\Product;
use App\Models\CouponRedemption;
use App\Models\RewardPointTransaction;
use App\Models\CustomerWallet;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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

        // Recently viewed product IDs (from session, same key as product page)
        $recentlyViewedIds = session('recently_viewed', []);
        $recentlyViewed = $recentlyViewedIds
            ? Product::whereIn('id', array_slice(array_reverse($recentlyViewedIds), 0, 6))
                ->where('status', 'active')
                ->with('category')
                ->get()
            : collect();

        // Recommended: from categories of recent orders or featured
        $recommended = Product::where('status', 'active')
            ->where('featured', true)
            ->with('category')
            ->inRandomOrder()
            ->take(6)
            ->get();
        if ($recommended->count() < 6) {
            $extra = Product::where('status', 'active')
                ->whereNotIn('id', $recommended->pluck('id'))
                ->inRandomOrder()
                ->take(6 - $recommended->count())
                ->get();
            $recommended = $recommended->merge($extra);
        }
        
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

        // Monthly orders & spending for current year (for charts)
        $ordersByMonth = collect(range(1, 12))->map(function ($month) use ($user) {
            return [
                'month' => Carbon::createFromDate(Carbon::now()->year, $month, 1)->format('M'),
                'orders' => $user->orders()
                    ->whereMonth('created_at', $month)
                    ->whereYear('created_at', Carbon::now()->year)
                    ->count(),
                'spent' => $user->orders()
                    ->where('status', 'completed')
                    ->whereMonth('created_at', $month)
                    ->whereYear('created_at', Carbon::now()->year)
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

        // Profile completeness (name, email, phone, default address)
        $profileCompleteness = 0;
        $profileTotal = 4;
        if ($user->name) $profileCompleteness++;
        if ($user->email) $profileCompleteness++;
        if ($user->phone) $profileCompleteness++;
        if ($user->addresses()->where('is_default', true)->exists()) $profileCompleteness++;
        $profileCompletenessPercent = $profileTotal > 0 ? round(100 * $profileCompleteness / $profileTotal) : 0;

        return view('frontend.account.overview', compact(
            'user', 'stats', 'recentOrders', 'ordersLast7Days', 'ordersByMonth',
            'recentlyViewed', 'recommended', 'profileCompletenessPercent', 'profileCompleteness', 'profileTotal'
        ));
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

        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                // Search by order number or ID
                if (is_numeric($search)) {
                    $q->where('id', $search)
                      ->orWhere('order_number', 'like', "%{$search}%");
                } else {
                    $q->where('order_number', 'like', "%{$search}%");
                }
                
                // Search by tracking number
                $q->orWhere('tracking_number', 'like', "%{$search}%");
                
                // Search by product names in order items
                $q->orWhereHas('items.product', function ($productQuery) use ($search) {
                    $productQuery->where('name', 'like', "%{$search}%");
                });
            });
        }

        $orders = $query->latest()->paginate(10)->withQueryString();

        return view('frontend.account.orders', compact('user', 'orders'));
    }

    /**
     * Export orders to CSV
     */
    public function exportOrders()
    {
        $user = auth()->user();
        
        try {
            if (ob_get_level()) {
                ob_end_clean();
            }

            $query = $user->orders()->with('items.product');

            // Apply same filters as orders page
            if ($status = request('status')) {
                $query->where('status', $status);
            }

            if ($from = request('from_date')) {
                $query->whereDate('created_at', '>=', $from);
            }

            if ($to = request('to_date')) {
                $query->whereDate('created_at', '<=', $to);
            }

            if ($search = request('search')) {
                $query->where(function ($q) use ($search) {
                    if (is_numeric($search)) {
                        $q->where('id', $search)
                          ->orWhere('order_number', 'like', "%{$search}%");
                    } else {
                        $q->where('order_number', 'like', "%{$search}%");
                    }
                    $q->orWhere('tracking_number', 'like', "%{$search}%");
                    $q->orWhereHas('items.product', function ($productQuery) use ($search) {
                        $productQuery->where('name', 'like', "%{$search}%");
                    });
                });
            }

            $orders = $query->latest()->get();

            $filename = 'my_orders_' . date('Y-m-d_His') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0'
            ];

            $callback = function() use ($orders) {
                $file = fopen('php://output', 'w');
                
                // Add BOM for UTF-8
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                
                fputcsv($file, ['Order Number', 'Date', 'Status', 'Total (KES)', 'Tracking Number', 'Items Count', 'Products']);
                
                foreach ($orders as $order) {
                    $products = $order->items->map(function ($item) {
                        return ($item->product->name ?? 'N/A') . ' (x' . $item->quantity . ')';
                    })->implode('; ');
                    
                    fputcsv($file, [
                        $order->order_number ?? '#' . $order->id,
                        $order->created_at->format('Y-m-d H:i:s'),
                        ucfirst($order->status),
                        number_format($order->total, 2),
                        $order->tracking_number ?? 'N/A',
                        $order->items->count(),
                        $products,
                    ]);
                }
                
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            return redirect()->route('account.orders')
                ->with('error', 'Failed to export orders: ' . $e->getMessage());
        }
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
        
        $query = \App\Models\SystemNotification::where('user_id', $user->id);
        
        // Filter by type: order_updates, delivery, promotion, or all
        $filter = request('filter', 'all');
        if ($filter === 'order_updates') {
            $query->where(function ($q) {
                $q->where('type', 'like', '%order%')->orWhere('type', 'like', '%payment%');
            });
        } elseif ($filter === 'delivery') {
            $query->where(function ($q) {
                $q->where('type', 'like', '%shipping%')->orWhere('type', 'like', '%delivery%');
            });
        } elseif ($filter === 'promotion') {
            $query->where('type', 'like', '%promo%')->orWhere('type', 'like', '%promotion%');
        }
        
        $notifications = $query->latest()->paginate(20)->withQueryString();
        
        if (view()->exists('frontend.account.notifications.index')) {
            return view('frontend.account.notifications.index', compact('user', 'notifications'));
        }
        return view('frontend.account.notifications', compact('user', 'notifications'));
    }
    
    /**
     * Mark a single notification as read
     */
    public function markNotificationAsRead(Request $request, $notificationId)
    {
        $user = auth()->user();
        
        // Find notification belonging to this user
        $notification = \App\Models\SystemNotification::where('user_id', $user->id)
            ->where('id', $notificationId)
            ->firstOrFail();
        
        if (!$notification->read_at) {
            $notification->update(['read_at' => now()]);
        }
        
        return redirect()->back()->with('success', 'Notification marked as read.');
    }
    
    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsAsRead(Request $request)
    {
        $user = auth()->user();
        
        \App\Models\SystemNotification::where('user_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
        
        return redirect()->back()->with('success', 'All notifications marked as read.');
    }

    // ---------- My Reviews ----------
    public function reviews()
    {
        $user = auth()->user();
        $reviews = $user->reviews()->with(['product', 'images'])->latest()->paginate(10);
        return view('frontend.account.reviews.index', compact('user', 'reviews'));
    }

    public function editReview(Review $review)
    {
        $user = auth()->user();
        if ($review->user_id !== $user->id) {
            abort(403, 'You do not have permission to edit this review.');
        }
        $review->load(['product', 'images']);
        return view('frontend.account.reviews.edit', compact('review'));
    }

    public function updateReview(Request $request, Review $review)
    {
        $user = auth()->user();
        if ($review->user_id !== $user->id) {
            abort(403, 'You do not have permission to edit this review.');
        }
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'comment' => 'required|string|min:10|max:1000',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);
        $review->rating = $validated['rating'];
        $review->title = $validated['title'] ?? null;
        $review->comment = $validated['comment'];
        $review->status = 'pending'; // re-submit for approval
        $review->save();

        if ($request->hasFile('images')) {
            foreach ($review->images as $img) {
                Storage::disk('public')->delete($img->path);
            }
            $review->images()->delete();
            foreach ($request->file('images') as $index => $file) {
                $path = $file->store('reviews/' . $review->id, 'public');
                $review->images()->create(['path' => $path, 'sort_order' => $index]);
            }
        }

        return redirect()->route('account.reviews')->with('success', 'Review updated. It will be published after approval.');
    }

    // ---------- Reorder ----------
    public function reorder(Order $order)
    {
        $user = auth()->user();
        if ($order->user_id !== $user->id) {
            abort(403, 'You do not have permission to reorder this order.');
        }
        foreach ($order->items as $item) {
            if (!$item->product || $item->product->status !== 'active') {
                continue;
            }
            $existing = Cart::where('user_id', $user->id)->where('product_id', $item->product_id)->first();
            if ($existing) {
                $existing->quantity += $item->quantity;
                $existing->save();
            } else {
                Cart::create([
                    'user_id' => $user->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                ]);
            }
        }
        return redirect()->route('account.cart.index')->with('success', 'Order items added to cart. You can adjust quantities and checkout.');
    }

    // ---------- Support / My Tickets ----------
    public function supportTickets()
    {
        $user = auth()->user();
        $tickets = $user->supportTickets()->withCount('replies')->latest()->paginate(10);
        return view('frontend.account.support.index', compact('user', 'tickets'));
    }

    public function showSupportTicket(SupportTicket $ticket)
    {
        $user = auth()->user();
        if ($ticket->user_id !== $user->id) {
            abort(403, 'You do not have permission to view this ticket.');
        }
        $ticket->load(['replies.user']);
        return view('frontend.account.support.show', compact('ticket'));
    }

    public function createSupportTicket()
    {
        $user = auth()->user();
        $orders = $user->orders()->latest()->take(20)->get(['id', 'order_number', 'created_at']);
        return view('frontend.account.support.create', compact('user', 'orders'));
    }

    public function storeSupportTicket(Request $request)
    {
        $user = auth()->user();
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
            'ticket_type' => 'nullable|in:general,complaint,return,refund',
            'order_number' => 'nullable|string|max:50',
        ]);
        $ticket = $user->supportTickets()->create([
            'name' => $user->name,
            'email' => $user->email,
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'status' => 'open',
            'ticket_type' => $validated['ticket_type'] ?? 'general',
            'order_number' => $validated['order_number'] ?? null,
        ]);
        return redirect()->route('account.support.show', $ticket)->with('success', 'Support ticket created. We will respond shortly.');
    }

    public function replySupportTicket(Request $request, SupportTicket $ticket)
    {
        $user = auth()->user();
        if ($ticket->user_id !== $user->id) {
            abort(403, 'You do not have permission to reply to this ticket.');
        }
        $validated = $request->validate(['message' => 'required|string|max:5000']);
        $ticket->replies()->create([
            'user_id' => $user->id,
            'is_staff' => false,
            'message' => $validated['message'],
        ]);
        $ticket->update(['status' => 'open']);
        return redirect()->route('account.support.show', $ticket)->with('success', 'Reply sent.');
    }

    // ---------- Spending Analytics ----------
    public function analytics()
    {
        $user = auth()->user();
        $year = (int) request('year', date('Y'));
        $years = range(date('Y'), max(date('Y') - 5, date('Y') - 10));
        $orders = $user->orders()->where('status', 'completed')->whereYear('created_at', $year)->get();
        $totalYearlySpent = $orders->sum('total');
        $byMonth = collect(range(1, 12))->mapWithKeys(function ($m) use ($user, $year) {
            $total = $user->orders()->where('status', 'completed')->whereYear('created_at', $year)->whereMonth('created_at', $m)->sum('total');
            return [$m => $total];
        });
        $frequentProducts = OrderItem::whereIn('order_id', $user->orders()->where('status', 'completed')->select('id'))
            ->select('product_id', DB::raw('SUM(quantity) as total_qty'))
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->take(10)
            ->get()
            ->load('product');
        $monthlyBudget = (float) request('budget', 0);
        return view('frontend.account.analytics.index', compact(
            'user', 'year', 'years', 'totalYearlySpent', 'byMonth', 'frequentProducts', 'monthlyBudget'
        ));
    }

    // ---------- Coupons & Rewards ----------
    public function rewards()
    {
        $user = auth()->user();
        $couponHistory = $user->couponRedemptions()->with('coupon')->latest()->paginate(10, ['*'], 'coupons');
        $pointsBalance = $user->rewardPointTransactions()->sum('points');
        $pointsHistory = $user->rewardPointTransactions()->with('order')->latest()->take(20)->get();
        $wallet = $user->customerWallet;
        $walletBalance = $wallet ? (float) $wallet->balance : 0;
        $walletTransactions = $wallet ? $wallet->transactions()->latest()->take(20)->get() : collect();
        $referralCode = $user->referral_code ?? null;
        return view('frontend.account.rewards.index', compact(
            'user', 'couponHistory', 'pointsBalance', 'pointsHistory', 'walletBalance', 'walletTransactions', 'referralCode'
        ));
    }

    // ---------- FAQ ----------
    public function faq()
    {
        return view('frontend.account.faq');
    }

    // ---------- Contact (within account layout) ----------
    public function contact()
    {
        $user = auth()->user();
        return view('frontend.account.contact', compact('user'));
    }

    // ---------- Returns & refunds (dedicated page) ----------
    public function returns()
    {
        $user = auth()->user();
        $orders = $user->orders()->latest()->take(30)->get(['id', 'order_number', 'created_at', 'status']);
        return view('frontend.account.returns', compact('user', 'orders'));
    }
}

