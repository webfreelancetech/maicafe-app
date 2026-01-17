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
        $orders = Order::with('user', 'store')->latest()->paginate(15);
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
            'status' => 'required|in:pending,processing,completed,cancelled',
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
}


