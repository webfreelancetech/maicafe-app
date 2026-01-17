<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Total Revenue
        $totalRevenue = Order::where('status', 'completed')->sum('total');
        
        // Total Orders
        $totalOrders = Order::count();
        
        // Total Products
        $totalProducts = Product::count();
        
        // Total Customers
        $totalCustomers = User::where('role', 'customer')->count();

        // Recent Orders (for the small table)
        $recentOrders = Order::with('items.product')->latest()->limit(5)->get();
        
        // All Orders (for the full table - paginated)
        $allOrders = Order::with('items.product')->latest()->paginate(10);

        // Order Status Counts
        $orderStatusCounts = Order::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Revenue by Month (last 6 months)
        $revenueByMonth = Order::where('status', 'completed')
            ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'), DB::raw('SUM(total) as revenue'))
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        // Top Products
        $topProducts = Product::withCount('orderItems')
            ->orderBy('order_items_count', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalRevenue',
            'totalOrders',
            'totalProducts',
            'totalCustomers',
            'recentOrders',
            'allOrders',
            'orderStatusCounts',
            'revenueByMonth',
            'topProducts'
        ));
    }
}


