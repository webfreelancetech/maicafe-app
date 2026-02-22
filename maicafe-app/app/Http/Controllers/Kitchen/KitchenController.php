<?php

namespace App\Http\Controllers\Kitchen;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class KitchenController extends Controller
{
    /**
     * Kitchen dashboard - main order display
     */
    public function dashboard(Request $request)
    {
        $date = $request->get('date', now()->toDateString());
        
        // Get order counts for stats (exclude awaiting_payment - those haven't paid yet)
        $stats = [
            'pending' => Order::forDate($date)->whereIn('status', ['pending', 'confirmed'])->count(),
            'preparing' => Order::forDate($date)->where('status', 'preparing')->count(),
            'ready' => Order::forDate($date)->whereIn('status', ['ready', 'out_for_delivery'])->count(),
            'completed' => Order::forDate($date)->where('status', 'completed')->count(),
            'total' => Order::forDate($date)->whereNotIn('status', ['awaiting_payment'])->count(),
            'awaiting_payment' => Order::forDate($date)->where('status', 'awaiting_payment')->count(),
        ];

        // Get orders grouped by status
        $pendingOrders = Order::with('items.product.category')
            ->forDate($date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->orderBy('daily_token')
            ->get();

        $preparingOrders = Order::with('items.product.category')
            ->forDate($date)
            ->where('status', 'preparing')
            ->orderBy('daily_token')
            ->get();

        $readyOrders = Order::with('items.product.category')
            ->forDate($date)
            ->whereIn('status', ['ready', 'out_for_delivery'])
            ->orderBy('daily_token')
            ->get();

        return view('kitchen.dashboard', compact(
            'pendingOrders', 
            'preparingOrders', 
            'readyOrders',
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

        $query = Order::with(['items.product.category', 'user'])
            ->forDate($date)
            ->orderBy('daily_token', 'desc');

        if ($status) {
            $query->where('status', $status);
        }

        $orders = $query->paginate(20);

        return view('kitchen.orders', compact('orders', 'date', 'status'));
    }

    /**
     * Single order detail view
     */
    public function orderDetail(Order $order)
    {
        $order->load('items.product.category', 'user');
        return view('kitchen.order-detail', compact('order'));
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, Order $order)
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:pending,confirmed,preparing,ready,out_for_delivery,completed,cancelled',
            ]);

            $oldStatus = $order->status;
            $order->update(['status' => $validated['status']]);

            // Deduct stock when order is completed
            if ($validated['status'] === 'completed' && $oldStatus !== 'completed') {
                foreach ($order->items as $item) {
                    $product = Product::find($item->product_id);
                    if ($product) {
                        $product->decrement('stock_quantity', $item->quantity);
                    }
                }
            }

            if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Order status updated successfully',
                    'order' => [
                        'id' => $order->id,
                        'status' => $order->status,
                        'formatted_token' => $order->formatted_token,
                    ]
                ]);
            }

            return redirect()->back()->with('success', 'Order status updated successfully!');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update order: ' . $e->getMessage(),
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to update order status.');
        }
    }

    /**
     * Get orders data for AJAX refresh
     */
    public function ordersData(Request $request)
    {
        $date = $request->get('date', now()->toDateString());

        $pending = Order::with('items.product.category')
            ->forDate($date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->orderBy('daily_token')
            ->get()
            ->map(fn($o) => $this->formatOrderForJson($o));

        $preparing = Order::with('items.product.category')
            ->forDate($date)
            ->where('status', 'preparing')
            ->orderBy('daily_token')
            ->get()
            ->map(fn($o) => $this->formatOrderForJson($o));

        $ready = Order::with('items.product.category')
            ->forDate($date)
            ->whereIn('status', ['ready', 'out_for_delivery'])
            ->orderBy('daily_token')
            ->get()
            ->map(fn($o) => $this->formatOrderForJson($o));

        $stats = [
            'pending' => $pending->count(),
            'preparing' => $preparing->count(),
            'ready' => $ready->count(),
            'completed' => Order::forDate($date)->where('status', 'completed')->count(),
            'total' => Order::forDate($date)->whereNotIn('status', ['awaiting_payment'])->count(),
            'awaiting_payment' => Order::forDate($date)->where('status', 'awaiting_payment')->count(),
        ];

        return response()->json([
            'pending' => $pending,
            'preparing' => $preparing,
            'ready' => $ready,
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
            'status' => $order->status,
            'order_type' => $order->order_type,
            'created_at' => $order->created_at->format('H:i'),
            'items' => $order->items->map(function ($item) {
                $customizations = $item->customizations ?? [];
                return [
                    'name' => $item->product_name,
                    'quantity' => $item->quantity,
                    'variant' => $customizations['variant'] ?? null,
                    'addons' => $customizations['addons'] ?? [],
                    'product_type' => $item->product && $item->product->category ? $item->product->category->type : null,
                ];
            }),
        ];
    }
}
