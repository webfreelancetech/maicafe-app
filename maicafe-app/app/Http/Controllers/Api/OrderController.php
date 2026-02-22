<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Models\Setting;
use App\Models\Coupon;
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
     * Checkout - Create order from cart
     */
    public function checkout(Request $request)
    {
        $validated = $request->validate([
            'order_type' => 'required|in:pickup,delivery',
            'delivery_address' => 'required_if:order_type,delivery|nullable|string|max:500',
            'payment_method' => 'required|in:online,pay_at_counter',
            'store_id' => 'nullable|exists:stores,id',
            'notes' => 'nullable|string|max:500',
        ]);

        $userId = $request->user()->id;
        $cart = Cart::where('user_id', $userId)->with('items')->first();

        if (!$cart || $cart->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Your cart is empty',
            ], 422);
        }

        try {
            $order = DB::transaction(function () use ($validated, $request, $cart) {
                // Calculate totals from cart
                $subtotal = $cart->subtotal;
                $taxRate = (float) Setting::get('tax_rate', 0);
                $tax = $subtotal * ($taxRate / 100);
                $discount = (float) $cart->discount_amount;
                $deliveryCharge = $validated['order_type'] === 'delivery' 
                    ? (float) Setting::get('delivery_charge', 2.50) 
                    : 0;
                $total = $subtotal + $tax + $deliveryCharge - $discount;

                // Determine payment status based on method
                $paymentStatus = 'pending';
                $orderStatus = 'pending';

                if ($validated['payment_method'] === 'online') {
                    // For online payment, status will be updated after payment confirmation
                    $paymentStatus = 'pending';
                    $orderStatus = 'pending'; // Will change to 'confirmed' after payment
                } else {
                    // Pay at counter - order goes to "awaiting_payment" status
                    // Customer needs to pay at counter first before order is sent to kitchen
                    $paymentStatus = 'pending'; // Will be 'paid' when counter staff confirms payment
                    $orderStatus = 'awaiting_payment'; // Will change to 'pending' after payment confirmed
                }

                // Create order
                $order = Order::create([
                    'user_id' => $request->user()->id,
                    'store_id' => $validated['store_id'] ?? $cart->store_id,
                    'order_type' => $validated['order_type'],
                    'delivery_address' => $validated['delivery_address'] ?? null,
                    'payment_method' => $validated['payment_method'],
                    'payment_status' => $paymentStatus,
                    'subtotal' => $subtotal,
                    'tax' => $tax,
                    'delivery_charge' => $deliveryCharge,
                    'discount' => $discount,
                    'coupon_code' => $cart->coupon_code,
                    'total' => $total,
                    'status' => $orderStatus,
                    'notes' => $validated['notes'] ?? $cart->notes,
                ]);

                // Create order items from cart items
                foreach ($cart->items as $cartItem) {
                    // Build customizations array
                    $customizations = [];
                    
                    // Add variant info if present
                    if ($cartItem->variant_id && $cartItem->variant_name) {
                        $customizations['variant'] = [
                            'id' => $cartItem->variant_id,
                            'name' => $cartItem->variant_name,
                            'price' => (float) $cartItem->unit_price,
                        ];
                    }

                    // Add addons if present
                    if (!empty($cartItem->addons)) {
                        $customizations['addons'] = $cartItem->addons;
                    }

                    // Add special instructions if present
                    if ($cartItem->special_instructions) {
                        $customizations['special_instructions'] = $cartItem->special_instructions;
                    }

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $cartItem->product_id,
                        'product_name' => $cartItem->product_name,
                        'price' => $cartItem->unit_price + $cartItem->addons_total,
                        'quantity' => $cartItem->quantity,
                        'subtotal' => $cartItem->item_total,
                        'customizations' => !empty($customizations) ? $customizations : null,
                    ]);
                }

                // Update coupon usage if used
                if ($cart->coupon_code) {
                    Coupon::where('code', $cart->coupon_code)->increment('used_count');
                }

                // Clear the cart after successful order
                $cart->clear();

                return $order->fresh(['items']);
            });

            // Prepare response based on payment method
            $responseData = [
                'order' => $this->formatOrder($order, true),
            ];

            if ($validated['payment_method'] === 'online') {
                // For online payment, include payment URL/info
                $responseData['payment'] = [
                    'required' => true,
                    'method' => 'online',
                    'amount' => (float) $order->total,
                    'currency' => Setting::get('currency_code', 'GBP'),
                    // In a real app, you'd generate a payment session here
                    'payment_url' => url('/api/orders/' . $order->id . '/pay'),
                    'instructions' => 'Please complete online payment to confirm your order.',
                ];
            } else {
                // Pay at counter flow
                $responseData['payment'] = [
                    'required' => true,
                    'method' => 'pay_at_counter',
                    'amount' => (float) $order->total,
                    'currency' => Setting::get('currency_code', 'GBP'),
                    'awaiting_confirmation' => true,
                    'instructions' => 'Please proceed to the counter to complete your payment. Your order will be prepared once payment is confirmed.',
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully',
                'data' => $responseData
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to place order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel an order (only if status allows)
     */
    public function cancel(Request $request, $id)
    {
        $order = Order::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        // Only allow cancellation of pending or awaiting_payment orders
        $cancellableStatuses = ['pending', 'awaiting_payment'];
        if (!in_array($order->status, $cancellableStatuses)) {
            return response()->json([
                'success' => false,
                'message' => 'This order cannot be cancelled. Only pending or awaiting payment orders can be cancelled.',
            ], 422);
        }

        $order->update(['status' => 'cancelled']);

        return response()->json([
            'success' => true,
            'message' => 'Order cancelled successfully',
            'data' => [
                'order' => $this->formatOrder($order->fresh(['items']), true)
            ]
        ], 200);
    }

    /**
     * Reorder - Add items from a previous order to cart
     */
    public function reorder(Request $request, $id)
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

        $cart = Cart::getOrCreateForUser($request->user()->id);
        
        $addedItems = 0;
        $skippedItems = [];

        foreach ($order->items as $orderItem) {
            // Check if product still exists and is active
            $product = \App\Models\Product::where('id', $orderItem->product_id)
                ->where('is_active', true)
                ->first();

            if (!$product) {
                $skippedItems[] = $orderItem->product_name . ' (no longer available)';
                continue;
            }

            // Get customizations
            $customizations = $orderItem->customizations ?? [];
            $variant = $customizations['variant'] ?? null;
            $addons = $customizations['addons'] ?? [];

            // Validate variant if product has variants
            $variantId = null;
            $unitPrice = $product->price;
            $variantName = null;

            if ($product->has_variants) {
                if ($variant && isset($variant['id'])) {
                    $productVariant = \App\Models\ProductVariant::where('id', $variant['id'])
                        ->where('product_id', $product->id)
                        ->where('is_active', true)
                        ->first();

                    if ($productVariant) {
                        $variantId = $productVariant->id;
                        $unitPrice = $productVariant->price;
                        $variantName = $productVariant->name;
                    } else {
                        $skippedItems[] = $orderItem->product_name . ' (variant no longer available)';
                        continue;
                    }
                } else {
                    $skippedItems[] = $orderItem->product_name . ' (requires variant selection)';
                    continue;
                }
            }

            // Validate and calculate addons
            $addonsTotal = 0;
            $validAddons = [];

            foreach ($addons as $addon) {
                if (isset($addon['id'])) {
                    $productAddon = \App\Models\ProductAddon::where('id', $addon['id'])
                        ->where('is_active', true)
                        ->first();

                    if ($productAddon) {
                        $addonQty = $addon['quantity'] ?? 1;
                        $addonsTotal += $productAddon->price * $addonQty;
                        $validAddons[] = [
                            'id' => $productAddon->id,
                            'addon_name' => $productAddon->name,
                            'price' => (float) $productAddon->price,
                            'quantity' => $addonQty,
                            'total' => $productAddon->price * $addonQty,
                            'group_id' => $productAddon->addon_group_id,
                        ];
                    }
                }
            }

            // Calculate item total
            $quantity = $orderItem->quantity;
            $itemTotal = ($unitPrice + $addonsTotal) * $quantity;

            // Create cart item
            \App\Models\CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'variant_id' => $variantId,
                'product_name' => $product->name,
                'variant_name' => $variantName,
                'unit_price' => $unitPrice,
                'quantity' => $quantity,
                'addons_total' => $addonsTotal,
                'item_total' => $itemTotal,
                'addons' => $validAddons,
                'special_instructions' => $customizations['special_instructions'] ?? null,
            ]);

            $addedItems++;
        }

        $cart->load('items.product', 'items.variant');

        $message = $addedItems > 0 
            ? "$addedItems item(s) added to cart" 
            : 'No items could be added to cart';

        if (count($skippedItems) > 0) {
            $message .= '. Skipped: ' . implode(', ', $skippedItems);
        }

        return response()->json([
            'success' => $addedItems > 0,
            'message' => $message,
            'data' => [
                'added_items' => $addedItems,
                'skipped_items' => $skippedItems,
                'cart' => $this->formatCartForReorder($cart),
            ]
        ], $addedItems > 0 ? 200 : 422);
    }

    /**
     * Confirm payment (for online payments)
     */
    public function confirmPayment(Request $request, $id)
    {
        $validated = $request->validate([
            'payment_reference' => 'required|string|max:255',
            'payment_provider' => 'nullable|string|max:50',
        ]);

        $order = Order::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        if ($order->payment_status === 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'Payment already confirmed for this order'
            ], 422);
        }

        if ($order->payment_method !== 'online') {
            return response()->json([
                'success' => false,
                'message' => 'This order does not require online payment'
            ], 422);
        }

        // In a real app, you'd verify the payment with your payment provider here
        // For now, we'll just update the order status

        $order->update([
            'payment_status' => 'paid',
            'payment_reference' => $validated['payment_reference'],
            'status' => 'confirmed', // Auto-confirm after payment
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment confirmed successfully',
            'data' => [
                'order' => $this->formatOrder($order->fresh(['items']), true)
            ]
        ], 200);
    }

    /**
     * Track order status
     */
    public function track(Request $request, $id)
    {
        $order = Order::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        // Build status flow based on payment method
        $statusFlow = [];
        
        if ($order->payment_method === 'pay_at_counter') {
            $statusFlow = [
                'awaiting_payment' => ['label' => 'Awaiting Payment', 'icon' => 'clock', 'completed' => true],
                'pending' => ['label' => 'Payment Received', 'icon' => 'receipt', 'completed' => false],
                'preparing' => ['label' => 'Preparing', 'icon' => 'fire', 'completed' => false],
                'ready' => ['label' => 'Ready for Pickup', 'icon' => 'bell', 'completed' => false],
                'completed' => ['label' => 'Completed', 'icon' => 'check-double', 'completed' => false],
            ];
        } else {
            $statusFlow = [
                'pending' => ['label' => 'Order Placed', 'icon' => 'receipt', 'completed' => true],
                'confirmed' => ['label' => 'Order Confirmed', 'icon' => 'check-circle', 'completed' => false],
                'preparing' => ['label' => 'Preparing', 'icon' => 'fire', 'completed' => false],
                'ready' => ['label' => 'Ready for Pickup', 'icon' => 'bell', 'completed' => false],
                'out_for_delivery' => ['label' => 'Out for Delivery', 'icon' => 'motorcycle', 'completed' => false],
                'completed' => ['label' => 'Completed', 'icon' => 'check-double', 'completed' => false],
            ];
        }

        $currentStatusFound = false;
        foreach ($statusFlow as $status => &$info) {
            if ($status === $order->status) {
                $info['completed'] = true;
                $info['current'] = true;
                $currentStatusFound = true;
            } elseif (!$currentStatusFound) {
                $info['completed'] = true;
            }
        }

        // Remove delivery step for pickup orders
        if ($order->order_type === 'pickup') {
            unset($statusFlow['out_for_delivery']);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'token' => $order->formatted_token,
                'current_status' => $order->status,
                'status_label' => ucfirst(str_replace('_', ' ', $order->status)),
                'order_type' => $order->order_type,
                'estimated_time' => $this->getEstimatedTime($order),
                'timeline' => array_values($statusFlow),
                'is_cancelled' => $order->status === 'cancelled',
            ]
        ], 200);
    }

    /**
     * Get estimated preparation/delivery time
     */
    protected function getEstimatedTime($order)
    {
        // This would typically come from settings or be calculated based on order complexity
        $baseTime = 15; // minutes
        $itemCount = $order->items->sum('quantity');
        $extraTime = floor($itemCount / 3) * 5;

        if ($order->order_type === 'delivery') {
            $extraTime += 20; // Add delivery time
        }

        $estimatedMinutes = $baseTime + $extraTime;

        if (in_array($order->status, ['completed', 'cancelled'])) {
            return null;
        }

        if ($order->status === 'ready') {
            return 'Ready now';
        }

        return "$estimatedMinutes - " . ($estimatedMinutes + 10) . " mins";
    }

    /**
     * Format cart for reorder response
     */
    protected function formatCartForReorder(Cart $cart)
    {
        $items = $cart->items->map(function ($item) {
            return [
                'id' => $item->id,
                'product_name' => $item->product_name,
                'variant_name' => $item->variant_name,
                'quantity' => $item->quantity,
                'item_total' => (float) $item->item_total,
            ];
        });

        return [
            'items_count' => $cart->items_count,
            'subtotal' => (float) $cart->subtotal,
            'items' => $items,
        ];
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
            'is_awaiting_counter_payment' => $order->isAwaitingCounterPayment(),
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
