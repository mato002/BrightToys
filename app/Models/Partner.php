<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    protected $fillable = [
        'user_id',
        'approval_document_id',
        'name',
        'email',
        'phone',
        'status',
        'notes',
        // Member/onboarding fields
        'onboarding_token',
        'onboarding_token_expires_at',
        'biodata_completed_at',
        'id_verified_at',
        'date_of_birth',
        'national_id_number',
        'address',
        'id_document_path',
    ];

    protected $casts = [
        'onboarding_token_expires_at' => 'datetime',
        'biodata_completed_at' => 'datetime',
        'id_verified_at' => 'datetime',
        'date_of_birth' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ownerships()
    {
        return $this->hasMany(PartnerOwnership::class);
    }

    public function contributions()
    {
        return $this->hasMany(PartnerContribution::class);
    }

    public function financialRecords()
    {
        return $this->hasMany(FinancialRecord::class);
    }

    /**
     * Projects created by this partner
     */
    public function createdProjects()
    {
        return $this->hasMany(Project::class, 'created_by');
    }

    /**
     * Approval document (meeting minutes/resolution) that approved this partner/member
     */
    public function approvalDocument()
    {
        return $this->belongsTo(Document::class, 'approval_document_id');
    }

    /**
     * Wallets for this partner/member (welfare and investment)
     */
    public function wallets()
    {
        return $this->hasMany(MemberWallet::class, 'partner_id');
    }

    /**
     * Entry contribution for this partner/member
     */
    public function entryContribution()
    {
        return $this->hasOne(EntryContribution::class);
    }

    /**
     * Get the current ownership percentage for this partner (based on effective dates).
     */
    public function getCurrentOwnershipPercentage(): float
    {
        $currentOwnership = $this->ownerships()
            ->where('effective_from', '<=', now())
            ->where(function ($q) {
                $q->whereNull('effective_to')
                  ->orWhere('effective_to', '>=', now());
            })
            ->first();

        return $currentOwnership ? (float) $currentOwnership->percentage : 0.0;
    }
}

