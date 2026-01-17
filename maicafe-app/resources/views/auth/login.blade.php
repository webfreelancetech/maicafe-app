<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Mai Cafe</title>
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
        .login-container {
            width: 100%;
            max-width: 450px;
        }
        .login-box { 
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
        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            font-size: 14px;
        }
        .remember-forgot label {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0;
            font-weight: 400;
            cursor: pointer;
        }
        .remember-forgot input[type="checkbox"] {
            width: auto;
        }
        .remember-forgot a {
            color: #10b981;
            text-decoration: none;
        }
        .remember-forgot a:hover {
            text-decoration: underline;
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
        .register-link {
            text-align: center;
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 14px;
        }
        .register-link a {
            color: #10b981;
            text-decoration: none;
            font-weight: 600;
        }
        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="logo">
                <h1><i class="fas fa-coffee"></i> Mai Cafe</h1>
                <p>Welcome back! Please login to your account</p>
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

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="{{ old('email') }}" 
                        required 
                        autofocus
                        placeholder="Enter your email"
                    >
                    @error('email')
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
                        placeholder="Enter your password"
                    >
                    @error('password')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                <div class="remember-forgot">
                    <label>
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        Remember me
                    </label>
                    <a href="#">Forgot password?</a>
                </div>
                <button type="submit">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
            </form>

            <div class="register-link">
                Don't have an account? <a href="{{ route('register') }}">Register here</a>
            </div>
        </div>
    </div>
</body>
</html>


