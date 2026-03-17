<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'pos_order_id',
        'amount',
        'reason',
        'refunded_by',
        'status',
        'transaction_reference',
        'refunded_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'refunded_at' => 'datetime',
    ];

    public function posOrder()
    {
        return $this->belongsTo(PosOrder::class);
    }

    public function refundedByUser()
    {
        return $this->belongsTo(User::class, 'refunded_by');
    }
}
