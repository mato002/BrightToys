<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'pos_order_id',
        'receipt_number',
        'copy_number',
        'printed_at',
    ];

    protected $casts = [
        'printed_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function (Receipt $receipt) {
            if (empty($receipt->receipt_number)) {
                $receipt->receipt_number = self::generateReceiptNumber();
            }
        });
    }

    public static function generateReceiptNumber(): string
    {
        $prefix = 'RCP-' . now()->format('Y');
        $last = static::where('receipt_number', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('receipt_number');
        $seq = $last ? (int) substr($last, -5) + 1 : 1;
        return $prefix . '-' . str_pad((string) $seq, 5, '0', STR_PAD_LEFT);
    }

    public function posOrder()
    {
        return $this->belongsTo(PosOrder::class);
    }
}
