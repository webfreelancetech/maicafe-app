<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::guard('web')->check()) {
            return redirect()->route('home');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->filled('remember');

        // Try to authenticate
        if (Auth::guard('web')->attempt($credentials, $remember)) {
            $user = Auth::guard('web')->user();
            
            // Check if user is admin, also login to admin guard and redirect to admin dashboard
            if ($user->role === 'admin') {
                Auth::guard('admin')->login($user, $remember);
                $request->session()->regenerate();
                return redirect()->route('admin.dashboard');
            }

            $request->session()->regenerate();
            return redirect()->intended(route('home'))->with('success', 'Welcome back, ' . $user->name . '!');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('email'));
    }

    public function showRegisterForm()
    {
        if (Auth::guard('web')->check()) {
            return redirect()->route('home');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'customer', // Set default role as customer
        ]);

        Auth::guard('web')->login($user);

        return redirect()->route('home')->with('success', 'Account created successfully! Welcome to Mai Cafe!');
    }

    public function logout(Request $request)
    {
        // Logout from web guard only
        Auth::guard('web')->logout();
        
        // Forget only the web session key
        $request->session()->forget('login_web_' . sha1('App\Models\User'));

        return redirect()->route('home')->with('success', 'You have been logged out successfully.');
    }
}
