<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\products;


class ProductExtended extends Model
{
    use HasFactory;
    protected $table = 'product_extended';
    protected $fillable = ['product_id', 'product_attr_id','prd_header_id', 'value_type'];

    public function product()
    {
        return $this->belongsTo(products::class, 'product_id');
    }

    public function attributes()
    {
        return $this->belongsTo(ProductAttribute::class, 'product_attr_id');
    }

    public function attrDocuments()
    {
        return $this->hasMany(ProductAttrDocument::class, 'product_ext_id');
    }

    public function headers()
    {
        return $this->belongsTo(ProductHeader::class, 'prd_header_id','id');
    }


    public function documents()
    {
        return $this->hasMany(ProductAttrDocument::class, 'product_ext_id');
    }
}
