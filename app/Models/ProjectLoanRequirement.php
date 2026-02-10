<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectLoanRequirement extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_APPROVED = 'approved';

    public const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_SUBMITTED,
        self::STATUS_APPROVED,
    ];

    protected $fillable = [
        'project_funding_id',
        'name',
        'status',
        'responsible_user_id',
        'due_date',
        'submitted_at',
        'approved_at',
        'notes',
    ];

    protected $casts = [
        'due_date' => 'date',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function funding()
    {
        return $this->belongsTo(ProjectFunding::class, 'project_funding_id');
    }

    public function responsibleOfficer()
    {
        return $this->belongsTo(User::class, 'responsible_user_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }
}

