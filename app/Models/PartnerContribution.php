<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartnerContribution extends Model
{
    protected $fillable = [
        'partner_id',
        'type',
        'fund_type',
        'amount',
        'currency',
        'contributed_at',
        'reference',
        'notes',
        'status',
        'created_by',
        'approved_by',
        'approved_at',
        'is_archived',
        'archived_at',
        'archived_by',
    ];

    protected $casts = [
        'contributed_at' => 'datetime',
        'approved_at' => 'datetime',
        'archived_at' => 'datetime',
        'is_archived' => 'boolean',
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

    public function archiver()
    {
        return $this->belongsTo(User::class, 'archived_by');
    }
}

