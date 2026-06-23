<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KasirSession extends Model
{
    protected $fillable = [
        'date', 'shift', 'payment_method', 'total_amount', 'notes', 'created_by',
    ];

    protected $casts = [
        'date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(KasirItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
