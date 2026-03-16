<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_head_id', 'product_id', 'product_name', 'product_sku',
        'unit_price', 'cost_price', 'quantity',
        'discount_percent', 'discount_amount', 'subtotal'
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function saleHead()
    {
        return $this->belongsTo(SaleHead::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}