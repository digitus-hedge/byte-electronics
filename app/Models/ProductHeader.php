<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductHeader extends Model
{
    use HasFactory;
    protected $table = 'product_headers';
    protected $fillable = ['name'];

    public function attributes()
    {
        return $this->hasMany(ProductAttribute::class, 'prd_header_id');
    }

    public function extendedAttributes()
    {
        return $this->hasMany(ProductExtended::class, 'prd_header_id');
    }
}
