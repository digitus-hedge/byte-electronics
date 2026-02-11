<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Products extends Model
{
    use HasFactory, SoftDeletes , Sluggable;

    protected $fillable = [
        'name', 
        'slug', 
        'brand_id', 
        'category_id',
        'sub_category_id', 
        'file_name',
        'org_name', 
        'manufacturers_no', 
        'price', 
        'description',
        'more_description', 
        'meta_title', 
        'meta_description',
        'tag', 
        'minimum_qty', 
        'is_repairable',
        'featured', 
        'status',
        'best_sellers'
    ];

    public function product()
    {
        return $this->belongsTo(Products::class);
    }

    public function extendedAttributes()
    {
        return $this->hasMany(ProductExtended::class, 'product_id');
    }

    public function prices()
    {
        return $this->hasMany(ProductPrice::class, 'product_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class,'category_id');
    }

    public function subcategories()
    {
        return $this->belongsTo(SubCategory::class,'sub_category_id');
    }

    public function brands()
    {
        return $this->belongsTo(Brands::class,'brand_id');
    }

    public function attributes()
{
    return $this->hasMany(ProductExtended::class, 'product_id');
}

    public function scopeActive($query)
    {
       // return $query->where('status', 1);
    }

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    public static function boot(){
        parent::boot();

        // static::deleting(function($category){
        //     $category->sub_categories()->delete();
        //     $category->products()->delete();
       // });
    }

    public function minQuantity()
    {
       return $this->hasOne(ProductPrice::class, 'product_id')
                   ->where('qty','>', 0)
                   ->orderBy('qty', 'asc');
    }
}
