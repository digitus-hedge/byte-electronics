<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderDetails extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id', 'product_id', 'part_no', 'brand_id', 'brand_name',
        'category_id', 'category_name', 'unit', 'unit_price', 'quantity', 
        'total_price', 'status_id'
    ];

    // Relationship with Order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Relationship with Product
    public function product()
    {
        return $this->belongsTo(Products::class);
    }

    // Relationship with Status
    public function status()
    {
        return $this->belongsTo(Status::class);
    }
}
