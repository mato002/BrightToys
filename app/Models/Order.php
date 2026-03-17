<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    public const SOURCE_ECOMMERCE = 'ecommerce';
    public const SOURCE_POS = 'pos';

    protected $fillable = [
        'user_id',
        'order_number',
        'tracking_number',
        'total',
        'status',
        'payment_method',
        'payment_status',
        'shipping_address',
        'phone',
        'notes',
        'coupon_id',
        'discount_amount',
        'source',
    ];

    protected $casts = [
        'total' => 'float',
        'discount_amount' => 'float',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'BT-' . strtoupper(uniqid());
            }
            if (empty($order->tracking_number)) {
                $order->tracking_number = 'TRK-' . strtoupper(uniqid());
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }
}

