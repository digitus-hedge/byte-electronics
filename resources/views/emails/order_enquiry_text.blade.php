ORDER HAS BEEN PLACED

Customer Details:
Name: {{ $customer->name ?? 'N/A' }}
Phone: {{ $billingAddress['phone'] ?? 'N/A' }}
Email: {{ $customer->email ?? 'N/A' }}
Company: {{ $billingAddress['company_name'] ?? 'N/A' }}

Order Details:
Order ID: {{ $order->id }}
Total Amount: {{ $order->total_amount }} AED
Order Date: {{ $order->created_at->format('d M Y H:i') }}
Total Items: {{ $order->total_distinct_items }}

Billing Address:
{{ $billingAddress['address_name'] ?? 'N/A' }}
{{ $billingAddress['company_name'] ? $billingAddress['company_name'] : '' }}
{{ $billingAddress['address_line1'] ?? 'N/A' }}
{{ $billingAddress['address_line2'] ? $billingAddress['address_line2'] : '' }}
{{ $billingAddress['city'] ?? 'N/A' }}, {{ $billingAddress['state'] ?? 'N/A' }} {{ $billingAddress['postal_code'] ?? 'N/A' }}
Phone: {{ $billingAddress['phone'] ?? 'N/A' }}
Email: {{ $billingAddress['email'] ?? 'N/A' }}

@if(!empty($shippingAddress))
Shipping Address:
{{ $shippingAddress['address_name'] ?? 'N/A' }}
{{ $shippingAddress['company_name'] ? $shippingAddress['company_name'] : '' }}
{{ $shippingAddress['address_line1'] ?? 'N/A' }}
{{ $shippingAddress['address_line2'] ? $shippingAddress['address_line2'] : '' }}
{{ $shippingAddress['city'] ?? 'N/A' }}, {{ $shippingAddress['state'] ?? 'N/A' }} {{ $shippingAddress['postal_code'] ?? 'N/A' }}
Phone: {{ $shippingAddress['phone'] ?? 'N/A' }}
Email: {{ $shippingAddress['email'] ?? 'N/A' }}
@endif

Order Items:
@foreach($cartItems as $item)
Item: {{ $item['product_name'] }} ({{ $item['part_no'] }})
Quantity: {{ $item['qty'] }}
Unit Price: {{ $item['unit_price'] }} AED
Total Price: {{ $item['qty'] * $item['unit_price'] }} AED
Brand: {{ $item['brand_name'] }}
Category: {{ $item['category_name'] }}
--------------------
@endforeach

Please review the order and proceed with sending the payment link to the customer.

Best Regards,
Byte Electronics Team