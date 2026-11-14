<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\InvoiceService;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CustomerOrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService,
        protected InvoiceService $invoiceService
    ) {}

    public function index(): View
    {
        $orders = Order::where('user_id', Auth::id())
            ->with(['items.product', 'items.variant'])
            ->latest()
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    public function show(string $orderNumber): View
    {
        $order = Order::where('order_number', $orderNumber)
            ->with(['items.product.primaryImage', 'items.variant', 'payments'])
            ->firstOrFail();

        return view('orders.show', compact('order'));
    }

    public function cancel(Request $request, string $orderNumber): RedirectResponse
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if (!$order->canCancel()) {
            return back()->with('error', 'This order has already been processed or shipped and cannot be cancelled online.');
        }

        $reason = $request->input('reason') ?? $request->input('cancel_reason') ?? 'Customer requested cancellation';

        $order->update([
            'order_status' => 'cancelled',
            'cancelled_reason' => $reason,
        ]);

        // Restock items
        $this->orderService->restockOrder($order, 'order_cancelled', 'Customer cancelled');

        return back()->with('success', 'Your order has been cancelled successfully.');
    }

    public function requestReturn(Request $request, string $orderNumber): RedirectResponse
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if (!$order->canReturn()) {
            return back()->with('error', 'This order is not eligible for return or exchange.');
        }

        $reason = $request->input('return_reason') 
            ?? trim(($request->input('reason') ?? '') . ($request->input('details') ? ' - ' . $request->input('details') : ''));
        
        if (empty($reason)) {
            $reason = 'Customer requested return/exchange';
        }

        $order->update([
            'return_status' => 'requested',
            'return_reason' => $reason,
        ]);

        return back()->with('success', 'Return/exchange request submitted! Our Amritsar team will contact you within 24 hours.');
    }

    public function downloadInvoice(string $orderNumber): Response
    {
        $query = Order::where('order_number', $orderNumber);

        if (Auth::check() && !Auth::user()->canAccessAdmin()) {
            $query->where('user_id', Auth::id());
        }

        $order = $query->firstOrFail();

        return $this->invoiceService->downloadInvoice($order);
    }

    public function track(Request $request): View
    {
        $order = null;
        $orderNumber = trim($request->input('order_number', ''));
        $phone = trim($request->input('phone', $request->input('contact', '')));
        $searched = false;

        if (!empty($orderNumber)) {
            $searched = true;

            $query = Order::where('order_number', $orderNumber)
                ->with(['items.product', 'items.variant']);

            if (!empty($phone)) {
                $query->where(function ($q) use ($phone) {
                    $q->where('customer_email', $phone)
                      ->orWhere('customer_phone', 'like', "%{$phone}%");
                });
            }

            $order = $query->first();
        }

        return view('orders.track', compact('order', 'searched', 'orderNumber', 'phone'));
    }
}
