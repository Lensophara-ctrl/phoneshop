@extends('frontend.layouts.app')

@section('content')
<div class="bakong-page">
    <div class="bakong-card">
        <!-- Header -->
        <div class="bakong-header">
            <h2>Bakong Payment</h2>
            <p>Scan QR Code to Pay</p>
        </div>

        <!-- Amount -->
        <div class="bakong-amount">
            <div class="amount-label">TOTAL AMOUNT</div>
            <div class="amount-number">${{ number_format($totalAmount, 2) }}</div>
            <div class="invoice-label">Invoice: {{ $billNumber }}</div>
        </div>

        <!-- QR Code -->
        <div class="bakong-qr">
            <div class="qr-container">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=300x300&data={{ urlencode($khqr) }}" 
                     alt="Bakong QR Code"
                     class="qr-image">
            </div>
            <div class="qr-hint">
                <i class="fa-solid fa-mobile-screen"></i> Scan with Bakong App
            </div>
        </div>

        <!-- Status -->
        <div class="bakong-status" id="bakongStatus">
            <div class="status-indicator waiting">
                <div class="spinner"></div>
                <span id="statusText">Waiting for payment...</span>
            </div>
        </div>

        <!-- Check Button -->
        <div id="paymentActions">
            <button type="button" class="bakong-btn" id="checkPaymentBtn" onclick="manualCheckPayment()">
                <i class="fa-solid fa-rotate"></i> Check Payment Status
            </button>
            
            <!-- Test Button (Development Only) -->
            <button type="button" class="bakong-btn" onclick="simulatePayment()" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                <i class="fa-solid fa-flask"></i> Simulate Payment (Test)
            </button>
        </div>

        <!-- Invoice Section -->
        <div id="invoiceSection" style="display: none;">
            <div class="payment-success">
                <i class="fa-solid fa-check-circle"></i>
                <span>Payment Confirmed!</span>
            </div>
            
            <button type="button" class="bakong-btn" onclick="printInvoice()">
                <i class="fa-solid fa-print"></i> Print Invoice
            </button>
            
            <a href="{{ route('shop.success', $billNumber) }}?print=auto" class="bakong-btn">
                <i class="fa-solid fa-arrow-right"></i> Continue
            </a>
        </div>

        <!-- Instructions -->
        <div class="bakong-steps" id="paymentSteps">
            <h4>How to Pay</h4>
            <ol>
                <li>Open Bakong app</li>
                <li>Tap Scan QR</li>
                <li>Enter amount: ${{ number_format($totalAmount, 2) }}</li>
                <li>Confirm payment</li>
                <li>Wait for confirmation</li>
            </ol>
        </div>

        <a href="{{ route('shop.cart') }}" class="back-link">
            <i class="fa-solid fa-arrow-left"></i> Back to Cart
        </a>
    </div>

    <!-- Invoice Template -->
    <div id="printableInvoice" style="display: none;">
        <div class="invoice-container">
            <h2>PhoneShop</h2>
            <h3>INVOICE</h3>
            <p><strong>Bill No:</strong> {{ $billNumber }}</p>
            <p><strong>Date:</strong> {{ date('M d, Y') }}</p>
            <table id="invoiceTable">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody id="invoiceItems"></tbody>
                <tfoot>
                    <tr>
                        <td colspan="3"><strong>Total:</strong></td>
                        <td id="invoiceTotal">${{ number_format($totalAmount, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
            <p>Thank you!</p>
        </div>
    </div>

<style>
.bakong-page {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.bakong-card {
    background: white;
    border-radius: 20px;
    padding: 40px;
    max-width: 440px;
    width: 100%;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    text-align: center;
}

.bakong-header h2 {
    color: #1f2937;
    font-size: 28px;
    margin: 0 0 8px 0;
}

.bakong-header p {
    color: #6b7280;
    font-size: 15px;
    margin: 0 0 30px 0;
}

.bakong-amount {
    background: #f3f4f6;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 30px;
}

.amount-label {
    color: #6b7280;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 8px;
}

.amount-number {
    font-size: 42px;
    font-weight: 900;
    color: #111827;
    margin: 8px 0;
}

.invoice-label {
    color: #9ca3af;
    font-size: 13px;
}

.bakong-qr {
    margin-bottom: 30px;
}

.qr-container {
    background: white;
    padding: 15px;
    border-radius: 12px;
    display: inline-block;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    margin-bottom: 15px;
}

.qr-image {
    width: 240px;
    height: 240px;
    border-radius: 8px;
}

.qr-hint {
    color: #6b7280;
    font-size: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.qr-hint i {
    color: #667eea;
}

.bakong-status {
    margin-bottom: 20px;
}

.status-indicator {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    padding: 15px;
    border-radius: 10px;
    font-size: 15px;
    font-weight: 600;
}

.status-indicator.waiting {
    background: #fef3c7;
    color: #92400e;
}

.status-indicator.success {
    background: #d1fae5;
    color: #065f46;
}

.spinner {
    width: 16px;
    height: 16px;
    border: 3px solid rgba(146, 64, 14, 0.2);
    border-top-color: #92400e;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.bakong-btn {
    width: 100%;
    padding: 16px;
    border: none;
    border-radius: 12px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    font-size: 16px;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    margin-bottom: 15px;
    text-decoration: none;
    transition: transform 0.2s;
}

.bakong-btn:hover {
    transform: translateY(-2px);
}

.bakong-btn:disabled {
    background: #9ca3af;
    cursor: not-allowed;
}

.payment-success {
    background: #d4edda;
    color: #155724;
    padding: 15px;
    border-radius: 10px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    font-weight: 600;
}

.payment-success i {
    font-size: 24px;
}

.bakong-steps {
    background: #f9fafb;
    border-radius: 12px;
    padding: 20px;
    text-align: left;
    margin-bottom: 20px;
}

.bakong-steps h4 {
    margin: 0 0 15px 0;
    font-size: 16px;
    color: #111827;
}

.bakong-steps ol {
    margin: 0;
    padding-left: 20px;
}

.bakong-steps li {
    margin-bottom: 8px;
    color: #374151;
}

.back-link {
    color: #6b7280;
    text-decoration: none;
    font-size: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 10px;
}

.back-link:hover {
    color: #374151;
}

@media print {
    body * { visibility: hidden; }
    #printableInvoice, #printableInvoice * { visibility: visible; }
    #printableInvoice {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        display: block !important;
    }
}
</style>

<script>
const billNumber = '{{ $billNumber }}';
const totalAmount = {{ $totalAmount }};
let isPaid = false;
let checkTimer;

// Auto-check payment every 2 seconds
function checkPayment() {
    fetch(`/api/payment/check-status/${billNumber}`)
        .then(res => res.json())
        .then(data => {
            if (data.paid && !isPaid) {
                isPaid = true;
                clearInterval(checkTimer);
                showSuccess();
            }
        })
        .catch(err => console.log('Check error:', err));
}

// Manual check
function manualCheckPayment() {
    const btn = document.getElementById('checkPaymentBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Checking...';
    
    fetch(`/api/payment/check-status/${billNumber}`)
        .then(res => res.json())
        .then(data => {
            console.log('Payment check response:', data);
            if (data.paid) {
                isPaid = true;
                clearInterval(checkTimer);
                showSuccess();
            } else {
                alert(data.message || 'Payment not confirmed yet. Please scan and pay with Bakong app.');
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-rotate"></i> Check Payment Status';
            }
        })
        .catch(err => {
            console.error('Check error:', err);
            alert('Error checking payment. Please try again.');
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-rotate"></i> Check Payment Status';
        });
}

// Simulate payment (for testing only)
function simulatePayment() {
    if (!confirm('This will mark the payment as completed for testing. Continue?')) {
        return;
    }
    
    fetch(`/api/payment/mark-paid/${billNumber}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            isPaid = true;
            clearInterval(checkTimer);
            showSuccess();
            alert('✅ Payment simulated successfully! Check your Telegram for notification.');
        } else {
            alert('Error: ' + (data.message || 'Failed to simulate payment'));
        }
    })
    .catch(err => {
        console.error('Simulate error:', err);
        alert('Error simulating payment');
    });
}

function showSuccess() {
    document.getElementById('bakongStatus').innerHTML = `
        <div class="status-indicator success">
            <i class="fa-solid fa-check-circle"></i>
            <span>Payment Confirmed!</span>
        </div>
    `;
    document.getElementById('paymentActions').style.display = 'none';
    document.getElementById('paymentSteps').style.display = 'none';
    document.getElementById('invoiceSection').style.display = 'block';
    
    // Load invoice data
    const cart = @json($cart);
    const tbody = document.getElementById('invoiceItems');
    Object.entries(cart).forEach(([id, item]) => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${item.name}</td>
            <td>${item.qty}</td>
            <td>$${parseFloat(item.price).toFixed(2)}</td>
            <td>$${(item.price * item.qty).toFixed(2)}</td>
        `;
        tbody.appendChild(row);
    });
}

function printInvoice() {
    window.print();
}

// Start auto-check when page loads
document.addEventListener('DOMContentLoaded', function() {
    checkTimer = setInterval(checkPayment, 2000);
});
</script>

@endsection
