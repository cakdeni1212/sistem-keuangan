<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Journal extends Model
{
    protected $fillable = [
        'journal_number', 'journal_date', 'description', 'reference',
        'journal_type', 'fiscal_period_id', 'created_by',
        'is_posted', 'posted_at', 'posted_by',
        'total_debit', 'total_credit',
    ];

    protected $casts = [
        'journal_date' => 'date',
        'is_posted' => 'boolean',
        'posted_at' => 'datetime',
        'total_debit' => 'float',
        'total_credit' => 'float',
    ];

    public function lines(): HasMany
    {
        return $this->hasMany(JournalEntryLine::class);
    }

    public function fiscalPeriod(): BelongsTo
    {
        return $this->belongsTo(FiscalPeriod::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function poster(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function post(): void
    {
        if ($this->is_posted) return;

        $totalDebit = $this->lines()->sum('debit');
        $totalCredit = $this->lines()->sum('credit');

        if (abs($totalDebit - $totalCredit) > 0.01) {
            throw new \Exception('Journal is not balanced. Debit: ' . $totalDebit . ' Credit: ' . $totalCredit);
        }

        $this->update([
            'is_posted' => true,
            'posted_at' => now(),
            'posted_by' => auth()->id(),
            'total_debit' => $totalDebit,
            'total_credit' => $totalCredit,
        ]);
    }

    public function unpost(): void
    {
        $this->update(['is_posted' => false, 'posted_at' => null, 'posted_by' => null]);
    }

    public static function journalTypes(): array
    {
        return ['general', 'sales', 'purchase', 'cash_receipt', 'cash_payment', 'bank', 'adjusting', 'opening', 'closing', 'depreciation', 'tax'];
    }

    public static function generateNumber(): string
    {
        $prefix = 'JV-' . date('Ym') . '-';
        $last = static::where('journal_number', 'like', $prefix . '%')->latest()->first();
        $seq = $last ? (int) substr($last->journal_number, -4) + 1 : 1;

        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}
