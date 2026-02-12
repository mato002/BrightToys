<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyAsset extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'asset_code',
        'asset_type',
        'account_id',
        'purchase_value',
        'current_value',
        'purchase_date',
        'depreciation_start_date',
        'depreciation_method',
        'depreciation_rate',
        'description',
        'location',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'depreciation_start_date' => 'date',
        'purchase_value' => 'decimal:2',
        'current_value' => 'decimal:2',
        'depreciation_rate' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'account_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
