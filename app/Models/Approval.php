<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Approval extends Model
{
    protected $fillable = [
        'action',
        'subject_type',
        'subject_id',
        'created_by',
        'status',
    ];

    public function subject()
    {
        return $this->morphTo();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function decisions()
    {
        return $this->hasMany(ApprovalDecision::class);
    }
}

