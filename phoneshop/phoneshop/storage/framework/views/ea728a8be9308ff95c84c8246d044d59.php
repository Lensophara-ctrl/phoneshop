

<?php $__env->startSection('content'); ?>

<style>
    /* About Us Page Styles */
    .about-hero {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        border-radius: 24px;
        padding: 4rem 2rem;
        margin-bottom: 3rem;
        position: relative;
        overflow: hidden;
    }

    .about-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        opacity: 0.3;
    }

    .about-hero-content {
        position: relative;
        z-index: 2;
        text-align: center;
        color: white;
    }

    .about-hero h1 {
        font-size: 3.5rem;
        font-weight: 800;
        margin-bottom: 1rem;
        text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    }

    .about-hero p {
        font-size: 1.3rem;
        opacity: 0.95;
        max-width: 700px;
        margin: 0 auto;
    }

    .feature-card {
        border-radius: 20px;
        padding: 2rem;
        height: 100%;
        transition: all 0.3s ease;
        border: 2px solid transparent;
        background: var(--bs-body-bg);
    }

    .feature-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(79, 70, 229, 0.15);
        border-color: #4f46e5;
    }

    .feature-icon {
        width: 80px;
        height: 80px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        margin-bottom: 1.5rem;
    }

    .stats-section {
        background: linear-gradient(135deg, rgba(79, 70, 229, 0.05) 0%, rgba(124, 58, 237, 0.05) 100%);
        border-radius: 24px;
        padding: 3rem 2rem;
        margin: 3rem 0;
    }

    .stat-item {
        text-align: center;
        padding: 1.5rem;
    }

    .stat-number {
        font-size: 3rem;
        font-weight: 800;
        color: #4f46e5;
        margin-bottom: 0.5rem;
    }

    .stat-label {
        font-size: 1.1rem;
        color: var(--text-muted);
        font-weight: 600;
    }

    .team-section {
        margin: 4rem 0;
    }

    .section-title {
        font-size: 2.5rem;
        font-weight: 800;
        color: #4f46e5;
        margin-bottom: 1rem;
        text-align: center;
    }

    .section-subtitle {
        text-align: center;
        color: var(--text-muted);
        font-size: 1.1rem;
        margin-bottom: 3rem;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
    }

    .value-card {
        text-align: center;
        padding: 2rem;
        border-radius: 20px;
        background: var(--bs-body-bg);
        border: 2px solid var(--bs-border-color);
        transition: all 0.3s ease;
        height: 100%;
    }

    .value-card:hover {
        transform: translateY(-5px);
        border-color: #4f46e5;
        box-shadow: 0 10px 30px rgba(79, 70, 229, 0.1);
    }

    .value-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
    }

    @media (max-width: 768px) {
        .about-hero h1 {
            font-size: 2.5rem;
        }
        
        .about-hero p {
            font-size: 1.1rem;
        }
        
        .stat-number {
            font-size: 2rem;
        }
    }
</style>

<!-- Hero Section -->
<div class="about-hero">
    <div class="about-hero-content">
        <h1><?php echo e($settings['about_hero_title'] ?? 'About Us'); ?></h1>
        <p><?php echo e($settings['about_hero_subtitle'] ?? 'Your trusted partner in mobile technology, delivering quality products and exceptional service since day one.'); ?></p>
    </div>
</div>

<!-- Our Story Section -->
<div class="row mb-5">
    <div class="col-lg-6 mb-4 mb-lg-0">
        <h2 class="fw-bold mb-4"><?php echo e($settings['about_story_title'] ?? 'Our Story'); ?></h2>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($settings['about_story_content']) && $settings['about_story_content']): ?>
            <?php echo nl2br(e($settings['about_story_content'])); ?>

        <?php else: ?>
            <p class="text-muted mb-3" style="font-size: 1.05rem; line-height: 1.8;">
                Welcome to <strong><?php echo e($settings['store_name'] ?? 'PhoneShop'); ?></strong>, your premier destination for the latest smartphones and mobile accessories. 
                Founded with a passion for technology and a commitment to customer satisfaction, we've grown to become a trusted name in the mobile retail industry.
            </p>
            <p class="text-muted mb-3" style="font-size: 1.05rem; line-height: 1.8;">
                Our journey began with a simple mission: to make cutting-edge mobile technology accessible to everyone. 
                We believe that everyone deserves access to quality devices at fair prices, backed by reliable service and support.
            </p>
            <p class="text-muted" style="font-size: 1.05rem; line-height: 1.8;">
                Today, we continue to uphold these values, offering an extensive selection of authentic products from leading brands, 
                competitive pricing, and a shopping experience that puts you first.
            </p>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
    <div class="col-lg-6">
        <div class="position-relative" style="height: 100%; min-height: 300px;">
            <div style="background: linear-gradient(135deg, rgba(79, 70, 229, 0.1) 0%, rgba(124, 58, 237, 0.1) 100%); 
                        border-radius: 24px; height: 100%; display: flex; align-items: center; justify-content: center;">
                <?php
                    $storeIcon = $settings['store_icon'] ?? 'fa-store';
                ?>
                <i class="fa-solid <?php echo e($storeIcon); ?>" style="font-size: 10rem; color: #4f46e5; opacity: 0.2;"></i>
            </div>
        </div>
    </div>
</div>

<!-- Stats Section -->
<div class="stats-section">
    <div class="row">
        <div class="col-6 col-md-3">
            <div class="stat-item">
                <div class="stat-number"><?php echo e($settings['about_stat_customers'] ?? '1000+'); ?></div>
                <div class="stat-label">Happy Customers</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-item">
                <div class="stat-number"><?php echo e($settings['about_stat_products'] ?? '500+'); ?></div>
                <div class="stat-label">Products</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-item">
                <div class="stat-number"><?php echo e($settings['about_stat_authentic'] ?? '100%'); ?></div>
                <div class="stat-label">Authentic</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-item">
                <div class="stat-number"><?php echo e($settings['about_stat_support'] ?? '24/7'); ?></div>
                <div class="stat-label">Support</div>
            </div>
        </div>
    </div>
</div>

<!-- Why Choose Us Section -->
<div class="mb-5">
    <h2 class="section-title">Why Choose Us</h2>
    <p class="section-subtitle">We're committed to providing the best shopping experience with unmatched quality and service</p>
    
    <div class="row g-4">
        <div class="col-md-6 col-lg-3">
            <div class="feature-card">
                <div class="feature-icon bg-primary-subtle text-primary">
                    <i class="fa-solid fa-shield-check"></i>
                </div>
                <h5 class="fw-bold mb-3">100% Authentic</h5>
                <p class="text-muted mb-0">All our products are genuine and sourced directly from authorized distributors.</p>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="feature-card">
                <div class="feature-icon bg-success-subtle text-success">
                    <i class="fa-solid fa-truck-fast"></i>
                </div>
                <h5 class="fw-bold mb-3">Fast Delivery</h5>
                <p class="text-muted mb-0">Quick and secure shipping to get your products to you as fast as possible.</p>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="feature-card">
                <div class="feature-icon bg-warning-subtle text-warning">
                    <i class="fa-solid fa-headset"></i>
                </div>
                <h5 class="fw-bold mb-3">24/7 Support</h5>
                <p class="text-muted mb-0">Our dedicated support team is always ready to assist you with any questions.</p>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="feature-card">
                <div class="feature-icon bg-info-subtle text-info">
                    <i class="fa-solid fa-rotate"></i>
                </div>
                <h5 class="fw-bold mb-3">Easy Returns</h5>
                <p class="text-muted mb-0">Hassle-free return policy to ensure your complete satisfaction.</p>
            </div>
        </div>
    </div>
</div>

<!-- Our Values Section -->
<div class="team-section">
    <h2 class="section-title">Our Core Values</h2>
    <p class="section-subtitle">The principles that guide everything we do</p>
    
    <div class="row g-4">
        <div class="col-md-4">
            <div class="value-card">
                <div class="value-icon">🎯</div>
                <h5 class="fw-bold mb-3">Customer First</h5>
                <p class="text-muted mb-0">Your satisfaction is our top priority. We go above and beyond to ensure you have the best experience.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="value-card">
                <div class="value-icon">💎</div>
                <h5 class="fw-bold mb-3">Quality Assurance</h5>
                <p class="text-muted mb-0">We never compromise on quality. Every product is carefully inspected before reaching you.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="value-card">
                <div class="value-icon">🤝</div>
                <h5 class="fw-bold mb-3">Trust & Integrity</h5>
                <p class="text-muted mb-0">We build lasting relationships through honest business practices and transparent communication.</p>
            </div>
        </div>
    </div>
</div>

<!-- Team/CV Section -->
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($settings['about_cv_enabled']) && $settings['about_cv_enabled'] == '1'): ?>
<div class="team-section">
    <h2 class="section-title">Meet Our Team</h2>
    <p class="section-subtitle">The people behind our success</p>
    
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-lg">
                <div class="card-body p-5">
                    <div class="row">
                        <div class="col-md-4 text-center mb-4 mb-md-0">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($settings['about_cv_photo']) && $settings['about_cv_photo']): ?>
                                <img src="<?php echo e(asset('storage/' . $settings['about_cv_photo'])); ?>" 
                                     alt="<?php echo e($settings['about_cv_name'] ?? 'Team Member'); ?>" 
                                     class="rounded-circle mb-3 shadow" 
                                     style="width: 180px; height: 180px; object-fit: cover;">
                            <?php else: ?>
                                <div class="rounded-circle bg-primary-subtle d-inline-flex align-items-center justify-content-center mb-3" 
                                     style="width: 180px; height: 180px;">
                                    <i class="fa-solid fa-user fa-5x text-primary opacity-50"></i>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <h4 class="fw-bold mb-1"><?php echo e($settings['about_cv_name'] ?? 'Team Member'); ?></h4>
                            <p class="text-primary fw-semibold mb-3"><?php echo e($settings['about_cv_position'] ?? 'Position'); ?></p>
                            
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($settings['about_cv_email']) && $settings['about_cv_email']): ?>
                                <div class="mb-2">
                                    <i class="fa-solid fa-envelope text-muted me-2"></i>
                                    <small><?php echo e($settings['about_cv_email']); ?></small>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($settings['about_cv_phone']) && $settings['about_cv_phone']): ?>
                                <div class="mb-2">
                                    <i class="fa-solid fa-phone text-muted me-2"></i>
                                    <small><?php echo e($settings['about_cv_phone']); ?></small>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        
                        <div class="col-md-8">
                            <h5 class="fw-bold mb-3">
                                <i class="fa-solid fa-user-tie text-primary me-2"></i>About
                            </h5>
                            <p class="text-muted mb-4" style="line-height: 1.8;">
                                <?php echo e($settings['about_cv_bio'] ?? 'Professional bio goes here.'); ?>

                            </p>
                            
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($settings['about_cv_skills']) && $settings['about_cv_skills']): ?>
                                <h5 class="fw-bold mb-3">
                                    <i class="fa-solid fa-code text-primary me-2"></i>Skills
                                </h5>
                                <div class="mb-4">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = explode(',', $settings['about_cv_skills']); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $skill): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                        <span class="badge bg-primary-subtle text-primary me-2 mb-2 px-3 py-2">
                                            <?php echo e(trim($skill)); ?>

                                        </span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($settings['about_cv_education']) && $settings['about_cv_education']): ?>
                                <h5 class="fw-bold mb-3">
                                    <i class="fa-solid fa-graduation-cap text-primary me-2"></i>Education
                                </h5>
                                <p class="text-muted mb-4" style="line-height: 1.8;">
                                    <?php echo e($settings['about_cv_education']); ?>

                                </p>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($settings['about_cv_experience']) && $settings['about_cv_experience']): ?>
                                <h5 class="fw-bold mb-3">
                                    <i class="fa-solid fa-briefcase text-primary me-2"></i>Experience
                                </h5>
                                <p class="text-muted" style="line-height: 1.8;">
                                    <?php echo e($settings['about_cv_experience']); ?>

                                </p>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<!-- Call to Action -->
<div class="text-center mt-5 pt-5 border-top">
    <h3 class="fw-bold mb-3">Ready to Shop?</h3>
    <p class="text-muted mb-4">Explore our collection of premium smartphones and accessories</p>
    <a href="<?php echo e(route('shop.home')); ?>" class="btn btn-primary btn-lg px-5 rounded-pill">
        <i class="fa-solid fa-shopping-bag me-2"></i>Start Shopping
    </a>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('frontend.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Practice\phoneshop\phoneshop\resources\views/frontend/about.blade.php ENDPATH**/ ?>