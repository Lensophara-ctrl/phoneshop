<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP System Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0"><i class="fas fa-shield-alt me-2"></i>OTP System Test Panel</h3>
                    </div>
                    <div class="card-body">
                        <!-- Status Display -->
                        <div id="statusMessage" class="alert d-none"></div>

                        <!-- Test 1: Request OTP -->
                        <div class="mb-4">
                            <h5><i class="fas fa-paper-plane text-primary me-2"></i>Test 1: Request OTP</h5>
                            <div class="input-group mb-3">
                                <input type="email" class="form-control" id="requestEmail" placeholder="Enter email" value="admin@gmail.com">
                                <select class="form-select" id="requestType" style="max-width: 150px;">
                                    <option value="login">Login</option>
                                    <option value="register">Register</option>
                                    <option value="reset_password">Reset Password</option>
                                </select>
                                <button class="btn btn-primary" onclick="requestOtp()">
                                    <i class="fas fa-paper-plane me-1"></i>Request OTP
                                </button>
                            </div>
                        </div>

                        <hr>

                        <!-- Test 2: Verify OTP -->
                        <div class="mb-4">
                            <h5><i class="fas fa-check-circle text-success me-2"></i>Test 2: Verify OTP</h5>
                            <div class="row g-2">
                                <div class="col-md-5">
                                    <input type="email" class="form-control" id="verifyEmail" placeholder="Enter email" value="admin@gmail.com">
                                </div>
                                <div class="col-md-3">
                                    <input type="text" class="form-control" id="verifyCode" placeholder="6-digit code" maxlength="6">
                                </div>
                                <div class="col-md-2">
                                    <select class="form-select" id="verifyType">
                                        <option value="login">Login</option>
                                        <option value="register">Register</option>
                                        <option value="reset_password">Reset</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-success w-100" onclick="verifyOtp()">
                                        <i class="fas fa-check me-1"></i>Verify
                                    </button>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Test 3: Resend OTP -->
                        <div class="mb-4">
                            <h5><i class="fas fa-redo text-warning me-2"></i>Test 3: Resend OTP</h5>
                            <div class="input-group">
                                <input type="email" class="form-control" id="resendEmail" placeholder="Enter email" value="admin@gmail.com">
                                <select class="form-select" id="resendType" style="max-width: 150px;">
                                    <option value="login">Login</option>
                                    <option value="register">Register</option>
                                    <option value="reset_password">Reset Password</option>
                                </select>
                                <button class="btn btn-warning" onclick="resendOtp()">
                                    <i class="fas fa-redo me-1"></i>Resend
                                </button>
                            </div>
                        </div>

                        <hr>

                        <!-- Test 4: Get Statistics -->
                        <div class="mb-4">
                            <h5><i class="fas fa-chart-bar text-info me-2"></i>Test 4: OTP Statistics</h5>
                            <button class="btn btn-info" onclick="getStats()">
                                <i class="fas fa-chart-bar me-1"></i>Get Statistics
                            </button>
                            <div id="statsDisplay" class="mt-3"></div>
                        </div>

                        <hr>

                        <!-- Response Display -->
                        <div>
                            <h5><i class="fas fa-terminal text-secondary me-2"></i>API Response</h5>
                            <pre id="responseDisplay" class="bg-dark text-light p-3 rounded" style="max-height: 300px; overflow-y: auto;">Waiting for API call...</pre>
                        </div>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="card shadow mt-4">
                    <div class="card-body">
                        <h5><i class="fas fa-link me-2"></i>Quick Links</h5>
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="/admin-login" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-user-shield me-1"></i>Admin Login
                            </a>
                            <a href="/login" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-user me-1"></i>Customer Login
                            </a>
                            <a href="/test/telegram" class="btn btn-sm btn-outline-success">
                                <i class="fab fa-telegram me-1"></i>Test Telegram
                            </a>
                            <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Documentation -->
                <div class="card shadow mt-4">
                    <div class="card-body">
                        <h5><i class="fas fa-book me-2"></i>Documentation</h5>
                        <p class="mb-2">Check these files for detailed information:</p>
                        <ul>
                            <li><code>OTP_IMPLEMENTATION_GUIDE.md</code> - Complete implementation guide</li>
                            <li><code>TEST_OTP.md</code> - Testing instructions</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showStatus(message, type = 'info') {
            const statusDiv = document.getElementById('statusMessage');
            statusDiv.className = `alert alert-${type}`;
            statusDiv.textContent = message;
            statusDiv.classList.remove('d-none');
            setTimeout(() => statusDiv.classList.add('d-none'), 5000);
        }

        function displayResponse(data) {
            document.getElementById('responseDisplay').textContent = JSON.stringify(data, null, 2);
        }

        async function requestOtp() {
            const email = document.getElementById('requestEmail').value;
            const type = document.getElementById('requestType').value;

            try {
                const response = await fetch('/api/otp/request', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email, type })
                });

                const data = await response.json();
                displayResponse(data);

                if (data.success) {
                    showStatus('OTP sent successfully! Check your email and Telegram.', 'success');
                } else {
                    showStatus(data.message || 'Failed to send OTP', 'danger');
                }
            } catch (error) {
                showStatus('Error: ' + error.message, 'danger');
                displayResponse({ error: error.message });
            }
        }

        async function verifyOtp() {
            const email = document.getElementById('verifyEmail').value;
            const code = document.getElementById('verifyCode').value;
            const type = document.getElementById('verifyType').value;

            if (code.length !== 6) {
                showStatus('Please enter a 6-digit code', 'warning');
                return;
            }

            try {
                const response = await fetch('/api/otp/verify', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email, code, type })
                });

                const data = await response.json();
                displayResponse(data);

                if (data.success) {
                    showStatus('OTP verified successfully!', 'success');
                } else {
                    showStatus(data.message || 'Invalid OTP code', 'danger');
                }
            } catch (error) {
                showStatus('Error: ' + error.message, 'danger');
                displayResponse({ error: error.message });
            }
        }

        async function resendOtp() {
            const email = document.getElementById('resendEmail').value;
            const type = document.getElementById('resendType').value;

            try {
                const response = await fetch('/api/otp/resend', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email, type })
                });

                const data = await response.json();
                displayResponse(data);

                if (data.success) {
                    showStatus('New OTP sent successfully!', 'success');
                } else {
                    showStatus(data.message || 'Failed to resend OTP', 'danger');
                }
            } catch (error) {
                showStatus('Error: ' + error.message, 'danger');
                displayResponse({ error: error.message });
            }
        }

        async function getStats() {
            try {
                const response = await fetch('/api/otp/stats');
                const data = await response.json();
                displayResponse(data);

                if (data.success) {
                    const stats = data.data;
                    const statsHtml = `
                        <div class="row g-2">
                            <div class="col-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body text-center">
                                        <h3>${stats.total}</h3>
                                        <small>Total</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <h3>${stats.verified}</h3>
                                        <small>Verified</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body text-center">
                                        <h3>${stats.expired}</h3>
                                        <small>Expired</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body text-center">
                                        <h3>${stats.active}</h3>
                                        <small>Active</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    document.getElementById('statsDisplay').innerHTML = statsHtml;
                    showStatus('Statistics loaded successfully!', 'success');
                } else {
                    showStatus(data.message || 'Failed to load statistics', 'danger');
                }
            } catch (error) {
                showStatus('Error: ' + error.message, 'danger');
                displayResponse({ error: error.message });
            }
        }
    </script>
</body>
</html>
