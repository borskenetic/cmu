<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset(config('branding.css_path', 'branding/branding.css')) }}">
    <style>
        body.login-page {
            min-height: 100vh;
            margin: 0;
            background: var(--brand-page-bg, #f5f7fa);
            font-family: var(--brand-font-family, 'Inter', 'Segoe UI', system-ui, sans-serif);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 24px 16px;
            box-sizing: border-box;
        }

        .login-shell {
            width: 100%;
            max-width: 460px;
        }

        .login-card {
            width: 100%;
            max-width: 460px;
            margin: 0 auto;
            padding: 2rem 2rem 1.75rem;
            border: 0 !important;
            border-radius: 18px !important;
            box-shadow: 0 12px 32px rgba(34, 51, 59, 0.12);
            background: #fff;
        }

        .login-back {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            margin-bottom: 0.85rem;
            color: var(--brand-nav-link, #1f7a1f);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
        }

        .login-back:hover {
            color: var(--brand-nav-link-hover, #145214);
            text-decoration: underline;
        }

        .login-logo {
            width: 100%;
            max-width: 130px;
            height: auto;
        }

        .login-title {
            color: var(--brand-card-header-bg, #22333b);
            font-size: 1.8rem;
            margin-bottom: 0.35rem;
        }

        .login-subtitle {
            color: #667085;
            margin-bottom: 1.35rem;
        }

        .login-card .form-control {
            border-radius: 14px;
            padding: 0.9rem 1rem;
            background: #f3f6f4;
            border: 1.5px solid #dbe5dd;
            box-shadow: none;
        }

        .login-card .form-control:focus {
            background: #fff;
            border-color: var(--brand-button-bg, #1f7a1f);
            box-shadow: 0 0 0 0.2rem rgba(31, 122, 31, 0.18);
        }

        .login-card .form-check-input:checked {
            background-color: var(--brand-button-bg, #1f7a1f);
            border-color: var(--brand-button-bg, #1f7a1f);
        }

        .login-card .form-check-input:focus {
            border-color: var(--brand-button-bg, #1f7a1f);
            box-shadow: 0 0 0 0.2rem rgba(31, 122, 31, 0.18);
        }

        .login-card .form-check-label,
        .login-forgot {
            font-size: 0.95rem;
        }

        .login-forgot {
            color: var(--brand-nav-link, #1f7a1f);
            text-decoration: none;
            font-weight: 500;
        }

        .login-forgot:hover {
            color: var(--brand-nav-link-hover, #145214);
            text-decoration: underline;
        }

        .btn-login {
            background: var(--brand-button-bg, #1f7a1f);
            border-color: var(--brand-button-bg, #1f7a1f);
            color: var(--brand-button-text, #fff);
            border-radius: 14px;
            padding: 0.85rem 1rem;
            font-weight: 600;
        }

        .btn-login:hover,
        .btn-login:focus {
            background: var(--brand-button-hover-bg, #ffb845);
            border-color: var(--brand-button-hover-bg, #ffb845);
            color: var(--brand-button-hover-text, #22333b);
        }

        .btn-register {
            border-radius: 14px;
            padding: 0.85rem 1rem;
            font-weight: 600;
            border: 1.5px solid var(--brand-button-bg, #1f7a1f);
            color: var(--brand-button-bg, #1f7a1f);
            background: transparent;
        }

        .btn-register:hover,
        .btn-register:focus {
            background: var(--brand-button-bg, #1f7a1f);
            border-color: var(--brand-button-bg, #1f7a1f);
            color: var(--brand-button-text, #fff);
        }

        @media (max-width: 576px) {
            body.login-page {
                align-items: flex-start;
                padding: 16px 12px;
            }

            .login-card {
                padding: 1.5rem 1.2rem;
            }

            .login-title {
                font-size: 1.45rem;
            }
        }
    </style>
</head>
<body class="login-page">
    <div class="login-shell">
    <div class="card login-card">
        <a href="{{ route('home') }}" class="login-back">
            <span class="login-back-icon" aria-hidden="true">←</span>
            Back to Home
        </a>

        <div class="text-center">
            <img src="{{ asset('images/d.png') }}" alt="Logo" class="mb-3 login-logo">
        </div>

        <h5 class="text-center fw-bold login-title">Welcome! Let’s Begin</h5>
        <p class="text-center login-subtitle">Log in to Continue</p>

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-3">
                <input type="email" name="email" class="form-control" placeholder="Email" value="{{ old('email') }}" required autofocus>
            </div>
            <div class="mb-3">
                <input type="password" name="password" class="form-control" placeholder="Password" required>
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
    </div>

</body>
</html>
