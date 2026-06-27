<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    protected $fillable = ['code', 'name', 'account_type', 'normal_balance', 'parent_id', 'description', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Account::class, 'parent_id');
    }

    public function journalLines(): HasMany
    {
        return $this->hasMany(JournalEntryLine::class);
    }

    public function getBalanceAttribute(): float
    {
        $debits = $this->journalLines()->sum('debit');
        $credits = $this->journalLines()->sum('credit');

        if ($this->normal_balance === 'debit') {
            return $debits - $credits;
        }

        return $credits - $debits;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('account_type', $type);
    }

    public static function accountTypes(): array
    {
        return ['asset', 'liability', 'equity', 'revenue', 'expense'];
    }

    public static function normalBalanceFor(string $type): string
    {
        return match ($type) {
            'asset', 'expense' => 'debit',
            'liability', 'equity', 'revenue' => 'credit',
        };
    }
}
