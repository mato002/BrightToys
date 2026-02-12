<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class JournalEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'transaction_date',
        'reference_number',
        'transaction_details',
        'comments',
        'branch_id',
        'branch_name',
        'status',
        'posted_by',
        'posted_at',
        'created_by',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'posted_at' => 'datetime',
    ];

    public static function generateTransactionId(): string
    {
        $date = Carbon::now()->format('Ymd');
        $lastEntry = self::whereDate('created_at', Carbon::today())
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = $lastEntry ? (int)substr($lastEntry->transaction_id, -4) + 1 : 1;
        
        return $date . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    public function lines(): HasMany
    {
        return $this->hasMany(JournalEntryLine::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function poster(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function getTotalDebitAttribute(): float
    {
        return $this->lines()->where('entry_type', 'debit')->sum('amount');
    }

    public function getTotalCreditAttribute(): float
    {
        return $this->lines()->where('entry_type', 'credit')->sum('amount');
    }

    public function isBalanced(): bool
    {
        return abs($this->total_debit - $this->total_credit) < 0.01;
    }
}
