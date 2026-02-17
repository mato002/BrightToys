<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WelfareRule extends Model
{
    protected $fillable = [
        'rule_type',
        'name',
        'description',
        'max_amount',
        'max_per_year',
        'min_months_membership',
        'requires_approval',
        'approval_levels',
        'required_documents',
        'is_active',
        'priority',
    ];

    protected $casts = [
        'max_amount' => 'decimal:2',
        'requires_approval' => 'boolean',
        'approval_levels' => 'array',
        'required_documents' => 'array',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('rule_type', $type);
    }
}
