<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class CounterController extends Controller
{
    /**
     * Get orders awaiting payment at counter
     */
    public function awaitingPayment(Request $request)
    {
        $date = $request->get('date', now()->toDateString());

        $orders = Order::with(['items', 'user'])
            ->forDate($date)
            ->awaitingCounterPayment()
            ->orderBy('daily_token')
            ->get()
            ->map(fn($order) => $this->formatOrder($order));

        return response()->json([
            'success' => true,
            'data' => [
                'orders' => $orders,
                'count' => $orders->count(),
            ]
        ]);
    }

    /**
     * Confirm payment at counter
     */
    public function confirmPayment(Request $request, $orderId)
    {
        $order = Order::find($orderId);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        if ($order->status !== 'awaiting_payment') {
            return response()->json([
                'success' => false,
                'message' => 'This order is not awaiting payment confirmation.'
            ], 422);
        }

        if ($order->payment_method !== 'pay_at_counter') {
            return response()->json([
                'success' => false,
                'message' => 'This order is not a pay-at-counter order.'
            ], 422);
        }

        // Confirm payment
        $order->update([
            'payment_status' => 'paid',
            'payment_confirmed_by' => $request->user()->id,
            'payment_confirmed_at' => now(),
            'status' => 'pending', // Move to pending so kitchen can start preparing
            'payment_reference' => 'COUNTER-' . strtoupper(uniqid()),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment confirmed successfully. Order sent to kitchen.',
            'data' => [
                'order' => $this->formatOrder($order->fresh(['items', 'user']))
            ]
        ]);
    }

    /**
     * Get order details for counter
     */
    public function orderDetails(Request $request, $orderId)
    {
        $order = Order::with(['items', 'user', 'store'])->find($orderId);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'order' => $this->formatOrder($order, true)
            ]
        ]);
    }

    /**
     * Cancel order (from counter - only for awaiting_payment orders)
     */
    public function cancelOrder(Request $request, $orderId)
    {
        $order = Order::find($orderId);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        if ($order->status !== 'awaiting_payment') {
            return response()->json([
                'success' => false,
                'message' => 'Only orders awaiting payment can be cancelled from counter.'
            ], 422);
        }

        $order->update([
            'status' => 'cancelled',
            'notes' => ($order->notes ? $order->notes . ' | ' : '') . 'Cancelled by counter staff',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Order cancelled successfully.',
            'data' => [
                'order' => $this->formatOrder($order)
            ]
        ]);
    }

    /**
     * Get counter dashboard stats
     */
    public function stats(Request $request)
    {
        $date = $request->get('date', now()->toDateString());

        $stats = [
            'awaiting_payment' => Order::forDate($date)->where('status', 'awaiting_payment')->count(),
            'paid_today' => Order::forDate($date)->where('payment_status', 'paid')->count(),
            'total_collected' => (float) Order::forDate($date)->where('payment_status', 'paid')->sum('total'),
            'pending_amount' => (float) Order::forDate($date)->where('status', 'awaiting_payment')->sum('total'),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'stats' => $stats,
                'date' => $date,
            ]
        ]);
    }

    /**
     * Format order for API response
     */
    protected function formatOrder($order, $detailed = false)
    {
        $data = [
            'id' => $order->id,
            'order_number' => $order->order_number,
            'daily_token' => $order->daily_token,
            'formatted_token' => $order->formatted_token,
            'token_date' => $order->token_date ? $order->token_date->format('Y-m-d') : null,
            'status' => $order->status,
            'order_type' => $order->order_type,
            'total' => (float) $order->total,
            'payment_method' => $order->payment_method,
            'payment_status' => $order->payment_status,
            'is_awaiting_counter_payment' => $order->isAwaitingCounterPayment(),
            'created_at' => $order->created_at->toIso8601String(),
            'customer' => $order->user ? [
                'id' => $order->user->id,
                'name' => $order->user->name,
                'phone' => $order->user->phone,
                'email' => $order->user->email,
            ] : null,
            'items_count' => $order->items->count(),
        ];

        if ($detailed) {
            $data['subtotal'] = (float) $order->subtotal;
            $data['tax'] = (float) $order->tax;
            $data['delivery_charge'] = (float) $order->delivery_charge;
            $data['discount'] = (float) $order->discount;
            $data['coupon_code'] = $order->coupon_code;
            $data['delivery_address'] = $order->delivery_address;
            $data['notes'] = $order->notes;
            $data['payment_reference'] = $order->payment_reference;
            $data['payment_confirmed_at'] = $order->payment_confirmed_at ? $order->payment_confirmed_at->toIso8601String() : null;
            $data['store'] = $order->store ? [
                'id' => $order->store->id,
                'name' => $order->store->name,
            ] : null;
            $data['items'] = $order->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product_name,
                    'price' => (float) $item->price,
                    'quantity' => $item->quantity,
                    'subtotal' => (float) $item->subtotal,
                    'customizations' => $item->customizations,
                ];
            });
        } else {
            $data['items'] = $order->items->map(function ($item) {
                return [
                    'product_name' => $item->product_name,
                    'quantity' => $item->quantity,
                    'subtotal' => (float) $item->subtotal,
                ];
            });
        }

        return $data;
    }
}
