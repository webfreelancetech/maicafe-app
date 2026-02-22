<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAccess
{
    /**
     * The guard to use for admin authentication.
     */
    protected $guard = 'admin';

    /**
     * Handle an incoming request.
     * Only allow users with 'admin' role to access.
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard($this->guard)->check()) {
            if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated. Please login.',
                ], 401);
            }
            return redirect()->route('login');
        }

        $user = Auth::guard($this->guard)->user();
        
        if ($user->role !== 'admin') {
            Auth::guard($this->guard)->logout();
            if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Admin access only.',
                ], 403);
            }
            return redirect()->route('login')
                ->with('error', 'Access denied. Admin access only.');
        }

        return $next($request);
    }
}
