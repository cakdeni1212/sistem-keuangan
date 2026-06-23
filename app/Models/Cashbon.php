<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cashbon extends Model
{
    protected $fillable = [
        'debtor_name',
        'debtor_type',
        'employee_id',
        'amount',
        'description',
        'debt_date',
        'due_date',
        'paid_at',
        'status',
        'notes',
        'receipt_path',
        'payment_receipt_path',
        'out_transaction_id',
        'in_transaction_id',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'debt_date' => 'date',
        'due_date' => 'date',
        'paid_at' => 'date',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function outTransaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'out_transaction_id');
    }

    public function inTransaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'in_transaction_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status === 'lunas' ? 'Lunas' : 'Belum Bayar';
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->status === 'belum_bayar' && $this->due_date && $this->due_date->isPast();
    }

    public function scopeUnpaid($query)
    {
        return $query->where('status', 'belum_bayar');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'lunas');
    }
}
