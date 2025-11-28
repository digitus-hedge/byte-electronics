<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestQuote extends Model
{
    protected $table = 'request_quotes';

    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
        'status',
        'email',
        'phone',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Products::class);
    }
}
