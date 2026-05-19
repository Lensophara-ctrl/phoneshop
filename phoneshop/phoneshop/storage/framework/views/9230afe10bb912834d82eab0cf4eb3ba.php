<?php $__env->startSection('content'); ?>

<style>
    /* Modern POS Styling */
    :root {
        --pos-primary: #0ea5e9;
        --pos-secondary: #64748b;
        --pos-success: #10b981;
        --pos-warning: #f59e0b;
        --pos-danger: #ef4444;
        --pos-bg: #0f172a;
        --pos-card-bg: #1e293b;
        --pos-border: rgba(255, 255, 255, 0.1);
        --pos-text: #f1f5f9;
        --pos-text-muted: #94a3b8;
    }

    /* Override for POS page to ensure modern dark look regardless of theme toggle if desired, 
       but here we'll just enhance the current theme */
    
    .pos-container {
        padding: 1.5rem;
        background: transparent;
    }

    .modern-card {
        background: var(--bs-body-bg);
        border: 1px solid var(--bs-border-color);
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        transition: all 0.3s ease;
    }

    [data-bs-theme="dark"] .modern-card {
        background: #1e293b;
        border-color: rgba(255, 255, 255, 0.05);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }

    .product-grid-container {
        max-height: calc(100vh - 280px) !important;
        padding-right: 8px;
    }

    .product-grid-container::-webkit-scrollbar {
        width: 6px;
    }

    .product-grid-container::-webkit-scrollbar-thumb {
        background: rgba(0, 0, 0, 0.1);
        border-radius: 10px;
    }

    [data-bs-theme="dark"] .product-grid-container::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.1);
    }

    .modern-product-card {
        border-radius: 12px;
        border: 1px solid transparent;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        position: relative;
    }

    .modern-product-card:hover {
        transform: translateY(-8px);
        border-color: var(--pos-primary);
        box-shadow: 0 12px 24px rgba(14, 165, 233, 0.15);
    }

    .modern-product-card .img-wrapper {
        height: 120px;
        overflow: hidden;
        border-radius: 10px;
        margin: 8px;
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    [data-bs-theme="dark"] .modern-product-card .img-wrapper {
        background: #0f172a;
    }

    .modern-product-card img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        transition: transform 0.5s ease;
    }

    .modern-product-card:hover img {
        transform: scale(1.1);
    }

    .category-pill {
        padding: 6px 18px;
        border-radius: 30px;
        font-size: 0.85rem;
        font-weight: 500;
        border: 1px solid var(--bs-border-color);
        background: var(--bs-body-bg);
        color: var(--bs-body-color);
        transition: all 0.2s;
        cursor: pointer;
        white-space: nowrap;
    }

    .category-pill.active {
        background: var(--pos-primary);
        color: white;
        border-color: var(--pos-primary);
        box-shadow: 0 4px 12px rgba(14, 165, 233, 0.3);
    }

    .order-item-row {
        border-bottom: 1px solid var(--bs-border-color);
        transition: background 0.2s;
    }

    .order-item-row:hover {
        background: rgba(0, 0, 0, 0.02);
    }

    [data-bs-theme="dark"] .order-item-row:hover {
        background: rgba(255, 255, 255, 0.02);
    }

    .qty-control {
        display: flex;
        align-items: center;
        background: rgba(0, 0, 0, 0.05);
        border-radius: 8px;
        padding: 2px;
    }

    [data-bs-theme="dark"] .qty-control {
        background: rgba(255, 255, 255, 0.05);
    }

    .qty-btn {
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        border: none;
        background: transparent;
        color: var(--bs-body-color);
        transition: all 0.2s;
    }

    .qty-btn:hover {
        background: var(--pos-primary);
        color: white;
    }

    .summary-box {
        background: #f8fafc;
        border-radius: 12px;
        padding: 1.5rem;
    }

    [data-bs-theme="dark"] .summary-box {
        background: #0f172a;
    }

    .total-display {
        font-size: 2rem;
        font-weight: 800;
        color: var(--pos-primary);
        letter-spacing: -1px;
    }

    .complete-btn {
        border-radius: 12px;
        padding: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: all 0.3s;
    }

    .complete-btn:not(:disabled) {
        background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
        border: none;
        box-shadow: 0 4px 15px rgba(14, 165, 233, 0.4);
    }

    .complete-btn:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(14, 165, 233, 0.5);
    }

    .search-modern {
        border-radius: 12px;
        padding-left: 45px;
        background: #f8fafc;
        border: 1px solid var(--bs-border-color);
        height: 48px;
    }

    [data-bs-theme="dark"] .search-modern {
        background: #0f172a;
    }

    .search-icon {
        position: absolute;
        left: 15px;
        top: 14px;
        color: var(--pos-secondary);
    }

    .stock-badge {
        position: absolute;
        top: 12px;
        right: 12px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 700;
        z-index: 2;
    }

    /* Money animation */
    @keyframes moneyFloat {
        0% {
            transform: translateY(0) rotate(0deg);
            opacity: 1;
        }
        100% {
            transform: translateY(-100px) rotate(360deg);
            opacity: 0;
        }
    }

    .money-icon {
        position: fixed;
        font-size: 2rem;
        animation: moneyFloat 2s ease-out forwards;
        pointer-events: none;
        z-index: 9999;
    }
</style>

<div class="pos-container">
    <div class="row g-4">
        <!-- Left Column - Products -->
        <div class="col-lg-8">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold mb-0">POS System</h4>
                <div class="text-muted small fw-medium" id="current-time"></div>
            </div>

            <!-- Search and Filter -->
            <div class="modern-card p-3 mb-4">
                <div class="row g-3 align-items-center">
                    <div class="col-md-5 position-relative">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" id="product-search" class="form-control search-modern" placeholder="Search by name or barcode...">
                    </div>
                    <div class="col-md-7">
                        <div class="d-flex gap-2 overflow-auto pb-2" id="category-filters">
                            <div class="category-pill active" data-category="all">All Items</div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                <div class="category-pill" data-category="<?php echo e($category->id); ?>"><?php echo e($category->name); ?></div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="row row-cols-2 row-cols-md-3 row-cols-xl-4 g-3 product-grid-container mb-4" id="products-grid" style="overflow-y: auto;">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $phones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                <div class="col product-item" data-category="<?php echo e($product->category_id); ?>" data-name="<?php echo e(strtolower($product->name)); ?>">
                    <div class="card h-100 modern-card modern-product-card <?php echo e($product->qty <= 0 ? 'opacity-50' : ''); ?>" 
                         onclick="<?php echo e($product->qty > 0 ? 'addToCart('.json_encode($product).')' : 'alert(\'Out of stock!\')'); ?>">
                        
                        <span class="stock-badge <?php echo e($product->qty > 10 ? 'bg-success text-white' : ($product->qty > 0 ? 'bg-warning text-dark' : 'bg-danger text-white')); ?>">
                            <?php echo e($product->qty > 0 ? $product->qty . ' in stock' : 'Out of stock'); ?>

                        </span>

                        <div class="img-wrapper">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->image): ?>
                                <img src="<?php echo e(asset('storage/'.$product->image)); ?>" alt="<?php echo e($product->name); ?>">
                            <?php else: ?>
                                <i class="fas fa-box fa-3x text-muted opacity-25"></i>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        
                        <div class="card-body p-3 pt-0">
                            <h6 class="fw-bold mb-1 text-truncate"><?php echo e($product->name); ?></h6>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <span class="text-primary fw-bold h6 mb-0">$<?php echo e(number_format($product->price, 2)); ?></span>
                                <div class="btn btn-sm btn-outline-primary rounded-circle" style="width: 28px; height: 28px; padding: 0; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-plus small"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>

            <!-- Cart Section -->
            <div class="modern-card">
                <div class="card-header bg-transparent border-0 p-3 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0"><i class="fas fa-shopping-bag me-2 text-primary"></i>Current Order</h6>
                    <button class="btn btn-sm btn-outline-danger border-0" onclick="clearCart()">
                        <i class="fas fa-trash-can me-1"></i> Clear
                    </button>
                </div>
                <div class="table-responsive" style="min-height: 250px;">
                    <table class="table align-middle mb-0">
                        <thead class="text-muted small text-uppercase">
                            <tr>
                                <th class="ps-3">Product</th>
                                <th class="text-center">Price</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end pe-3">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="cart-table-body">
                            <!-- Dynamically populated -->
                        </tbody>
                    </table>
                    
                    <div id="empty-cart-msg" class="text-center py-5">
                        <div class="opacity-25 mb-3">
                            <i class="fas fa-shopping-basket fa-4x"></i>
                        </div>
                        <h6 class="text-muted">No items in order</h6>
                        <p class="small text-muted opacity-75">Click on products to add them here</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-lg-4">
            <div class="sticky-top" style="top: 1.5rem; z-index: 10;">
                <!-- Order Summary -->
                <form action="<?php echo e(route('sales.store')); ?>" method="POST" id="pos-form">
                    <?php echo csrf_field(); ?>
                    <div class="modern-card p-4 mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="fw-bold mb-0">Summary</h5>
                            <div class="btn-group btn-group-sm p-1 bg-light dark-bg-dark rounded-pill" style="background: rgba(0,0,0,0.05);">
                                <input type="radio" class="btn-check" name="currency_toggle" id="currency_usd" value="USD" checked>
                                <label class="btn btn-sm rounded-pill px-3 border-0 py-1" for="currency_usd">USD</label>
                                <input type="radio" class="btn-check" name="currency_toggle" id="currency_khr" value="KHR">
                                <label class="btn btn-sm rounded-pill px-3 border-0 py-1" for="currency_khr">KHR</label>
                            </div>
                        </div>

                        <div class="summary-box mb-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Subtotal</span>
                                <span class="fw-bold" id="summary-subtotal">$0.00</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Tax (%)</span>
                                <div style="width: 70px;">
                                    <input type="number" id="tax_percent" class="form-control form-control-sm text-center bg-transparent border-0 fw-bold p-0" value="0" min="0" max="100" step="1">
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mb-3 pb-3 border-bottom border-opacity-10">
                                <span class="text-muted">Tax Amount</span>
                                <span class="fw-bold" id="summary-tax">$0.00</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-end">
                                <div>
                                    <span class="text-muted d-block small mb-1">Total Payable</span>
                                    <div class="total-display" id="summary-total">$0.00</div>
                                </div>
                                <div class="text-end">
                                    <span class="text-muted small fw-bold" id="summary-total-alt">0 ៛</span>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-2">Payment Method</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <input type="radio" class="btn-check" name="payment_method" id="pay_bakong" value="bakong" checked>
                                    <label class="btn btn-outline-secondary w-100 py-3 rounded-4 d-flex flex-column align-items-center gap-2" for="pay_bakong" style="border: 2px solid #7c3aed !important; background: linear-gradient(135deg, rgba(124, 58, 237, 0.1) 0%, rgba(91, 33, 182, 0.1) 100%);">
                                        <i class="fas fa-qrcode fa-2x" style="color: #7c3aed;"></i>
                                        <span class="fw-bold" style="color: #7c3aed;">Bakong</span>
                                        <span class="small text-muted">QR Payment</span>
                                    </label>
                                </div>
                                <div class="col-6">
                                    <input type="radio" class="btn-check" name="payment_method" id="pay_cash" value="cash">
                                    <label class="btn btn-outline-secondary w-100 py-3 rounded-4 d-flex flex-column align-items-center gap-2" for="pay_cash" style="border: 2px solid #10b981 !important; background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(5, 150, 105, 0.1) 100%);">
                                        <i class="fas fa-money-bill-wave fa-2x" style="color: #10b981;"></i>
                                        <span class="fw-bold" style="color: #10b981;">Cash</span>
                                        <span class="small text-muted">Pay Now</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Cash Calculator (shown when cash is selected) -->
                        <div id="cash-calculator-section" class="mb-4" style="display: none;">
                            <div class="bg-light rounded-3 p-3 mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted small">Charged</span>
                                    <div class="h4 fw-bold mb-0 text-success" id="cash-charged-display">$0.00</div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted small">Change</span>
                                    <div class="h5 fw-bold mb-0 text-primary" id="cash-change-inline">$0.00</div>
                                </div>
                            </div>
                            
                            <div class="row g-2 mb-2">
                                <div class="col-4"><button type="button" class="btn btn-outline-secondary w-100 py-2" onclick="addQuickAmount(1)">$1</button></div>
                                <div class="col-4"><button type="button" class="btn btn-outline-secondary w-100 py-2" onclick="addQuickAmount(5)">$5</button></div>
                                <div class="col-4"><button type="button" class="btn btn-outline-secondary w-100 py-2" onclick="addQuickAmount(10)">$10</button></div>
                            </div>
                            <div class="row g-2 mb-2">
                                <div class="col-4"><button type="button" class="btn btn-outline-secondary w-100 py-2" onclick="addQuickAmount(20)">$20</button></div>
                                <div class="col-4"><button type="button" class="btn btn-outline-secondary w-100 py-2" onclick="addQuickAmount(50)">$50</button></div>
                                <div class="col-4"><button type="button" class="btn btn-outline-secondary w-100 py-2" onclick="addQuickAmount(100)">$100</button></div>
                            </div>
                            
                            <div class="calculator-grid mb-2">
                                <button type="button" class="calc-btn" onclick="appendCashDigit('1')">1</button>
                                <button type="button" class="calc-btn" onclick="appendCashDigit('2')">2</button>
                                <button type="button" class="calc-btn" onclick="appendCashDigit('3')">3</button>
                                <button type="button" class="calc-btn" onclick="appendCashDigit('4')">4</button>
                                <button type="button" class="calc-btn" onclick="appendCashDigit('5')">5</button>
                                <button type="button" class="calc-btn" onclick="appendCashDigit('6')">6</button>
                                <button type="button" class="calc-btn" onclick="appendCashDigit('7')">7</button>
                                <button type="button" class="calc-btn" onclick="appendCashDigit('8')">8</button>
                                <button type="button" class="calc-btn" onclick="appendCashDigit('9')">9</button>
                                <button type="button" class="calc-btn" onclick="appendCashDigit('.')">.</button>
                                <button type="button" class="calc-btn" onclick="appendCashDigit('0')">0</button>
                                <button type="button" class="calc-btn calc-btn-danger" onclick="backspaceCash()"><i class="fas fa-backspace"></i></button>
                            </div>
                            
                            <div class="row g-2">
                                <div class="col-6"><button type="button" class="btn btn-outline-primary w-100" onclick="setExactAmount()">Exact</button></div>
                                <div class="col-6"><button type="button" class="btn btn-outline-danger w-100" onclick="clearCashInput()">Clear</button></div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 complete-btn d-flex justify-content-between align-items-center" id="btn-complete-sale" disabled>
                            <span>Complete Sale</span>
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </form>

                <!-- Mini History -->
                <div class="modern-card p-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0">Recent Activity</h6>
                        <span class="badge bg-primary rounded-pill px-3" id="sales-count-badge">0</span>
                    </div>
                    <div id="mini-history-list" style="max-height: 180px; overflow-y: auto;">
                        <div class="text-center py-3 text-muted small" id="history-loading">
                            <i class="fas fa-spinner fa-spin me-2"></i>Loading...
                        </div>
                        <table class="table table-sm mb-0">
                            <tbody id="pos-sales-tbody">
                                <!-- Sales will be rendered here -->
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 pt-3 border-top d-flex justify-content-between align-items-center">
                        <span class="text-muted small fw-medium">Daily Revenue</span>
                        <span class="fw-bold text-success h6 mb-0" id="today-revenue">$0.00</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 bg-gradient" style="background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%);">
                <h5 class="modal-title text-white fw-bold">
                    <i class="fas fa-qrcode me-2"></i>Scan to Pay
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-5">
                <div id="payment-loading">
                    <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;"></div>
                    <p class="h5 text-dark">Generating QR Code...</p>
                    <p class="text-muted small">Please wait a moment</p>
                </div>
                
                <div id="payment-qr-container" class="d-none">
                    <div class="mb-3">
                        <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill">
                            <i class="fas fa-building-columns me-1"></i>Bakong Payment
                        </span>
                    </div>
                    
                    <p class="text-muted small mb-1">TOTAL AMOUNT</p>
                    <h2 class="mb-4 fw-bold" style="color: #7c3aed;" id="payment-amount-display">$0.00</h2>
                    
                    <div id="qrcode-container" class="d-inline-block p-4 bg-white rounded-4 shadow-sm mb-4" style="border: 3px solid #7c3aed;">
                        <div id="qrcode" style="width: 240px; height: 240px;"></div>
                        <img id="qrimage" src="" class="d-none" style="width: 240px; height: 240px;">
                    </div>
                    
                    <div class="alert alert-info border-0 mx-4 mb-4" style="background: #eff6ff;">
                        <i class="fas fa-mobile-alt me-2 text-primary"></i>
                        <span class="text-dark">Open your banking app and scan this QR code</span>
                    </div>
                    
                    <div class="bg-light rounded-3 p-3 mx-4">
                        <div class="d-flex align-items-center justify-content-center">
                            <div class="spinner-grow spinner-grow-sm me-2" style="color: #7c3aed;" role="status"></div>
                            <span class="small fw-medium" style="color: #7c3aed;">Waiting for payment confirmation...</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 justify-content-center pb-4">
                <button type="button" class="btn btn-outline-secondary px-4 rounded-pill" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancel Order
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Cash Payment Calculator Modal -->
<div class="modal fade" id="cashCalculatorModal" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 bg-gradient" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                <h5 class="modal-title text-white fw-bold">
                    <i class="fas fa-money-bill-wave me-2"></i>Cash Payment
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row">
                    <!-- Left Side - Order Summary -->
                    <div class="col-md-5">
                        <div class="bg-light rounded-3 p-3 mb-3">
                            <h6 class="text-muted small mb-3">ORDER SUMMARY</h6>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Total Amount:</span>
                                <span class="fw-bold h5 mb-0" id="cash-total-display">$0.00</span>
                            </div>
                        </div>
                        
                        <div class="bg-success bg-opacity-10 rounded-3 p-3 mb-3">
                            <h6 class="text-success small mb-2">AMOUNT RECEIVED</h6>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-white border-0">$</span>
                                <input type="text" class="form-control form-control-lg border-0 text-end fw-bold" id="cash-received-input" value="0.00" readonly style="font-size: 2rem; background: transparent;">
                            </div>
                        </div>
                        
                        <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                            <h6 class="text-primary small mb-2">CHANGE</h6>
                            <div class="h2 fw-bold text-primary mb-0" id="cash-change-display">$0.00</div>
                        </div>
                        
                        <div class="alert alert-warning mt-3 small" id="insufficient-alert" style="display: none;">
                            <i class="fas fa-exclamation-triangle me-2"></i>Insufficient amount received!
                        </div>
                    </div>
                    
                    <!-- Right Side - Calculator -->
                    <div class="col-md-7">
                        <div class="mb-3">
                            <h6 class="text-muted small mb-2">QUICK AMOUNTS</h6>
                            <div class="row g-2">
                                <div class="col-3"><button class="btn btn-outline-secondary w-100 py-2" onclick="addQuickAmount(1)">$1</button></div>
                                <div class="col-3"><button class="btn btn-outline-secondary w-100 py-2" onclick="addQuickAmount(5)">$5</button></div>
                                <div class="col-3"><button class="btn btn-outline-secondary w-100 py-2" onclick="addQuickAmount(10)">$10</button></div>
                                <div class="col-3"><button class="btn btn-outline-secondary w-100 py-2" onclick="addQuickAmount(20)">$20</button></div>
                                <div class="col-3"><button class="btn btn-outline-secondary w-100 py-2" onclick="addQuickAmount(50)">$50</button></div>
                                <div class="col-3"><button class="btn btn-outline-secondary w-100 py-2" onclick="addQuickAmount(100)">$100</button></div>
                                <div class="col-3"><button class="btn btn-outline-primary w-100 py-2" onclick="setExactAmount()">Exact</button></div>
                                <div class="col-3"><button class="btn btn-outline-danger w-100 py-2" onclick="clearCashInput()">Clear</button></div>
                            </div>
                        </div>
                        
                        <div class="calculator-grid">
                            <button class="calc-btn" onclick="appendCashDigit('1')">1</button>
                            <button class="calc-btn" onclick="appendCashDigit('2')">2</button>
                            <button class="calc-btn" onclick="appendCashDigit('3')">3</button>
                            <button class="calc-btn" onclick="appendCashDigit('4')">4</button>
                            <button class="calc-btn" onclick="appendCashDigit('5')">5</button>
                            <button class="calc-btn" onclick="appendCashDigit('6')">6</button>
                            <button class="calc-btn" onclick="appendCashDigit('7')">7</button>
                            <button class="calc-btn" onclick="appendCashDigit('8')">8</button>
                            <button class="calc-btn" onclick="appendCashDigit('9')">9</button>
                            <button class="calc-btn" onclick="appendCashDigit('.')">.</button>
                            <button class="calc-btn" onclick="appendCashDigit('0')">0</button>
                            <button class="calc-btn calc-btn-danger" onclick="backspaceCash()"><i class="fas fa-backspace"></i></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 justify-content-between">
                <button type="button" class="btn btn-outline-secondary px-4 rounded-pill" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancel
                </button>
                <button type="button" class="btn btn-success px-5 py-2 rounded-pill" id="btn-confirm-cash" onclick="confirmCashPayment()" disabled>
                    <i class="fas fa-check me-2"></i>Confirm Payment
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.calculator-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 10px;
}

.calc-btn {
    padding: 20px;
    font-size: 1.5rem;
    font-weight: 600;
    border: 2px solid #e5e7eb;
    background: white;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.2s;
}

.calc-btn:hover {
    background: #0ea5e9;
    color: white;
    border-color: #0ea5e9;
    transform: translateY(-2px);
}

.calc-btn:active {
    transform: translateY(0);
}

.calc-btn-danger {
    background: #fee2e2;
    color: #dc2626;
    border-color: #fecaca;
}

.calc-btn-danger:hover {
    background: #dc2626;
    color: white;
    border-color: #dc2626;
}
</style>

<style>
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    }
    .filter-btn {
        white-space: nowrap;
        border-radius: 20px;
        padding: 5px 15px;
    }
    #category-filters::-webkit-scrollbar {
        height: 4px;
    }
    #category-filters::-webkit-scrollbar-thumb {
        background: #dee2e6;
        border-radius: 4px;
    }
    .product-card .badge {
        font-size: 0.7rem;
    }
</style>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('product-search');
        const productsGrid = document.getElementById('products-grid');
        const cartTableBody = document.getElementById('cart-table-body');
        const summarySubtotal = document.getElementById('summary-subtotal');
        const summaryTax = document.getElementById('summary-tax');
        const summaryTotal = document.getElementById('summary-total');
        const summaryTotalAlt = document.getElementById('summary-total-alt');
        const taxPercentInput = document.getElementById('tax_percent');
        const btnCompleteSale = document.getElementById('btn-complete-sale');
        const posForm = document.getElementById('pos-form');
        const paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));
        const currencyToggles = document.querySelectorAll('input[name="currency_toggle"]');
        
        // Sales History Elements
        const posSalesTbody = document.getElementById('pos-sales-tbody');
        const salesCountBadge = document.getElementById('sales-count-badge');
        const todayRevenue = document.getElementById('today-revenue');
        
        const EXCHANGE_RATE = <?php echo e($exchange_rate ?? 4100); ?>;
        let cart = [];
        let pollingInterval = null;
        let salesPollingInterval = null;
        let selectedCurrency = 'USD';
        let lastSaleId = 0;

        // ==================== SALES HISTORY FUNCTIONS ====================
        
        // Fetch today's sales for POS
        function fetchTodaySales() {
            fetch('<?php echo e(route("sales.latest")); ?>?filter=today', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    renderPosSales(data.sales, data.summary);
                }
            })
            .catch(err => console.error('Error fetching sales:', err));
        }

        // Render sales in POS panel
        function renderPosSales(sales, summary) {
            // Hide loading spinner and show table
            const historyLoading = document.getElementById('history-loading');
            if (historyLoading) {
                historyLoading.style.display = 'none';
            }

            // Update summary
            if (summary) {
                salesCountBadge.textContent = summary.total_orders;
                todayRevenue.textContent = '$' + summary.total_revenue.toFixed(2);
            }

            if (sales.length === 0) {
                posSalesTbody.innerHTML = `
                    <tr>
                        <td colspan="4" class="text-center py-4 text-muted">
                            <i class="fas fa-receipt fa-2x mb-2 opacity-25"></i><br>
                            No sales today
                        </td>
                    </tr>
                `;
                return;
            }

            // Track the latest sale ID
            if (sales.length > 0) {
                const maxId = Math.max(...sales.map(s => s.id));
                if (maxId > lastSaleId) {
                    lastSaleId = maxId;
                    // Show notification for new sales
                    const newSalesCount = sales.filter(s => s.id > lastSaleId - sales.length).length;
                    if (newSalesCount > 0) {
                        showNewSaleNotification(sales[0]);
                    }
                }
            }

            posSalesTbody.innerHTML = sales.slice(0, 10).map(sale => `
                <tr data-id="${sale.id}" class="${sale.status === 'pending' ? 'table-warning' : ''}">
                    <td class="ps-2">
                        <span class="badge bg-secondary-subtle text-secondary border">${sale.bill_no.substring(0, 10)}</span>
                    </td>
                    <td>
                        <span class="small">${sale.items_count} items</span>
                        <span class="text-muted small d-block" style="font-size: 0.7rem;">${sale.items_total_qty} units</span>
                    </td>
                    <td class="text-end pe-2">
                        <span class="fw-bold text-primary">$${sale.total_price.toFixed(2)}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge ${sale.status === 'completed' ? 'bg-success' : 'bg-warning'}">
                            ${sale.status === 'completed' ? '<i class="fas fa-check"></i>' : '<i class="fas fa-clock fa-spin"></i>'}
                        </span>
                    </td>
                </tr>
            `).join('');
        }

        // Show notification for new sale
        function showNewSaleNotification(sale) {
            // Create toast notification
            const toast = document.createElement('div');
            toast.className = 'position-fixed top-0 end-0 m-3 alert alert-success alert-dismissible fade show';
            toast.style.zIndex = '9999';
            toast.innerHTML = `
                <i class="fas fa-check-circle me-2"></i>
                <strong>New Sale!</strong><br>
                ${sale.bill_no} - $${sale.total_price.toFixed(2)}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(toast);

            // Play sound (optional)
            try {
                const audio = new Audio('https://assets.mixkit.co/active_storage/sfx/2899/2899-preview.mp3');
                audio.volume = 0.3;
                audio.play().catch(() => {}); // Ignore errors
            } catch(e) {}

            // Auto remove after 4 seconds
            setTimeout(() => {
                toast.remove();
            }, 4000);
        }

        // Start polling for sales history
        function startSalesPolling() {
            // Initial fetch
            fetchTodaySales();
            
            // Poll every 5 seconds
            salesPollingInterval = setInterval(fetchTodaySales, 5000);
        }

        // Stop polling when leaving page
        function stopSalesPolling() {
            if (salesPollingInterval) {
                clearInterval(salesPollingInterval);
                salesPollingInterval = null;
            }
        }

        // Initialize sales polling
        if (posSalesTbody) {
            startSalesPolling();
            
            // Stop when page hides
            document.addEventListener('visibilitychange', () => {
                if (document.hidden) {
                    stopSalesPolling();
                } else {
                    startSalesPolling();
                    fetchTodaySales();
                }
            });
        }

        // ==================== EXISTING CART FUNCTIONS ====================

        // Currency Toggle
        currencyToggles.forEach(toggle => {
            toggle.addEventListener('change', function() {
                selectedCurrency = this.value;
                renderCart();
            });
        });

        // Tax Input
        taxPercentInput.addEventListener('input', renderCart);

        // Category Filtering
        const categoryPills = document.querySelectorAll('.category-pill');
        categoryPills.forEach(pill => {
            pill.addEventListener('click', function() {
                // Remove active from all pills
                categoryPills.forEach(p => {
                    p.classList.remove('active');
                });
                // Add active to clicked pill
                this.classList.add('active');

                const category = this.dataset.category;
                const products = document.querySelectorAll('.product-item');

                products.forEach(p => {
                    if (category === 'all' || p.dataset.category === category) {
                        p.classList.remove('d-none');
                    } else {
                        p.classList.add('d-none');
                    }
                });
            });
        });

        // Search products in grid
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            const products = document.querySelectorAll('.product-item');

            products.forEach(p => {
                const name = p.dataset.name;
                if (name.includes(query)) {
                    p.classList.remove('d-none');
                } else {
                    p.classList.add('d-none');
                }
            });
        });

        window.addToCart = function(product) {
            const existingItem = cart.find(item => item.id === product.id);
            if (existingItem) {
                if (existingItem.quantity < product.qty) {
                    existingItem.quantity++;
                } else {
                    alert('Out of stock!');
                }
            } else {
                cart.push({
                    id: product.id,
                    name: product.name,
                    image: product.image,
                    price: parseFloat(product.price),
                    quantity: 1,
                    stock: product.qty
                });
            }
            renderCart();
        };

        window.updateQuantity = function(index, delta) {
            const item = cart[index];
            if (delta > 0 && item.quantity >= item.stock) {
                alert('No more stock available!');
                return;
            }
            item.quantity += delta;
            if (item.quantity <= 0) {
                cart.splice(index, 1);
            }
            renderCart();
        };

        window.removeFromCart = function(index) {
            cart.splice(index, 1);
            renderCart();
        };

        window.clearCart = function() {
            if (cart.length === 0) return;
            if (confirm('Clear all items from cart?')) {
                cart = [];
                renderCart();
            }
        };

        // Play cash register sound
        function playCashRegisterSound() {
            try {
                // Cash register "cha-ching" sound
                const audio = new Audio('https://assets.mixkit.co/active_storage/sfx/2856/2856-preview.mp3');
                audio.volume = 0.7;
                audio.play().catch(() => {
                    console.log('Audio playback failed - user interaction may be required');
                });
            } catch(e) {
                console.log('Cash register sound not available');
            }
            
            // Add money animation
            showMoneyAnimation();
        }

        // Show floating money icons
        function showMoneyAnimation() {
            const moneyIcons = ['💵', '💰', '💸', '💴', '💶', '💷'];
            const container = document.body;
            
            for (let i = 0; i < 8; i++) {
                setTimeout(() => {
                    const money = document.createElement('div');
                    money.className = 'money-icon';
                    money.textContent = moneyIcons[Math.floor(Math.random() * moneyIcons.length)];
                    
                    // Random position
                    money.style.left = Math.random() * window.innerWidth + 'px';
                    money.style.top = window.innerHeight / 2 + 'px';
                    
                    container.appendChild(money);
                    
                    // Remove after animation
                    setTimeout(() => {
                        money.remove();
                    }, 2000);
                }, i * 100);
            }
        }

        function formatMoney(amount, currency = 'USD') {
            if (currency === 'KHR') {
                return new Intl.NumberFormat('km-KH', { style: 'currency', currency: 'KHR', minimumFractionDigits: 0 }).format(amount);
            }
            return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(amount);
        }

        function renderCart() {
            cartTableBody.innerHTML = '';
            const emptyCartMsg = document.getElementById('empty-cart-msg');
            let subtotalUSD = 0;
            let totalItems = 0;

            if (cart.length === 0) {
                if (emptyCartMsg) emptyCartMsg.classList.remove('d-none');
                btnCompleteSale.disabled = true;
            } else {
                if (emptyCartMsg) emptyCartMsg.classList.add('d-none');
                btnCompleteSale.disabled = false;
                cart.forEach((item, index) => {
                    const rowSubtotalUSD = item.price * item.quantity;
                    subtotalUSD += rowSubtotalUSD;
                    totalItems += item.quantity;

                    const displayPrice = selectedCurrency === 'KHR' ? item.price * EXCHANGE_RATE : item.price;
                    const displaySubtotal = selectedCurrency === 'KHR' ? rowSubtotalUSD * EXCHANGE_RATE : rowSubtotalUSD;

                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="ps-3">
                            <div class="d-flex align-items-center">
                                <div class="me-3" style="width: 40px; height: 40px;">
                                    ${item.image ? `<img src="/storage/${item.image}" class="w-100 h-100 rounded shadow-sm border" style="object-fit: cover;">` : `<div class="bg-light w-100 h-100 rounded d-flex align-items-center justify-content-center"><i class="fas fa-mobile-alt text-muted small"></i></div>`}
                                </div>
                                <div>
                                    <div class="fw-bold small">${item.name}</div>
                                    <div class="text-muted text-xs">Stock: ${item.stock}</div>
                                </div>
                            </div>
                        </td>
                        <td class="text-center small">${formatMoney(displayPrice, selectedCurrency)}</td>
                        <td class="text-center">
                            <div class="input-group input-group-sm justify-content-center mx-auto" style="width: 100px;">
                                <button class="btn btn-outline-secondary p-1" type="button" onclick="updateQuantity(${index}, -1)"><i class="fas fa-minus fa-xs"></i></button>
                                <input type="text" class="form-control text-center p-1" value="${item.quantity}" readonly>
                                <button class="btn btn-outline-secondary p-1" type="button" onclick="updateQuantity(${index}, 1)"><i class="fas fa-plus fa-xs"></i></button>
                            </div>
                        </td>
                        <td class="text-end pe-3 fw-bold small">${formatMoney(displaySubtotal, selectedCurrency)}</td>
                        <td>
                            <button class="btn btn-link btn-sm text-danger p-0" onclick="removeFromCart(${index})">
                                <i class="fas fa-times"></i>
                            </button>
                        </td>
                    `;
                    cartTableBody.appendChild(row);
                });
            }

            // Calculations
            const taxPercent = parseFloat(taxPercentInput.value) || 0;
            const taxAmountUSD = subtotalUSD * (taxPercent / 100);
            const totalUSD = subtotalUSD + taxAmountUSD;

            if (selectedCurrency === 'USD') {
                summarySubtotal.innerText = formatMoney(subtotalUSD, 'USD');
                summaryTax.innerText = formatMoney(taxAmountUSD, 'USD');
                summaryTotal.innerText = formatMoney(totalUSD, 'USD');
                summaryTotalAlt.innerText = formatMoney(totalUSD * EXCHANGE_RATE, 'KHR');
            } else {
                summarySubtotal.innerText = formatMoney(subtotalUSD * EXCHANGE_RATE, 'KHR');
                summaryTax.innerText = formatMoney(taxAmountUSD * EXCHANGE_RATE, 'KHR');
                summaryTotal.innerText = formatMoney(totalUSD * EXCHANGE_RATE, 'KHR');
                summaryTotalAlt.innerText = formatMoney(totalUSD, 'USD');
            }
        }

        posForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (cart.length === 0) return;

            const taxPercent = parseFloat(taxPercentInput.value) || 0;
            const subtotalUSD = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            const taxAmountUSD = subtotalUSD * (taxPercent / 100);
            
            // Get selected payment method
            const selectedPaymentMethod = document.querySelector('input[name="payment_method"]:checked').value;

            const formData = {
                _token: document.querySelector('input[name="_token"]').value,
                items: cart.map(item => ({
                    phone_id: item.id,
                    qty: item.quantity
                })),
                payment_method: selectedPaymentMethod,
                currency: selectedCurrency,
                tax_amount: taxAmountUSD,
                exchange_rate: EXCHANGE_RATE
            };

            btnCompleteSale.disabled = true;
            btnCompleteSale.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';

            fetch(this.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': formData._token
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Handle based on payment method
                    if (selectedPaymentMethod === 'bakong') {
                        handleQrPayment(data);
                    } else if (selectedPaymentMethod === 'cash') {
                        handleCashPayment(data);
                    }
                } else {
                    alert(data.message || 'Error creating sale');
                    resetBtn();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
                resetBtn();
            });
        });

        function resetBtn() {
            btnCompleteSale.disabled = false;
            btnCompleteSale.innerHTML = '<i class="fas fa-check-circle me-2"></i>Complete Sale';
        }

        // Show payment success alert with animation
        function showPaymentSuccessAlert(data, callback) {
            // Play cash register sound
            playCashRegisterSound();
            
            // Create success modal
            const modalHtml = `
                <div class="modal fade" id="successModal" data-bs-backdrop="static" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered modal-sm">
                        <div class="modal-content border-0 shadow-lg">
                            <div class="modal-body text-center p-5">
                                <div class="mb-4">
                                    <div style="width: 80px; height: 80px; margin: 0 auto; background: linear-gradient(135deg, #10b981, #059669); border-radius: 50%; display: flex; align-items: center; justify-content: center; animation: pulse 1s ease-in-out infinite;">
                                        <i class="fas fa-check text-white" style="font-size: 40px;"></i>
                                    </div>
                                </div>
                                <h4 class="fw-bold text-dark mb-2">Payment Successful!</h4>
                                <p class="text-muted mb-3">Your payment has been processed successfully.</p>
                                <div class="bg-light rounded p-3 mb-4">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Bill No:</span>
                                        <span class="fw-bold text-primary">${data.bill_no}</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">Total:</span>
                                        <span class="fw-bold text-success h5 mb-0">$${data.total.toFixed(2)}</span>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-primary w-100 py-2 fw-bold" id="btn-view-receipt">
                                    <i class="fas fa-receipt me-2"></i>View Receipt
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <style>
                    @keyframes pulse {
                        0% { transform: scale(1); }
                        50% { transform: scale(1.1); }
                        100% { transform: scale(1); }
                    }
                </style>
            `;
            
            // Remove existing modal if any
            const existingModal = document.getElementById('successModal');
            if (existingModal) {
                existingModal.remove();
            }
            
            // Add modal to body
            document.body.insertAdjacentHTML('beforeend', modalHtml);
            
            // Show modal
            const successModal = new bootstrap.Modal(document.getElementById('successModal'));
            successModal.show();
            
            // Play success sound
            try {
                const audio = new Audio('https://assets.mixkit.co/active_storage/sfx/2899/2899-preview.mp3');
                audio.volume = 0.5;
                audio.play().catch(() => {});
            } catch(e) {}
            
            // Handle button click
            document.getElementById('btn-view-receipt').addEventListener('click', function() {
                successModal.hide();
                callback();
            });
            
            // Reset form after successful payment
            cart = [];
            renderCart();
            resetBtn();
            
            // Refresh sales history
            fetchTodaySales();
        }

        function handleQrPayment(data) {
            document.getElementById('payment-loading').classList.remove('d-none');
            document.getElementById('payment-qr-container').classList.add('d-none');
            
            const displayTotal = data.currency === 'KHR' ? data.total * EXCHANGE_RATE : data.total;
            document.getElementById('payment-amount-display').innerText = formatMoney(displayTotal, data.currency);
            
            // Reset QR elements
            document.getElementById('qrcode').innerHTML = '';
            document.getElementById('qrimage').src = '';
            document.getElementById('qrimage').classList.add('d-none');
            document.getElementById('qrcode').classList.remove('d-none');
            
            paymentModal.show();

            fetch(`/sales/${data.sale_id}/generate-qr`)
                .then(res => res.json())
                .then(qrResult => {
                    document.getElementById('payment-loading').classList.add('d-none');
                    document.getElementById('payment-qr-container').classList.remove('d-none');
                    
                    if (qrResult.success) {
                        if (qrResult.qr_image) {
                            // If API returns ready-to-use image (base64 or URL)
                            const qrImg = document.getElementById('qrimage');
                            qrImg.src = qrResult.qr_image;
                            qrImg.classList.remove('d-none');
                            document.getElementById('qrcode').classList.add('d-none');
                        } else if (qrResult.qr_data) {
                            // If API returns string to be encoded
                            new QRCode(document.getElementById("qrcode"), {
                                text: qrResult.qr_data,
                                width: 220,
                                height: 220,
                                colorDark : "#000000",
                                colorLight : "#ffffff",
                                correctLevel : QRCode.CorrectLevel.M
                            });
                        }
                        
                        startPolling(data.sale_id);
                    } else {
                        alert('Failed to generate QR: ' + qrResult.message);
                        paymentModal.hide();
                        resetBtn();
                    }
                })
                .catch(error => {
                    console.error('QR Generation Error:', error);
                    alert('Failed to generate QR code. Please try again.');
                    paymentModal.hide();
                    resetBtn();
                });
        }

        function startPolling(saleId) {
            if (pollingInterval) clearInterval(pollingInterval);
            
            pollingInterval = setInterval(() => {
                fetch(`/sales/${saleId}/check-status`)
                    .then(res => res.json())
                    .then(statusData => {
                        if (statusData.success && statusData.status === 'completed') {
                            clearInterval(pollingInterval);
                            paymentModal.hide();
                            
                            // Play cash register sound
                            playCashRegisterSound();
                            
                            // Show success notification
                            showPaymentSuccessAlert({
                                bill_no: 'Sale #' + saleId,
                                total: parseFloat(document.getElementById('summary-total').innerText.replace(/[^0-9.]/g, '')),
                                sale_id: saleId
                            }, () => {
                                window.location.href = `/sales/${saleId}`;
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Status check error:', error);
                    });
            }, 3000);
        }

        function handleCashPayment(data) {
            // For cash payment, immediately show success and redirect
            playCashRegisterSound();
            
            // Clear cart
            cart = [];
            renderCart();
            
            // Show success notification
            showPaymentSuccessAlert({
                bill_no: data.bill_no,
                total: data.total,
                sale_id: data.sale_id
            }, () => {
                window.location.href = `/sales/${data.sale_id}`;
            });
            
            resetBtn();
        }

        // Cash Calculator Functions
        let cashReceivedValue = '0';
        
        // Show/hide cash calculator based on payment method selection
        document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const cashSection = document.getElementById('cash-calculator-section');
                if (this.value === 'cash') {
                    cashSection.style.display = 'block';
                    clearCashInput();
                    updateInlineCashChange();
                } else {
                    cashSection.style.display = 'none';
                }
            });
        });
        
        function appendCashDigit(digit) {
            if (cashReceivedValue === '0' && digit !== '.') {
                cashReceivedValue = digit;
            } else if (digit === '.' && cashReceivedValue.includes('.')) {
                return; // Don't allow multiple decimals
            } else {
                cashReceivedValue += digit;
            }
            
            updateInlineCashChange();
        }
        
        function backspaceCash() {
            if (cashReceivedValue.length > 1) {
                cashReceivedValue = cashReceivedValue.slice(0, -1);
            } else {
                cashReceivedValue = '0';
            }
            
            updateInlineCashChange();
        }
        
        function clearCashInput() {
            cashReceivedValue = '0';
            updateInlineCashChange();
        }
        
        function addQuickAmount(amount) {
            const currentAmount = parseFloat(cashReceivedValue) || 0;
            cashReceivedValue = (currentAmount + amount).toString();
            updateInlineCashChange();
        }
        
        function setExactAmount() {
            const taxPercent = parseFloat(taxPercentInput.value) || 0;
            const subtotalUSD = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            const taxAmountUSD = subtotalUSD * (taxPercent / 100);
            const totalUSD = subtotalUSD + taxAmountUSD;
            
            cashReceivedValue = totalUSD.toFixed(2);
            updateInlineCashChange();
        }
        
        function updateInlineCashChange() {
            const taxPercent = parseFloat(taxPercentInput.value) || 0;
            const subtotalUSD = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            const taxAmountUSD = subtotalUSD * (taxPercent / 100);
            const totalUSD = subtotalUSD + taxAmountUSD;
            
            const receivedAmount = parseFloat(cashReceivedValue) || 0;
            const change = receivedAmount - totalUSD;
            
            const chargedDisplay = document.getElementById('cash-charged-display');
            const changeDisplay = document.getElementById('cash-change-inline');
            const completeBtn = document.getElementById('btn-complete-sale');
            
            chargedDisplay.innerText = formatMoney(receivedAmount, 'USD');
            
            if (change < 0) {
                changeDisplay.innerText = '$0.00';
                changeDisplay.classList.remove('text-primary');
                changeDisplay.classList.add('text-danger');
                completeBtn.disabled = true;
            } else {
                changeDisplay.innerText = formatMoney(change, 'USD');
                changeDisplay.classList.remove('text-danger');
                changeDisplay.classList.add('text-primary');
                completeBtn.disabled = cart.length === 0;
            }
        }

        document.getElementById('paymentModal').addEventListener('hidden.bs.modal', function () {
            if (pollingInterval) clearInterval(pollingInterval);
            resetBtn();
        });

        renderCart();
    });
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Practice\phoneshop\phoneshop\resources\views/sales/create.blade.php ENDPATH**/ ?>