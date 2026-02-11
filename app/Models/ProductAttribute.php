<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductAttribute extends Model
{
    use HasFactory;
    protected $table = 'product_attributes';

    protected $fillable = ['prd_header_id', 'name', 'value_type'];

    public function header()
    {
        return $this->belongsTo(ProductHeader::class, 'prd_header_id');
    }

    public function extendedAttributes()
    {
        return $this->hasMany(ProductExtended::class, 'product_attr_id');
    }
}
