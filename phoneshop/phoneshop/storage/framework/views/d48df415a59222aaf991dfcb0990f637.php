

<?php $__env->startSection('content'); ?>
<div class="bakong-page">
    <div class="bakong-card">
        <!-- Header -->
        <div class="bakong-header">
            <a href="<?php echo e(route('shop.cart')); ?>" class="back-btn">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <h2>Pay</h2>
            <div class="menu-btn">
                <i class="fa-solid fa-ellipsis-vertical"></i>
            </div>
        </div>

        <!-- QR Code Section -->
        <div class="qr-section">
            <!-- Barcode (optional - using invoice number) -->
            <div class="barcode-section">
                <svg class="barcode" viewBox="0 0 200 40" xmlns="http://www.w3.org/2000/svg">
                    <rect x="0" y="0" width="3" height="40" fill="#000"/>
                    <rect x="5" y="0" width="2" height="40" fill="#000"/>
                    <rect x="9" y="0" width="4" height="40" fill="#000"/>
                    <rect x="15" y="0" width="2" height="40" fill="#000"/>
                    <rect x="19" y="0" width="3" height="40" fill="#000"/>
                    <rect x="24" y="0" width="2" height="40" fill="#000"/>
                    <rect x="28" y="0" width="4" height="40" fill="#000"/>
                    <rect x="34" y="0" width="2" height="40" fill="#000"/>
                    <rect x="38" y="0" width="3" height="40" fill="#000"/>
                    <rect x="43" y="0" width="2" height="40" fill="#000"/>
                    <rect x="47" y="0" width="4" height="40" fill="#000"/>
                    <rect x="53" y="0" width="3" height="40" fill="#000"/>
                    <rect x="58" y="0" width="2" height="40" fill="#000"/>
                    <rect x="62" y="0" width="4" height="40" fill="#000"/>
                    <rect x="68" y="0" width="2" height="40" fill="#000"/>
                    <rect x="72" y="0" width="3" height="40" fill="#000"/>
                    <rect x="77" y="0" width="2" height="40" fill="#000"/>
                    <rect x="81" y="0" width="4" height="40" fill="#000"/>
                    <rect x="87" y="0" width="3" height="40" fill="#000"/>
                    <rect x="92" y="0" width="2" height="40" fill="#000"/>
                    <rect x="96" y="0" width="4" height="40" fill="#000"/>
                    <rect x="102" y="0" width="2" height="40" fill="#000"/>
                    <rect x="106" y="0" width="3" height="40" fill="#000"/>
                    <rect x="111" y="0" width="2" height="40" fill="#000"/>
                    <rect x="115" y="0" width="4" height="40" fill="#000"/>
                    <rect x="121" y="0" width="3" height="40" fill="#000"/>
                    <rect x="126" y="0" width="2" height="40" fill="#000"/>
                    <rect x="130" y="0" width="4" height="40" fill="#000"/>
                    <rect x="136" y="0" width="2" height="40" fill="#000"/>
                    <rect x="140" y="0" width="3" height="40" fill="#000"/>
                    <rect x="145" y="0" width="2" height="40" fill="#000"/>
                    <rect x="149" y="0" width="4" height="40" fill="#000"/>
                    <rect x="155" y="0" width="3" height="40" fill="#000"/>
                    <rect x="160" y="0" width="2" height="40" fill="#000"/>
                    <rect x="164" y="0" width="4" height="40" fill="#000"/>
                    <rect x="170" y="0" width="2" height="40" fill="#000"/>
                    <rect x="174" y="0" width="3" height="40" fill="#000"/>
                    <rect x="179" y="0" width="2" height="40" fill="#000"/>
                    <rect x="183" y="0" width="4" height="40" fill="#000"/>
                    <rect x="189" y="0" width="3" height="40" fill="#000"/>
                    <rect x="194" y="0" width="2" height="40" fill="#000"/>
                </svg>
                <div class="barcode-number"><?php echo e($billNumber); ?></div>
            </div>

            <!-- QR Code -->
            <div class="qr-container">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=<?php echo e(urlencode($khqr)); ?>" 
                     alt="Bakong QR Code"
                     class="qr-image">
                <div class="qr-logo">
                    <i class="fa-solid fa-qrcode"></i>
                </div>
            </div>

            <!-- Refresh Text -->
            <div class="refresh-text">
                <i class="fa-solid fa-rotate"></i> Refresh every <span id="timerDisplay">10</span> seconds automatically
            </div>
        </div>

        <!-- Balance Section -->
        <div class="balance-section">
            <div class="balance-label">eWallet Balance</div>
            <div class="balance-amount">$<?php echo e(number_format($totalAmount, 2)); ?></div>
        </div>

        <!-- Payment Status -->
        <div class="payment-status" id="paymentStatus">
            <div class="status-indicator waiting">
                <div class="spinner"></div>
                <span>Waiting for payment...</span>
            </div>
        </div>

        <!-- Check Payment Button -->
        <button type="button" class="check-payment-btn" id="checkPaymentBtn" onclick="console.log('Button clicked!'); manualCheckPayment();">
            <i class="fa-solid fa-rotate"></i> Check Payment Status
        </button>

        <!-- Invoice Section (Hidden until payment confirmed) -->
        <div id="invoiceSection" style="display: none;">
            <div class="payment-success">
                <i class="fa-solid fa-check-circle"></i>
                <span>Payment Confirmed!</span>
            </div>
            
            <a href="<?php echo e(route('shop.success', $billNumber)); ?>" class="continue-btn">
                Continue <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>
    </div>

    <!-- Hidden Invoice Template for Printing -->
    <div id="printableInvoice" style="display: none;">
        <div class="invoice-container">
            <div class="invoice-header">
                <h2>PhoneShop</h2>
                <p>123 Modern Street, Phnom Penh</p>
                <p>support@phoneshop.com</p>
            </div>
            <div class="invoice-info">
                <h3>INVOICE</h3>
                <p><strong>Bill No:</strong> <?php echo e($billNumber); ?></p>
                <p><strong>Date:</strong> <?php echo e(date('M d, Y')); ?></p>
            </div>
            <div class="invoice-customer">
                <h4>Billed To:</h4>
                <p><strong>Customer:</strong> <?php echo e(auth()->user()->name ?? 'Guest'); ?></p>
                <p><strong>Email:</strong> <?php echo e(auth()->user()->email ?? 'N/A'); ?></p>
            </div>
            <table class="invoice-table">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody id="invoiceItems">
                    <!-- Items will be loaded dynamically -->
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3"><strong>Total:</strong></td>
                        <td id="invoiceTotal">$<?php echo e(number_format($totalAmount, 2)); ?></td>
                    </tr>
                </tfoot>
            </table>
            <div class="invoice-footer">
                <p>Thank you for choosing PhoneShop!</p>
                <p>This is a computer-generated invoice.</p>
            </div>
        </div>
    </div>

<style>
* { 
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

.bakong-page {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0;
    background: linear-gradient(180deg, #4FC3F7 0%, #29B6F6 50%, #03A9F4 100%);
    position: relative;
}

.bakong-card {
    background: white;
    border-radius: 30px;
    padding: 0;
    width: 100%;
    max-width: 480px;
    min-height: 100vh;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    position: relative;
    display: flex;
    flex-direction: column;
}

/* Header */
.bakong-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px 20px;
    background: linear-gradient(180deg, #4FC3F7 0%, #29B6F6 100%);
    border-radius: 30px 30px 0 0;
}

.bakong-header h2 {
    color: white;
    font-size: 20px;
    font-weight: 600;
    flex: 1;
    text-align: center;
}

.back-btn, .menu-btn {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 18px;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.3s ease;
}

.back-btn:hover, .menu-btn:hover {
    opacity: 0.8;
}

/* QR Section */
.qr-section {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 30px 20px;
    background: white;
}

/* Barcode */
.barcode-section {
    margin-bottom: 30px;
    text-align: center;
}

.barcode {
    width: 280px;
    height: 50px;
    margin-bottom: 8px;
}

.barcode-number {
    color: #9E9E9E;
    font-size: 12px;
    letter-spacing: 2px;
    font-family: 'Courier New', monospace;
}

/* QR Code */
.qr-container {
    position: relative;
    margin-bottom: 20px;
}

.qr-image {
    width: 240px;
    height: 240px;
    display: block;
    border-radius: 12px;
}

.qr-logo {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 50px;
    height: 50px;
    background: #29B6F6;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

/* Refresh Text */
.refresh-text {
    color: #9E9E9E;
    font-size: 13px;
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 30px;
}

.refresh-text i {
    color: #29B6F6;
    font-size: 14px;
}

.refresh-text span {
    color: #29B6F6;
    font-weight: 600;
}

/* Balance Section */
.balance-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    background: white;
    border-top: 1px solid #F5F5F5;
}

.balance-label {
    color: #757575;
    font-size: 14px;
    font-weight: 500;
}

.balance-amount {
    color: #212121;
    font-size: 20px;
    font-weight: 700;
}

/* Payment Status */
.payment-status {
    padding: 15px 20px;
    background: white;
}

.status-indicator {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 12px;
    border-radius: 12px;
    font-size: 13px;
    font-weight: 600;
    background: #FFF3E0;
    color: #F57C00;
}

.status-indicator.waiting {
    background: #FFF3E0;
    color: #F57C00;
}

.status-indicator.success {
    background: #E8F5E9;
    color: #2E7D32;
}

.spinner {
    width: 16px;
    height: 16px;
    border: 2px solid rgba(245, 124, 0, 0.3);
    border-top-color: #F57C00;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Check Payment Button */
.check-payment-btn {
    margin: 15px 20px;
    padding: 16px;
    background: linear-gradient(135deg, #29B6F6, #03A9F4);
    color: white;
    border: none;
    border-radius: 14px;
    font-size: 15px;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(3, 169, 244, 0.3);
}

.check-payment-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(3, 169, 244, 0.4);
}

.check-payment-btn:active {
    transform: translateY(0);
}

.check-payment-btn:disabled {
    background: #BDBDBD;
    cursor: not-allowed;
    box-shadow: none;
}

/* Payment Success */
.payment-success {
    background: linear-gradient(135deg, #E8F5E9, #C8E6C9);
    color: #2E7D32;
    padding: 16px;
    border-radius: 14px;
    margin: 15px 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    font-weight: 700;
    font-size: 15px;
}

.payment-success i {
    font-size: 24px;
}

/* Continue Button */
.continue-btn {
    margin: 0 20px 20px;
    padding: 16px;
    background: linear-gradient(135deg, #66BB6A, #43A047);
    color: white;
    border: none;
    border-radius: 14px;
    font-size: 15px;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    text-decoration: none;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(67, 160, 71, 0.3);
}

.continue-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(67, 160, 71, 0.4);
}

/* Responsive */
@media (max-width: 480px) {
    .bakong-card {
        border-radius: 0;
        max-width: 100%;
    }
    
    .bakong-header {
        border-radius: 0;
    }
    
    .qr-image {
        width: 200px;
        height: 200px;
    }
    
    .barcode {
        width: 240px;
    }
}

@media (min-width: 481px) {
    .bakong-page {
        padding: 20px;
    }
    
    .bakong-card {
        min-height: auto;
        max-height: 95vh;
        overflow-y: auto;
    }
}

/* Print styles */
@media print {
    body * {
        visibility: hidden;
    }
    #printableInvoice, #printableInvoice * {
        visibility: visible;
    }
    #printableInvoice {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        display: block !important;
        padding: 20px;
        background: white;
    }
}

/* Invoice template styles */
#printableInvoice {
    font-family: Arial, sans-serif;
    max-width: 600px;
    margin: 0 auto;
    padding: 30px;
    background: white;
}

#printableInvoice .invoice-container {
    border: 2px solid #29B6F6;
    border-radius: 10px;
    padding: 30px;
}

#printableInvoice .invoice-header {
    text-align: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid #e5e7eb;
}

#printableInvoice .invoice-header h2 {
    color: #29B6F6;
    margin: 0 0 10px 0;
    font-size: 28px;
}

#printableInvoice .invoice-header p {
    margin: 3px 0;
    color: #6b7280;
    font-size: 14px;
}

#printableInvoice .invoice-info {
    margin-bottom: 20px;
}

#printableInvoice .invoice-info h3 {
    color: #1f2937;
    margin: 0 0 10px 0;
    font-size: 24px;
}

#printableInvoice .invoice-info p {
    margin: 5px 0;
    color: #4b5563;
}

#printableInvoice .invoice-customer {
    margin-bottom: 20px;
    padding: 15px;
    background: #f9fafb;
    border-radius: 8px;
}

#printableInvoice .invoice-customer h4 {
    margin: 0 0 10px 0;
    color: #374151;
}

#printableInvoice .invoice-customer p {
    margin: 3px 0;
    color: #4b5563;
}

#printableInvoice .invoice-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

#printableInvoice .invoice-table th {
    background: #29B6F6;
    color: white;
    padding: 12px;
    text-align: left;
}

#printableInvoice .invoice-table td {
    padding: 12px;
    border-bottom: 1px solid #e5e7eb;
}

#printableInvoice .invoice-table tfoot td {
    border-top: 2px solid #29B6F6;
    font-size: 18px;
    color: #1f2937;
}

#printableInvoice .invoice-footer {
    text-align: center;
    padding-top: 20px;
    border-top: 1px solid #e5e7eb;
    color: #6b7280;
    font-size: 14px;
}
</style>

<script>
// Real Bakong Payment Check
const billNumber = '<?php echo e($billNumber); ?>';
const totalAmount = <?php echo e($totalAmount); ?>;
let isPaid = false;
let checkTimer;
let countdownTimer;
let countdownSeconds = 10;

// Toast notification
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.style.cssText = `
        position: fixed; top: 20px; right: 20px; z-index: 10000;
        background: ${type === 'success' ? '#E8F5E9' : '#FFF3E0'};
        color: ${type === 'success' ? '#2E7D32' : '#F57C00'};
        padding: 15px 20px; border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        font-size: 14px; font-weight: 600;
    `;
    toast.innerHTML = `<i class="fa-solid fa-${type === 'success' ? 'check' : 'info'}-circle"></i> ${message}`;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 5000);
}

// Countdown
function updateCountdown() {
    if (isPaid) return;
    countdownSeconds--;
    const el = document.getElementById('timerDisplay');
    if (el) el.textContent = countdownSeconds;
    if (countdownSeconds <= 0) {
        countdownSeconds = 10;
        checkRealPayment();
    }
}

function startCountdown() {
    updateCountdown();
    countdownTimer = setInterval(updateCountdown, 1000);
}

// Check payment
async function manualCheckPayment() {
    console.log('Check button clicked');
    if (isPaid) {
        showToast('Payment already confirmed!', 'success');
        return;
    }
    
    const btn = document.getElementById('checkPaymentBtn');
    const status = document.getElementById('paymentStatus');
    
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<div class="spinner"></div> Checking...';
    }
    
    try {
        const response = await fetch(`/api/payment/check-status/${billNumber}`);
        const data = await response.json();
        console.log('Response:', data);
        
        if (data.success && data.paid) {
            isPaid = true;
            clearInterval(countdownTimer);
            clearInterval(checkTimer);
            
            if (status) {
                status.innerHTML = `<div class="status-indicator success"><i class="fa-solid fa-check-circle"></i> Payment Confirmed!</div>`;
            }
            
            document.getElementById('invoiceSection').style.display = 'block';
            if (btn) btn.style.display = 'none';
            
            showToast('Payment confirmed!', 'success');
        } else {
            if (status) {
                status.innerHTML = `<div class="status-indicator waiting"><div class="spinner"></div> Waiting for payment...</div>`;
            }
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-rotate"></i> Check Payment Status';
            }
            showToast(data.message || 'Payment not detected yet', 'info');
        }
    } catch (error) {
        console.error('Error:', error);
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-rotate"></i> Check Payment Status';
        }
        showToast('Error checking payment', 'error');
    }
}

// Auto check
async function checkRealPayment() {
    if (isPaid) return;
    try {
        const response = await fetch(`/api/payment/check-status/${billNumber}`);
        const data = await response.json();
        if (data.success && data.paid) {
            isPaid = true;
            clearInterval(countdownTimer);
            clearInterval(checkTimer);
            
            const status = document.getElementById('paymentStatus');
            if (status) {
                status.innerHTML = `<div class="status-indicator success"><i class="fa-solid fa-check-circle"></i> Payment Confirmed!</div>`;
            }
            
            document.getElementById('invoiceSection').style.display = 'block';
            document.getElementById('checkPaymentBtn').style.display = 'none';
            showToast('Payment confirmed!', 'success');
        }
    } catch (error) {
        console.error('Auto check error:', error);
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    console.log('Page loaded - Bill:', billNumber);
    startCountdown();
    checkTimer = setInterval(checkRealPayment, 2000);
});
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('frontend.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Practice\phoneshop\phoneshop\resources\views/frontend/payment.blade.php ENDPATH**/ ?>