<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class MonthlyContributionPenaltyRate extends Model
{
    protected $fillable = [
        'name',
        'rate',
        'effective_from',
        'effective_to',
        'is_active',
        'description',
        'created_by',
    ];

    protected $casts = [
        'rate' => 'decimal:4',
        'effective_from' => 'date',
        'effective_to' => 'date',
        'is_active' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the active rate for a given date (defaults to current date)
     */
    public static function getActiveRateForDate($date = null)
    {
        $date = $date ? Carbon::parse($date) : Carbon::now();
        
        return static::where('is_active', true)
            ->where('effective_from', '<=', $date->format('Y-m-d'))
            ->where(function ($query) use ($date) {
                $query->whereNull('effective_to')
                      ->orWhere('effective_to', '>=', $date->format('Y-m-d'));
            })
            ->orderBy('effective_from', 'desc')
            ->first();
    }

    /**
     * Get the rate that will be effective for the next month
     */
    public static function getNextMonthRate()
    {
        $nextMonth = Carbon::now()->addMonth()->startOfMonth();
        return static::getActiveRateForDate($nextMonth);
    }
}
