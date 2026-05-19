<?php $__env->startSection('content'); ?>

<div class="row mb-5">
    <div class="col-lg-8">
        <div class="d-flex align-items-center mb-4">
            <h3 class="fw-bold mb-0">Shopping Cart</h3>
            <span class="badge bg-primary rounded-pill ms-3 px-3 py-2"><?php echo e(session('cart') ? count(session('cart')) : 0); ?> Items</span>
        </div>

        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="card-body p-0">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('cart') && count(session('cart')) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted small text-uppercase">
                                <tr>
                                    <th class="px-4 py-3">Product</th>
                                    <th class="py-3">Price</th>
                                    <th class="py-3">Quantity</th>
                                    <th class="py-3 text-end px-4">Subtotal</th>
                                    <th class="py-3"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $total = 0 ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = session('cart'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $details): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                    <?php 
                                        $total += $details['price'] * $details['qty'];
                                        $phone = \App\Models\Phone::find($id);
                                        $maxQty = $phone ? $phone->qty : ($details['stock'] ?? $details['qty']);
                                    ?>
                                    <tr>
                                        <td class="px-4 py-4">
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-3 overflow-hidden me-3 shadow-sm" style="width: 70px; height: 70px;">
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($details['image']): ?>
                                                        <img src="<?php echo e(asset('storage/'.$details['image'])); ?>" class="w-100 h-100" style="object-fit: cover;">
                                                    <?php else: ?>
                                                        <div class="bg-light w-100 h-100 d-flex align-items-center justify-content-center">
                                                            <i class="fa-solid fa-mobile-screen text-muted"></i>
                                                        </div>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </div>
                                                <div>
                                                    <h6 class="fw-bold mb-0"><?php echo e($details['name']); ?></h6>
                                                    <small class="text-muted"><?php echo e($maxQty > 0 ? 'In Stock' : 'Out of Stock'); ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-4 text-dark fw-medium">$<?php echo e(number_format($details['price'], 2)); ?></td>
                                        <td class="py-4">
                                            <form action="<?php echo e(route('shop.update-cart')); ?>" method="POST" class="d-inline">
                                                <?php echo csrf_field(); ?>
                                                <input type="hidden" name="product_id" value="<?php echo e($id); ?>">
                                                <div class="input-group" style="width: 100px;">
                                                    <input type="number" name="quantity" value="<?php echo e($details['qty']); ?>" min="1" max="<?php echo e($maxQty); ?>" class="form-control form-control-sm text-center" onchange="this.form.submit()">
                                                </div>
                                                <small class="d-block text-muted mt-1">Max: <?php echo e($maxQty); ?></small>
                                            </form>
                                        </td>
                                        <td class="py-4 text-end px-4 fw-bold text-primary">$<?php echo e(number_format($details['price'] * $details['qty'], 2)); ?></td>
                                        <td class="py-4 text-center">
                                            <a href="<?php echo e(route('shop.remove', $id)); ?>" class="btn btn-link text-danger p-0">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <div class="mb-4">
                            <i class="fa-solid fa-cart-shopping opacity-10" style="font-size: 5rem;"></i>
                        </div>
                        <h4 class="fw-bold text-muted">Your cart is empty</h4>
                        <p class="text-muted mb-4">Looks like you haven't added anything to your cart yet.</p>
                        <a href="<?php echo e(route('shop.home')); ?>" class="btn btn-primary px-5 py-3 rounded-pill">Start Shopping</a>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('cart') && count(session('cart')) > 0): ?>
            <div class="mt-4">
                <a href="<?php echo e(route('shop.home')); ?>" class="btn btn-light rounded-pill px-4 py-2">
                    <i class="fa-solid fa-arrow-left me-2"></i>Continue Shopping
                </a>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('cart') && count(session('cart')) > 0): ?>
    <div class="col-lg-4 mt-4 mt-lg-0">
        <div class="card border-0 shadow-sm sticky-top" style="top: 100px;">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4">Order Summary</h5>
                
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Subtotal</span>
                    <span class="fw-bold">$<?php echo e(number_format($total, 2)); ?></span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Shipping</span>
                    <span class="text-success fw-bold">Free</span>
                </div>
                <hr class="my-4 opacity-50">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <span class="h5 fw-bold mb-0 text-dark">Total</span>
                    <span class="h4 fw-bold mb-0 text-primary">$<?php echo e(number_format($total, 2)); ?></span>
                </div>

                <a href="<?php echo e(route('shop.checkout')); ?>" class="btn btn-primary w-100 py-3 rounded-3 fw-bold" onclick="return confirm('Proceed to secure Bakong payment?')">
                    <i class="fa-solid fa-lock me-2"></i>Proceed to Secure Checkout
                </a>
                
                <div class="mt-4 text-center">
                    <div class="d-flex justify-content-center gap-3 opacity-50">
                        <i class="fa-brands fa-cc-visa fs-4"></i>
                        <i class="fa-brands fa-cc-mastercard fs-4"></i>
                        <i class="fa-solid fa-qrcode fs-4"></i>
                    </div>
                    <small class="text-muted d-block mt-2">Secure checkout with Bakong KHQR</small>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('frontend.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Practice\phoneshop\phoneshop\resources\views/frontend/cart.blade.php ENDPATH**/ ?>