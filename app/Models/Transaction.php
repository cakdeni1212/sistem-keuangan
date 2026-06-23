<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = [
        'transaction_date',
        'transaction_type_id',
        'amount',
        'description',
        'nota_path',
        'status',
        'created_by',
        'approved_by',
        'approved_at',
        'rejection_reason',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'approved_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function transactionType(): BelongsTo
    {
        return $this->belongsTo(TransactionType::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getNotaUrlAttribute(): ?string
    {
        return $this->nota_path ? asset('storage/'.$this->nota_path) : null;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'Draft',
            'pending' => 'Menunggu Approval',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'gray',
            'pending' => 'yellow',
            'approved' => 'green',
            'rejected' => 'red',
            default => 'gray',
        };
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($q, $s) {
            // Bersihkan pemisah ribuan jika ada (50.000 → 50000)
            $cleaned = str_replace(['.', ','], '', $s);
            $q->where(function ($sub) use ($s, $cleaned) {
                // Catatan / deskripsi
                $sub->where('description', 'like', "%{$s}%")
                    // Jenis transaksi
                    ->orWhereHas('transactionType', fn ($t) => $t->where('name', 'like', "%{$s}%"))
                    // Jumlah — cast ke string agar bisa LIKE (partial match)
                    ->orWhereRaw('CAST(amount AS CHAR) LIKE ?', ["%{$cleaned}%"]);
            });
        });
        $query->when($filters['category'] ?? null, fn ($q, $c) => $q->whereHas('transactionType', fn ($t) => $t->where('category', $c))
        );
        $query->when($filters['status'] ?? null, fn ($q, $s) => $q->where('status', $s)
        );
        $query->when($filters['from'] ?? null, fn ($q, $d) => $q->whereDate('transaction_date', '>=', $d)
        );
        $query->when($filters['to'] ?? null, fn ($q, $d) => $q->whereDate('transaction_date', '<=', $d)
        );
    }
}
