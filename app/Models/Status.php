<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Status extends Model
{
    use HasFactory;
    protected $table = 'statuses';
    protected $fillable = ['name', 'is_active'];

    // Orders using this status
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // Order details using this status
    public function orderDetails()
    {
        return $this->hasMany(OrderDetails::class);
    }
}
