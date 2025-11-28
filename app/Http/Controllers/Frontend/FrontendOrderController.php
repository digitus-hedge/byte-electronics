<?php
namespace App\Http\Controllers\Frontend;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Inertia\Inertia;
class FrontendOrderController extends Controller
{


    public function myOrders()
    {

    $userId = Auth::id();
        $sessionId = Session::getId();
       
        $orders = Order::with([
            'status',
            'orderDetails.product.brands',
            'orderDetails.product.category'
        ])
        ->when($userId, fn($q) => $q->where('user_id', $userId))
        ->when(!$userId, fn($q) => $q->where('session_id', $sessionId))
        ->orderBy('created_at', 'desc')
        ->get();
      
        $formattedOrders = [];

        foreach ($orders as $order) {
            foreach ($order->orderDetails as $i => $detail) {
                $product = $detail->product;
                $formattedOrders[] = [
                    'id' => $order->id,
                    'sort' => $i + 1,
                    'byte_no' => $detail->id ?? 'N/A',
                    'mfr_no' => $detail->part_no ?? 'N/A',
                    'manufacturer' => $product->brands->name ?? 'Unknown',
                    'product_name' => $product->name ?? 'Unnamed Product',
                    'image' => $product->file_name
                        ? ($product->file_name === 'uploads/products/pending'
                            ? asset('/assets/images/dummy_product.webp')
                            : asset('uploads/products/' . $product->file_name))
                        : asset('assets/images/dummy_product.webp'),
                    'description' => $product->description ?? '',
                    'quantity' => $detail->quantity,
                    'availability' => $product->availability_text ?? 'In Stock',
                    'unit_price' => $detail->unit_price,
                    'ext_price' => $detail->total_price,
                    'status' => $order->status->name ?? 'Pending',
                    'trackingNumber' => $detail->tracking_number ?? '',
                    'statusDates' => [
                        'pending' => $order->created_at ? $order->created_at->toDateString() : '',
                        'confirmed' => $detail->confirmed_at ? $detail->confirmed_at->toDateString() : '',
                        'completed' => $detail->completed_at ? $detail->completed_at->toDateString() : '',
                        'cancelled' => $detail->cancelled_at ? $detail->cancelled_at->toDateString() : ''
                    ]
                ];
            }
        }

        return Inertia::render('Orders/Orders', [
            'orders' => $formattedOrders
        ]);
    }

    public function getOrders($id)
    {
        // dd($id);
        $userId = Auth::id();
        $sessionId = Session::getId();

        $orders = Order::with([
            'status',
            'orderDetails.product.brands',
            'orderDetails.product.category'
        ])
        ->where('id', $id)
        ->when($userId, fn($q) => $q->where('user_id', $userId))
        ->when(!$userId, fn($q) => $q->where('session_id', $sessionId))
        ->orderBy('created_at', 'desc')
        ->get();

        $formattedOrders = [];

        foreach ($orders as $order) {
            foreach ($order->orderDetails as $i => $detail) {
                $product = $detail->product;
                $formattedOrders[] = [
                    'id' => $order->id,
                    'sort' => $i + 1,
                    'byte_no' => $detail->id ?? 'N/A',
                    'mfr_no' => $detail->part_no ?? 'N/A',
                    'manufacturer' => $product->brands->name ?? 'Unknown',
                    'product_name' => $product->name ?? 'Unnamed Product',
                    'image' => $product->file_name
                        ? asset('Uploads/products/' . $product->file_name)
                        : asset('assets/images/dummy_product.webp'),
                    'description' => $product->description ?? '',
                    'quantity' => $detail->quantity,
                    'availability' => $product->availability_text ?? 'In Stock',
                    'unit_price' => $detail->unit_price,
                    'ext_price' => $detail->total_price,
                    'status' => $order->status->name ?? 'Pending',
                    'trackingNumber' => $detail->tracking_number ?? '',
                    'statusDates' => [
                        'pending' => $order->created_at ? $order->created_at->toDateString() : '',
                        'confirmed' => $detail->confirmed_at ? $detail->confirmed_at->toDateString() : '',
                        'completed' => $detail->completed_at ? $detail->completed_at->toDateString() : '',
                        'cancelled' => $detail->cancelled_at ? $detail->cancelled_at->toDateString() : ''
                    ]
                ];
            }
        }

        return Inertia::render('Orders/Orders', [
            'orders' => $formattedOrders
        ]);
    }

}
