<?php $__env->startSection('content'); ?>

<div class="row justify-content-center mb-5">
    <div class="col-md-8">
        <div class="text-center no-print mb-4">
            <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                <i class="fa-solid fa-circle-check fs-1 text-success"></i>
            </div>
            <h2 class="fw-bold">Payment Successful!</h2>
            <p class="text-muted">Thank you for your purchase. Your order has been confirmed.</p>
            
            <div class="d-flex justify-content-center gap-3 mt-4 flex-wrap">
                <button onclick="window.print()" class="btn btn-primary rounded-pill px-4 shadow-sm">
                    <i class="fa-solid fa-print me-2"></i>Print Invoice
                </button>
                <button onclick="downloadPDF()" class="btn btn-info rounded-pill px-4 shadow-sm">
                    <i class="fa-solid fa-file-pdf me-2"></i>Download PDF
                </button>
                <a href="<?php echo e(route('shop.home')); ?>" class="btn btn-outline-secondary rounded-pill px-4">
                    <i class="fa-solid fa-home me-2"></i>Return to Shop
                </a>
            </div>
        </div>

        <!-- Invoice Card -->
        <div class="card border-0 shadow-lg invoice-container" id="invoice">
            <div class="card-body p-5">
                <div class="row mb-5">
                    <div class="col-6">
                        <h3 class="fw-bold text-primary mb-1"><i class="fa-solid fa-mobile-screen-button me-2"></i>PhoneShop</h3>
                        <p class="text-muted small mb-0">123 Modern Street, Phnom Penh</p>
                        <p class="text-muted small">support@phoneshop.com</p>
                    </div>
                    <div class="col-3 text-center">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=<?php echo e(urlencode($billNo)); ?>" 
                             alt="Bill QR" 
                             style="width: 100px; height: 100px;">
                        <div class="text-muted x-small mt-2">Bill Code</div>
                    </div>
                    <div class="col-3 text-end">
                        <h4 class="fw-bold text-uppercase mb-1">Invoice</h4>
                        <div class="text-muted small">Bill No: <strong>#<?php echo e($billNo); ?></strong></div>
                        <div class="text-muted small">Date: <?php echo e(date('M d, Y')); ?></div>
                    </div>
                </div>

                <div class="row mb-5">
                    <div class="col-12">
                        <h6 class="text-muted text-uppercase fw-bold small mb-2">Billed To:</h6>
                        <div class="fw-bold h5 mb-1"><?php echo e($customer->name); ?></div>
                        <div class="text-muted small"><?php echo e($customer->email); ?></div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-borderless">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-3 py-2 small text-uppercase fw-bold text-muted">Item Description</th>
                                <th class="py-2 small text-uppercase fw-bold text-muted text-center">Qty</th>
                                <th class="py-2 small text-uppercase fw-bold text-muted text-end px-3">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $sale->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                            <tr class="border-bottom border-secondary border-opacity-10">
                                <td class="px-3 py-3">
                                    <div class="fw-bold"><?php echo e($item->phone->name); ?></div>
                                    <small class="text-muted"><?php echo e($item->phone->category->name); ?></small>
                                </td>
                                <td class="py-3 text-center"><?php echo e($item->qty); ?></td>
                                <td class="py-3 text-end px-3 fw-bold">$<?php echo e(number_format($item->subtotal, 2)); ?></td>
                            </tr>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2" class="text-end py-4 fw-bold">Total Amount:</td>
                                <td class="text-end py-4 px-3">
                                    <span class="h4 fw-bold text-primary">$<?php echo e(number_format($totalAmount, 2)); ?></span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="mt-5 border-top pt-4 text-center">
                    <p class="text-muted small mb-0">Thank you for choosing PhoneShop!</p>
                    <p class="text-muted x-small">This is a computer-generated invoice.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .invoice-container {
        background: linear-gradient(135deg, rgba(79, 70, 229, 0.02) 0%, rgba(123, 92, 246, 0.02) 100%);
    }

    @media print {
        .no-print, .navbar, .footer, .btn-close {
            display: none !important;
        }
        body {
            background-color: white !important;
            padding: 0 !important;
        }
        .container-fluid, .container {
            max-width: 100% !important;
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        .row, .col-md-8 {
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        .invoice-container {
            box-shadow: none !important;
            border: none !important;
            width: 100% !important;
            background: white !important;
            margin: 0 !important;
        }
        .card-body {
            padding: 20mm !important;
        }
        .table {
            margin-bottom: 0 !important;
        }
    }

    @page {
        size: A4;
        margin: 0;
    }
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    // Play success sound (celebration)
    function playSuccessSound() {
        try {
            // Create a more celebratory sound using Web Audio API
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            
            // Play a pleasant success chime
            const playChime = (delay, frequency, duration) => {
                setTimeout(() => {
                    const oscillator = audioContext.createOscillator();
                    const gainNode = audioContext.createGain();
                    
                    oscillator.connect(gainNode);
                    gainNode.connect(audioContext.destination);
                    
                    oscillator.frequency.value = frequency;
                    oscillator.type = 'sine';
                    
                    gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
                    gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + duration);
                    
                    oscillator.start(audioContext.currentTime);
                    oscillator.stop(audioContext.currentTime + duration);
                }, delay);
            };
            
            // Play a pleasant ascending chime sequence
            playChime(0, 523.25, 0.3);    // C5
            playChime(150, 659.25, 0.3); // E5
            playChime(300, 783.99, 0.4); // G5
            
        } catch (e) {
            console.log('Audio playback not supported');
        }
    }

    function downloadPDF() {
        const element = document.getElementById('invoice');
        const opt = {
            margin: 10,
            filename: 'Invoice-<?php echo e($billNo); ?>.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2 },
            jsPDF: { orientation: 'portrait', unit: 'mm', format: 'a4' }
        };
        html2pdf().set(opt).from(element).save();
    }

// Auto-print functionality when redirected from payment page
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('print') === 'auto') {
            // Show browser alert
            alert('✅ Payment Successful! Your invoice is ready.');
            
            // Play success sound
            playSuccessSound();
            
            // Show success notification
            showPaymentSuccessNotification();
            
            // Auto-print after a short delay to let the page render and images load
            setTimeout(() => {
                window.print();
            }, 1500);
            
            // Remove the query parameter to avoid auto-print on refresh
            setTimeout(() => {
                window.history.replaceState({}, document.title, window.location.pathname);
            }, 2000);
        }
    });

    // Show payment success notification
    function showPaymentSuccessNotification() {
        // Create notification HTML
        const notificationHtml = `
            <div class="payment-success-notification" style="
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 9999;
                background: linear-gradient(135deg, #10b981 0%, #059669 100%);
                color: white;
                padding: 20px 30px;
                border-radius: 12px;
                box-shadow: 0 10px 25px rgba(0,0,0,0.2);
                animation: slideInRight 0.5s ease-out;
                max-width: 400px;
            ">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <div style="
                        width: 50px;
                        height: 50px;
                        background: rgba(255,255,255,0.2);
                        border-radius: 50%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        font-size: 24px;
                    ">
                        ✓
                    </div>
                    <div>
                        <h5 style="margin: 0 0 5px 0; font-weight: bold;">Payment Successful!</h5>
                        <p style="margin: 0; font-size: 14px; opacity: 0.9;">Your invoice is ready. Print dialog will open shortly.</p>
                    </div>
                </div>
            </div>
        `;
        
        // Add to body
        document.body.insertAdjacentHTML('beforeend', notificationHtml);
        
        // Remove after 6 seconds
        setTimeout(() => {
            const notification = document.querySelector('.payment-success-notification');
            if (notification) {
                notification.style.animation = 'slideOutRight 0.5s ease-out forwards';
                setTimeout(() => notification.remove(), 500);
            }
        }, 6000);
    }

    // Add animations for notification
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideInRight {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(style);
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('frontend.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Practice\phoneshop\phoneshop\resources\views/frontend/success.blade.php ENDPATH**/ ?>