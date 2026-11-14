<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class InvoiceService
{
    /**
     * Generate downloadable PDF Invoice for an order.
     */
    public function downloadInvoice(Order $order): Response
    {
        $order->load(['items.product', 'items.variant']);

        $storeInfo = [
            'name' => Setting::get('store_name', 'Bombay Fashion'),
            'address' => Setting::get('store_address', 'Mahila College Road, opposite Mahila College, Amritsar, Punjab, India'),
            'phone' => Setting::get('store_phone', '+91 98765 43210'),
            'email' => Setting::get('store_email', 'care@bombayfashionamritsar.com'),
            'timings' => Setting::get('store_timings', 'Open daily, closes 9:30 PM'),
        ];

        $pdf = Pdf::loadView('invoices.order-invoice', [
            'order' => $order,
            'store' => $storeInfo,
        ]);

        return $pdf->download("invoice-{$order->order_number}.pdf");
    }

    /**
     * Stream / Print PDF Packing Slip.
     */
    public function downloadPackingSlip(Order $order): Response
    {
        $order->load(['items.product', 'items.variant']);

        $storeInfo = [
            'name' => Setting::get('store_name', 'Bombay Fashion'),
            'address' => Setting::get('store_address', 'Mahila College Road, opposite Mahila College, Amritsar, Punjab, India'),
            'phone' => Setting::get('store_phone', '+91 98765 43210'),
            'email' => Setting::get('store_email', 'care@bombayfashionamritsar.com'),
        ];

        $pdf = Pdf::loadView('invoices.packing-slip', [
            'order' => $order,
            'store' => $storeInfo,
        ]);

        return $pdf->stream("packing-slip-{$order->order_number}.pdf");
    }
}
