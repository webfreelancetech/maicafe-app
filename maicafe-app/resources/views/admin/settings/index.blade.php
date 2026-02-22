@extends('layouts.admin')

@section('title', 'Settings - Mai Cafe Admin')

@push('styles')
<style>
    .settings-container {
        display: grid;
        grid-template-columns: 1fr;
        gap: 24px;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-group label {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 8px;
    }
    
    .form-group input[type="text"],
    .form-group input[type="email"],
    .form-group input[type="number"],
    .form-group input[type="password"],
    .form-group select {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        font-size: 14px;
        color: #1e293b;
        background: #fff;
        transition: all 0.2s;
        font-family: inherit;
    }
    
    .form-group input[type="number"] {
        -moz-appearance: textfield;
    }
    
    .form-group input[type="number"]::-webkit-outer-spin-button,
    .form-group input[type="number"]::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    
    .form-group input:focus,
    .form-group select:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    .form-group input::placeholder {
        color: #94a3b8;
    }
    
    .form-group select {
        cursor: pointer;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2364748b' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        padding-right: 40px;
    }
    
    .btn-primary {
        background: #3b82f6;
        color: #fff;
        padding: 12px 24px;
        border-radius: 8px;
        border: none;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }
    
    .btn-primary:hover {
        background: #2563eb;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }
    
    .btn-primary:active {
        transform: translateY(0);
    }
    
    .alert {
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 24px;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .alert-success {
        background: #d1fae5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }
    
    .alert-error {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }
    
    .card h3 {
        font-size: 18px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 24px;
        padding-bottom: 16px;
        border-bottom: 1px solid #e2e8f0;
    }
    
    @media (min-width: 768px) {
        .settings-container {
            grid-template-columns: 1fr;
            max-width: 800px;
        }
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <h1>Settings</h1>
</div>

@if(session('success'))
<div class="alert alert-success">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="alert alert-error">
    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
</div>
@endif

@if(session('smtp_success'))
<div class="alert alert-success">
    <i class="fas fa-check-circle"></i> {{ session('smtp_success') }}
</div>
@endif

@if(session('smtp_error'))
<div class="alert alert-error">
    <i class="fas fa-exclamation-circle"></i> {{ session('smtp_error') }}
</div>
@endif

<div class="settings-container">
    <!-- General Settings Card -->
    <div class="card">
        <h3>General Settings</h3>
        <form action="{{ route('admin.settings.update') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label for="country">Country</label>
                <select name="country" id="country" required>
                    @php
                        $currentCountry = \App\Models\Setting::get('country', 'GB');
                        $currentCurrency = \App\Models\Setting::get('currency_symbol', '£');
                        $currentCurrencyCode = \App\Models\Setting::get('currency_code', 'GBP');
                    @endphp
                    <option value="GB" {{ $currentCountry === 'GB' ? 'selected' : '' }}>United Kingdom</option>
                    <option value="US" {{ $currentCountry === 'US' ? 'selected' : '' }}>United States</option>
                    <option value="EU" {{ $currentCountry === 'EU' ? 'selected' : '' }}>Europe</option>
                    <option value="IN" {{ $currentCountry === 'IN' ? 'selected' : '' }}>India</option>
                </select>
                <small style="color: #6b7280; display: block; margin-top: 8px;">
                    <i class="fas fa-info-circle"></i> Current Currency: <strong>{{ $currentCurrency }} ({{ $currentCurrencyCode }})</strong>
                </small>
            </div>

            <div class="form-group">
                <label for="tax_rate">Tax Rate (%)</label>
                <input type="number" step="0.01" min="0" max="100" name="tax_rate" id="tax_rate" value="{{ \App\Models\Setting::get('tax_rate', 5) }}" required>
            </div>

            <div class="form-group">
                <label for="admin_email">Admin Email</label>
                <input type="email" name="admin_email" id="admin_email" value="{{ \App\Models\Setting::get('admin_email', '') }}" placeholder="Enter admin email">
            </div>

            <div style="margin-top: 24px;">
                <button class="btn btn-primary" type="submit">
                    <i class="fas fa-save"></i> Save Settings
                </button>
            </div>
        </form>
    </div>

    <!-- Email (SMTP) Settings Card -->
    <div class="card">
        <h3><i class="fas fa-envelope" style="color:#3b82f6;margin-right:8px;"></i>Email (SMTP) Configuration</h3>
        <p style="color:#6b7280;font-size:13px;margin:-12px 0 24px;">
            Configure your webmail SMTP credentials for sending OTP emails on registration and password reset.
            The password is stored encrypted in the database.
        </p>

        @php
            $smtpHost       = \App\Models\Setting::get('smtp_host', '');
            $smtpPort       = \App\Models\Setting::get('smtp_port', '587');
            $smtpUsername   = \App\Models\Setting::get('smtp_username', '');
            $smtpEncryption = \App\Models\Setting::get('smtp_encryption', 'tls');
            $smtpFromAddr   = \App\Models\Setting::get('smtp_from_address', '');
            $smtpFromName   = \App\Models\Setting::get('smtp_from_name', 'Mai Cafe');
            $smtpConfigured = !empty($smtpHost) && !empty($smtpUsername);
        @endphp

        @if($smtpConfigured)
        <div style="background:#d1fae5;border:1px solid #a7f3d0;border-radius:8px;padding:10px 16px;font-size:13px;color:#065f46;margin-bottom:20px;display:flex;align-items:center;gap:8px;">
            <i class="fas fa-check-circle"></i>
            SMTP configured — using <strong>{{ $smtpUsername }}</strong> via <strong>{{ $smtpHost }}:{{ $smtpPort }}</strong>
        </div>
        @else
        <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:8px;padding:10px 16px;font-size:13px;color:#92400e;margin-bottom:20px;display:flex;align-items:center;gap:8px;">
            <i class="fas fa-exclamation-triangle"></i>
            SMTP not configured — OTP emails will not be sent until you save valid settings below.
        </div>
        @endif

        <form action="{{ route('admin.settings.email.update') }}" method="POST">
            @csrf

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div class="form-group" style="margin-bottom:0;">
                    <label for="smtp_host">SMTP Host *</label>
                    <input type="text" name="smtp_host" id="smtp_host"
                           value="{{ old('smtp_host', $smtpHost) }}"
                           placeholder="e.g. mail.yourdomain.com" required>
                    <small style="color:#6b7280;font-size:12px;">Your webmail server hostname</small>
                </div>

                <div class="form-group" style="margin-bottom:0;">
                    <label for="smtp_port">SMTP Port *</label>
                    <select name="smtp_port" id="smtp_port" required>
                        <option value="587" {{ $smtpPort == '587' ? 'selected' : '' }}>587 — TLS (Recommended)</option>
                        <option value="465" {{ $smtpPort == '465' ? 'selected' : '' }}>465 — SSL</option>
                        <option value="25"  {{ $smtpPort == '25'  ? 'selected' : '' }}>25 — No Encryption</option>
                        <option value="2525" {{ $smtpPort == '2525' ? 'selected' : '' }}>2525 — Alternative TLS</option>
                    </select>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:16px;">
                <div class="form-group" style="margin-bottom:0;">
                    <label for="smtp_username">SMTP Username / Email *</label>
                    <input type="email" name="smtp_username" id="smtp_username"
                           value="{{ old('smtp_username', $smtpUsername) }}"
                           placeholder="noreply@yourdomain.com" required>
                    <small style="color:#6b7280;font-size:12px;">Usually the full email address</small>
                </div>

                <div class="form-group" style="margin-bottom:0;">
                    <label for="smtp_password">SMTP Password</label>
                    <input type="password" name="smtp_password" id="smtp_password"
                           placeholder="{{ $smtpConfigured ? 'Leave blank to keep current password' : 'Enter SMTP password' }}"
                           autocomplete="new-password">
                    <small style="color:#6b7280;font-size:12px;">
                        {{ $smtpConfigured ? 'Leave blank to keep existing password' : 'Your webmail account password' }}
                    </small>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:16px;">
                <div class="form-group" style="margin-bottom:0;">
                    <label for="smtp_encryption">Encryption</label>
                    <select name="smtp_encryption" id="smtp_encryption">
                        <option value="tls" {{ $smtpEncryption === 'tls' ? 'selected' : '' }}>TLS</option>
                        <option value="ssl" {{ $smtpEncryption === 'ssl' ? 'selected' : '' }}>SSL</option>
                        <option value=""    {{ $smtpEncryption === ''    ? 'selected' : '' }}>None</option>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom:0;">
                    <label for="smtp_from_name">From Name</label>
                    <input type="text" name="smtp_from_name" id="smtp_from_name"
                           value="{{ old('smtp_from_name', $smtpFromName) }}"
                           placeholder="Mai Cafe">
                    <small style="color:#6b7280;font-size:12px;">Name shown in the "From" field of emails</small>
                </div>
            </div>

            <div class="form-group" style="margin-top:16px;">
                <label for="smtp_from_address">From Email Address</label>
                <input type="email" name="smtp_from_address" id="smtp_from_address"
                       value="{{ old('smtp_from_address', $smtpFromAddr) }}"
                       placeholder="noreply@yourdomain.com">
                <small style="color:#6b7280;font-size:12px;">Defaults to SMTP username if left blank</small>
            </div>

            <div style="margin-top:24px;">
                <button class="btn btn-primary" type="submit">
                    <i class="fas fa-save"></i> Save Email Settings
                </button>
            </div>
        </form>

        {{-- Test Email --}}
        <div style="margin-top:32px;padding-top:24px;border-top:1px solid #e5e7eb;">
            <h4 style="font-size:15px;font-weight:700;color:#374151;margin-bottom:8px;">
                <i class="fas fa-paper-plane" style="color:#10b981;margin-right:6px;"></i>Send Test Email
            </h4>
            <p style="color:#6b7280;font-size:13px;margin-bottom:16px;">
                Verify your SMTP settings are working by sending a test OTP email.
                Save your settings first, then test.
            </p>
            <form action="{{ route('admin.settings.email.test') }}" method="POST" style="display:flex;gap:12px;align-items:flex-end;">
                @csrf
                <div class="form-group" style="margin-bottom:0;flex:1;">
                    <label for="test_email">Send Test To</label>
                    <input type="email" name="test_email" id="test_email"
                           value="{{ old('test_email', \App\Models\Setting::get('admin_email', '')) }}"
                           placeholder="you@example.com" required>
                </div>
                <button class="btn btn-primary" type="submit"
                        style="background:#10b981;white-space:nowrap;"
                        {{ !$smtpConfigured ? 'disabled title=Save SMTP settings first' : '' }}>
                    <i class="fas fa-paper-plane"></i> Send Test
                </button>
            </form>
        </div>
    </div>

    <!-- Change Password Card -->
    <div class="card">
        <h3>Change Password</h3>
        <form action="{{ route('admin.settings.update') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label for="current_password">Current Password</label>
                <input type="password" name="current_password" id="current_password" required placeholder="Enter current password">
            </div>

            <div class="form-group">
                <label for="new_password">New Password</label>
                <input type="password" name="new_password" id="new_password" required placeholder="Enter new password" minlength="8">
            </div>

            <div style="margin-top: 24px;">
                <button class="btn btn-primary" type="submit">
                    <i class="fas fa-lock"></i> Update Password
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
