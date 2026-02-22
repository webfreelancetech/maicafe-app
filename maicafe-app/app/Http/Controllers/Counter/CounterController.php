<?php

namespace App\Http\Controllers\Counter;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Category;
use App\Models\Setting;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CounterController extends Controller
{
    /**
     * The guard to use for counter authentication.
     */
    protected $guard = 'counter';

    /**
     * Get the authenticated user.
     */
    protected function user()
    {
        return Auth::guard($this->guard)->user();
    }

    /**
     * Counter dashboard - main display for pending payments
     */
    public function dashboard(Request $request)
    {
        $date = $request->get('date', now()->toDateString());
        
        // Get order counts for stats
        $stats = [
            'awaiting_payment' => Order::forDate($date)->where('status', 'awaiting_payment')->count(),
            'paid_today' => Order::forDate($date)->where('payment_status', 'paid')->count(),
            'total_collected' => Order::forDate($date)->where('payment_status', 'paid')->sum('total'),
            'pending_amount' => Order::forDate($date)->where('status', 'awaiting_payment')->sum('total'),
        ];

        // Get orders awaiting payment (Pay at Counter)
        $awaitingPaymentOrders = Order::with('items', 'user')
            ->forDate($date)
            ->where('status', 'awaiting_payment')
            ->where('payment_method', 'pay_at_counter')
            ->orderBy('daily_token')
            ->get();

        // Get recently paid orders (for reference)
        $recentlyPaidOrders = Order::with('items', 'user')
            ->forDate($date)
            ->where('payment_status', 'paid')
            ->where('payment_method', 'pay_at_counter')
            ->orderBy('payment_confirmed_at', 'desc')
            ->limit(10)
            ->get();

        return view('counter.dashboard', compact(
            'awaitingPaymentOrders', 
            'recentlyPaidOrders',
            'stats',
            'date'
        ));
    }

    /**
     * All orders list view
     */
    public function orders(Request $request)
    {
        $date = $request->get('date', now()->toDateString());
        $status = $request->get('status');
        $paymentStatus = $request->get('payment_status');

        $query = Order::with(['items', 'user'])
            ->forDate($date)
            ->orderBy('daily_token', 'desc');

        if ($status) {
            $query->where('status', $status);
        }

        if ($paymentStatus) {
            $query->where('payment_status', $paymentStatus);
        }

        $orders = $query->paginate(20);

        return view('counter.orders', compact('orders', 'date', 'status', 'paymentStatus'));
    }

    /**
     * Single order detail view
     */
    public function orderDetail(Order $order)
    {
        $order->load('items', 'user', 'store');
        return view('counter.order-detail', compact('order'));
    }

    /**
     * Confirm payment at counter (Web)
     */
    public function confirmPayment(Request $request, Order $order)
    {
        try {
            // Validate the order can have payment confirmed
            if ($order->status !== 'awaiting_payment') {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'This order is not awaiting payment confirmation.'
                    ], 422);
                }
                return redirect()->back()->with('error', 'This order is not awaiting payment confirmation.');
            }

            if ($order->payment_method !== 'pay_at_counter') {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'This order is not a pay-at-counter order.'
                    ], 422);
                }
                return redirect()->back()->with('error', 'This order is not a pay-at-counter order.');
            }

            // Confirm payment
            $order->update([
                'payment_status' => 'paid',
                'payment_confirmed_by' => $this->user()->id,
                'payment_confirmed_at' => now(),
                'status' => 'pending', // Move to pending so kitchen can start preparing
                'payment_reference' => 'COUNTER-' . strtoupper(uniqid()),
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Payment confirmed successfully! Order sent to kitchen.',
                    'order' => [
                        'id' => $order->id,
                        'token' => $order->formatted_token,
                        'status' => $order->status,
                        'payment_status' => $order->payment_status,
                    ]
                ]);
            }

            return redirect()->back()->with('success', 'Payment confirmed! Order #' . $order->formatted_token . ' sent to kitchen.');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to confirm payment: ' . $e->getMessage(),
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to confirm payment.');
        }
    }

    /**
     * Cancel order (if payment not received and customer left)
     */
    public function cancelOrder(Request $request, Order $order)
    {
        try {
            // Only allow cancelling orders that are awaiting payment
            if ($order->status !== 'awaiting_payment') {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Only orders awaiting payment can be cancelled from counter.'
                    ], 422);
                }
                return redirect()->back()->with('error', 'Only orders awaiting payment can be cancelled from counter.');
            }

            $order->update([
                'status' => 'cancelled',
                'notes' => ($order->notes ? $order->notes . ' | ' : '') . 'Cancelled by counter staff: ' . $this->user()->name,
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Order cancelled successfully.',
                ]);
            }

            return redirect()->back()->with('success', 'Order cancelled successfully.');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to cancel order: ' . $e->getMessage(),
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to cancel order.');
        }
    }

    /**
     * Get dashboard data for AJAX refresh
     */
    public function dashboardData(Request $request)
    {
        $date = $request->get('date', now()->toDateString());

        $awaitingPayment = Order::with('items', 'user')
            ->forDate($date)
            ->where('status', 'awaiting_payment')
            ->where('payment_method', 'pay_at_counter')
            ->orderBy('daily_token')
            ->get()
            ->map(fn($o) => $this->formatOrderForJson($o));

        $recentlyPaid = Order::with('items', 'user')
            ->forDate($date)
            ->where('payment_status', 'paid')
            ->where('payment_method', 'pay_at_counter')
            ->orderBy('payment_confirmed_at', 'desc')
            ->limit(10)
            ->get()
            ->map(fn($o) => $this->formatOrderForJson($o));

        $stats = [
            'awaiting_payment' => Order::forDate($date)->where('status', 'awaiting_payment')->count(),
            'paid_today' => Order::forDate($date)->where('payment_status', 'paid')->count(),
            'total_collected' => (float) Order::forDate($date)->where('payment_status', 'paid')->sum('total'),
            'pending_amount' => (float) Order::forDate($date)->where('status', 'awaiting_payment')->sum('total'),
        ];

        return response()->json([
            'awaiting_payment' => $awaitingPayment,
            'recently_paid' => $recentlyPaid,
            'stats' => $stats,
        ]);
    }

    /**
     * Format order for JSON response
     */
    protected function formatOrderForJson($order)
    {
        return [
            'id' => $order->id,
            'token' => $order->formatted_token,
            'daily_token' => $order->daily_token,
            'order_number' => $order->order_number,
            'status' => $order->status,
            'payment_status' => $order->payment_status,
            'payment_method' => $order->payment_method,
            'order_type' => $order->order_type,
            'total' => (float) $order->total,
            'created_at' => $order->created_at->format('H:i'),
            'customer' => $order->user ? [
                'name' => $order->user->name,
                'phone' => $order->user->phone,
            ] : null,
            'items' => $order->items->map(function ($item) {
                $customizations = $item->customizations ?? [];
                return [
                    'name' => $item->product_name,
                    'quantity' => $item->quantity,
                    'price' => (float) $item->price,
                    'subtotal' => (float) $item->subtotal,
                    'variant' => $customizations['variant'] ?? null,
                    'addons' => $customizations['addons'] ?? [],
                ];
            }),
        ];
    }

    /**
     * Counter Sale / POS - New order page
     */
    public function newSale(Request $request)
    {
        $categories = Category::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $taxRate = (float) Setting::get('tax_rate', 0);
        $currency = Setting::get('currency_symbol', '£');

        return view('counter.sale', compact('categories', 'taxRate', 'currency'));
    }

    /**
     * Get products for POS (AJAX)
     */
    public function getProducts(Request $request)
    {
        $categoryId = $request->get('category_id');
        $search = $request->get('search');

        $query = Product::where('is_active', true)
            ->with(['variants' => function($q) {
                $q->where('is_active', true)->orderBy('sort_order');
            }]);

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $products = $query->orderBy('name')->get()->map(function($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'price' => (float) $product->price,
                'image' => $product->image ? asset('storage/' . $product->image) : null,
                'has_variants' => $product->has_variants,
                'variants' => $product->variants->map(function($v) {
                    return [
                        'id' => $v->id,
                        'name' => $v->name,
                        'price' => (float) $v->price,
                    ];
                }),
            ];
        });

        return response()->json([
            'success' => true,
            'products' => $products,
        ]);
    }

    /**
     * Get single product details with addons (AJAX)
     */
    public function getProductDetails(Request $request, $id)
    {
        $product = Product::with([
            'variants' => function($q) {
                $q->where('is_active', true)->orderBy('sort_order');
            },
            'addonGroups' => function($q) {
                $q->where('is_active', true)->orderBy('sort_order');
            },
            'addonGroups.addons' => function($q) {
                $q->where('is_active', true)->orderBy('sort_order');
            }
        ])->find($id);

        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found'], 404);
        }

        return response()->json([
            'success' => true,
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'price' => (float) $product->price,
                'image' => $product->image ? asset('storage/' . $product->image) : null,
                'has_variants' => $product->has_variants,
                'variants' => $product->variants->map(function($v) {
                    return [
                        'id' => $v->id,
                        'name' => $v->name,
                        'price' => (float) $v->price,
                    ];
                }),
                'addon_groups' => $product->addonGroups->map(function($group) {
                    return [
                        'id' => $group->id,
                        'name' => $group->name,
                        'min_selections' => $group->min_selections,
                        'max_selections' => $group->max_selections,
                        'addons' => $group->addons->map(function($addon) {
                            return [
                                'id' => $addon->id,
                                'name' => $addon->name,
                                'price' => (float) $addon->price,
                            ];
                        }),
                    ];
                }),
            ],
        ]);
    }

    /**
     * Create counter sale order
     */
    public function createSale(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.product_name' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.variant_id' => 'nullable|integer',
            'items.*.variant_name' => 'nullable|string',
            'items.*.addons' => 'nullable|array',
            'items.*.addons_total' => 'nullable|numeric|min:0',
            'items.*.item_total' => 'required|numeric|min:0',
            'items.*.special_instructions' => 'nullable|string',
            'payment_method' => 'required|in:cash,card',
            'customer_name' => 'nullable|string|max:100',
            'customer_phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string|max:500',
            'discount' => 'nullable|numeric|min:0',
            'coupon_code' => 'nullable|string',
        ]);

        try {
            $order = DB::transaction(function () use ($validated, $request) {
                // Calculate totals
                $subtotal = collect($validated['items'])->sum('item_total');
                $taxRate = (float) Setting::get('tax_rate', 0);
                $tax = $subtotal * ($taxRate / 100);
                $discount = (float) ($validated['discount'] ?? 0);
                $total = $subtotal + $tax - $discount;

                // Create order - directly as pending (paid at counter)
                $order = Order::create([
                    'user_id' => null, // Walk-in customer
                    'store_id' => null,
                    'order_type' => 'pickup',
                    'payment_method' => 'counter_' . $validated['payment_method'], // counter_cash or counter_card
                    'payment_status' => 'paid', // Already paid
                    'payment_confirmed_by' => $this->user()->id,
                    'payment_confirmed_at' => now(),
                    'payment_reference' => 'POS-' . strtoupper(uniqid()),
                    'subtotal' => $subtotal,
                    'tax' => $tax,
                    'delivery_charge' => 0,
                    'discount' => $discount,
                    'coupon_code' => $validated['coupon_code'] ?? null,
                    'total' => $total,
                    'status' => 'pending', // Goes directly to kitchen
                    'notes' => $this->buildOrderNotes($validated),
                ]);

                // Create order items
                foreach ($validated['items'] as $item) {
                    $customizations = [];
                    
                    if (!empty($item['variant_id'])) {
                        $customizations['variant'] = [
                            'id' => $item['variant_id'],
                            'name' => $item['variant_name'] ?? '',
                            'price' => (float) $item['unit_price'],
                        ];
                    }

                    if (!empty($item['addons'])) {
                        $customizations['addons'] = $item['addons'];
                    }

                    if (!empty($item['special_instructions'])) {
                        $customizations['special_instructions'] = $item['special_instructions'];
                    }

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item['product_id'],
                        'product_name' => $item['product_name'],
                        'price' => $item['unit_price'] + ($item['addons_total'] ?? 0),
                        'quantity' => $item['quantity'],
                        'subtotal' => $item['item_total'],
                        'customizations' => !empty($customizations) ? $customizations : null,
                    ]);
                }

                // Update coupon usage if used
                if (!empty($validated['coupon_code'])) {
                    Coupon::where('code', $validated['coupon_code'])->increment('used_count');
                }

                return $order->fresh(['items']);
            });

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully!',
                'order' => [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'token' => $order->formatted_token,
                    'total' => (float) $order->total,
                    'status' => $order->status,
                    'payment_status' => $order->payment_status,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create order: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Build order notes from customer info
     */
    protected function buildOrderNotes($data)
    {
        $notes = [];
        
        if (!empty($data['customer_name'])) {
            $notes[] = 'Customer: ' . $data['customer_name'];
        }
        
        if (!empty($data['customer_phone'])) {
            $notes[] = 'Phone: ' . $data['customer_phone'];
        }
        
        if (!empty($data['notes'])) {
            $notes[] = $data['notes'];
        }

        $notes[] = 'Counter Sale by ' . $this->user()->name;

        return implode(' | ', $notes);
    }
}
