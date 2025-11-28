Order Confirmation

Thank you for your order with Byte Electronics! Your order #{{ $order->id }} has been successfully placed.

Your order has been successfully submitted. Our team will review the order shortly and send a confirmation email with the payment link.

Customer Details
----------------
Name: {{ $customer->name ?? 'N/A' }}
Email: {{ $customer->email ?? 'N/A' }}
Phone: {{ $billingAddress['phone'] ?? 'N/A' }}
Company: {{ $billingAddress['company_name'] ?? 'N/A' }}

Order Details
----------------
Order ID: {{ $order->id }}
Order Date: {{ $order->created_at->format('d M Y H:i') }}
Total Amount: {{ $order->total_amount }} AED
Total Items: {{ $order->total_distinct_items }}

Billing Address
----------------
{{ $billingAddress['address_name'] ?? 'N/A' }}
@if($billingAddress['company_name'])
{{ $billingAddress['company_name'] }}
@endif
{{ $billingAddress['address_line1'] ?? 'N/A' }}
@if($billingAddress['address_line2'])
{{ $billingAddress['address_line2'] }}
@endif
{{ $billingAddress['city'] ?? 'N/A' }}, {{ $billingAddress['state'] ?? 'N/A' }} {{ $billingAddress['postal_code'] ?? 'N/A' }}
Phone: {{ $billingAddress['phone'] ?? 'N/A' }}
Email: {{ $billingAddress['email'] ?? 'N/A' }}

@if(!empty($shippingAddress))
Shipping Address
----------------
{{ $shippingAddress['address_name'] ?? 'N/A' }}
@if($shippingAddress['company_name'])
{{ $shippingAddress['company_name'] }}
@endif
{{ $shippingAddress['address_line1'] ?? 'N/A' }}
@if($shippingAddress['address_line2'])
{{ $shippingAddress['address_line2'] }}
@endif
{{ $shippingAddress['city'] ?? 'N/A' }}, {{ $shippingAddress['state'] ?? 'N/A' }} {{ $shippingAddress['postal_code'] ?? 'N/A' }}
Phone: {{ $shippingAddress['phone'] ?? 'N/A' }}
Email: {{ $shippingAddress['email'] ?? 'N/A' }}
@endif

Order Items
----------------
@foreach($cartItems as $item)
Item: {{ $item['product_name'] }} ({{ $item['part_no'] }})
Quantity: {{ $item['qty'] }}
Unit Price: {{ $item['unit_price'] }} AED
Total Price: {{ $item['qty'] * $item['unit_price'] }} AED
Brand: {{ $item['brand_name'] }}
Category: {{ $item['category_name'] }}
----------------
@endforeach

If you have any questions, please contact us at info@byte-electronics.com.

Thank you for shopping with us!

Best Regards,
Byte Electronics Team