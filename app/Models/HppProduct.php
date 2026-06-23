<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HppProduct extends Model
{
    protected $fillable = [
        'name', 'sku', 'category', 'satuan', 'stok_minimum',
        'bahan_baku', 'tenaga_kerja', 'overhead',
        'harga_jual', 'notes', 'is_active',
        'created_by', 'updated_by',
    ];

    protected $casts = [
        'bahan_baku' => 'decimal:2',
        'tenaga_kerja' => 'decimal:2',
        'overhead' => 'decimal:2',
        'harga_jual' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function ingredients()
    {
        return $this->hasMany(HppProductIngredient::class);
    }

    public function getIngredientsBahanBakuAttribute(): float
    {
        if ($this->relationLoaded('ingredients')) {
            return $this->ingredients->sum(fn ($i) => $i->total_cost);
        }

        return 0.0;
    }

    // Total HPP per unit
    public function getHppTotalAttribute(): float
    {
        $ingredientCost = $this->getIngredientsBahanBakuAttribute();

        if ($ingredientCost > 0) {
            return $ingredientCost + (float) $this->tenaga_kerja + (float) $this->overhead;
        }

        return (float) $this->bahan_baku + (float) $this->tenaga_kerja + (float) $this->overhead;
    }

    // Margin keuntungan dalam rupiah
    public function getMarginAmountAttribute(): float
    {
        return (float) $this->harga_jual - $this->hpp_total;
    }

    // Markup keuntungan dalam persen (laba / HPP × 100)
    public function getMarginPercentAttribute(): float
    {
        if ($this->hpp_total <= 0) {
            return 0;
        }

        return ($this->margin_amount / $this->hpp_total) * 100;
    }

    // Persentase bahan baku dari HPP
    public function getBahanBakuPercentAttribute(): float
    {
        if ($this->hpp_total <= 0) {
            return 0;
        }

        return ((float) $this->bahan_baku / $this->hpp_total) * 100;
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
