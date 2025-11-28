@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Order Details</h2>

    <!-- Flash Messages -->
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- Order Summary -->
    <div class="card mb-4">
        <div class="card-header">Order Information</div>
        <div class="card-body">
            <p><strong>Order ID:</strong> {{ $order->id }}</p>
            <p><strong>Customer Name:</strong> {{ $order->customer_name }}</p>
            <p><strong>Email:</strong> {{ $order->email }}</p>
            <p><strong>Billing Address:</strong></p>
            @php
                $billingAddress = json_decode($order->billing_address, true);
            @endphp
            @if ($billingAddress)
                <address>
                    {{ $billingAddress['address_name'] ?? 'N/A' }}<br>
                    {{ $billingAddress['company_name'] ?? '' }}<br>
                    {{ $billingAddress['address_line1'] ?? '' }}<br>
                    {{ $billingAddress['address_line2'] ? $billingAddress['address_line2'] . '<br>' : '' }}
                    {{ $billingAddress['city'] ?? '' }},
                    {{ $billingAddress['state'] ?? '' }}
                    {{ $billingAddress['postal_code'] ?? '' }}<br>
                    <strong>Attention:</strong> {{ $billingAddress['attention'] ?? 'N/A' }}<br>
                    <strong>Phone:</strong> {{ $billingAddress['phone'] ?? 'N/A' }}<br>
                    <strong>Country:</strong> {{ $billingAddress['country_code'] ?? '' }}
                </address>
            @else
                <p>No billing address provided.</p>
            @endif
            <p><strong>Delivery Address:</strong></p>
            @php
                $deliveryAddress = json_decode($order->delivery_address, true);
            @endphp
            @if ($deliveryAddress && $deliveryAddress['id'])
                <address>
                    {{ $deliveryAddress['address_name'] ?? 'N/A' }}<br>
                    {{ $deliveryAddress['company_name'] ?? '' }}<br>
                    {{ $deliveryAddress['address_line1'] ?? '' }}<br>
                    {{ $deliveryAddress['address_line2'] ? $deliveryAddress['address_line2'] . '<br>' : '' }}
                    {{ $deliveryAddress['city'] ?? '' }},
                    {{ $deliveryAddress['state'] ?? '' }}
                    {{ $deliveryAddress['postal_code'] ?? '' }}<br>
                    <strong>Attention:</strong> {{ $deliveryAddress['attention'] ?? 'N/A' }}<br>
                    <strong>Phone:</strong> {{ $deliveryAddress['phone'] ?? 'N/A' }}<br>
                    <strong>Country:</strong> {{ $deliveryAddress['country_code'] ?? '' }}
                </address>
            @else
                <p>Same as billing address.</p>
            @endif
            <p><strong>Total Items:</strong> {{ $order->total_distinct_items }}</p>
            <p><strong>Total Amount:</strong> ${{ number_format($order->total_amount, 2) }}</p>
            <p><strong>Currency:</strong> {{ $order->currency }}</p>
            <p><strong>Status:</strong></p>
            <form action="{{ route('admin.orders.update-status', $order) }}" method="POST">
                @csrf
                <div class="input-group w-25">
                    <select name="status_id" class="form-control" onchange="this.form.submit()">
                        @foreach ($statuses as $id => $name)
                            <option value="{{ $id }}" {{ $order->status_id == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>

    <!-- Order Items -->
    <div class="card">
        <div class="card-header">Ordered Items</div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th>Brand</th>
                        <th>Category</th>
                        <th>Unit</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->orderDetails as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->product->name ?? 'Unknown' }}</td>
                            <td>{{ $item->brand_name }}</td>
                            <td>{{ $item->category_name }}</td>
                            <td>{{ $item->unit }}</td>
                            <td>${{ number_format($item->unit_price, 2) }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>${{ number_format($item->total_price, 2) }}</td>
                            <td>
                                <form action="{{ route('admin.order-details.update-status', $item) }}" method="POST">
                                    @csrf
                                    <select name="status" class="form-control" onchange="this.form.submit()">
                                        @foreach (['Pending', 'Confirmed','Completed', 'Cancelled'] as $status)
                                            <option value="{{ $status }}" {{ $item->status == $status ? 'selected' : '' }}>{{ $status }}</option>
                                        @endforeach
                                    </select>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary mt-3">Back to Orders</a>
</div>
@endsection