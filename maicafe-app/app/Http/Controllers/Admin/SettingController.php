<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        return view('admin.settings.index');
    }

    public function update(Request $request)
    {
        $countryCurrencyMap = [
            'GB' => ['symbol' => '£', 'code' => 'GBP'],
            'US' => ['symbol' => '$', 'code' => 'USD'],
            'EU' => ['symbol' => '€', 'code' => 'EUR'],
            'IN' => ['symbol' => '₹', 'code' => 'INR'],
        ];

        if ($request->has('country')) {
            $country = $request->country;
            if (isset($countryCurrencyMap[$country])) {
                Setting::set('country', $country);
                Setting::set('currency_symbol', $countryCurrencyMap[$country]['symbol']);
                Setting::set('currency_code', $countryCurrencyMap[$country]['code']);
            }
        }

        if ($request->has('tax_rate')) {
            Setting::set('tax_rate', $request->tax_rate);
        }

        if ($request->has('admin_email')) {
            Setting::set('admin_email', $request->admin_email);
        }

        if ($request->has('current_password') && $request->has('new_password')) {
            if (\Hash::check($request->current_password, auth()->user()->password)) {
                auth()->user()->update(['password' => \Hash::make($request->new_password)]);
                return redirect()->back()->with('success', 'Password updated successfully!');
            } else {
                return redirect()->back()->with('error', 'Current password is incorrect!');
            }
        }

        return redirect()->back()->with('success', 'Settings updated successfully!');
    }
}


