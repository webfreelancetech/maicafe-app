<?php

namespace App\Http\Controllers\Counter;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * The guard to use for counter authentication.
     */
    protected $guard = 'counter';

    /**
     * Show the login form for counter staff
     */
    public function showLoginForm()
    {
        if (Auth::guard($this->guard)->check()) {
            $user = Auth::guard($this->guard)->user();
            if (in_array($user->role, ['counter', 'admin'])) {
                return redirect()->route('counter.dashboard');
            }
        }
        
        return view('counter.auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard($this->guard)->attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::guard($this->guard)->user();
            
            // Check if user has counter or admin role
            if (!in_array($user->role, ['counter', 'admin'])) {
                Auth::guard($this->guard)->logout();
                return back()->withErrors([
                    'email' => 'Access denied. Counter staff or admin credentials required.',
                ])->withInput($request->only('email'));
            }
            
            $request->session()->regenerate();
            return redirect()->intended(route('counter.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('email'));
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        Auth::guard($this->guard)->logout();
        
        // Only invalidate counter session keys, not the entire session
        $request->session()->forget('login_counter_' . sha1('App\Models\User'));
        
        return redirect()->route('counter.login');
    }
}
