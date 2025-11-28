<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentSluggable\Sluggable;
use App\Traits\Imageable;


class SubCategory extends Model
{
     use HasFactory, SoftDeletes,  Imageable;//Sluggable,
     protected $table="sub_categories";// Ensure this matches your database table name
     protected $primaryKey = 'id'; 
        
    protected $fillable = [
        'name',
        'slug',
        'category_id',
        'parent_id',
        'description',
        'status',
        'image_sub_cat',
        'product_default_sub_cat',
        'meta_tag',
        'meta_description'
    ];

    public function products()
    {
         return $this->hasMany(Products::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function parent()
    {
        return $this->belongsTo(SubCategory::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(SubCategory::class, 'parent_id');
    }

    public function descendants()
    {
        return $this->hasMany(SubCategory::class, 'parent_id')->with('descendants');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
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

        // static::deleting(function($subcategory){
        //     $subcategory->products()->delete();
        // });
    }
}
