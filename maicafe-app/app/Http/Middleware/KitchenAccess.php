<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class KitchenAccess
{
    /**
     * Handle an incoming request.
     * Allow access only to users with 'kitchen' or 'admin' role.
     */
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated. Please login.',
                ], 401);
            }
            return redirect()->route('kitchen.login');
        }

        $allowedRoles = ['kitchen', 'admin'];
        
        if (!in_array(auth()->user()->role, $allowedRoles)) {
            auth()->logout();
            if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Kitchen staff access only.',
                ], 403);
            }
            return redirect()->route('kitchen.login')
                ->with('error', 'Access denied. Kitchen staff access only.');
        }

        return $next($request);
    }
}
