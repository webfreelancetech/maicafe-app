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
    <i class="fas fa-check-circle"></i>
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="alert alert-error">
    <i class="fas fa-exclamation-circle"></i>
    {{ session('error') }}
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
                    @endphp
                    <option value="GB" {{ $currentCountry === 'GB' ? 'selected' : '' }}>United Kingdom</option>
                    <option value="US" {{ $currentCountry === 'US' ? 'selected' : '' }}>United States</option>
                    <option value="EU" {{ $currentCountry === 'EU' ? 'selected' : '' }}>Europe</option>
                    <option value="IN" {{ $currentCountry === 'IN' ? 'selected' : '' }}>India</option>
                </select>
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
