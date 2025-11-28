<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
    ];

    // A cart belongs to a user (optional, for logged-in users)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // A cart has many cart items
    public function items()
    {
        return $this->hasMany(CartItems::class);
    }

    // Get or create a cart for the current user or session
    public static function getUserCart()
    {
        if (Auth::check()) {
            return self::firstOrCreate(['user_id' => Auth::id()]);
        } else {
            $sessionId = session()->get('cart_session_id');
            if (!$sessionId) {
                $sessionId = Str::random(40);
                session()->put('cart_session_id', $sessionId);
            }
            return self::firstOrCreate(['session_id' => $sessionId]);
        }
    }
}