<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'customer_name', 'email', 'company_name', 'billing_address', 
        'delivery_address', 'total_distinct_items', 'total_amount', 'currency', 'status_id'
    ];

    // Relationship with OrderDetails
    public function orderDetails()
    {
        return $this->hasMany(OrderDetails::class);
    }

    // Relationship with User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship with Status
    public function status()
    {
        return $this->belongsTo(Status::class);
    }
}
