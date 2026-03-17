<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosOrder extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_REFUNDED = 'refunded';

    public const PAYMENT_STATUS_PENDING = 'pending';
    public const PAYMENT_STATUS_PARTIAL = 'partial';
    public const PAYMENT_STATUS_PAID = 'paid';
    public const PAYMENT_STATUS_REFUNDED = 'refunded';

    protected $fillable = [
        'order_number',
        'register_id',
        'user_id',
        'customer_id',
        'status',
        'payment_status',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total',
        'notes',
        'completed_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'completed_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function (PosOrder $order) {
            if (empty($order->order_number)) {
                $order->order_number = self::generateOrderNumber();
            }
        });
    }

    public static function generateOrderNumber(): string
    {
        $prefix = 'POS-' . now()->format('Y');
        $last = static::where('order_number', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('order_number');
        $seq = $last ? (int) substr($last, -5) + 1 : 1;
        return $prefix . '-' . str_pad((string) $seq, 5, '0', STR_PAD_LEFT);
    }

    public function register()
    {
        return $this->belongsTo(Register::class);
    }

    /** Cashier who created the order */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function items()
    {
        return $this->hasMany(PosOrderItem::class, 'pos_order_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'pos_order_id');
    }

    public function receipts()
    {
        return $this->hasMany(Receipt::class, 'pos_order_id');
    }

    public function refunds()
    {
        return $this->hasMany(Refund::class, 'pos_order_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'pos_order_items', 'pos_order_id', 'product_id')
            ->withPivot(['quantity', 'unit_price', 'discount_amount', 'line_total', 'product_name', 'product_sku'])
            ->withTimestamps();
    }
}
