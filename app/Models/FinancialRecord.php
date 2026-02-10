<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialRecord extends Model
{
    protected $fillable = [
        'type',
        'fund_type',
        'category',
        'amount',
        'currency',
        'paid_from',
        'occurred_at',
        'description',
        'order_id',
        'partner_id',
        'project_id',
        'status',
        'created_by',
        'approved_by',
        'approved_at',
        'is_archived',
        'archived_at',
        'archived_by',
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
        'approved_at' => 'datetime',
        'archived_at' => 'datetime',
        'is_archived' => 'boolean',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
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

    public function documents()
    {
        return $this->hasMany(FinancialRecordDocument::class);
    }
}

