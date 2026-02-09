<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Project extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'type',
        'url',
        'route_name',
        'icon',
        'color',
        'is_active',
        'sort_order',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
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
}
