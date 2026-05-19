@extends('frontend.layouts.app')

@section('content')
<div class="checkout-page">
    <div class="checkout-container">
        <div class="checkout-header">
            <h1>Checkout</h1>
            <p>Please provide your information to complete the order</p>
        </div>

        <div class="checkout-content">
            <div class="customer-info-section">
                <h2><i class="fa-solid fa-user"></i> Customer Information</h2>
                
                <form action="{{ route('shop.payment.method') }}" method="POST" id="checkoutForm">
                    @csrf
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="customer_name">Full Name <span class="required">*</span></label>
                            <input type="text" 
                                   id="customer_name" 
                                   name="customer_name" 
                                   class="form-control" 
                                   value="{{ auth()->check() ? auth()->user()->name : old('customer_name') }}" 
                                   required>
                        </div>

                        <div class="form-group">
                            <label for="customer_email">Email Address <span class="required">*</span></label>
                            <input type="email" 
                                   id="customer_email" 
                                   name="customer_email" 
                                   class="form-control" 
                                   value="{{ auth()->check() ? auth()->user()->email : old('customer_email') }}" 
                                   required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="customer_phone">Phone Number <span class="required">*</span></label>
                            <input type="tel" 
                                   id="customer_phone" 
                                   name="customer_phone" 
                                   class="form-control" 
                                   value="{{ auth()->check() ? auth()->user()->phone : old('customer_phone') }}" 
                                   placeholder="+855 12 345 678"
                                   required>
                        </div>

                        <div class="form-group">
                            <label for="customer_city">City <span class="required">*</span></label>
                            <input type="text" 
                                   id="customer_city" 
                                   name="customer_city" 
                                   class="form-control" 
                                   value="{{ old('customer_city') }}" 
                                   required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="customer_address">Delivery Address <span class="required">*</span></label>
                        <textarea id="customer_address" 
                                  name="customer_address" 
                                  class="form-control" 
                                  rows="3" 
                                  placeholder="Street address, building, apartment number"
                                  required>{{ auth()->check() ? auth()->user()->address : old('customer_address') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label>Our Store Location</label>
                        <div style="border-radius: 10px; overflow: hidden; border: 2px solid #e5e7eb;">
                            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d67256.45219470539!2d104.81912119895989!3d11.597286982956795!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31094d78fd2853eb%3A0x5835125415aec4ea!2z4Z6B4Z6O4Z-S4Z6M4Z6f4Z-C4Z6T4Z6f4Z674Z6BLCDhnpfhn5LhnpPhn4bhnpbhn4Hhnok!5e1!3m2!1skm!2skh!4v1779110603640!5m2!1skm!2skh" width="100%" height="400" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                        <small class="text-muted mt-2 d-block">
                            <i class="fa-solid fa-location-dot"></i> Visit us at our store location
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="customer_postal_code">Postal Code</label>
                        <input type="text" 
                               id="customer_postal_code" 
                               name="customer_postal_code" 
                               class="form-control" 
                               value="{{ old('customer_postal_code') }}" 
                               placeholder="Optional">
                    </div>

                    <div class="form-group">
                        <label for="order_notes">Order Notes</label>
                        <textarea id="order_notes" 
                                  name="order_notes" 
                                  class="form-control" 
                                  rows="3" 
                                  placeholder="Any special instructions or notes for your order (optional)">{{ old('order_notes') }}</textarea>
                    </div>

                    <button type="submit" class="continue-btn">
                        <i class="fa-solid fa-arrow-right"></i> Continue to Payment Method
                    </button>
                </form>

                <a href="{{ route('shop.cart') }}" class="back-link">
                    <i class="fa-solid fa-arrow-left"></i> Back to Cart
                </a>
            </div>

            <div class="order-summary-section">
                <h2><i class="fa-solid fa-shopping-cart"></i> Order Summary</h2>
                
                <div class="summary-items">
                    @php
                        $subtotal = 0;
                    @endphp
                    @foreach($cart as $id => $item)
                        @php
                            $phone = \App\Models\Phone::find($id);
                            $itemTotal = $phone->price * $item['qty'];
                            $subtotal += $itemTotal;
                        @endphp
                        <div class="summary-item">
                            <div class="item-info">
                                <span class="item-name">{{ $phone->name }}</span>
                                <span class="item-qty">x{{ $item['qty'] }}</span>
                            </div>
                            <span class="item-price">${{ number_format($itemTotal, 2) }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="summary-totals">
                    <div class="summary-row">
                        <span>Subtotal:</span>
                        <span>${{ number_format($subtotal, 2) }}</span>
                    </div>
                    <div class="summary-row">
                        <span>Tax:</span>
                        <span>${{ number_format($subtotal * 0.1, 2) }}</span>
                    </div>
                    <div class="summary-row total">
                        <span>Total:</span>
                        <span>${{ number_format($subtotal * 1.1, 2) }}</span>
                    </div>
                </div>

                <div class="secure-checkout">
                    <i class="fa-solid fa-lock"></i>
                    <span>Secure Checkout</span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
* {
    box-sizing: border-box;
}

.checkout-page {
    min-height: 100vh;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 40px 20px;
}

.checkout-container {
    max-width: 1200px;
    margin: 0 auto;
}

.checkout-header {
    text-align: center;
    color: white;
    margin-bottom: 40px;
}

.checkout-header h1 {
    font-size: 36px;
    margin-bottom: 8px;
    font-weight: 700;
}

.checkout-header p {
    font-size: 16px;
    opacity: 0.9;
}

.checkout-content {
    display: grid;
    grid-template-columns: 1fr 400px;
    gap: 30px;
}

.customer-info-section,
.order-summary-section {
    background: white;
    border-radius: 20px;
    padding: 32px;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

.customer-info-section h2,
.order-summary-section h2 {
    font-size: 22px;
    color: #1f2937;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.customer-info-section h2 i,
.order-summary-section h2 i {
    color: #667eea;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    font-weight: 600;
    color: #374151;
    margin-bottom: 8px;
    font-size: 14px;
}

.required {
    color: #ef4444;
}

.form-control {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    font-size: 15px;
    transition: all 0.3s ease;
}

.form-control:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

textarea.form-control {
    resize: vertical;
    font-family: inherit;
}

.continue-btn {
    width: 100%;
    padding: 16px 24px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    transition: all 0.3s ease;
    margin-top: 24px;
}

.continue-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 24px -8px rgba(102, 126, 234, 0.5);
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
    border-radius: 10px;
    transition: all 0.3s ease;
    margin-top: 16px;
}

.back-link:hover {
    background: #f3f4f6;
    color: #374151;
}

.summary-items {
    margin-bottom: 24px;
}

.summary-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 0;
    border-bottom: 1px solid #e5e7eb;
}

.item-info {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.item-name {
    font-weight: 600;
    color: #1f2937;
}

.item-qty {
    font-size: 13px;
    color: #6b7280;
}

.item-price {
    font-weight: 700;
    color: #667eea;
}

.summary-totals {
    padding: 20px 0;
    border-top: 2px solid #e5e7eb;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    color: #4b5563;
    font-size: 15px;
}

.summary-row.total {
    font-size: 20px;
    font-weight: 700;
    color: #1f2937;
    padding-top: 16px;
    margin-top: 8px;
    border-top: 2px solid #e5e7eb;
}

.secure-checkout {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 16px;
    background: #f0fdf4;
    border-radius: 10px;
    color: #15803d;
    font-weight: 600;
    margin-top: 20px;
}

@media (max-width: 992px) {
    .checkout-content {
        grid-template-columns: 1fr;
    }

    .order-summary-section {
        order: -1;
    }
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }

    .checkout-header h1 {
        font-size: 28px;
    }

    .customer-info-section,
    .order-summary-section {
        padding: 24px;
    }
}
</style>

<script>
document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    const requiredFields = this.querySelectorAll('[required]');
    let isValid = true;

    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            isValid = false;
            field.style.borderColor = '#ef4444';
        } else {
            field.style.borderColor = '#e5e7eb';
        }
    });

    if (!isValid) {
        e.preventDefault();
        alert('Please fill in all required fields');
    }
});
</script>
@endsection
