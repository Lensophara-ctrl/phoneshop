<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Phara_SHOP</title>
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
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
        }
        .form-control {
            padding: 0.75rem 1rem;
            border-radius: 0.75rem;
            border: 1px solid #e2e8f0;
            background-color: #f8fafc;
        }
        .form-control:focus {
            background-color: #fff;
            border-color: #4f46e5;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        }
        .btn-primary {
            background-color: #4f46e5;
            border: none;
            padding: 0.75rem;
            border-radius: 0.75rem;
            font-weight: 600;
            transition: all 0.2s;
        }
        .btn-primary:hover {
            background-color: #4338ca;
            transform: translateY(-1px);
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="text-center mb-5">
            <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                <i class="fa-solid fa-lock fs-3 text-primary"></i>
            </div>
            <h3 class="fw-bold text-dark">Admin Portal</h3>
            <p class="text-muted">Phara_SHOP Administration Panel</p>
            <div class="alert alert-info small mt-3 mb-0" role="alert">
                <i class="fa-solid fa-shield me-2"></i>Admin & Staff access only
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger border-0 small mb-4">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.login') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label small fw-semibold text-muted">Email Address</label>
                <input type="email" name="email" class="form-control" id="email" placeholder="name@example.com" value="{{ old('email') }}" required autofocus>
            </div>
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label for="password" class="form-label small fw-semibold text-muted mb-0">Password</label>
                </div>
                <input type="password" name="password" class="form-control" id="password" placeholder="••••••••" required>
            </div>
            <div class="mb-4 form-check">
                <input type="checkbox" name="remember" class="form-check-input" id="remember">
                <label class="form-check-label small text-muted" for="remember">Keep me logged in</label>
            </div>
            <button type="submit" class="btn btn-primary w-100 shadow-sm">
                <i class="fa-solid fa-sign-in-alt me-2"></i>Sign In to Admin Panel
            </button>
        </form>

        <div class="text-center mt-5 pt-4 border-top">
            <p class="small text-muted mb-2">
                <i class="fa-solid fa-info-circle me-1"></i>Admin accounts can only be created by administrators
            </p>
            <p class="small text-muted mb-0">
                <a href="{{ route('shop.home') }}" class="text-decoration-none" style="color: #667eea;">
                    <i class="fa-solid fa-arrow-left me-1"></i>Back to Shop
                </a>
            </p>
        </div>
    </div>
</body>
</html>
