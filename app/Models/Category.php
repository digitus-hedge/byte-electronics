<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
//use Cviebrock\EloquentSluggable\Sluggable;
use App\Traits\Imageable;

class Category extends Model
{
    use HasFactory, SoftDeletes, Imageable; //, Sluggable;
    protected $table = 'categories'; // Ensure this matches your database table name
    protected $primaryKey = 'id';   // Adjust if the primary key is different

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name',
        'slug',
        'description',
        'status',
        'meta_tag',
        'meta_description',
        'featured'
    ];

    public function products()
    {
       return $this->hasMany(Products::class);
    }

    public function subcategories()
    {
        return $this->hasMany(SubCategory::class,'category_id');
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

        static::deleting(function ($category) {
            // Soft-delete subcategories if they exist
            if ($category->subcategories()->exists()) {
                $category->subcategories()->delete();
            }
            // Soft-delete products if they exist
            if ($category->products()->exists()) {
                $category->products()->delete();
            }
        });
    }
}
