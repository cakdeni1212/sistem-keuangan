<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RawMaterial extends Model
{
    protected $fillable = [
        'name', 'unit', 'stock_quantity', 'price_per_unit',
        'category', 'notes', 'is_active', 'created_by', 'updated_by',
    ];

    protected $casts = [
        'stock_quantity' => 'decimal:3',
        'price_per_unit' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function ingredients()
    {
        return $this->hasMany(HppProductIngredient::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
