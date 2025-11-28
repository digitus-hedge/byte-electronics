<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Currency extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_id',
        'currency_code',
        'currency_symbol',
        'conversion_rate',
        'status',
        'is_default',
    ];

    public function productPrices() {
        return $this->hasMany(ProductPrice::class, 'currency_id');
    }
    public function country()
    {
        return $this->belongsTo(Country::class);
    }


}
