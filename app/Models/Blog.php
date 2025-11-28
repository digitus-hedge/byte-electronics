<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Blog extends Model
{
   
    use HasFactory, Sluggable, SoftDeletes;

    protected $fillable = [
        'title', 'author', 'publish_date', 'content', 'content_1',
        'categories', 'tags', 'summary', 'featured_image', 'image_1', 'image_2', 'image_3', 'image_4',   
        'seo_title', 'meta_description', 'slug', 'status', 'social_sharing'
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }
    
    
}
