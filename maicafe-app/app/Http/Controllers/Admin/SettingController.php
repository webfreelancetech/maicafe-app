<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use App\Models\Setting;
use App\Services\MailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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

    /**
     * Save SMTP / Email settings.
     */
    public function updateEmail(Request $request)
    {
        $request->validate([
            'smtp_host'         => 'required|string|max:255',
            'smtp_port'         => 'required|integer|in:25,465,587,2525',
            'smtp_username'     => 'required|email|max:255',
            'smtp_encryption'   => 'nullable|in:tls,ssl,',
            'smtp_from_address' => 'nullable|email|max:255',
            'smtp_from_name'    => 'nullable|string|max:100',
        ]);

        Setting::set('smtp_host',         $request->smtp_host);
        Setting::set('smtp_port',         $request->smtp_port);
        Setting::set('smtp_username',     $request->smtp_username);
        Setting::set('smtp_encryption',   $request->smtp_encryption ?? 'tls');
        Setting::set('smtp_from_address', $request->smtp_from_address ?: $request->smtp_username);
        Setting::set('smtp_from_name',    $request->smtp_from_name ?: 'Mai Cafe');

        // Only update the password if a new one was provided
        if ($request->filled('smtp_password')) {
            Setting::set('smtp_password', MailService::encryptPassword($request->smtp_password));
        }

        return redirect()->back()->with('smtp_success', 'Email settings saved successfully!');
    }

    /**
     * Send a test email using the current SMTP settings.
     */
    public function testEmail(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email',
        ]);

        if (!MailService::isConfigured()) {
            return redirect()->back()->with('smtp_error', 'SMTP is not configured yet. Please save settings first.');
        }

        try {
            MailService::configure();
            Mail::to($request->test_email)->send(new OtpMail('123456', 'registration', 'Test User'));
            return redirect()->back()->with('smtp_success', 'Test email sent to ' . $request->test_email . ' successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('smtp_error', 'Failed to send test email: ' . $e->getMessage());
        }
    }
}
