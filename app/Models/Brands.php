<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentSluggable\Sluggable;
use App\Models\Products;
use App\Traits\Imageable;

class Brands extends Model
{
    use HasFactory, SoftDeletes,Imageable; //, Sluggable

    protected $fillable = [
        'name',
        'description',
        'slug',
        'file_name',
        'org_name',
        'secondary_logo',
        'banner',
        'status',
        'meta_tag',
        'meta_description',
        'featured',
    ];

    public function products()
    {
        return $this->hasMany(products::class,'brand_id');
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
}
