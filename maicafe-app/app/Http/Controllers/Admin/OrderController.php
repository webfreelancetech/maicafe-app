<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['user', 'store', 'items'])->latest()->paginate(15);
        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load('items.product', 'user', 'store');
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,preparing,ready,out_for_delivery,completed,cancelled',
        ]);

        $oldStatus = $order->status;
        $order->update($validated);

        // Deduct stock when order is completed
        if ($validated['status'] === 'completed' && $oldStatus !== 'completed') {
            foreach ($order->items as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->decrement('stock_quantity', $item->quantity);
                }
            }
        }

        return redirect()->back()->with('success', 'Order status updated successfully!');
    }

    /**
     * Kitchen display for today's orders
     */
    public function kitchen(Request $request)
    {
        $date = $request->get('date', now()->toDateString());
        
        // Get orders grouped by status
        $pendingOrders = Order::with('items')
            ->forDate($date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->orderBy('daily_token')
            ->get();

        $preparingOrders = Order::with('items')
            ->forDate($date)
            ->where('status', 'preparing')
            ->orderBy('daily_token')
            ->get();

        $readyOrders = Order::with('items')
            ->forDate($date)
            ->whereIn('status', ['ready', 'out_for_delivery'])
            ->orderBy('daily_token')
            ->get();

        $completedOrders = Order::with('items')
            ->forDate($date)
            ->where('status', 'completed')
            ->orderBy('daily_token', 'desc')
            ->limit(10)
            ->get();

        return view('admin.orders.kitchen', compact(
            'pendingOrders', 
            'preparingOrders', 
            'readyOrders', 
            'completedOrders',
            'date'
        ));
    }

    /**
     * API endpoint for kitchen display updates
     */
    public function kitchenData(Request $request)
    {
        $date = $request->get('date', now()->toDateString());

        $orders = Order::with('items')
            ->forDate($date)
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->orderBy('daily_token')
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'token' => $order->formatted_token,
                    'status' => $order->status,
                    'order_type' => $order->order_type,
                    'items' => $order->items->map(function ($item) {
                        return [
                            'name' => $item->product_name,
                            'quantity' => $item->quantity,
                            'customizations' => $item->customizations,
                        ];
                    }),
                    'created_at' => $order->created_at->format('H:i'),
                ];
            });

        return response()->json(['orders' => $orders]);
    }
}


