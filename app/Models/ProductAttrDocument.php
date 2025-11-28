<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductAttrDocument extends Model
{
    use HasFactory;

    protected $fillable = ['product_ext_id', 'name', 'value'];

    public function productExtended()
    {
        return $this->belongsTo(ProductExtended::class, 'product_ext_id');
    }
}
