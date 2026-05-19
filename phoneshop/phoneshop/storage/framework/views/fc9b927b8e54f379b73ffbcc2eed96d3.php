<?php $__env->startSection('content'); ?>

<style>
    .product-gallery {
        position: sticky;
        top: 100px;
    }

    .main-image-container {
        border-radius: 20px;
        overflow: hidden;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        padding: 2rem;
        min-height: 400px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    [data-bs-theme="dark"] .main-image-container {
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
    }

    .main-image {
        max-width: 100%;
        max-height: 500px;
        object-fit: contain;
        transition: transform 0.3s ease;
    }

    .main-image:hover {
        transform: scale(1.05);
    }

    .thumbnail-gallery {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 1rem;
    }

    .thumbnail-item {
        width: 80px;
        height: 80px;
        border-radius: 12px;
        overflow: hidden;
        cursor: pointer;
        border: 3px solid transparent;
        transition: all 0.3s ease;
        background: var(--bs-body-bg);
    }

    .thumbnail-item:hover {
        border-color: #667eea;
        transform: scale(1.05);
    }

    .thumbnail-item.active {
        border-color: #667eea;
        box-shadow: 0 0 20px rgba(102, 126, 234, 0.4);
    }

    .thumbnail-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .price-display {
        font-size: 3rem;
        font-weight: 800;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .btn-add-cart {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        font-weight: 700;
        padding: 16px 40px;
        border-radius: 50px;
        font-size: 1.1rem;
        transition: all 0.3s ease;
    }

    .btn-add-cart:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 35px rgba(102, 126, 234, 0.4);
        color: white;
    }

    .product-info-card {
        background: var(--bs-body-bg);
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }

    .stock-indicator {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        border-radius: 50px;
        font-weight: 600;
    }

    .stock-indicator.in-stock {
        background: rgba(16, 185, 129, 0.1);
        color: #10b981;
    }

    .stock-indicator.low-stock {
        background: rgba(249, 115, 22, 0.1);
        color: #f97316;
    }

    .stock-indicator.out-stock {
        background: rgba(239, 68, 68, 0.1);
        color: #ef4444;
    }

    .feature-badge {
        display: inline-block;
        padding: 8px 16px;
        background: rgba(102, 126, 234, 0.1);
        color: #667eea;
        border-radius: 50px;
        font-size: 0.9rem;
        font-weight: 600;
        margin-right: 8px;
        margin-bottom: 8px;
    }
</style>

<div class="container py-5">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo e(route('shop.home')); ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?php echo e(route('shop.category', $phone->category_id)); ?>"><?php echo e($phone->category->name); ?></a></li>
            <li class="breadcrumb-item active"><?php echo e($phone->name); ?></li>
        </ol>
    </nav>

    <div class="row g-4">
        <!-- Product Gallery -->
        <div class="col-lg-6">
            <div class="product-gallery">
                <div class="main-image-container">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($phone->image): ?>
                        <img src="<?php echo e(asset('storage/' . $phone->image)); ?>" alt="<?php echo e($phone->name); ?>" class="main-image" id="mainImage">
                    <?php else: ?>
                        <i class="fa-solid fa-box fa-5x text-muted opacity-25"></i>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($phone->detail_images && count($phone->detail_images) > 0): ?>
                    <div class="thumbnail-gallery">
                        <!-- Main image thumbnail -->
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($phone->image): ?>
                            <div class="thumbnail-item active" onclick="changeImage('<?php echo e(asset('storage/' . $phone->image)); ?>', this)">
                                <img src="<?php echo e(asset('storage/' . $phone->image)); ?>" alt="Main">
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <!-- Detail images thumbnails -->
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $phone->detail_images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detailImage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                            <div class="thumbnail-item" onclick="changeImage('<?php echo e(asset('storage/' . $detailImage)); ?>', this)">
                                <img src="<?php echo e(asset('storage/' . $detailImage)); ?>" alt="Detail <?php echo e($loop->iteration); ?>">
                            </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        <!-- Product Info -->
        <div class="col-lg-6">
            <div class="product-info-card">
                <span class="badge bg-primary-subtle text-primary mb-3"><?php echo e($phone->category->name); ?></span>
                
                <h1 class="fw-bold mb-3"><?php echo e($phone->name); ?></h1>

                <div class="mb-4">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($phone->qty > 10): ?>
                        <span class="stock-indicator in-stock">
                            <i class="fa-solid fa-circle-check"></i>
                            In Stock (<?php echo e($phone->qty); ?> available)
                        </span>
                    <?php elseif($phone->qty > 0): ?>
                        <span class="stock-indicator low-stock">
                            <i class="fa-solid fa-exclamation-triangle"></i>
                            Low Stock (Only <?php echo e($phone->qty); ?> left)
                        </span>
                    <?php else: ?>
                        <span class="stock-indicator out-stock">
                            <i class="fa-solid fa-circle-xmark"></i>
                            Out of Stock
                        </span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <div class="price-display mb-4">$<?php echo e(number_format($phone->price, 2)); ?></div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($phone->description): ?>
                    <div class="mb-4">
                        <h5 class="fw-bold mb-3">Description</h5>
                        <p class="text-muted" style="line-height: 1.8;"><?php echo e($phone->description); ?></p>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <div class="mb-4">
                    <h5 class="fw-bold mb-3">Product Features</h5>
                    <div>
                        <span class="feature-badge"><i class="fa-solid fa-shield-halved me-1"></i> Quality Guaranteed</span>
                        <span class="feature-badge"><i class="fa-solid fa-truck-fast me-1"></i> Fast Delivery</span>
                        <span class="feature-badge"><i class="fa-solid fa-rotate-left me-1"></i> Easy Returns</span>
                        <span class="feature-badge"><i class="fa-solid fa-headset me-1"></i> 24/7 Support</span>
                    </div>
                </div>

                <div class="d-grid gap-3">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($phone->qty > 0): ?>
                        <a href="<?php echo e(route('shop.add', $phone->id)); ?>" class="btn btn-add-cart">
                            <i class="fa-solid fa-cart-plus me-2"></i>Add to Cart
                        </a>
                    <?php else: ?>
                        <button class="btn btn-add-cart" disabled>
                            <i class="fa-solid fa-ban me-2"></i>Out of Stock
                        </button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    
                    <a href="<?php echo e(route('shop.home')); ?>" class="btn btn-outline-secondary btn-lg rounded-pill">
                        <i class="fa-solid fa-arrow-left me-2"></i>Continue Shopping
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function changeImage(src, element) {
        // Update main image
        document.getElementById('mainImage').src = src;
        
        // Update active thumbnail
        document.querySelectorAll('.thumbnail-item').forEach(item => {
            item.classList.remove('active');
        });
        element.classList.add('active');
    }
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('frontend.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Practice\phoneshop\phoneshop\resources\views/frontend/product.blade.php ENDPATH**/ ?>