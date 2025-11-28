<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItems extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
        'unit',
        'price',
    ];

    // A cart item belongs to a cart
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    // A cart item belongs to a product
    public function product()
    {
        return $this->belongsTo(Products::class)->with(['brands', 'category']);
    }
}