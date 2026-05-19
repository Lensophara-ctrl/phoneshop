<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biometric Login - PhoneShop</title>
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
        }

        .back-btn {
            color: #667eea;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            margin-bottom: 20px;
            font-weight: 500;
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

        .fingerprint-icon {
            text-align: center;
            margin: 30px 0;
        }

        .fingerprint-icon i {
            font-size: 80px;
            color: #667eea;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .form-control {
            padding: 12px 15px;
            border-radius: 10px;
            border: 2px solid #e0e0e0;
        }

        .btn-biometric {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            color: white;
            font-weight: 600;
            font-size: 16px;
            margin-top: 20px;
        }

        .btn-biometric:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <a href="{{ route('login') }}" class="back-btn">
            <i class="fas fa-arrow-left"></i> Back
        </a>

        <h2><i class="fas fa-fingerprint me-2" style="color: #667eea;"></i>Biometric Login</h2>
        <p class="subtitle">Use Face ID or Touch ID to login</p>

        <div class="fingerprint-icon">
            <i class="fas fa-fingerprint"></i>
        </div>

        <form id="biometricForm">
            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" class="form-control" id="email" name="email" 
                       placeholder="Enter your email" required autofocus>
            </div>

            <button type="button" class="btn btn-biometric" onclick="startBiometric()">
                <i class="fas fa-fingerprint me-2"></i>Use Face ID / Touch ID
            </button>
        </form>

        <div class="alert alert-info mt-3">
            <i class="fas fa-info-circle me-2"></i>
            <small>Make sure you've registered your device first in your profile settings.</small>
        </div>
    </div>

    <script>
        async function startBiometric() {
            const email = document.getElementById('email').value;
            
            if (!email) {
                alert('Please enter your email');
                return;
            }

            try {
                // Check if biometric is available
                const checkResponse = await fetch('/api/biometric/check', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email })
                });

                const checkData = await checkResponse.json();

                if (!checkData.biometric_enabled) {
                    alert('Biometric not enabled for this account. Please set it up first.');
                    window.location.href = '/profile/biometric-setup';
                    return;
                }

                // Get challenge
                const challengeResponse = await fetch('/api/biometric/challenge', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email })
                });

                const challengeData = await challengeResponse.json();

                // Simulate biometric authentication
                // In production, use WebAuthn API
                alert('Biometric authentication would happen here. For demo, click OK to continue.');

                // Verify (simplified for demo)
                const verifyResponse = await fetch('/api/biometric/verify', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        user_id: checkData.user_id,
                        device_id: 'demo_device',
                        signature: 'demo_signature'
                    })
                });

                const verifyData = await verifyResponse.json();

                if (verifyData.success) {
                    window.location.href = verifyData.redirect;
                } else {
                    alert('Authentication failed');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error during biometric authentication');
            }
        }
    </script>
</body>
</html>
