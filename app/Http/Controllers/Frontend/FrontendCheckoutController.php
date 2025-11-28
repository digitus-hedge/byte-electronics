<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use App\Models\Country;
use App\Models\CustomerAddress;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use \App\Models\Products;
use Illuminate\Support\Facades\Session;

class FrontendCheckoutController extends Controller
{
    public function checkoutPage()
    {
        $user = Auth::user();
        $customerAddresses = [];
        $cartItems = [];
        $subtotal = 0;

        if ($user) {
            $customerAddresses = CustomerAddress::where('customer_id', $user->id)
                ->select('id', 'customer_id', 'address_name', 'company_name', 'address_line1', 'address_line2', 'city', 'state', 'postal_code', 'attention', 'phone', 'country_id', 'country_code')
                ->get()
                ->map(function ($address) {
                    // Ensure country_code has + by inferring from country_id
                    if (!$address->country_code && $address->country_id) {
                        $country = Country::find($address->country_id);
                        $address->country_code = $country ? ('+' . ltrim($country->country_code, '+')) : '';
                    } elseif ($address->country_code && !str_starts_with($address->country_code, '+')) {
                        $address->country_code = '+' . $address->country_code;
                    }
                    return $address;
                });
        }

        $countries = Country::select('id', 'name', 'short_name', 'country_code')
            ->get()
            ->map(function ($country) {
                // Ensure country_code has +
                if ($country->country_code && !str_starts_with($country->country_code, '+')) {
                    $country->country_code = '+' . $country->country_code;
                }
                return $country;
            });

        $cart = Cart::getUserCart();
        if ($cart) {
            $cartItems = $cart->items()->with(['product'])->get();
            $subtotal = $cartItems->sum(function ($item) {
                return $item->quantity * $item->price;
            });
        }

        return inertia('Checkout/Checkout', [
            'user'=>$user,
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'customerAddresses' => $customerAddresses,
            'countries' => $countries,
            'authUser' => $user ? ['email' => $user->email] : null,
        ]);
    }
    
}
