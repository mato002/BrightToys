<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartnerOwnership extends Model
{
    protected $fillable = [
        'partner_id',
        'percentage',
        'effective_from',
        'effective_to',
    ];

    protected $casts = [
        'effective_from' => 'date',
        'effective_to' => 'date',
    ];

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }
}

