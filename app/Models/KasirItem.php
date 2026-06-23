<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KasirItem extends Model
{
    protected $fillable = [
        'kasir_session_id', 'hpp_product_id', 'product_name',
        'product_price', 'quantity', 'subtotal',
    ];

    protected $casts = [
        'product_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'quantity' => 'integer',
    ];

    public function session()
    {
        return $this->belongsTo(KasirSession::class, 'kasir_session_id');
    }

    public function product()
    {
        return $this->belongsTo(HppProduct::class, 'hpp_product_id');
    }
}
