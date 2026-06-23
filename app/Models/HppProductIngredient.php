<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HppProductIngredient extends Model
{
    protected $fillable = [
        'hpp_product_id', 'raw_material_id', 'quantity', 'usage_unit',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
    ];

    public function hppProduct()
    {
        return $this->belongsTo(HppProduct::class);
    }

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class);
    }

    public function getTotalCostAttribute(): float
    {
        return (float) $this->quantity * (float) optional($this->rawMaterial)->price_per_unit;
    }
}
