<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Get user's orders
     */
    public function index(Request $request)
    {
        $orders = Order::where('user_id', $request->user()->id)
            ->with('items')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => [
                'orders' => $orders->map(function ($order) {
                    return $this->formatOrder($order);
                }),
                'pagination' => [
                    'current_page' => $orders->currentPage(),
                    'last_page' => $orders->lastPage(),
                    'per_page' => $orders->perPage(),
                    'total' => $orders->total(),
                ]
            ]
        ], 200);
    }

    /**
     * Get single order details
     */
    public function show(Request $request, $id)
    {
        $order = Order::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->with('items')
            ->first();

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
        ], 200);
    }

    /**
     * Create a new order
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'store_id' => 'nullable|exists:stores,id',
            'order_type' => 'required|in:pickup,delivery',
            'delivery_address' => 'required_if:order_type,delivery|nullable|string|max:500',
            'payment_method' => 'required|in:cash,card,online',
            'notes' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.product_name' => 'required|string',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.customizations' => 'nullable|array',
        ]);

        try {
            $order = DB::transaction(function () use ($validated, $request) {
                // Calculate totals
                $subtotal = 0;
                foreach ($validated['items'] as $item) {
                    $subtotal += $item['price'] * $item['quantity'];
                }

                $taxRate = (float) \App\Models\Setting::get('tax_rate', 0);
                $tax = $subtotal * ($taxRate / 100);
                $deliveryCharge = $validated['order_type'] === 'delivery' ? 2.50 : 0;
                $total = $subtotal + $tax + $deliveryCharge;

                // Create order
                $order = Order::create([
                    'user_id' => $request->user()->id,
                    'store_id' => $validated['store_id'] ?? null,
                    'order_type' => $validated['order_type'],
                    'delivery_address' => $validated['delivery_address'] ?? null,
                    'payment_method' => $validated['payment_method'],
                    'payment_status' => 'pending',
                    'subtotal' => $subtotal,
                    'tax' => $tax,
                    'delivery_charge' => $deliveryCharge,
                    'total' => $total,
                    'status' => 'pending',
                    'notes' => $validated['notes'] ?? null,
                ]);

                // Create order items
                foreach ($validated['items'] as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item['product_id'],
                        'product_name' => $item['product_name'],
                        'price' => $item['price'],
                        'quantity' => $item['quantity'],
                        'subtotal' => $item['price'] * $item['quantity'],
                        'customizations' => $item['customizations'] ?? null,
                    ]);
                }

                return $order->fresh(['items']);
            });

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully',
                'data' => [
                    'order' => $this->formatOrder($order, true)
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to place order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get order by token (for display screens)
     */
    public function getByToken(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required|integer',
            'date' => 'nullable|date',
        ]);

        $date = $validated['date'] ?? now()->toDateString();

        $order = Order::where('daily_token', $validated['token'])
            ->where('token_date', $date)
            ->with('items')
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'order' => $this->formatOrder($order)
            ]
        ], 200);
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
            'subtotal' => (float) $order->subtotal,
            'tax' => (float) $order->tax,
            'delivery_charge' => (float) $order->delivery_charge,
            'discount' => (float) $order->discount,
            'total' => (float) $order->total,
            'payment_method' => $order->payment_method,
            'payment_status' => $order->payment_status,
            'created_at' => $order->created_at->toIso8601String(),
        ];

        if ($detailed) {
            $data['delivery_address'] = $order->delivery_address;
            $data['notes'] = $order->notes;
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
            $data['items_count'] = $order->items->count();
        }

        return $data;
    }
}
