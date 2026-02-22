<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CounterAccess
{
    /**
     * The guard to use for counter authentication.
     */
    protected $guard = 'counter';

    /**
     * Handle an incoming request.
     * Only allow users with 'counter' or 'admin' role to access.
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
            return redirect()->route('counter.login');
        }

        $user = Auth::guard($this->guard)->user();
        $allowedRoles = ['counter', 'admin'];
        
        if (!in_array($user->role, $allowedRoles)) {
            Auth::guard($this->guard)->logout();
            if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Counter staff only.',
                ], 403);
            }
            return redirect()->route('counter.login')
                ->with('error', 'Access denied. Counter staff only.');
        }

        return $next($request);
    }
}
