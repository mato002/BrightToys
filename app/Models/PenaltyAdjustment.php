<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenaltyAdjustment extends Model
{
    protected $fillable = [
        'partner_id',
        'type',
        'scope',
        'target_year',
        'target_month',
        'amount',
        'paused_from',
        'paused_to',
        'status',
        'reason',
        'created_by',
        'approved_by',
        'approved_at',
        'rejected_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paused_from' => 'date',
        'paused_to' => 'date',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}

