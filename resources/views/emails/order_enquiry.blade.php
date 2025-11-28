<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Open Sans';
            background-color: #ffffff;
            /* color: #333333; */
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .rectangle-box {
            /* background-color: #f8f8f8; */
            border: 1px solid #e0e0e0;
            padding: 20px;
            border-radius: 4px;
        }
        .sub-section {
            background-color: #f1f1f1;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        hr {
            border: 0;
            border-top: 1px solid #e0e0e0;
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-top: 1px solid #e0e0e0;
            border-bottom: 1px solid #e0e0e0;
        }
        th {
            background-color: #f1f1f1;
            font-weight: bold;
            color: #000000;
        }
        h2 {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }
        .label {
            font-weight: bold;
            color: #000000;
        }

        .row {
            margin-left: -15px;
            margin-right: -15px;
            overflow: hidden; /* Clearfix for floats */
        }
        .row:before,
        .row:after {
            content: " ";
            display: table;
        }
        .row:after {
            clear: both;
        }
        [class*="col-"] {
            position: relative;
            min-height: 1px;
            padding-left: 15px;
            padding-right: 15px;
            float: left;
            box-sizing: border-box;
        }
        .col-1, .col-2, .col-3, .col-4, .col-5, .col-6, .col-7, .col-8, .col-9, .col-10, .col-11, .col-12 {
            float: left;
        }
        .col-12 { width: 100%; }
        .col-11 { width: 91.66666667%; }
        .col-10 { width: 83.33333333%; }
        .col-9 { width: 75%; }
        .col-8 { width: 66.66666667%; }
        .col-7 { width: 58.33333333%; }
        .col-6 { width: 50%; }
        .col-5 { width: 41.66666667%; }
        .col-4 { width: 33.33333333%; }
        .col-3 { width: 25%; }
        .col-2 { width: 16.66666667%; }
        .col-1 { width: 8.33333333%; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-12 text-center" style="margin-bottom: 20px;">
                <img src="{{ $logoUrl }}" alt="Byte Electronics Logo" style="max-width: 200px; display: inline-block;">
                {{-- <img src="https://drive.google.com/file/d/13qgl36zV4-ezFzNjBpgt4BZkMvWi0MNJ/view?usp=drive_link" alt="Byte Electronics Logo" style="max-width: 200px; display: inline-block;">  --}}
            </div>
        </div>

        <div class="rectangle-box">
            <div class="row">
                <div class="col-12 text-center" style="margin-bottom: 15px;">
                    <img src="{{ $checkMark }}" alt="Green Checkmark" style="width: 48px; height: 48px; display: inline-block; margin-bottom: 10px;">
                    {{-- <img src="https://drive.google.com/file/d/14vMQy6V5NKq1WJ_YlOuLdjstqnaNTC8A/view?usp=drive_link" alt="Green Checkmark" style="width: 48px; height: 48px; display: inline-block; margin-bottom: 10px;"> --}}
                    <div style="font-size: 16px; font-weight: 700; color: #333;">ORDER HAS BEEN PLACED</div>
                </div>
            </div>

            <div class="sub-section">
                <div class="row">
                    <div class="col-6">
                        <div class="row">
                            <div class="col-4 text-left">
                                <span class="label" style="font-weight:700;font-size:14px;">Name:</span>
                            </div>
                            <div class="col-8 text-left">
                                {{ $customer->name ?? 'N/A' }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-4 text-left">
                                <span class="label" style="font-weight:700;font-size:14px;">Email:</span>
                            </div>
                            <div class="col-8 text-left">
                                {{ $customer->email ?? 'N/A' }}
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="row">
                            <div class="col-5 text-left">
                                <span class="label" style="font-weight:700;font-size:14px;">Phone:</span>
                            </div>
                            <div class="col-7 text-left">
                                {{ $billingAddress['phone'] ?? 'N/A' }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-5 text-left">
                                <span class="label" style="font-weight:700;font-size:14px;">Company:</span>
                            </div>
                            <div class="col-7 text-left">
                                {{ $billingAddress['company_name'] ?? 'N/A' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div style="margin-bottom: 15px;">
                <h2>Order Details</h2>
                <hr>
                <div class="row">
                    <div class="col-6">
                        <div class="row">
                            <div class="col-4 text-left">
                                <span class="label" style="font-weight:700;font-size:14px;">Order ID:</span>
                            </div>
                            <div class="col-8 text-left">
                                {{ $order->id }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-4 text-left">
                                <span class="label" style="font-weight:700;font-size:14px;">Order Date:</span>
                            </div>
                            <div class="col-8 text-left">
                                {{ $order->created_at->format('d M Y H:i') }}
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="row">
                            <div class="col-5 text-left">
                                <span class="label" style="font-weight:700;font-size:14px;">Total Amount:</span>
                            </div>
                            <div class="col-7 text-left">
                                {{ $order->total_amount }} AED
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-5 text-left">
                                <span class="label" style="font-weight:700;font-size:14px;">Total Items:</span>
                            </div>
                            <div class="col-7 text-left">
                                {{ $order->total_distinct_items }}
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
            </div>

            <div style="margin-bottom: 15px;">
                <h2>Billing Address</h2>
                <div class="sub-section">
                    <div class="row">
                        <div class="col-12" style="font-weight:700;font-size:14px;">
                            {{-- <span class="label" >Name:</span> --}}
                            {{ $billingAddress['address_name'] ?? 'N/A' }}
                        </div>
                    </div>
                    @if($billingAddress['company_name'])
                    <div class="row">
                        <div class="col-12">
                            {{-- <span class="label" style="font-weight:700;font-size:14px;">Company:</span>  --}}
                            {{ $billingAddress['company_name'] }}
                        </div>
                    </div>
                    @endif
                    <div class="row">
                        <div class="col-12">
                            {{-- <span class="label">Address:</span> --}}
                            {{ $billingAddress['address_line1'] ?? 'N/A' }}
                        </div>
                    </div>
                    @if($billingAddress['address_line2'])
                    <div class="row">
                        <div class="col-12">
                            {{ $billingAddress['address_line2'] }}
                        </div>
                    </div>
                    @endif
                    <div class="row">
                        <div class="col-12">
                            {{ $billingAddress['city'] ?? 'N/A' }}, {{ $billingAddress['state'] ?? 'N/A' }} {{ $billingAddress['postal_code'] ?? 'N/A' }}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            {{-- <span class="label">Phone:</span>  --}}
                            {{ $billingAddress['phone'] ?? 'N/A' }}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            {{-- <span class="label">Email:</span> --}}
                            {{ $billingAddress['email'] ?? 'N/A' }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- @if(!empty($shippingAddress))
            <div style="margin-bottom: 15px;">
                <h2>Shipping Address</h2>
                <div class="sub-section">
                    <div class="row">
                        <div class="col-12">
                            <span class="label">Name:</span> {{ $shippingAddress['address_name'] ?? 'N/A' }}
                        </div>
                    </div>
                    @if($shippingAddress['company_name'])
                    <div class="row">
                        <div class="col-12">
                            <span class="label">Company:</span> {{ $shippingAddress['company_name'] }}
                        </div>
                    </div>
                    @endif
                    <div class="row">
                        <div class="col-12">
                            <span class="label">Address:</span> {{ $shippingAddress['address_line1'] ?? 'N/A' }}
                        </div>
                    </div>
                    @if($shippingAddress['address_line2'])
                    <div class="row">
                        <div class="col-12">
                            {{ $shippingAddress['address_line2'] }}
                        </div>
                    </div>
                    @endif
                    <div class="row">
                        <div class="col-12">
                            {{ $shippingAddress['city'] ?? 'N/A' }}, {{ $shippingAddress['state'] ?? 'N/A' }} {{ $shippingAddress['postal_code'] ?? 'N/A' }}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <span class="label">Phone:</span> {{ $shippingAddress['phone'] ?? 'N/A' }}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <span class="label">Email:</span> {{ $shippingAddress['email'] ?? 'N/A' }}
                        </div>
                    </div>
                </div>
            </div>
            @endif --}}

            <div style="margin-bottom: 15px;">
                <h2>Order Items</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Total Price</th>
                            <th>Brand</th>
                            <th>Category</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cartItems as $item)
                        <tr>
                            <td>{{ $item['product_name'] }} ({{ $item['part_no'] }})</td>
                            <td>{{ $item['qty'] }}</td>
                            <td>{{ $item['unit_price'] }} AED</td>
                            <td>{{ $item['qty'] * $item['unit_price'] }} AED</td>
                            <td>{{ $item['brand_name'] }}</td>
                            <td>{{ $item['category_name'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="row">
                <div class="col-12" style="font-weight: bold; margin-bottom: 5px;">
                    Please review the order and proceed with sending the payment link to the customer.
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-12">
                    Best Regards,<br>
                    <strong>Byte Electronics Team</strong>
                </div>
            </div>
        </div>
    </div>
</body>
</html>