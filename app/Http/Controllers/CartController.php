<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItems;
use App\Models\Products;
use App\Models\ProductPrice;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Illuminate\Http\JsonResponse;


class CartController extends Controller
{


    public function index(Request $request): Response
    {
        if (Auth::check()) {
            // Fetch user cart items
            $cartItems = $this->getCartItems();

            $cartCount = $this->getCartItemCount();
            // dd($cartItems);

        } else {
            // Fetch guest cart using session ID
            $sessionId = session()->get('cart_session_id');
            $cart = $sessionId ? Cart::where('session_id', $sessionId)->first() : null;
            $cartItems = $cart ? $this->getGuestCartItems($cart) : [];
            $cartCount = count($cartItems);
        }

        return Inertia::render('Cart/Cart', [
            'cartItems' => $cartItems,
            'subtotal' => collect($cartItems)->sum(fn($item) => $item['unitPrice'] * $item['quantity']),
            'cartCount' => $cartCount,
        ]);
    }

    protected function getGuestCartItems($cart)
{
    return $cart->items()->with('product.brands')->get()->map(fn ($item) => [
        'id' => $item->id,
        'name' => $item->product->name,
        'slug'=>$item->product->slug,
        'image' => asset('uploads/products/' . $item->product->file_name),
        'partno' => $item->product->manufacturers_no,
        'brand' => optional($item->product->brands)->name,
        'unitPrice' => $item->price,
        'quantity' => $item->quantity,
    ]);
}

    public function fetchCart() : JsonResponse
    {
        $cartItems = session()->get('cart', []); // Fetch dynamically

        return response()->json([
            'cartItems' => $cartItems,
            'subtotal' => collect($cartItems)->sum(fn ($item) => $item['unitPrice'] * $item['quantity']),
        ]);
    }

    
    // public function addToCart(Request $request, $productId)
    // {  
    //     $product = Products::findOrFail($productId);
     
 
    //     // Set default unit price if not provided
    //     if (!$request->has('unit_price')) {
        
    //         $price = ProductPrice::where('product_id', $productId)->where('qty', 1)->first();
            
    //         if ($price) {
    //             $request->merge(['unit_price' => $price->single_price]);
    //         } else {
    //             $request->merge(['unit_price' => $product->price]);
    //         }
    //     }
    
    //     // Set default unit_key if not provided
    //     if (!$request->has('unit')) {
          
    //         $request->merge(['unit' => 'Normal']);
    //     }
    
    //     $cart = $this->getCart();
      
    //     $cartItem = $cart->items()->where('product_id', $productId)->first();

    //     if ($cartItem) {
    //         $cartItem->update([
    //             'quantity' => $cartItem->quantity + $request->input('quantity', 1),
    //             'price' => $request->unit_price,
    //             'unit' => $request->unit_key, // Update unit_key
    //         ]);
    //     } else {
    //         $cart->items()->create([
    //             'product_id' => $productId,
    //             'quantity' => $request->input('quantity', 1),
    //             'price' => $request->unit_price,
    //             'unit' => $request->unit_key, // Store unit_key
    //         ]);
    //     }
    
    //     return back();
    // }
    public function addToCart(Request $request, $productId)
    {
        $quantity = (int) $request->input('quantity', 1);
        // dd($quantity);
        // Try to find price for the exact qty
        $productPrice = ProductPrice::where('product_id', $productId)
            ->where('qty', $quantity)
            ->first();
   
        // If not found, fall back to the minimum qty for this product
        if (!$productPrice) {
            $productPrice = ProductPrice::where('product_id', $productId)
                ->orderBy('qty', 'asc')
                ->first();
        }

        if (!$productPrice) {
            return response()->json(['error' => 'Price not found for this product.'], 404);
        }
    
        // If minimum qty > requested qty → ask confirmation
        // if ($productPrice->qty > 1 && $quantity < $productPrice->qty) {
          
          
        // }
     // 4. Get or create cart (user_id or session_id)
     $cart = $this->getCart();

     // 5. Check if product already in cart
        $cartItem = $cart->items()->where('product_id', $productId)->first();
 
        if ($cartItem) {
            // Update existing item
            $cartItem->update([
                'quantity' => $cartItem->quantity + max($quantity, $productPrice->qty),
                'price'    => $productPrice->single_price,
                'unit'     => $productPrice->unit_name ?? 'Normal',
            ]);
        } else {
            // Insert new cart item
            $cart->items()->create([
                'product_id' => $productId,
                'quantity'   => max($quantity, $productPrice->qty),
                'price'      => $productPrice->single_price,
                'unit'       => $productPrice->unit_name ?? 'Normal',
            ]);
        }
        return back();
    
       
    }
    

       public function updateCart(Request $request, $itemId)
    {
        try {
            $cartItem = CartItems::findOrFail($itemId);
    
            $newQuantity = (int) $request->input('quantity');
            $cartItem->quantity = $newQuantity;
    
            // Find matching price from product_price table
            $productPrice = ProductPrice::where('product_id', $cartItem->product_id)
            ->where('qty', '<=', $newQuantity)->orderBy('qty', 'desc')
                ->first();
    
            if ($productPrice) {
                $cartItem->price = $productPrice->single_price;
            }
    
            $cartItem->save();
    
            // Return only data for Vue to update UI — no messages
            return response()->json([
                'unit_price' => $cartItem->price,
                'total_price' => $cartItem->price * $cartItem->quantity
            ]);
    
        } catch (\Throwable $e) {
            Log::error('Cart update error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
    
            // Return HTTP 500 without any user-facing message
            return response()->json([], 500);
        }
    }
    
    




    public function removeFromCart($itemId)
    {
        $cartItem = CartItems::findOrFail($itemId);

        $cartItem->delete();

        // return Inertia::render('Cart/Cart', [
        //     'cartItems' => $this->getCartItems(),
        //     'subtotal' => $this->calculateSubtotal(),
        // ]);
    }

    protected function getCart()
    {
        if (Auth::check()) {
            // For logged-in users, fetch or create a cart associated with the user
            
            return Cart::firstOrCreate(['user_id' => Auth::id()]);
        } else {
            // For guest users, fetch or create a cart associated with the session
            $sessionId = session()->get('cart_session_id');
            if (!$sessionId) {
                $sessionId = Str::random(40);
                session()->put('cart_session_id', $sessionId);
            }
            return Cart::firstOrCreate(['session_id' => $sessionId]);
        }
    }


    protected function getCartItems()
    {
        $cart = $this->getCart();

        $items = $cart->items()->with('product.brands')->get();
       // Debugging: Check if items are being fetched correctly
 // Debugging: Check if items are being fetched correctly
        // Debugging: Dump cart items before returning

        return $items->map(fn ($item) => [
            'id' => $item->id,
            'name' => $item->product,
            'slug'=> $item->product->slug,
            'image' => asset('uploads/products/' . $item->product->file_name),
            'partno' => $item->product->manufacturers_no,
            'brand' => optional($item->product->brands)->name, // Ensure brand is being fetched
            'description' => $item->product->description,
            'unitPrice' => $item->price,
            'quantity' => $item->quantity,
        ]);
    }

    protected function calculateSubtotal()
    {
        return $this->getCartItems()->sum(fn ($item) => $item['unitPrice'] * $item['quantity']);
    }

    // View the cart
    public function viewCart()
    {
        $cart = $this->getCart();
        $cartItems = $cart->items()->with('product')->get();

        // return view('cart.view', compact('cartItems'));
        return Inertia::render('Cart/Cart',compact('cartItems'));
    }

    public function count()
    {
        $cart = $this->getCart();
        // $count = $cart->items()->sum('cart_items.quantity'); // Sum of all item quantities
        $count= $cart->items()->count();
        return response()->json([
            'count' => $count,
        ]);
    }

    protected function getCartItemCount()
    {
        if (Auth::check()) {
            // For logged-in users, count distinct cart items
            return CartItems::whereHas('cart', function ($query) {
                $query->where('user_id', Auth::id());
            })->count();
        } else {
            // For guest users, count distinct cart items using session_id
            $sessionId = session()->get('cart_session_id');
            if (!$sessionId) {
                return 0;
            }

            return CartItems::whereHas('cart', function ($query) use ($sessionId) {
                $query->where('session_id', $sessionId);
            })->count();
        }
    }
}
