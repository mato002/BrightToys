<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    protected $fillable = [
        'voting_topic_id',
        'partner_id',
        'choice',
        'weight_percentage',
        'weight_value',
        'cast_at',
        'created_by',
    ];

    protected $casts = [
        'weight_percentage' => 'decimal:2',
        'weight_value' => 'decimal:6',
        'cast_at' => 'datetime',
    ];

    public function topic()
    {
        return $this->belongsTo(VotingTopic::class, 'voting_topic_id');
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
