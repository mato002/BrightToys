<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberWallet extends Model
{
    public const TYPE_WELFARE = 'welfare';
    public const TYPE_INVESTMENT = 'investment';

    protected $fillable = [
        'partner_id', // Changed from member_id - members and partners are the same
        'type',
        'balance',
    ];

    protected $casts = [
        'balance' => 'float',
    ];

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class, 'member_wallet_id');
    }

    // Alias for backward compatibility during transition
    public function member()
    {
        return $this->partner();
    }
}

