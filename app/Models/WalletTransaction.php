<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    protected $fillable = [
        'member_wallet_id',
        'partner_id',
        'fund_type',
        'direction',
        'type',
        'amount',
        'balance_after',
        'occurred_at',
        'reference',
        'description',
        'source_type',
        'source_id',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'occurred_at' => 'datetime',
    ];

    public function wallet()
    {
        return $this->belongsTo(MemberWallet::class, 'member_wallet_id');
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function source()
    {
        return $this->morphTo();
    }
}

