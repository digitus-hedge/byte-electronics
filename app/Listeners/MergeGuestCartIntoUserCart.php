<?php

namespace App\Listeners;

use App\Events\CartMerged;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Cart;
use App\Models\CartItems;
use Illuminate\Support\Facades\Session;

class MergeGuestCartIntoUserCart
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(CartMerged $event): void
    {
       
        
            $user = $event->user;
            $sessionId = Session::get('cart_session_id');
    
            if (!$sessionId) {
                return;
            }
    
            // Fetch guest cart
            $guestCart = Cart::where('session_id', $sessionId)->first();
            if (!$guestCart) {
                return;
            }
    
            // Fetch or create user's cart
            $userCart = Cart::firstOrCreate(['user_id' => $user->id]);
          
            // Move items from guest cart to user cart
            foreach ($guestCart->items as $item) {
                $existingItem = CartItems::where('cart_id', $userCart->id)
                    ->where('product_id', $item->product_id)
                    ->first();
    
                if ($existingItem) {
                    $existingItem->update([
                        'quantity' => $existingItem->quantity + $item->quantity,
                    ]);
                } else {
                    $item->update(['cart_id' => $userCart->id]);
                }
            }
            // dd('userCart',$userCart,'guestCart',$guestCart);
            // Delete guest cart
            $guestCart->delete();
            Session::forget('cart_session_id');
        
    } 
}
