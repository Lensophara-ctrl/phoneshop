<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Login - PhoneShop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #4ECDC4 0%, #44A08D 100%);
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
        }

        .back-btn {
            color: #4ECDC4;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .back-btn:hover {
            text-decoration: underline;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .form-control {
            padding: 12px 15px;
            border-radius: 10px;
            border: 2px solid #e0e0e0;
        }

        .form-control:focus {
            border-color: #4ECDC4;
            box-shadow: 0 0 0 0.2rem rgba(78, 205, 196, 0.25);
        }

        .btn-send-otp {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #4ECDC4 0%, #44A08D 100%);
            border: none;
            border-radius: 10px;
            color: white;
            font-weight: 600;
            font-size: 16px;
            margin-top: 20px;
        }

        .btn-send-otp:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(78, 205, 196, 0.4);
        }

        .info-box {
            background: #f0f9ff;
            border-left: 4px solid #4ECDC4;
            padding: 15px;
            margin-top: 20px;
            border-radius: 8px;
        }

        .info-box i {
            color: #4ECDC4;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <a href="{{ route('login') }}" class="back-btn">
            <i class="fas fa-arrow-left"></i> Back
        </a>

        <h2><i class="fas fa-shield-alt text-info me-2"></i>OTP Login</h2>
        <p class="subtitle">We'll send a one-time password to your email</p>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('otp.request.web') }}">
            @csrf
            
            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" class="form-control" id="email" name="email" 
                       placeholder="Enter your email" value="{{ old('email') }}" required autofocus>
            </div>

            <button type="submit" class="btn btn-send-otp">
                <i class="fas fa-paper-plane me-2"></i>Send OTP Code
            </button>
        </form>

        <div class="info-box">
            <i class="fas fa-info-circle"></i>
            <small>You'll receive a 6-digit code via email and Telegram. The code is valid for 5 minutes.</small>
        </div>
    </div>
</body>
</html>
