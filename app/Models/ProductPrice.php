<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Products;

class ProductPrice extends Model
{
    protected $fillable = ['product_id', 'unit_name', 'qty', 'single_price', 'total_price', 'currency_name', 'currency_symbol','currency_id'];
    protected $casts = [
    'single_price' => 'decimal:2',
    'total_price' => 'decimal:2',
];
    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    // Automatically calculate single_price before saving
    public static function boot()
    {
        parent::boot();

        static::saving(function ($price) {
            if ($price->qty > 0) {
                $price->single_price = $price->total_price / $price->qty;
            }
        });
    }
}
