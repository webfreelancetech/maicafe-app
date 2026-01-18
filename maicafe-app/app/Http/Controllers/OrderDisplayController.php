<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderDisplayController extends Controller
{
    /**
     * Public order status display for pickup counter
     */
    public function index(Request $request)
    {
        $date = $request->get('date', now()->toDateString());
        
        // Get orders that customers care about (preparing and ready)
        $preparingOrders = Order::forDate($date)
            ->whereIn('status', ['confirmed', 'preparing'])
            ->orderBy('daily_token')
            ->get();

        $readyOrders = Order::forDate($date)
            ->where('status', 'ready')
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('order-display.index', compact('preparingOrders', 'readyOrders', 'date'));
    }

    /**
     * API endpoint for AJAX updates
     */
    public function data(Request $request)
    {
        $date = $request->get('date', now()->toDateString());

        $preparing = Order::forDate($date)
            ->whereIn('status', ['confirmed', 'preparing'])
            ->orderBy('daily_token')
            ->get()
            ->map(fn($o) => [
                'token' => $o->formatted_token,
                'status' => $o->status,
            ]);

        $ready = Order::forDate($date)
            ->where('status', 'ready')
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(fn($o) => [
                'token' => $o->formatted_token,
                'status' => $o->status,
            ]);

        return response()->json([
            'preparing' => $preparing,
            'ready' => $ready,
        ]);
    }
}
