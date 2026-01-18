<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson() && ! $this->isApiRequest($request)) {
            return route('login');
        }
    }

    /**
     * Handle unauthenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $guards
     * @return void
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    protected function unauthenticated($request, array $guards)
    {
        if ($this->isApiRequest($request)) {
            abort(response()->json([
                'success' => false,
                'message' => 'Unauthenticated. Please login to access this resource.',
            ], 401));
        }

        parent::unauthenticated($request, $guards);
    }

    /**
     * Check if the request is an API request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function isApiRequest(Request $request): bool
    {
        return $request->is('api/*') || $request->expectsJson();
    }
}
