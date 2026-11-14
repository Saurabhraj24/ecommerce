<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Packing Slip #{{ $order->order_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            font-size: 13px;
            margin: 0;
            padding: 25px;
        }
        .header {
            border-bottom: 2px dashed #999;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .title {
            font-size: 22px;
            font-weight: bold;
            color: #c2185b;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .table th, .table td {
            border: 1px solid #ccc;
            padding: 8px 12px;
            text-align: left;
        }
        .table th {
            background-color: #f7f7f7;
        }
        .check-box {
            width: 18px;
            height: 18px;
            border: 1px solid #333;
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="header">
        <table width="100%">
            <tr>
                <td>
                    <div class="title">PACKING SLIP</div>
                    <div><strong>{{ $store['name'] }}</strong>, Amritsar</div>
                </td>
                <td style="text-align: right;">
                    <strong>Order #:</strong> {{ $order->order_number }}<br>
                    <strong>Date:</strong> {{ $order->created_at->format('d M Y') }}<br>
                    <strong>Payment:</strong> {{ strtoupper($order->payment_method) }} ({{ strtoupper($order->payment_status) }})
                </td>
            </tr>
        </table>
    </div>

    <div style="margin-bottom: 20px;">
        <strong>Ship To:</strong><br>
        {{ $order->customer_name }}<br>
        @if (isset($order->shipping_address['address_line1']))
            {{ $order->shipping_address['address_line1'] }}, {{ $order->shipping_address['city'] }}, {{ $order->shipping_address['state'] }} - {{ $order->shipping_address['postal_code'] }}<br>
        @endif
        Phone: {{ $order->customer_phone }}
    </div>

    <table class="table">
        <thead>
            <tr>
                <th style="width: 10%;">Verified</th>
                <th>Item Name</th>
                <th>SKU</th>
                <th>Variant / Details</th>
                <th style="width: 10%; text-align: center;">Qty</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $item)
                <tr>
                    <td style="text-align: center;"><div class="check-box"></div></td>
                    <td><strong>{{ $item->product_name }}</strong></td>
                    <td>{{ $item->product_sku }}</td>
                    <td>{{ $item->variant_details ?? 'Standard' }}</td>
                    <td style="text-align: center; font-size: 14px; font-weight: bold;">{{ $item->quantity }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 30px; font-size: 11px; color: #666;">
        Packed by: _______________________ &nbsp;&nbsp;&nbsp;&nbsp; Date: _______________________
    </div>
</body>
</html>
