@extends('frontend.layouts.app')

@section('content')
<div class="payment-method-page">
    <div class="payment-method-container">
        <div class="payment-header">
            <h1>Choose Payment Method</h1>
            <p>Select how you want to pay for your order</p>
        </div>

        <div class="order-summary">
            <h3>Order Summary</h3>
            <div class="summary-row">
                <span>Total Items:</span>
                <span>{{ array_sum(array_column($cart, 'qty')) }} items</span>
            </div>
            <div class="summary-row total">
                <span>Total Amount:</span>
                <span>${{ number_format($totalAmount, 2) }}</span>
            </div>
        </div>

        <form action="{{ route('shop.payment.process') }}" method="POST" id="paymentForm">
            @csrf
            <input type="hidden" name="payment_type" id="paymentType" value="">
            
            <div class="payment-methods">
                <!-- Bakong Payment (Online) -->
                <div class="payment-option" onclick="selectPayment('standard')">
                    <input type="radio" name="payment_method" value="standard" id="standard">
                    <label for="standard">
                        <div class="method-icon">
                            <i class="fa-solid fa-qrcode"></i>
                        </div>
                        <div class="method-details">
                            <h3>Bakong Payment</h3>
                            <p>Scan QR code with your Bakong app</p>
                            <ul class="method-features">
                                <li><i class="fa-solid fa-check"></i> Quick QR scan</li>
                                <li><i class="fa-solid fa-check"></i> Auto-detect payment</li>
                                <li><i class="fa-solid fa-check"></i> Instant confirmation</li>
                            </ul>
                        </div>
                        <div class="method-badge">
                            <span class="badge recommended">Recommended</span>
                        </div>
                    </label>
                </div>

                <!-- Cash Payment -->
                <div class="payment-option" onclick="selectPayment('cash')">
                    <input type="radio" name="payment_method" value="cash" id="cash">
                    <label for="cash">
                        <div class="method-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                            <i class="fa-solid fa-money-bill-wave"></i>
                        </div>
                        <div class="method-details">
                            <h3>Cash Payment</h3>
                            <p>Pay with cash on delivery or at pickup</p>
                            <ul class="method-features">
                                <li><i class="fa-solid fa-check"></i> Pay when you receive</li>
                                <li><i class="fa-solid fa-check"></i> No online payment needed</li>
                                <li><i class="fa-solid fa-check"></i> Simple and convenient</li>
                            </ul>
                        </div>
                    </label>
                </div>
            </div>

            <button type="submit" class="continue-btn" id="continueBtn" disabled>
                <i class="fa-solid fa-arrow-right"></i> Continue to Payment
            </button>
        </form>

        <a href="{{ route('shop.cart') }}" class="back-link">
            <i class="fa-solid fa-arrow-left"></i> Back to Cart
        </a>
    </div>
</div>

<style>
* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

.payment-method-page {
    min-height: 100vh;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 40px 20px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.payment-method-container {
    background: white;
    border-radius: 24px;
    padding: 40px;
    max-width: 800px;
    width: 100%;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
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

.payment-header {
    text-align: center;
    margin-bottom: 32px;
}

.payment-header h1 {
    font-size: 32px;
    color: #1f2937;
    margin-bottom: 8px;
    font-weight: 700;
}

.payment-header p {
    color: #6b7280;
    font-size: 16px;
}

.order-summary {
    background: #f9fafb;
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 32px;
}

.order-summary h3 {
    font-size: 18px;
    color: #1f2937;
    margin-bottom: 16px;
    font-weight: 700;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    padding: 12px 0;
    color: #4b5563;
    font-size: 15px;
}

.summary-row.total {
    border-top: 2px solid #e5e7eb;
    margin-top: 8px;
    padding-top: 16px;
    font-size: 20px;
    font-weight: 700;
    color: #1f2937;
}

.payment-methods {
    display: flex;
    flex-direction: column;
    gap: 20px;
    margin-bottom: 32px;
}

.payment-option {
    position: relative;
    cursor: pointer;
}

.payment-option input[type="radio"] {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.payment-option label {
    display: flex;
    align-items: flex-start;
    gap: 20px;
    padding: 24px;
    border: 3px solid #e5e7eb;
    border-radius: 16px;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
}

.payment-option:hover label {
    border-color: #667eea;
    background: #f9fafb;
}

.payment-option input[type="radio"]:checked + label {
    border-color: #667eea;
    background: linear-gradient(135deg, #f0f4ff 0%, #e8eeff 100%);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
}

.method-icon {
    width: 64px;
    height: 64px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.method-icon i {
    font-size: 32px;
    color: white;
}

.method-details {
    flex: 1;
}

.method-details h3 {
    font-size: 20px;
    color: #1f2937;
    margin-bottom: 8px;
    font-weight: 700;
}

.method-details p {
    color: #6b7280;
    font-size: 14px;
    margin-bottom: 16px;
}

.method-features {
    list-style: none;
    padding: 0;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.method-features li {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #4b5563;
    font-size: 14px;
}

.method-features i {
    color: #10b981;
    font-size: 12px;
}

.method-badge {
    position: absolute;
    top: 16px;
    right: 16px;
}

.badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
}

.badge.recommended {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
}

.continue-btn {
    width: 100%;
    padding: 18px 24px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 14px;
    font-size: 18px;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    transition: all 0.3s ease;
    margin-bottom: 20px;
}

.continue-btn:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 12px 24px -8px rgba(102, 126, 234, 0.5);
}

.continue-btn:disabled {
    background: #9ca3af;
    cursor: not-allowed;
    opacity: 0.6;
}

.back-link {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    color: #6b7280;
    text-decoration: none;
    font-size: 15px;
    font-weight: 600;
    padding: 12px;
    border-radius: 12px;
    transition: all 0.3s ease;
}

.back-link:hover {
    background: #f3f4f6;
    color: #374151;
}

@media (max-width: 768px) {
    .payment-method-container {
        padding: 24px;
    }

    .payment-header h1 {
        font-size: 24px;
    }

    .method-details h3 {
        font-size: 18px;
    }

    .method-icon {
        width: 56px;
        height: 56px;
    }

    .method-icon i {
        font-size: 28px;
    }
}
</style>

<script>
function selectPayment(type) {
    // Uncheck all
    document.querySelectorAll('input[name="payment_method"]').forEach(input => {
        input.checked = false;
    });
    
    // Check selected
    document.getElementById(type).checked = true;
    document.getElementById('paymentType').value = type;
    
    // Enable continue button
    document.getElementById('continueBtn').disabled = false;
}

// Form submission
document.getElementById('paymentForm').addEventListener('submit', function(e) {
    const paymentType = document.getElementById('paymentType').value;
    
    if (!paymentType) {
        e.preventDefault();
        alert('Please select a payment method');
        return false;
    }
});
</script>
@endsection
