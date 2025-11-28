<?php

namespace App\Providers;

use App\Models\Cart;
use App\Models\CartItems;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // \Illuminate\Support\Facades\Log::info('Byte Reverb config: ' . json_encode(config('reverb')));
        Inertia::share([
            'cartCount' => function () {
                if (Auth::check()) {
                    // For authenticated users, count distinct cart items from the database
                    return CartItems::whereHas('cart', function ($query) {
                        $query->where('user_id', Auth::id());
                    })->distinct('product_id')->count('product_id');
                } else {
                    // For guest users, count distinct session-based cart items
                    $sessionId = Session::get('cart_session_id');
                    if (!$sessionId) {
                        return 0;
                    }
        
                    $cart = Cart::where('session_id', $sessionId)->first();
                    return $cart ? $cart->items()->distinct('product_id')->count('product_id') : 0;
                }
            }
        ]);
    }
}
