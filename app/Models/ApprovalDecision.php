<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApprovalDecision extends Model
{
    protected $fillable = [
        'approval_id',
        'user_id',
        'role_used',
        'decision',
        'comment',
    ];

    public function approval()
    {
        return $this->belongsTo(Approval::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

