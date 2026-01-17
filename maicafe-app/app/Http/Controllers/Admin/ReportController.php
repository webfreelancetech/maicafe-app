<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        // Sales Statistics
        $totalSales = Order::where('status', 'completed')->sum('total');
        $totalOrders = Order::where('status', 'completed')->count();
        $averageOrderValue = $totalOrders > 0 ? $totalSales / $totalOrders : 0;

        // Revenue by Month
        $revenueByMonth = Order::where('status', 'completed')
            ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'), DB::raw('SUM(total) as revenue'))
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        // Top Products
        $topProducts = Product::withCount('orderItems')
            ->orderBy('order_items_count', 'desc')
            ->limit(10)
            ->get();

        return view('admin.reports.index', compact(
            'totalSales',
            'totalOrders',
            'averageOrderValue',
            'revenueByMonth',
            'topProducts'
        ));
    }
}


