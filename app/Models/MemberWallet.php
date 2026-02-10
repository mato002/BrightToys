<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberWallet extends Model
{
    public const TYPE_WELFARE = 'welfare';
    public const TYPE_INVESTMENT = 'investment';

    protected $fillable = [
        'member_id',
        'type',
        'balance',
    ];

    protected $casts = [
        'balance' => 'float',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}

