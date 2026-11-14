<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Order;
use App\Models\Payment;
use App\Services\InvoiceService;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService,
        protected InvoiceService $invoiceService
    ) {}

    public function index(Request $request): View
    {
        $query = Order::with(['user', 'items']);

        if ($request->filled('q')) {
            $keyword = trim($request->input('q'));
            $query->where(function ($q) use ($keyword) {
                $q->where('order_number', 'like', "%{$keyword}%")
                  ->orWhere('customer_name', 'like', "%{$keyword}%")
                  ->orWhere('customer_phone', 'like', "%{$keyword}%")
                  ->orWhere('customer_email', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('order_status', $request->input('status'));
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->input('payment_status'));
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->input('payment_method'));
        }

        if ($request->filled('return_status')) {
            $query->where('return_status', $request->input('return_status'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        $orders = $query->latest()->paginate(15)->withQueryString();

        $canViewRevenue = Auth::user()->isAdmin();

        return view('admin.orders.index', compact('orders', 'canViewRevenue'));
    }

    public function show(Order $order): View
    {
        $order->load(['user', 'items.product.primaryImage', 'items.variant', 'payments']);
        $canViewRevenue = Auth::user()->isAdmin();

        return view('admin.orders.show', compact('order', 'canViewRevenue'));
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'order_status' => 'required|in:pending,confirmed,packed,shipped,delivered,cancelled',
            'tracking_number' => 'nullable|string|max:100',
            'courier_name' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        $oldStatus = $order->order_status;
        $newStatus = $validated['order_status'];

        $updates = [
            'order_status' => $newStatus,
            'tracking_number' => $validated['tracking_number'] ?? $order->tracking_number,
            'courier_name' => $validated['courier_name'] ?? $order->courier_name,
        ];

        if (!empty($validated['notes'])) {
            $updates['notes'] = $validated['notes'];
        }

        if ($newStatus === 'delivered' && !$order->delivered_at) {
            $updates['delivered_at'] = now();
        }

        if ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {
            $this->orderService->restockOrder($order, 'order_cancelled', 'Order cancelled by staff');
        }

        $order->update($updates);

        ActivityLog::log('order_status_updated', Order::class, $order->id, [
            'order_number' => $order->order_number,
            'from' => $oldStatus,
            'to' => $newStatus,
        ]);

        return back()->with('success', "Order status updated to " . ucfirst($newStatus) . ".");
    }

    public function markCodPaid(Order $order): RedirectResponse
    {
        if ($order->payment_method !== 'cod') {
            return back()->with('error', 'Only Cash on Delivery orders can be marked as paid manually.');
        }

        $order->update(['payment_status' => 'paid']);

        Payment::updateOrCreate(
            ['order_id' => $order->id],
            [
                'payment_method' => 'cod',
                'amount' => $order->grand_total,
                'currency' => 'INR',
                'status' => 'successful',
                'response_payload' => ['marked_by' => Auth::user()->name, 'date' => now()->toIso8601String()],
            ]
        );

        ActivityLog::log('cod_marked_paid', Order::class, $order->id, [
            'order_number' => $order->order_number,
            'amount' => $order->grand_total,
        ]);

        return back()->with('success', 'COD marked as paid on delivery.');
    }

    public function updateReturn(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'return_status' => 'required|in:approved,rejected,completed',
            'notes' => 'nullable|string|max:500',
        ]);

        $newStatus = $validated['return_status'];

        if ($newStatus === 'completed' && $order->return_status !== 'completed') {
            // Auto restock returned inventory
            $this->orderService->restockOrder($order, 'return_restocked', 'Customer return completed');
            $order->update(['order_status' => 'returned']);
        }

        $order->update(['return_status' => $newStatus]);

        ActivityLog::log('return_status_updated', Order::class, $order->id, [
            'order_number' => $order->order_number,
            'return_status' => $newStatus,
        ]);

        return back()->with('success', "Return status updated to {$newStatus}.");
    }

    public function printInvoice(Order $order): Response
    {
        $user = Auth::user();
        $isBuyer = ($order->user_id && $order->user_id === $user?->id) || ($order->customer_email && $order->customer_email === $user?->email);
        if (!$user?->isAdmin() && !$user?->hasPermission('orders.manage') && !$isBuyer) {
            abort(403, 'Unauthorized to view this order invoice.');
        }

        return $this->invoiceService->downloadInvoice($order);
    }

    public function printPackingSlip(Order $order): Response
    {
        return $this->invoiceService->downloadPackingSlip($order);
    }
}
