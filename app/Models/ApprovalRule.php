<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApprovalRule extends Model
{
    protected $fillable = [
        'action',
        'min_approvals',
        'required_roles',
        'allow_initiator_approve',
        'enabled',
    ];

    protected $casts = [
        'required_roles' => 'array',
        'allow_initiator_approve' => 'boolean',
        'enabled' => 'boolean',
    ];
}

