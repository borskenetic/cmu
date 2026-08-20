<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset(config('branding.css_path', 'branding/branding.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/auth/login.css') }}">
</head>
<body class="login-page d-flex justify-content-center align-items-center">

    <div class="card login-card w-100 mx-3">
        <a href="{{ route('home') }}" class="login-back">
            <span class="login-back-icon" aria-hidden="true">←</span>
            Back to Home
        </a>

        <div class="text-center">
            <img src="{{ asset('images/d.png') }}" alt="Area 51 Logo" class="mb-3 login-logo">
        </div>

        <h5 class="text-center fw-bold login-title">Welcome! Let’s Begin</h5>
        <p class="text-center login-subtitle">Log in to Continue</p>

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-3">
                <input type="email" name="email" class="form-control rounded-custom" placeholder="Email" value="{{ old('email') }}" required autofocus>
            </div>
            <div class="mb-3">
                <input type="password" name="password" class="form-control rounded-custom" placeholder="Password" required>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-2 flex-wrap gap-2">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="remember" name="remember">
                    <label class="form-check-label text-lowercase" for="remember">
                        Remember me
                    </label>
                </div>
                <a href="#" class="login-forgot">Forgot password?</a>
            </div>

            @error('email')
                <div class="alert alert-danger mt-3 mb-0">{{ $message }}</div>
            @enderror

            <div class="d-grid mt-4">
                <button type="submit" class="btn btn-login btn-lg">Login</button>
            </div>

            <div class="d-grid mt-3">
                <a href="{{ route('patron.register') }}" class="btn btn-register btn-lg">
                    Register
                </a>
            </div>
        </form>
    </div>

</body>
</html>
