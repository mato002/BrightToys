<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $fillable = [
        'user_id',
        'partner_id',
        'approval_document_id',
        'name',
        'email',
        'phone',
        'date_of_birth',
        'national_id_number',
        'address',
        'id_document_path',
        'status',
        'onboarding_token',
        'onboarding_token_expires_at',
        'biodata_completed_at',
        'id_verified_at',
    ];

    protected $casts = [
        'onboarding_token_expires_at' => 'datetime',
        'biodata_completed_at' => 'datetime',
        'id_verified_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function wallets()
    {
        return $this->hasMany(MemberWallet::class);
    }

    public function approvalDocument()
    {
        return $this->belongsTo(Document::class, 'approval_document_id');
    }
}

