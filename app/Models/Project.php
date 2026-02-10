<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Project extends Model
{
    public const TYPES = ['ecommerce', 'land', 'business', 'trading', 'other'];
    public const STATUSES = ['planning', 'active', 'completed', 'suspended'];

    protected $fillable = [
        'name',
        'slug',
        'description',
        'objective',
        'type',
        'status',
        'url',
        'route_name',
        'icon',
        'color',
        'is_active',
        'sort_order',
        'created_by',
        'created_by_user_id',
        'activated_by_user_id',
        'activated_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'activated_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($project) {
            if (empty($project->slug)) {
                $project->slug = Str::slug($project->name);
            }
        });
    }

    /**
     * Get the project URL - either external URL or Laravel route
     */
    public function getProjectUrlAttribute()
    {
        if ($this->url) {
            return $this->url;
        }

        if ($this->route_name && \Route::has($this->route_name)) {
            return route($this->route_name);
        }

        return '#';
    }

    /**
     * Partner who created this project
     */
    public function creator()
    {
        return $this->belongsTo(Partner::class, 'created_by');
    }

    /**
     * Users assigned to manage this project
     */
    public function assignedUsers()
    {
        return $this->belongsToMany(User::class, 'project_user')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Capital & funding structure for this project
     */
    public function funding()
    {
        return $this->hasOne(ProjectFunding::class);
    }

    public function loanRequirements()
    {
        return $this->hasManyThrough(
            ProjectLoanRequirement::class,
            ProjectFunding::class,
            'project_id',
            'project_funding_id'
        );
    }

    public function assets()
    {
        return $this->hasMany(ProjectAsset::class);
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public function kpi()
    {
        return $this->hasOne(ProjectKpi::class);
    }

    /**
     * Helper to compute equity/debt percentages based on funding
     */
    public function getCapitalMixAttribute(): array
    {
        $funding = $this->funding;

        if (!$funding) {
            return [
                'equity' => 0,
                'debt' => 0,
                'total' => 0,
            ];
        }

        $equity = (float) ($funding->member_capital_amount ?? 0);
        $debt = (float) ($funding->outstanding_balance ?? 0);
        $total = $equity + $debt;

        if ($total <= 0) {
            return [
                'equity' => 0,
                'debt' => 0,
                'total' => 0,
            ];
        }

        return [
            'equity' => round(($equity / $total) * 100, 1),
            'debt' => round(($debt / $total) * 100, 1),
            'total' => $total,
        ];
    }
}
