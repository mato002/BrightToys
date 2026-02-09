<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'status',
        'notes',
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
}

