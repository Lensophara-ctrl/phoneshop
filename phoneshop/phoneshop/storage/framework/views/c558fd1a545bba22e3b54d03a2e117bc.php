<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login to Your Account - PhoneShop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 450px;
            width: 100%;
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

        .logo-circle {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        }

        .logo-circle i {
            font-size: 40px;
            color: white;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
            font-weight: 600;
        }

        .social-btn {
            width: 100%;
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            background: white;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            font-size: 16px;
            font-weight: 500;
            color: #333;
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none;
        }

        .social-btn:hover {
            border-color: #667eea;
            background: #f8f9ff;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.2);
        }

        .social-btn i {
            font-size: 20px;
            margin-right: 15px;
            width: 25px;
            text-align: center;
        }

        .btn-email { color: #FF6B35; }
        .btn-email i { color: #FF6B35; }

        .btn-otp { color: #4ECDC4; }
        .btn-otp i { color: #4ECDC4; }

        .btn-google { color: #DB4437; }
        .btn-google i { color: #DB4437; }

        .btn-facebook { color: #4267B2; }
        .btn-facebook i { color: #4267B2; }

        .btn-apple { color: #000; }
        .btn-apple i { color: #000; }

        .btn-biometric { color: #667eea; }
        .btn-biometric i { color: #667eea; }

        .divider {
            text-align: center;
            margin: 25px 0;
            position: relative;
        }

        .divider::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            width: 100%;
            height: 1px;
            background: #e0e0e0;
        }

        .divider span {
            background: white;
            padding: 0 15px;
            position: relative;
            color: #999;
            font-size: 14px;
        }

        .signup-link {
            text-align: center;
            margin-top: 25px;
            color: #666;
            font-size: 14px;
        }

        .signup-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }

        .signup-link a:hover {
            text-decoration: underline;
        }

        .badge-new {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-size: 10px;
            padding: 2px 8px;
            border-radius: 10px;
            margin-left: 10px;
        }

        @media (max-width: 576px) {
            .login-container {
                padding: 30px 20px;
                margin: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo-circle">
            <i class="fas fa-mobile-alt"></i>
        </div>
        
        <h2>Login to Your Account</h2>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert" style="background: #f8d7da; border: 1px solid #f5c2c7; border-radius: 12px; padding: 15px; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between;">
                <span style="color: #842029;"><?php echo e(session('error')); ?></span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="padding: 0; background: transparent; border: none; font-size: 20px; color: #842029; cursor: pointer; opacity: 0.5;">×</button>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert" style="background: #d1e7dd; border: 1px solid #badbcc; border-radius: 12px; padding: 15px; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between;">
                <span style="color: #0f5132;"><?php echo e(session('success')); ?></span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="padding: 0; background: transparent; border: none; font-size: 20px; color: #0f5132; cursor: pointer; opacity: 0.5;">×</button>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <!-- Login Options -->
        <div class="login-options">
            <!-- Email Login -->
            <a href="<?php echo e(route('login.email')); ?>" class="social-btn btn-email">
                <i class="fas fa-envelope"></i>
                <span>Login with Email</span>
            </a>

            <!-- OTP Login -->
            <a href="<?php echo e(route('login.otp')); ?>" class="social-btn btn-otp">
                <i class="fas fa-shield-alt"></i>
                <span>Login with OTP</span>
            </a>

            <!-- Google Login -->
            <a href="<?php echo e(route('login.google')); ?>" class="social-btn btn-google">
                <i class="fab fa-google"></i>
                <span>Login with Google</span>
            </a>

            <!-- Facebook Login -->
            <a href="<?php echo e(route('login.facebook')); ?>" class="social-btn btn-facebook">
                <i class="fab fa-facebook"></i>
                <span>Login with Facebook</span>
            </a>

            <!-- Apple Login -->
            <a href="<?php echo e(route('login.apple')); ?>" class="social-btn btn-apple">
                <i class="fab fa-apple"></i>
                <span>Login with Apple</span>
            </a>

            <!-- Biometric Login -->
            <a href="<?php echo e(route('login.biometric')); ?>" class="social-btn btn-biometric">
                <i class="fas fa-fingerprint"></i>
                <span>Login with Face ID / Touch ID</span>
                <span class="badge-new">NEW</span>
            </a>
        </div>

        <div class="signup-link">
            Don't have an account? <a href="<?php echo e(route('register')); ?>">Sign Up</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php /**PATH C:\laragon\www\Practice\phoneshop\phoneshop\resources\views/auth/modern-login.blade.php ENDPATH**/ ?>