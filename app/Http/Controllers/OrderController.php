<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Status;
use App\Models\Cart;
use App\Models\User;
use App\Models\CartItems;
use App\Models\Country;
use App\Models\OrderDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Models\CustomerAddress;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Mail\OrderConfirmation;
use App\Mail\OrderEnquiry;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{

    public function index(Request $request) {
      
    //     $orders = Order::with([    'status',
    //     'orderDetails.product',  // Load product details for each order item
    //     'orderDetails.product.brands',    // Load brand details
    //     'orderDetails.product.category', // Load category details
    //     'user'                   // Load user details if available
    // ])->orderBy('created_at', 'desc')->paginate(10);
    // // dd($orders);
    //     return view('orders.index', compact('orders'));
        $query = Order::with([
            'status',
            'orderDetails.product',
            'orderDetails.product.brands',
            'orderDetails.product.category',
            'user'
        ]);

        // Apply search filter if keyword is present
        if ($request->ajax() && $request->has('keyword')) {
            $keyword = $request->keyword;

            $query->where(function ($q) use ($keyword) {
                $q->where('customer_name', 'like', '%' . $keyword . '%');
               
            });

            $orders = $query->orderBy('created_at', 'desc')->paginate(10);

            return view('orders.partials.table', compact('orders'))->render(); // only table rows
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['status', 'orderDetails.product']);
        $statuses = Status::pluck('name', 'id')->toArray();
        return view('orders.show', compact('order', 'statuses'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status_id' => 'required|exists:statuses,id',
        ]);

        DB::beginTransaction();
        try {
            $order->update(['status_id' => $request->status_id]);
            DB::commit();
            return redirect()->route('admin.orders.show', $order)->with('success', 'Order status updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.orders.show', $order)->with('error', 'Failed to update order status.');
        }
    }

    public function updateItemStatus(Request $request, OrderDetails $orderDetail)
    {
        $request->validate([
            'status' => 'required|in:Pending,Confirmed,Completed,Cancelled',
        ]);

        DB::beginTransaction();
        try {
            $orderDetail->update(['status' => $request->status]);
            DB::commit();
            return redirect()->route('admin.orders.show', $orderDetail->order_id)->with('success', 'Item status updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.orders.show', $orderDetail->order_id)->with('error', 'Failed to update item status.');
        }
    }

    // public function checkout(Request $request)
    // {

    //     DB::beginTransaction();
    //     try {
    //         // Check if user is logged in
    //         $user = Auth::user();
    //         $isGuest = !$user;

    //         // Fetch cart using user_id or session_id
    //         $cart = $isGuest
    //             ? Cart::where('session_id', Session::getId())->with('items.product.brands', 'items.product.category')->first()
    //             : Cart::where('user_id', $user->id)->with('items.product.brands', 'items.product.category')->first();

    //         if (!$cart || $cart->items->isEmpty()) {
    //             return redirect()->route('cart')->with('error', 'Your cart is empty.');
    //         }


    //         // Get pending status
    //         $pendingStatus = Status::where('name', 'Pending')->firstOrFail();

    //         // Calculate totals
    //         $totalAmount = $cart->items->sum(fn($item) => $item->quantity * $item->price);
    //         $totalDistinctItems = $cart->items->count();

    //         // Guest details (if applicable)
    //         $customerName = $isGuest ? $request->input('customer_name', 'Guest') : $user->name;
    //         $customerEmail = $isGuest ? $request->input('email', 'guest@example.com') : $user->email;
    //         $billingAddress = $isGuest ? $request->input('billing_address', 'N/A') : $user->billing_address;
    //         $deliveryAddress = $isGuest ? $request->input('delivery_address', 'N/A') : $user->delivery_address;
    //         // dd($customerName, $customerEmail, $billingAddress, $deliveryAddress);
    //         // Create Order

    //             $order = Order::create([
    //                 'user_id'            => $isGuest ? null : $user->id,
    //                 'session_id'         => $isGuest ? $cart->session_id : null,
    //                 'customer_name'      => $customerName,
    //                 'email'              => $customerEmail,
    //                 'company_name'       => 'Company Name',//$isGuest ? 'N/A' : ($user->company_name ?? 'N/A'),
    //                 'billing_address'    => 'billingAddress', //change to variable
    //                 'delivery_address'   => 'deliveryAddress', //change to variable
    //                 'total_distinct_items' => $totalDistinctItems,
    //                 'total_amount'       => $totalAmount,
    //                 'currency'           => 'USD', // Todo: Fetch default currency from settings
    //                 'status_id'          => $pendingStatus->id
    //             ]);

    //             foreach ($cart->items as $item) {
    //                 OrderDetails::create([
    //                     'order_id'      => $order->id,
    //                     'product_id'    => $item->product_id,
    //                     'part_no'       => $item->product?->manufacturers_no ?? 'N/A',
    //                     'brand_id'      => $item->product->brands?->id,
    //                     'brand_name'    => $item->product->brands?->name ?? 'Unknown',
    //                     'category_id'   => $item->product->category?->id,
    //                     'category_name' => $item->product->category?->name ?? 'Unknown',
    //                     'unit'          => $item->product?->unit ?? 'N/A',
    //                     'unit_price'    => $item->price,
    //                     'quantity'      => $item->quantity,
    //                     'total_price'   => $item->quantity * $item->price,
    //                     'status_id'     => $pendingStatus->id
    //                 ]);
    //             }

    //         // Remove cart and cart items after order is placed
    //         CartItems::where('cart_id', $cart->id)->delete();
    //         $cart->delete();

    //         DB::commit();
    //         return redirect()->route('cart')->with('success', 'Order placed successfully.');

    //     } catch (\Exception $e) {
    //         DB::rollback();
    //         return redirect()->route('cart')->with('error', 'Something went wrong: ' . $e->getMessage());
    //     }
    // }
    public function checkout(Request $request)
    {
        // Enforce user authentication
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in to proceed with checkout.');
        }

        DB::beginTransaction();
        try {
            // Fetch cart using user_id
            $cart = Cart::where('user_id', $user->id)
                ->with('items.product.brands', 'items.product.category')
                ->first();
            if (!$cart || $cart->items->isEmpty()) {
                DB::rollBack();
                return redirect()->route('cart')->with('error', 'Your cart is empty.');
            }

            // Validate the request
            $validated = $request->validate([
                'billing_address' => 'required|array',
                'billing_address.id' => 'nullable|integer|exists:customer_addresses,id',
                'billing_address.address_name' => 'required|string|max:255',
                'billing_address.company_name' => 'nullable|string|max:255',
                'billing_address.address_line1' => 'required|string|max:255',
                'billing_address.address_line2' => 'nullable|string|max:255',
                'billing_address.city' => 'required|string|max:255',
                'billing_address.state' => 'required|string|max:255',
                'billing_address.postal_code' => 'required|string|regex:/^[0-9]{6}$/',
                'billing_address.attention' => 'nullable|string|max:255',
                'billing_address.phone' => 'required|string|regex:/^[0-9]{8,15}$/',
                'billing_address.country_id' => 'required|integer|exists:countries,id',
                'billing_address.country_code' => 'required|string|regex:/^\+\d{1,4}$/',
                'billing_address.email' => 'nullable|string|email|max:255',

                'shipping_address' => 'required|array',
                'shipping_address.id' => 'nullable|integer|exists:customer_addresses,id',
                'shipping_address.address_name' => 'required|string|max:255',
                'shipping_address.company_name' => 'nullable|string|max:255',
                'shipping_address.address_line1' => 'required|string|max:255',
                'shipping_address.address_line2' => 'nullable|string|max:255',
                'shipping_address.city' => 'required|string|max:255',
                'shipping_address.state' => 'required|string|max:255',
                'shipping_address.postal_code' => 'required|string|regex:/^[0-9]{6}$/',
                'shipping_address.attention' => 'nullable|string|max:255',
                'shipping_address.phone' => 'required|string|regex:/^[0-9]{8,15}$/',
                'shipping_address.country_id' => 'required|integer|exists:countries,id',
                'shipping_address.country_code' => 'required|string|regex:/^\+\d{1,4}$/',
                'shipping_address.email' => 'nullable|string|email|max:255',

                'cart_items' => 'required|array',
                'cart_items.*.product_id' => 'required|integer|exists:products,id',
                'cart_items.*.quantity' => 'required|integer|min:1',
                'cart_items.*.price' => 'required|numeric|min:0',
                'subtotal' => 'required|numeric|min:0',
                'shipping' => 'nullable|numeric|min:0',
                'total' => 'required|numeric|min:0',
            ]);

            // Get pending status
            $pendingStatus = Status::where('name', 'Pending')->firstOrFail();

            // Calculate totals
            $totalDistinctItems = count($validated['cart_items']);
            $totalAmount = $validated['subtotal'] + ($validated['shipping'] ?? 0);

            // Use user details
            $customerName = $user->name ?? $validated['billing_address']['address_name'];
            $customerEmail = $user->email ?? $validated['billing_address']['email'];

            // Handle billing address
            $billingAddress = $validated['billing_address'];
            $billingAddressId = data_get($billingAddress, 'id');
            if ($billingAddressId && is_numeric($billingAddressId)) {
                $billingAddressRecord = CustomerAddress::where('id', $billingAddressId)
                    ->where('customer_id', $user->id)
                    ->firstOrFail();
                $billingAddressRecord->update([
                    'customer_id' => $user->id,
                    'address_name' => $billingAddress['address_name'],
                    'company_name' => $billingAddress['company_name'],
                    'address_line1' => $billingAddress['address_line1'],
                    'address_line2' => $billingAddress['address_line2'],
                    'city' => $billingAddress['city'],
                    'state' => $billingAddress['state'],
                    'postal_code' => $billingAddress['postal_code'],
                    'attention' => $billingAddress['attention'],
                    'phone' => $billingAddress['phone'],
                    'country_id' => $billingAddress['country_id'],
                    'country_code' => $billingAddress['country_code'],
                    'email' => $billingAddress['email'] ?? null,
                ]);
                $billingAddressId = $billingAddressRecord->id;
            } else {
                $billingAddressRecord = CustomerAddress::create([
                    'customer_id' => $user->id,
                    'address_name' => $billingAddress['address_name'],
                    'company_name' => $billingAddress['company_name'],
                    'address_line1' => $billingAddress['address_line1'],
                    'address_line2' => $billingAddress['address_line2'],
                    'city' => $billingAddress['city'],
                    'state' => $billingAddress['state'],
                    'postal_code' => $billingAddress['postal_code'],
                    'attention' => $billingAddress['attention'],
                    'phone' => $billingAddress['phone'],
                    'country_id' => $billingAddress['country_id'],
                    'country_code' => $billingAddress['country_code'],
                    'email' => $billingAddress['email'] ?? null,
                ]);
                $billingAddressId = $billingAddressRecord->id;
            }

            // Handle shipping address
            $shippingAddress = $validated['shipping_address'];
            $shippingAddressId = data_get($shippingAddress, 'id');
            $isSameAddress = ($billingAddressId && $shippingAddressId && $billingAddressId === $shippingAddressId) ||
                            ($billingAddressId && !$shippingAddressId && $this->areAddressesEqual($billingAddress, $shippingAddress));

            if ($isSameAddress && $billingAddressId) {
                $shippingAddressId = $billingAddressId;
            } elseif ($shippingAddressId && is_numeric($shippingAddressId)) {
                $shippingAddressRecord = CustomerAddress::where('id', $shippingAddressId)
                    ->where('customer_id', $user->id)
                    ->firstOrFail();
                $shippingAddressRecord->update([
                    'customer_id' => $user->id,
                    'address_name' => $shippingAddress['address_name'],
                    'company_name' => $shippingAddress['company_name'],
                    'address_line1' => $shippingAddress['address_line1'],
                    'address_line2' => $shippingAddress['address_line2'],
                    'city' => $shippingAddress['city'],
                    'state' => $shippingAddress['state'],
                    'postal_code' => $shippingAddress['postal_code'],
                    'attention' => $shippingAddress['attention'],
                    'phone' => $shippingAddress['phone'],
                    'country_id' => $shippingAddress['country_id'],
                    'country_code' => $shippingAddress['country_code'],
                    'email' => $shippingAddress['email'] ?? null,
                ]);
                $shippingAddressId = $shippingAddressRecord->id;
            } else {
                $shippingAddressRecord = CustomerAddress::create([
                    'customer_id' => $user->id,
                    'address_name' => $shippingAddress['address_name'],
                    'company_name' => $shippingAddress['company_name'],
                    'address_line1' => $shippingAddress['address_line1'],
                    'address_line2' => $shippingAddress['address_line2'],
                    'city' => $shippingAddress['city'],
                    'state' => $shippingAddress['state'],
                    'postal_code' => $shippingAddress['postal_code'],
                    'attention' => $shippingAddress['attention'],
                    'phone' => $shippingAddress['phone'],
                    'country_id' => $shippingAddress['country_id'],
                    'country_code' => $shippingAddress['country_code'],
                    'email' => $shippingAddress['email'] ?? null,
                ]);
                $shippingAddressId = $shippingAddressRecord->id;
            }

            // Split address_name into first_name and last_name
            $billingNames = $this->splitName($billingAddress['address_name']);
            $shippingNames = $this->splitName($shippingAddress['address_name']);

            // Create order
            $order = Order::create([
                'user_id' => $user->id,
                'first_name' => $billingNames['first_name'],
                'last_name' => $billingNames['last_name'],
                'customer_name' => $customerName,
                'email' => $customerEmail,
                'company_name' => $billingAddress['company_name'] ?? 'N/A',
                'billing_address' => json_encode($billingAddress),
                'delivery_address' => json_encode($shippingAddress),
                'billing_address_id' => $billingAddressId,
                'shipping_address_id' => $shippingAddressId,
                'billing_first_name' => $billingNames['first_name'],
                'billing_last_name' => $billingNames['last_name'],
                'billing_company_name' => $billingAddress['company_name'] ?? null,
                'billing_address_line1' => $billingAddress['address_line1'],
                'billing_address_line2' => $billingAddress['address_line2'] ?? null,
                'billing_city' => $billingAddress['city'],
                'billing_state' => $billingAddress['state'],
                'billing_postal_code' => $billingAddress['postal_code'],
                'billing_country_code' => $billingAddress['country_code'],
                'billing_country_id' => $billingAddress['country_id'],
                'billing_phone' => $billingAddress['phone'],
                'billing_attention' => $billingAddress['attention'] ?? null,
                'shipping_first_name' => $shippingNames['first_name'],
                'shipping_last_name' => $shippingNames['last_name'],
                'shipping_company_name' => $shippingAddress['company_name'] ?? null,
                'shipping_address_line1' => $shippingAddress['address_line1'],
                'shipping_address_line2' => $shippingAddress['address_line2'] ?? null,
                'shipping_city' => $shippingAddress['city'],
                'shipping_state' => $shippingAddress['state'],
                'shipping_postal_code' => $shippingAddress['postal_code'],
                'shipping_country_code' => $shippingAddress['country_code'],
                'shipping_country_id' => $shippingAddress['country_id'],
                'shipping_phone' => $shippingAddress['phone'],
                'shipping_attention' => $shippingAddress['attention'] ?? null,
                'total_distinct_items' => $totalDistinctItems,
                'total_amount' => $totalAmount,
                'currency' => 'AED', // should change this to dynamic  currency with conversion
                'status_id' => $pendingStatus->id,
            ]);
// dd($order);
            // Save order items
            $part_items = [];
            $serial = 1;
            foreach ($cart->items as $item) {
                OrderDetails::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'part_no' => $item->product?->manufacturers_no ?? 'N/A',
                    'brand_id' => $item->product->brands?->id,
                    'brand_name' => $item->product->brands?->name ?? 'Unknown',
                    'category_id' => $item->product->category?->id,
                    'category_name' => $item->product->category?->name ?? 'Unknown',
                    'unit' => $item->unit ?? 'Normal',
                    'unit_price' => $item->price,
                    'quantity' => $item->quantity,
                    'total_price' => $item->quantity * $item->price,
                    'status_id' => $pendingStatus->id,
                ]);
                $part_items[] = [
                    'part_no' => $item->product?->manufacturers_no ?? 'N/A',
                    'product_name' => $item->product?->name ?? $item->product_id,
                    'brand_name' => $item->product->brands?->name ?? 'Unknown',
                    'remarks' => $item->remarks ?? '',
                    'qty' => $item->quantity,
                    'serial_no_for_sort' => $serial++,
                    'unit_price' => $item->price,
                    'category_name' => $item->product->category?->name ?? 'Unknown',
                    'unit' => $item->unit ?? 'Normal',
                    'currency' => 'AED', // should change this to dynamic  currency with conversion
                ];
            }

            // Clear cart
            if ($cart) {
                $cart->items()->delete();
                $cart->delete();
            }

          
            $cusData = [
                'order' => $order,
                'address' => $shippingAddress,
                'parts' => $part_items,
            ];
            // $priceHubAPI = $this->callPriceHubApiLogin();
            // dd($priceHubAPI);
            // if (isset($priceHubAPI['status']) && $priceHubAPI['status'] == 'success') {
            //     // dd($priceHubAPI);
            //     $this->CallPriceHubApiCreateOrder($priceHubAPI, $cusData);
            // } else {
                
            //     DB::rollBack();
            //     dd("ki");
            //     return response()->json(['success' => false, 'message' => 'Something went wrong, try again'], 500);
            // }
              // Send order confirmation email to customer
              $confirmationEmail = new OrderConfirmation($order, $billingAddress, $shippingAddress, $part_items,$user);
              if ($customerEmail) {
                // dd($confirmationEmail);
                  Mail::to($customerEmail)->send($confirmationEmail);
              }
  
              // Send enquiry email to company
              $enquiryEmail = new OrderEnquiry($order, $billingAddress, $shippingAddress, $part_items, $user);
              Mail::to('info@byte-electronics.com')->send($enquiryEmail);
  
            DB::commit();
            return redirect()->route('cart')->with('success', 'Order placed successfully.');
        } catch (\Exception $e) {
            
            DB::rollBack();
            return redirect()->route('cart')->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }


    public function CallPriceHubApiCreateOrder($tokenData,$cusData){

        // dd($cusData['order']);
        $country=Country::find($cusData['address']['country_id']);
        $defaultOrderData = [
            'date' => now()->format('d-m-Y'),
            'organization' => 1,
            'mode_of_enquiry' => 'byte',
            'customer_email' => $cusData['order']['email'],
            'customer_phone' => $cusData['address']['phone'],
            'customer_name' =>  $cusData['order']['customer_name'],
            'customer_address' => json_encode($cusData['address']),
            'received_on' => now()->format('d-m-Y'),
            'customer_name_country' => $country->name,
            'country_code' => $cusData['address']['country_code'],
            'country_short_name' => $country->short_name,
            'parts' => $cusData['parts'],
        ];
        $orderData=[];
        // Merge provided order data with defaults
        $orderData = array_merge($defaultOrderData, $orderData);
        $apiUrl = env('PRICE_HUB_URL').'/api/auth/create-order';
        $timeout = 100;
        // dd($apiUrl);
        $ch = curl_init($apiUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($orderData),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json',
                'Authorization: Bearer ' . $tokenData['data']['token'],
            ],
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        try {
            $response = curl_exec($ch);
            if ($response === false) {
                $error = curl_error($ch);
                $errno = curl_errno($ch);
                curl_close($ch);
                return [
                    'status' => 'error',
                    'message' => 'cURL error: ' . $error,
                ];
            }
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            $data = json_decode($response, true);
            if ($httpCode >= 200 && $httpCode < 300) {
                if (isset($data['token'])) {
                    Cache::put('pricehub_token', $data['token'], now()->addHours(1));
                }
                return [
                    'status' => 'success',
                    'data' => $data,
                ];
            }
            Log::warning('PriceHub API login failed', [
                'url' => $apiUrl,
                'status' => $httpCode,
                'error' => $data,
            ]);
            return [
                'status' => 'error',
                'message' => 'API login failed with status ' . $httpCode . ': ' . ($data['message'] ?? 'Unknown error'),
                'error' => $data,
            ];

        } catch (\Exception $e) {

            if (isset($ch) && is_resource($ch)) {
                curl_close($ch);
            }

            Log::error('PriceHub API unexpected error', [
                'url' => $apiUrl,
                'error' => $e->getMessage(),
            ]);

            return [
                'status' => 'error',
                'message' => 'Unexpected error: ' . $e->getMessage(),
            ];
        }
    }
public function callPriceHubApiLogin(){
    // dd('here');  
    $defaultCredentials = [
        'username' => env('PRICEHUB_USERNAME'),
        'password' => env('PRICEHUB_PASSWORD'),
    ];
    $credentials = [
        'username' => env('PRICEHUB_USERNAME'),
        'password' => env('PRICEHUB_PASSWORD'),
    ];
    $credentials = array_merge($defaultCredentials, $credentials);
    $apiUrl = env('PRICE_HUB_URL').'/api/auth/login?email='.env('PRICEHUB_USERNAME').'&password='.env('PRICEHUB_PASSWORD').'';
    $timeout = 100;
    // dd($apiUrl);
    $ch = curl_init($apiUrl);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($credentials),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Accept: application/json',
        ],
        CURLOPT_TIMEOUT => $timeout,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER => false,
    ]);

    try {
        // dd($ch);
        $response = curl_exec($ch);
        if ($response === false) {
            $error = curl_error($ch);
            $errno = curl_errno($ch);
            curl_close($ch);
            return [
                'status' => 'error',
                'message' => 'cURL error: ' . $error,
            ];
        }
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $data = json_decode($response, true);
        if ($httpCode >= 200 && $httpCode < 300) {
            if (isset($data['token'])) {
                Cache::put('pricehub_token', $data['token'], now()->addHours(1));
            }
            return [
                'status' => 'success',
                'data' => $data,
            ];
        }
        Log::warning('PriceHub API login failed', [
            'url' => $apiUrl,
            'status' => $httpCode,
            'error' => $data,
        ]);
        return [
            'status' => 'error',
            'message' => 'API login failed with status ' . $httpCode . ': ' . ($data['message'] ?? 'Unknown error'),
            'error' => $data,
        ];

    } catch (\Exception $e) {
        // dd($e);

        if (isset($ch) && is_resource($ch)) {
            curl_close($ch);
        }

        Log::error('PriceHub API unexpected error', [
            'url' => $apiUrl,
            'error' => $e->getMessage(),
        ]);

        return [
            'status' => 'error',
            'message' => 'Unexpected error: ' . $e->getMessage(),
        ];
    }

}
    /**
     * Compare billing and shipping addresses to check if they are effectively the same
     */
    private function areAddressesEqual(array $billingAddress, array $shippingAddress): bool
    {
        $fieldsToCompare = [
            'address_name', 'company_name', 'address_line1', 'address_line2',
            'city', 'state', 'postal_code', 'attention', 'phone',
            'country_id', 'country_code', 'email'
        ];

        foreach ($fieldsToCompare as $field) {
            if (($billingAddress[$field] ?? null) !== ($shippingAddress[$field] ?? null)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Split address_name into first_name and last_name
     */
    private function splitName(string $name): array
    {
        $parts = explode(' ', trim($name));
        $firstName = $parts[0];
        $lastName = count($parts) > 1 ? implode(' ', array_slice($parts, 1)) : '';

        return [
            'first_name' => $firstName ?: 'Unknown',
            'last_name' => $lastName ?: null,
        ];
    }
}
