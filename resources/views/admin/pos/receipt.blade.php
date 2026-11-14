<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt #{{ $order->order_number }} - Bombay Fashion Amritsar</title>
    <style>
        @page {
            size: 80mm auto;
            margin: 5mm;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 11px;
            line-height: 1.3;
            color: #000;
            margin: 0;
            padding: 5px;
            max-width: 78mm;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        .divider {
            border-top: 1px dashed #000;
            margin: 6px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }
        th, td {
            padding: 3px 0;
        }
        .header h1 {
            font-size: 15px;
            margin: 0 0 3px 0;
            text-transform: uppercase;
        }
        .header p {
            margin: 1px 0;
            font-size: 10px;
        }
        .no-print {
            margin-bottom: 10px;
            text-align: center;
        }
        .btn-print {
            background: #800020;
            color: #fff;
            padding: 6px 12px;
            font-size: 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-family: sans-serif;
        }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print">
        <button class="btn-print" onclick="window.print()">🖨️ Print Receipt (80mm)</button>
    </div>

    <div class="header text-center">
        <h1>BOMBAY FASHION</h1>
        <p class="bold">Amritsar Showroom Counter</p>
        <p>Lawrence Road / Hall Bazaar, Amritsar, Punjab</p>
        <p>Ph: +91 98765 43210 | GSTIN: 03ABCDE1234F1Z5</p>
    </div>

    <div class="divider"></div>

    <div>
        <div><span class="bold">Receipt:</span> {{ $order->order_number }}</div>
        <div><span class="bold">Date:</span> {{ $order->created_at->format('d/m/Y h:i A') }}</div>
        <div><span class="bold">Customer:</span> {{ $order->customer_name }} ({{ $order->customer_phone }})</div>
        <div><span class="bold">Billed By:</span> {{ $order->cashier_name ?? $order->billedBy?->name ?? auth()->user()->name }} ({{ $order->cashier_email ?? $order->billedBy?->email ?? auth()->user()->email }})</div>
    </div>

    <div class="divider"></div>

    <table>
        <thead>
            <tr style="border-bottom: 1px solid #000;">
                <th style="text-align: left;">Item</th>
                <th style="text-align: center;">Qty</th>
                <th style="text-align: right;">Price</th>
                <th style="text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
                <tr>
                    <td colspan="4" class="bold" style="padding-top: 4px;">{{ $item->product_name }}</td>
                </tr>
                <tr style="border-bottom: 1px dotted #ccc;">
                    <td style="font-size: 9px; padding-left: 5px;">{{ $item->variant_details ?: 'Std' }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">₹{{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-right">₹{{ number_format($item->total_price, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="divider"></div>

    <table>
        <tr>
            <td>Subtotal:</td>
            <td class="text-right">₹{{ number_format($order->subtotal, 2) }}</td>
        </tr>
        @if($order->discount_amount > 0)
            <tr>
                <td>Discount:</td>
                <td class="text-right">-₹{{ number_format($order->discount_amount, 2) }}</td>
            </tr>
        @endif
        <tr>
            <td>GST (5% Apparel):</td>
            <td class="text-right">₹{{ number_format($order->tax_amount, 2) }}</td>
        </tr>
        <tr class="bold" style="font-size: 13px;">
            <td>GRAND TOTAL:</td>
            <td class="text-right">₹{{ number_format($order->grand_total, 2) }}</td>
        </tr>
        <tr>
            <td>Payment Mode:</td>
            <td class="text-right bold" style="text-transform: uppercase;">{{ $order->payment_method }}</td>
        </tr>
    </table>

    <div class="divider"></div>

    <div class="text-center" style="font-size: 9px; margin-top: 5px;">
        <p class="bold">THANK YOU FOR SHOPPING AT BOMBAY FASHION!</p>
        <p>Exchange allowed within 7 days with bill.</p>
        <p>No refund on bridal wear / cut fabrics.</p>
        <p>*** Have a Blessed Day ***</p>
    </div>

</body>
</html>
