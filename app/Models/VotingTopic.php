<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VotingTopic extends Model
{
    protected $fillable = [
        'title',
        'description',
        'status',
        'opens_at',
        'closes_at',
        'created_by',
    ];

    protected $casts = [
        'opens_at' => 'datetime',
        'closes_at' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    public function scopeOpen($query)
    {
        // Treat "status = open" as the main control and only hide topics
        // that have a closing time in the past.
        return $query->where('status', 'open')
            ->where(function ($q) {
                $q->whereNull('closes_at')->orWhere('closes_at', '>=', now());
            });
    }
}
