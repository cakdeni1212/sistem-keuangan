<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailySale extends Model
{
    protected $fillable = [
        'sale_date', 'shift', 'hpp_product_id', 'product_name',
        'unit_price', 'hpp_per_unit',
        'quantity_sold', 'subtotal', 'hpp_total', 'profit',
        'created_by',
    ];

    protected $casts = [
        'sale_date' => 'date',
        'unit_price' => 'decimal:2',
        'hpp_per_unit' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'hpp_total' => 'decimal:2',
        'profit' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(HppProduct::class, 'hpp_product_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
