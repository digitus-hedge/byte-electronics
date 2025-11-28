<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banners extends Model
{
    protected $fillable = [
        'bannername', 'type', 'priority', 'status', 'url1', 'url2', 'url3', 'pagename', 'redirect_url'
    ];

    public static function boot(){
        parent::boot();

        
       
    }
}
