<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Login - Phara_SHOP</title>
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }
        .login-card {
            width: 100%;
            max-width: 420px;
            background: #fff;
            padding: 2.5rem;
            border-radius: 1.5rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-control {
            padding: 0.75rem 1rem;
            border-radius: 0.75rem;
            border: 1px solid #e2e8f0;
            background-color: #f8fafc;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            background-color: #fff;
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 0.75rem;
            border-radius: 0.75rem;
            font-weight: 600;
            transition: all 0.3s ease;
            color: white;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .brand-logo {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 700;
            font-size: 1.25rem;
        }

        .divider-text {
            text-align: center;
            color: #94a3b8;
            font-size: 0.85rem;
            margin: 1.5rem 0;
            position: relative;
        }

        .divider-text::before,
        .divider-text::after {
            content: '';
            position: absolute;
            top: 50%;
            width: 45%;
            height: 1px;
            background: #e2e8f0;
        }

        .divider-text::before {
            left: 0;
        }

        .divider-text::after {
            right: 0;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="text-center mb-5">
            <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                <i class="fa-solid fa-shopping-cart fs-3" style="color: #667eea;"></i>
            </div>
            <h3 class="fw-bold text-dark">Welcome to Phara_SHOP</h3>
            <p class="text-muted">Sign in to your account to continue shopping</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger border-0 small mb-4">
                <i class="fa-solid fa-circle-exclamation me-2"></i>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('customer.login') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label small fw-semibold text-muted">Email Address</label>
                <input type="email" name="email" class="form-control" id="email" placeholder="name@example.com" value="{{ old('email') }}" required autofocus>
            </div>
            <div class="mb-4">
                <label for="password" class="form-label small fw-semibold text-muted">Password</label>
                <input type="password" name="password" class="form-control" id="password" placeholder="••••••••" required>
            </div>
            <div class="mb-4 form-check">
                <input type="checkbox" name="remember" class="form-check-input" id="remember">
                <label class="form-check-label small text-muted" for="remember">Keep me logged in</label>
            </div>
            <button type="submit" class="btn btn-primary w-100 shadow-sm">
                <i class="fa-solid fa-sign-in-alt me-2"></i>Sign In
            </button>
        </form>

        <div class="divider-text">or</div>

        <div class="text-center">
            <p class="small text-muted mb-0">
                <i class="fa-solid fa-info-circle me-1"></i>Don't have an account? Contact our store admin or check your email for access credentials.
            </p>
        </div>

        <div class="text-center mt-5 pt-4 border-top">
            <p class="small text-muted mb-0">
                <a href="{{ route('shop.home') }}" class="text-decoration-none" style="color: #667eea;">
                    <i class="fa-solid fa-arrow-left me-1"></i>Back to Shopping
                </a>
            </p>
        </div>
    </div>
</body>
</html>
