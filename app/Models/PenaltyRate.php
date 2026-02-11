<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenaltyRate extends Model
{
    protected $fillable = [
        'name',
        'rate',
        'calculation_method',
        'grace_period_days',
        'max_penalty_amount',
        'is_active',
        'description',
        'created_by',
    ];

    protected $casts = [
        'rate' => 'decimal:2',
        'max_penalty_amount' => 'decimal:2',
        'is_active' => 'boolean',
        'grace_period_days' => 'integer',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
