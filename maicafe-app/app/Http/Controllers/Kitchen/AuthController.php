<?php

namespace App\Http\Controllers\Kitchen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Show kitchen login form
     */
    public function showLoginForm()
    {
        if (Auth::check() && in_array(Auth::user()->role, ['kitchen', 'admin'])) {
            return redirect()->route('kitchen.dashboard');
        }
        
        return view('kitchen.auth.login');
    }

    /**
     * Handle kitchen login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();
            
            // Check if user has kitchen or admin role
            if (!in_array($user->role, ['kitchen', 'admin'])) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Access denied. Kitchen staff access only.',
                ])->withInput($request->only('email'));
            }

            $request->session()->regenerate();
            
            return redirect()->intended(route('kitchen.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('email'));
    }

    /**
     * Handle kitchen logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('kitchen.login');
    }
}
