<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;

    public const TYPE_POS_SALE = 'pos_sale';
    public const TYPE_ECOMMERCE_ORDER = 'ecommerce_order';
    public const TYPE_ADJUSTMENT_IN = 'adjustment_in';
    public const TYPE_ADJUSTMENT_OUT = 'adjustment_out';
    public const TYPE_RETURN_RESTORE = 'return_restore';
    public const TYPE_ORDER_CANCEL_RESTORE = 'order_cancel_restore';

    protected $fillable = [
        'product_id',
        'quantity',
        'balance_after',
        'type',
        'reference_type',
        'reference_id',
        'user_id',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'balance_after' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
