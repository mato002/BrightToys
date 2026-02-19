<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    protected $fillable = [
        'code', 'name', 'type', 'value', 'min_order_amount', 'max_discount',
        'usage_limit', 'usage_per_user', 'starts_at', 'ends_at', 'is_active',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function redemptions(): HasMany
    {
        return $this->hasMany(CouponRedemption::class);
    }

    public function isValidFor(float $subtotal, ?int $userId = null): bool
    {
        if (!$this->is_active) {
            return false;
        }
        if ($this->starts_at && now()->lt($this->starts_at)) {
            return false;
        }
        if ($this->ends_at && now()->gt($this->ends_at)) {
            return false;
        }
        if ($this->min_order_amount !== null && $subtotal < (float) $this->min_order_amount) {
            return false;
        }
        if ($this->usage_limit !== null && $this->redemptions()->count() >= $this->usage_limit) {
            return false;
        }
        if ($userId && $this->usage_per_user !== null) {
            $used = $this->redemptions()->where('user_id', $userId)->count();
            if ($used >= $this->usage_per_user) {
                return false;
            }
        }
        return true;
    }

    public function calculateDiscount(float $subtotal): float
    {
        if ($this->type === 'percentage') {
            $discount = $subtotal * ((float) $this->value / 100);
        } else {
            $discount = (float) $this->value;
        }
        if ($this->max_discount !== null && $discount > (float) $this->max_discount) {
            $discount = (float) $this->max_discount;
        }
        return round(min($discount, $subtotal), 2);
    }
}
