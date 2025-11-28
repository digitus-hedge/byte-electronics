<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
class CartController extends Controller
{
    public function index()
    {
        
        return Inertia::render('Cart/Cart');
    }

    // public function viewCart()
    // {
    //     $cart = $this->getCart();
    //     $cartItems = $cart->items()->with('product')->get();

    //     return view('cart.view', compact('cartItems'));
    // }
       
    

}
