<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialRecordDocument extends Model
{
    protected $fillable = [
        'financial_record_id',
        'file_path',
        'original_name',
        'mime_type',
        'size',
        'uploaded_by',
    ];

    protected $casts = [
        'size' => 'integer',
    ];

    public function financialRecord()
    {
        return $this->belongsTo(FinancialRecord::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
