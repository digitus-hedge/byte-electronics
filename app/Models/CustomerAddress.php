<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomerAddress extends Model
{
    use HasFactory;
    protected $table = 'customer_addresses';
    protected $fillable = [
        'customer_id', 
        'address_name', 
        'company_name', 
        'address_line1',
        'address_line2', 
        'city', 
        'state', 
        'postal_code', 
        'attention',
        'phone', 
        'country_id', 
        'country_code'
    ];

    public function customer()
    {
        return $this->belongsTo(User::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
