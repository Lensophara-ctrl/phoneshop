@extends('layouts.app')

@section('title', 'OTP Verification')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <div class="mb-3">
                            <i class="fas fa-shield-alt fa-3x text-primary"></i>
                        </div>
                        <h3 class="card-title">OTP Verification</h3>
                        <p class="text-muted">Enter the 6-digit code sent to your email/Telegram</p>
                    </div>

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('otp.verify.submit') }}" id="otpForm">
                        @csrf
                        <input type="hidden" name="email" value="{{ session('otp_email') }}">
                        <input type="hidden" name="type" value="{{ session('otp_type', 'login') }}">

                        <div class="mb-4">
                            <label class="form-label">OTP Code</label>
                            <div class="d-flex justify-content-between otp-inputs">
                                <input type="text" class="form-control text-center otp-digit" maxlength="1" name="digit1" required autofocus>
                                <input type="text" class="form-control text-center otp-digit" maxlength="1" name="digit2" required>
                                <input type="text" class="form-control text-center otp-digit" maxlength="1" name="digit3" required>
                                <input type="text" class="form-control text-center otp-digit" maxlength="1" name="digit4" required>
                                <input type="text" class="form-control text-center otp-digit" maxlength="1" name="digit5" required>
                                <input type="text" class="form-control text-center otp-digit" maxlength="1" name="digit6" required>
                            </div>
                            <input type="hidden" name="code" id="otpCode">
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-check-circle me-2"></i>Verify OTP
                            </button>
                        </div>

                        <div class="text-center">
                            <p class="text-muted mb-2">Didn't receive the code?</p>
                            <form method="POST" action="{{ route('otp.resend') }}" class="d-inline">
                                @csrf
                                <input type="hidden" name="email" value="{{ session('otp_email') }}">
                                <input type="hidden" name="type" value="{{ session('otp_type', 'login') }}">
                                <button type="submit" class="btn btn-link" id="resendBtn">
                                    <i class="fas fa-redo me-1"></i>Resend OTP
                                </button>
                            </form>
                        </div>

                        <div class="text-center mt-3">
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i>
                                Code expires in <span id="timer" class="fw-bold">5:00</span>
                            </small>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.otp-inputs {
    gap: 10px;
}
.otp-digit {
    width: 50px;
    height: 60px;
    font-size: 24px;
    font-weight: bold;
}
.otp-digit:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('.otp-digit');
    const otpCodeInput = document.getElementById('otpCode');
    const form = document.getElementById('otpForm');

    // Auto-focus next input
    inputs.forEach((input, index) => {
        input.addEventListener('input', function(e) {
            if (this.value.length === 1) {
                if (index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            }
            updateOtpCode();
        });

        input.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace' && this.value === '' && index > 0) {
                inputs[index - 1].focus();
            }
        });

        // Only allow numbers
        input.addEventListener('keypress', function(e) {
            if (!/[0-9]/.test(e.key)) {
                e.preventDefault();
            }
        });
    });

    function updateOtpCode() {
        let code = '';
        inputs.forEach(input => {
            code += input.value;
        });
        otpCodeInput.value = code;
    }

    // Timer countdown
    let timeLeft = 300; // 5 minutes
    const timerElement = document.getElementById('timer');
    
    const countdown = setInterval(function() {
        timeLeft--;
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        timerElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
        
        if (timeLeft <= 0) {
            clearInterval(countdown);
            timerElement.textContent = 'Expired';
            timerElement.classList.add('text-danger');
        }
    }, 1000);
});
</script>
@endsection
