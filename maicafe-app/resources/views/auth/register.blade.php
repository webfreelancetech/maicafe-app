<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Mai Cafe</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            min-height: 100vh; 
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            padding: 20px;
        }
        .register-container {
            width: 100%;
            max-width: 450px;
        }
        .register-box { 
            background: #fff; 
            padding: 40px; 
            border-radius: 16px; 
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }
        .logo {
            text-align: center;
            margin-bottom: 32px;
        }
        .logo h1 {
            color: #10b981;
            font-size: 32px;
            margin-bottom: 8px;
        }
        .logo p {
            color: #6b7280;
            font-size: 14px;
        }
        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .alert-success {
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }
        .alert-error {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
        .form-group { 
            margin-bottom: 20px; 
        }
        label { 
            display: block; 
            margin-bottom: 8px; 
            font-weight: 500; 
            color: #374151;
        }
        input { 
            width: 100%; 
            padding: 12px 16px; 
            border: 2px solid #e5e7eb; 
            border-radius: 8px; 
            font-size: 14px;
            transition: border-color 0.3s;
        }
        input:focus {
            outline: none;
            border-color: #10b981;
        }
        .error-message {
            color: #ef4444;
            font-size: 13px;
            margin-top: 4px;
        }
        button { 
            width: 100%; 
            padding: 14px; 
            background: #10b981; 
            color: #fff; 
            border: none; 
            border-radius: 8px; 
            font-weight: 600; 
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }
        button:hover {
            background: #059669;
        }
        .login-link {
            text-align: center;
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 14px;
        }
        .login-link a {
            color: #10b981;
            text-decoration: none;
            font-weight: 600;
        }
        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-box">
            <div class="logo">
                <h1><i class="fas fa-coffee"></i> Mai Cafe</h1>
                <p>Create your account to get started</p>
            </div>

            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> 
                    @foreach($errors->all() as $error)
                        {{ $error }}
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        value="{{ old('name') }}" 
                        required 
                        autofocus
                        placeholder="Enter your full name"
                    >
                    @error('name')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="{{ old('email') }}" 
                        required
                        placeholder="Enter your email"
                    >
                    @error('email')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number (Optional)</label>
                    <input 
                        type="tel" 
                        id="phone" 
                        name="phone" 
                        value="{{ old('phone') }}"
                        placeholder="Enter your phone number"
                    >
                    @error('phone')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required
                        placeholder="Enter your password (min. 8 characters)"
                    >
                    @error('password')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="password_confirmation">Confirm Password</label>
                    <input 
                        type="password" 
                        id="password_confirmation" 
                        name="password_confirmation" 
                        required
                        placeholder="Confirm your password"
                    >
                </div>
                <button type="submit">
                    <i class="fas fa-user-plus"></i> Create Account
                </button>
            </form>

            <div class="login-link">
                Already have an account? <a href="{{ route('login') }}">Login here</a>
            </div>
        </div>
    </div>
</body>
</html>

