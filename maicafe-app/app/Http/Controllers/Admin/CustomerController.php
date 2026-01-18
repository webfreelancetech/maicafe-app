<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = User::where('role', 'customer')
            ->withCount('orders')
            ->withSum(['orders as total_spent' => function ($query) {
                $query->where('status', 'completed');
            }], 'total')
            ->latest()
            ->paginate(15);
            
        return view('admin.customers.index', compact('customers'));
    }

    public function show(User $customer)
    {
        // Load customer with orders and order items
        $customer->load(['orders' => function ($query) {
            $query->with('items')->latest();
        }]);

        // Calculate statistics
        $stats = [
            'total_orders' => $customer->orders->count(),
            'completed_orders' => $customer->orders->where('status', 'completed')->count(),
            'cancelled_orders' => $customer->orders->where('status', 'cancelled')->count(),
            'pending_orders' => $customer->orders->whereIn('status', ['pending', 'confirmed', 'preparing', 'ready', 'out_for_delivery'])->count(),
            'total_spent' => $customer->orders->where('status', 'completed')->sum('total'),
            'average_order_value' => $customer->orders->where('status', 'completed')->count() > 0 
                ? $customer->orders->where('status', 'completed')->sum('total') / $customer->orders->where('status', 'completed')->count()
                : 0,
        ];

        // Get recent orders (paginated)
        $orders = Order::where('user_id', $customer->id)
            ->with('items')
            ->latest()
            ->paginate(10);

        // Get favorite products
        $favoriteProducts = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.user_id', $customer->id)
            ->select('order_items.product_name', DB::raw('SUM(order_items.quantity) as total_quantity'), DB::raw('COUNT(*) as order_count'))
            ->groupBy('order_items.product_name')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();

        return view('admin.customers.show', compact('customer', 'stats', 'orders', 'favoriteProducts'));
    }
}


