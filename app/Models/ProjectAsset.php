<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectAsset extends Model
{
    protected $fillable = [
        'project_id',
        'name',
        'category',
        'acquisition_cost',
        'date_acquired',
        'current_value',
        'notes',
        'supporting_document_path',
        'supporting_document_name',
        'created_by',
    ];

    protected $casts = [
        'acquisition_cost' => 'float',
        'current_value' => 'float',
        'date_acquired' => 'date',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

