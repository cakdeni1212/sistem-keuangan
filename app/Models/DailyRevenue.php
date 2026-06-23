<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyRevenue extends Model
{
    protected $fillable = [
        'date',
        'qris_amount',
        'tunai_amount',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'date' => 'date',
        'qris_amount' => 'decimal:2',
        'tunai_amount' => 'decimal:2',
    ];

    // Total omset hari itu
    public function getTotalAttribute(): float
    {
        return (float) $this->qris_amount + (float) $this->tunai_amount;
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scope filter by year+month
    public function scopeForMonth($query, $year, $month)
    {
        return $query->whereYear('date', $year)->whereMonth('date', $month);
    }

    public function scopeForYear($query, $year)
    {
        return $query->whereYear('date', $year);
    }
}
