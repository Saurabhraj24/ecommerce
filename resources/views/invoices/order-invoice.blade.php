<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $order->order_number }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #3b2a3e;
            font-size: 12px;
            line-height: 1.5;
            margin: 0;
            padding: 20px;
            background-color: #ffffff;
        }
        .header {
            border-bottom: 2px solid #c2185b;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .store-name {
            font-size: 24px;
            font-weight: bold;
            color: #c2185b;
            margin: 0;
        }
        .store-sub {
            color: #d4a017;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .info-table {
            width: 100%;
            margin-bottom: 25px;
        }
        .info-table td {
            vertical-align: top;
            width: 50%;
        }
        .section-title {
            font-size: 11px;
            font-weight: bold;
            color: #880e4f;
            text-transform: uppercase;
            border-bottom: 1px solid #fce4ec;
            padding-bottom: 4px;
            margin-bottom: 8px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th {
            background-color: #fde8ef;
            color: #880e4f;
            text-align: left;
            padding: 8px 10px;
            font-size: 11px;
            border-bottom: 1px solid #c2185b;
        }
        .items-table td {
            padding: 10px;
            border-bottom: 1px solid #f5e6eb;
        }
        .items-table tr:nth-child(even) td {
            background-color: #fff9f3;
        }
        .text-right {
            text-align: right;
        }
        .totals-table {
            width: 45%;
            margin-left: auto;
            border-collapse: collapse;
        }
        .totals-table td {
            padding: 6px 10px;
        }
        .totals-table tr.grand-total td {
            border-top: 2px solid #c2185b;
            font-size: 14px;
            font-weight: bold;
            color: #c2185b;
        }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-paid { background-color: #d1fae5; color: #065f46; }
        .badge-pending { background-color: #fef3c7; color: #92400e; }
        .footer {
            margin-top: 40px;
            border-top: 1px solid #fce4ec;
            padding-top: 15px;
            text-align: center;
            font-size: 10px;
            color: #7a6e7d;
        }
    </style>
</head>
<body>

    <table class="header" width="100%">
        <tr>
            <td>
                <h1 class="store-name">{{ $store['name'] }}</h1>
                <div class="store-sub">Amritsar • Punjab • India</div>
                <div style="font-size: 10px; color: #555; margin-top: 4px;">
                    {{ $store['address'] }}<br>
                    Phone: {{ $store['phone'] }} | {{ $store['timings'] }}
                </div>
            </td>
            <td class="text-right" style="vertical-align: top;">
                <div style="font-size: 18px; font-weight: bold; color: #3b2a3e;">TAX INVOICE</div>
                <div style="font-size: 12px; font-weight: bold; color: #c2185b; margin-top: 4px;">#{{ $order->order_number }}</div>
                <div style="font-size: 10px; color: #666; margin-top: 2px;">Date: {{ $order->created_at->format('d M Y, h:i A') }}</div>
                <div style="margin-top: 6px;">
                    <span class="badge {{ $order->payment_status === 'paid' ? 'badge-paid' : 'badge-pending' }}">
                        Payment: {{ strtoupper($order->payment_status) }} ({{ strtoupper($order->payment_method) }})
                    </span>
                </div>
            </td>
        </tr>
    </table>

    <table class="info-table">
        <tr>
            <td>
                <div class="section-title">Billed & Shipped To:</div>
                <strong>{{ $order->customer_name }}</strong><br>
                @if (isset($order->shipping_address['address_line1']))
                    {{ $order->shipping_address['address_line1'] }}<br>
                    @if (!empty($order->shipping_address['address_line2']))
                        {{ $order->shipping_address['address_line2'] }}<br>
                    @endif
                    @if (!empty($order->shipping_address['landmark']))
                        Near {{ $order->shipping_address['landmark'] }}<br>
                    @endif
                    {{ $order->shipping_address['city'] }}, {{ $order->shipping_address['state'] }} - {{ $order->shipping_address['postal_code'] }}<br>
                @endif
                Phone: {{ $order->customer_phone }}<br>
                Email: {{ $order->customer_email }}
            </td>
            <td>
                <div class="section-title">Order Summary:</div>
                <strong>Order Status:</strong> {{ ucfirst($order->order_status) }}<br>
                <strong>Payment Mode:</strong> {{ strtoupper($order->payment_method) }}<br>
                @if ($order->tracking_number)
                    <strong>Tracking No:</strong> {{ $order->tracking_number }} ({{ $order->courier_name ?? 'Standard Courier' }})<br>
                @endif
                @if ($order->coupon_code)
                    <strong>Coupon Applied:</strong> {{ $order->coupon_code }}<br>
                @endif
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 50%;">Item Description</th>
                <th style="width: 15%;">SKU</th>
                <th style="width: 10%;" class="text-right">Price</th>
                <th style="width: 8%;" class="text-right">Qty</th>
                <th style="width: 12%;" class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $idx => $item)
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td>
                        <strong>{{ $item->product_name }}</strong>
                        @if ($item->variant_details)
                            <div style="font-size: 10px; color: #777;">{{ $item->variant_details }}</div>
                        @endif
                    </td>
                    <td>{{ $item->product_sku }}</td>
                    <td class="text-right">Rs. {{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right">Rs. {{ number_format($item->total_price, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals-table">
        <tr>
            <td>Subtotal:</td>
            <td class="text-right">Rs. {{ number_format($order->subtotal, 2) }}</td>
        </tr>
        @if ($order->discount_amount > 0)
            <tr style="color: #c2185b;">
                <td>Discount:</td>
                <td class="text-right">- Rs. {{ number_format($order->discount_amount, 2) }}</td>
            </tr>
        @endif
        @if ($order->tax_amount > 0)
            <tr>
                <td>GST (Taxes):</td>
                <td class="text-right">Rs. {{ number_format($order->tax_amount, 2) }}</td>
            </tr>
        @endif
        <tr>
            <td>Delivery Charges:</td>
            <td class="text-right">
                {{ $order->shipping_charge > 0 ? ('Rs. ' . number_format($order->shipping_charge, 2)) : 'FREE' }}
            </td>
        </tr>
        <tr class="grand-total">
            <td>Grand Total:</td>
            <td class="text-right">Rs. {{ number_format($order->grand_total, 2) }}</td>
        </tr>
    </table>

    <div class="footer">
        Thank you for choosing {{ $store['name'] }}, Amritsar!<br>
        For inquiries or exchange requests, visit us on Mahila College Road, Amritsar or call {{ $store['phone'] }}.<br>
        This is a computer-generated tax invoice.
    </div>

</body>
</html>
