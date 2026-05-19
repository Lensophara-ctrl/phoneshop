<?php $__env->startSection('content'); ?>
<style>
    .settings-header {
        animation: slideIn 0.5s ease-out;
    }

    @keyframes slideIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .settings-tabs {
        margin-bottom: 2rem;
    }

    .nav-link.active {
        border-bottom: 3px solid var(--primary-color);
    }

    .nav-link {
        border-bottom: 3px solid transparent;
        transition: all 0.3s ease;
    }

    .settings-section {
        animation: fadeIn 0.6s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .form-section {
        background: var(--bs-body-bg);
        border-radius: 1rem;
        padding: 2rem;
        margin-bottom: 1.5rem;
        border: 1px solid var(--bs-border-color);
    }

    .form-section-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid var(--primary-color);
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 0.75rem;
    }

    .form-control {
        border-radius: 0.75rem;
        border: 1px solid var(--bs-border-color);
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.25rem rgba(79, 70, 229, 0.1);
    }

    textarea.form-control {
        min-height: 120px;
        resize: vertical;
    }

    .btn-save {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 0.75rem 2rem;
        border-radius: 0.75rem;
        font-weight: 600;
        border: none;
        transition: all 0.3s ease;
    }

    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        color: white;
    }

    .tab-content {
        border-radius: 1rem;
        padding: 2rem;
        background: var(--bs-body-bg);
    }
</style>

<div class="settings-header">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">
            <i class="fa-solid fa-gear me-2"></i>System Settings
        </h2>
    </div>
</div>

<form action="<?php echo e(route('settings.update')); ?>" method="POST" enctype="multipart/form-data">
    <?php echo csrf_field(); ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-circle-exclamation me-2"></i>
            <strong>Please fix the following errors:</strong>
            <ul class="mb-0 mt-2">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                    <li><?php echo e($error); ?></li>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <ul class="nav nav-tabs settings-tabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general-tab-pane" type="button" role="tab">
                <i class="fa-solid fa-store me-2"></i>Store Info
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="footer-tab" data-bs-toggle="tab" data-bs-target="#footer-tab-pane" type="button" role="tab">
                <i class="fa-solid fa-shoe-prints me-2"></i>Footer
            </button>
        </li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade show active" id="general-tab-pane" role="tabpanel" tabindex="0">
            <div class="form-section">
                <h4 class="form-section-title">Store Branding</h4>
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fa-solid fa-image me-2"></i>Store Logo
                            </label>
                            <?php
                                $currentLogo = $settings->firstWhere('key', 'store_logo')?->value;
                            ?>
                            
                            <!-- Current Logo Preview -->
                            <div id="logoPreviewContainer" class="mb-3 p-3 border rounded bg-light text-center" style="<?php echo e($currentLogo ? '' : 'display: none;'); ?>">
                                <img src="<?php echo e($currentLogo ? asset('storage/' . $currentLogo) : ''); ?>" 
                                     alt="Current Logo" 
                                     id="currentLogoPreview"
                                     class="img-thumbnail" 
                                     style="max-height: 120px; max-width: 100%; object-fit: contain;">
                                <div class="mt-2">
                                    <button type="button" class="btn btn-sm btn-danger" id="removeLogo">
                                        <i class="fa-solid fa-trash me-1"></i>Remove Logo
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Upload New Logo -->
                            <input type="file" name="store_logo" id="logoUpload" class="form-control mb-2" accept="image/*">
                            <input type="hidden" name="remove_logo" id="removeLogoInput" value="0">
                            <small class="text-muted d-block">Upload a logo image (PNG, JPG, SVG, WebP). Max 2MB.</small>
                            <small class="text-muted d-block mt-1">Recommended size: 200x60px or similar aspect ratio</small>
                            
                            <!-- New Upload Preview -->
                            <div id="newLogoPreview" class="mt-3 p-3 border rounded bg-light text-center" style="display: none;">
                                <p class="text-muted mb-2"><small>New logo preview:</small></p>
                                <img src="" alt="New Logo Preview" id="newLogoImage" class="img-thumbnail" style="max-height: 120px; max-width: 100%; object-fit: contain;">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fa-solid fa-icons me-2"></i>Store Icon (FontAwesome)
                            </label>
                            <?php
                                $currentIcon = $settings->firstWhere('key', 'store_icon')?->value ?? 'fa-store';
                            ?>
                            <input type="text" name="store_icon" id="storeIconInput" value="<?php echo e($currentIcon); ?>" class="form-control mb-3" placeholder="fa-store">
                            <small class="text-muted d-block mb-3">
                                Click an icon below or type any FontAwesome class (e.g., fa-rocket, fa-star, fa-heart)
                                <br>
                                <a href="https://fontawesome.com/search?o=r&m=free" target="_blank" rel="noopener" class="text-primary">
                                    <i class="fa-solid fa-external-link-alt me-1"></i>Browse all FontAwesome icons
                                </a>
                            </small>
                            
                            <!-- Icon Picker Grid -->
                            <div class="icon-picker-grid">
                                <div class="icon-option" data-icon="fa-store" title="Store">
                                    <i class="fa-solid fa-store"></i>
                                    <span>Store</span>
                                </div>
                                <div class="icon-option" data-icon="fa-shop" title="Shop">
                                    <i class="fa-solid fa-shop"></i>
                                    <span>Shop</span>
                                </div>
                                <div class="icon-option" data-icon="fa-shopping-bag" title="Shopping Bag">
                                    <i class="fa-solid fa-shopping-bag"></i>
                                    <span>Bag</span>
                                </div>
                                <div class="icon-option" data-icon="fa-cart-shopping" title="Cart">
                                    <i class="fa-solid fa-cart-shopping"></i>
                                    <span>Cart</span>
                                </div>
                                <div class="icon-option" data-icon="fa-mobile-screen-button" title="Mobile">
                                    <i class="fa-solid fa-mobile-screen-button"></i>
                                    <span>Mobile</span>
                                </div>
                                <div class="icon-option" data-icon="fa-mobile" title="Phone">
                                    <i class="fa-solid fa-mobile"></i>
                                    <span>Phone</span>
                                </div>
                                <div class="icon-option" data-icon="fa-bag-shopping" title="Shopping">
                                    <i class="fa-solid fa-bag-shopping"></i>
                                    <span>Shopping</span>
                                </div>
                                <div class="icon-option" data-icon="fa-basket-shopping" title="Basket">
                                    <i class="fa-solid fa-basket-shopping"></i>
                                    <span>Basket</span>
                                </div>
                                <div class="icon-option" data-icon="fa-building" title="Building">
                                    <i class="fa-solid fa-building"></i>
                                    <span>Building</span>
                                </div>
                                <div class="icon-option" data-icon="fa-house" title="House">
                                    <i class="fa-solid fa-house"></i>
                                    <span>House</span>
                                </div>
                                <div class="icon-option" data-icon="fa-gift" title="Gift">
                                    <i class="fa-solid fa-gift"></i>
                                    <span>Gift</span>
                                </div>
                                <div class="icon-option" data-icon="fa-tag" title="Tag">
                                    <i class="fa-solid fa-tag"></i>
                                    <span>Tag</span>
                                </div>
                                <div class="icon-option" data-icon="fa-tags" title="Tags">
                                    <i class="fa-solid fa-tags"></i>
                                    <span>Tags</span>
                                </div>
                                <div class="icon-option" data-icon="fa-box" title="Box">
                                    <i class="fa-solid fa-box"></i>
                                    <span>Box</span>
                                </div>
                                <div class="icon-option" data-icon="fa-boxes-stacked" title="Boxes">
                                    <i class="fa-solid fa-boxes-stacked"></i>
                                    <span>Boxes</span>
                                </div>
                                <div class="icon-option" data-icon="fa-truck-fast" title="Delivery">
                                    <i class="fa-solid fa-truck-fast"></i>
                                    <span>Delivery</span>
                                </div>
                                <div class="icon-option" data-icon="fa-star" title="Star">
                                    <i class="fa-solid fa-star"></i>
                                    <span>Star</span>
                                </div>
                                <div class="icon-option" data-icon="fa-heart" title="Heart">
                                    <i class="fa-solid fa-heart"></i>
                                    <span>Heart</span>
                                </div>
                                <div class="icon-option" data-icon="fa-rocket" title="Rocket">
                                    <i class="fa-solid fa-rocket"></i>
                                    <span>Rocket</span>
                                </div>
                                <div class="icon-option" data-icon="fa-crown" title="Crown">
                                    <i class="fa-solid fa-crown"></i>
                                    <span>Crown</span>
                                </div>
                                <div class="icon-option" data-icon="fa-gem" title="Gem">
                                    <i class="fa-solid fa-gem"></i>
                                    <span>Gem</span>
                                </div>
                                <div class="icon-option" data-icon="fa-fire" title="Fire">
                                    <i class="fa-solid fa-fire"></i>
                                    <span>Fire</span>
                                </div>
                                <div class="icon-option" data-icon="fa-bolt" title="Bolt">
                                    <i class="fa-solid fa-bolt"></i>
                                    <span>Bolt</span>
                                </div>
                            </div>
                            
                            <!-- Live Preview -->
                            <div class="mt-3 p-3 border rounded bg-light">
                                <small class="text-muted d-block mb-2">Preview:</small>
                                <div class="d-flex align-items-center">
                                    <i class="fa-solid <?php echo e($currentIcon); ?> me-2" id="iconPreview" style="font-size: 2rem; color: var(--primary-color); transition: all 0.2s ease;"></i>
                                    <span class="fw-bold" style="font-size: 1.5rem;"><?php echo e($settings->firstWhere('key', 'store_name')?->value ?? 'Your Store'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h4 class="form-section-title">Store Information</h4>
                <div class="row g-4">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $settings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $setting): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array($setting->key, ['store_name', 'store_email', 'store_phone'])): ?>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php switch($setting->key):
                                            case ('store_name'): ?>
                                                <i class="fa-solid fa-shop me-2"></i>Store Name
                                                <?php break; ?>
                                            <?php case ('store_email'): ?>
                                                <i class="fa-solid fa-envelope me-2"></i>Email Address
                                                <?php break; ?>
                                            <?php case ('store_phone'): ?>
                                                <i class="fa-solid fa-phone me-2"></i>Phone Number
                                                <?php break; ?>
                                        <?php endswitch; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </label>
                                    <input type="text" name="<?php echo e($setting->key); ?>" value="<?php echo e($setting->value); ?>" class="form-control">
                                </div>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="footer-tab-pane" role="tabpanel" tabindex="0">
            <div class="form-section">
                <h4 class="form-section-title">Footer Settings</h4>
                
                <div class="row g-4">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $settings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $setting): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($setting->key == 'footer_about'): ?>
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label">
                                        <i class="fa-solid fa-info-circle me-2"></i>About Section
                                    </label>
                                    <textarea name="<?php echo e($setting->key); ?>" class="form-control" placeholder="Enter footer about text"><?php echo e($setting->value); ?></textarea>
                                    <small class="text-muted">This text appears in the About section of the footer</small>
                                </div>
                            </div>
                        <?php elseif($setting->key == 'footer_text'): ?>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">
                                        <i class="fa-solid fa-heading me-2"></i>Footer Text
                                    </label>
                                    <input type="text" name="<?php echo e($setting->key); ?>" value="<?php echo e($setting->value); ?>" class="form-control" placeholder="e.g. PhoneShop. All rights reserved.">
                                </div>
                            </div>
                        <?php elseif($setting->key == 'footer_copyright'): ?>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">
                                        <i class="fa-solid fa-copyright me-2"></i>Copyright Text
                                    </label>
                                    <input type="text" name="<?php echo e($setting->key); ?>" value="<?php echo e($setting->value); ?>" class="form-control" placeholder="e.g. &copy; 2026">
                                </div>
                            </div>
                        <?php elseif($setting->key == 'footer_facebook'): ?>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">
                                        <i class="fab fa-facebook me-2"></i>Facebook URL
                                    </label>
                                    <input type="url" name="<?php echo e($setting->key); ?>" value="<?php echo e($setting->value); ?>" class="form-control" placeholder="https://facebook.com/yourpage">
                                </div>
                            </div>
                        <?php elseif($setting->key == 'footer_twitter'): ?>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">
                                        <i class="fab fa-twitter me-2"></i>Twitter URL
                                    </label>
                                    <input type="url" name="<?php echo e($setting->key); ?>" value="<?php echo e($setting->value); ?>" class="form-control" placeholder="https://twitter.com/yourhandle">
                                </div>
                            </div>
                        <?php elseif($setting->key == 'footer_instagram'): ?>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">
                                        <i class="fab fa-instagram me-2"></i>Instagram URL
                                    </label>
                                    <input type="url" name="<?php echo e($setting->key); ?>" value="<?php echo e($setting->value); ?>" class="form-control" placeholder="https://instagram.com/yourprofile">
                                </div>
                            </div>
                        <?php elseif($setting->key == 'footer_linkedin'): ?>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">
                                        <i class="fab fa-linkedin me-2"></i>LinkedIn URL
                                    </label>
                                    <input type="url" name="<?php echo e($setting->key); ?>" value="<?php echo e($setting->value); ?>" class="form-control" placeholder="https://linkedin.com/company/yourcompany">
                                </div>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            </div>

            <div class="form-section mt-4">
                <h4 class="form-section-title">Footer Section Titles</h4>
                <div class="row g-4">
                    <?php
                        $defaultTitles = '{"quick_links":"Quick Links","support":"Support","follow_us":"Follow Us"}';
                        $savedTitles = $settings->firstWhere('key', 'footer_section_titles')?->value;
                        $titles = $savedTitles ? json_decode($savedTitles, true) : json_decode($defaultTitles, true);
                    ?>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label"><i class="fa-solid fa-link me-2"></i>Quick Links Title</label>
                            <input type="text" name="footer_section_titles_quick_links" value="<?php echo e($titles['quick_links'] ?? 'Quick Links'); ?>" class="form-control" placeholder="Quick Links">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label"><i class="fa-solid fa-headset me-2"></i>Support Title</label>
                            <input type="text" name="footer_section_titles_support" value="<?php echo e($titles['support'] ?? 'Support'); ?>" class="form-control" placeholder="Support">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label"><i class="fa-solid fa-users me-2"></i>Follow Us Title</label>
                            <input type="text" name="footer_section_titles_follow_us" value="<?php echo e($titles['follow_us'] ?? 'Follow Us'); ?>" class="form-control" placeholder="Follow Us">
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-section mt-4">
                <h4 class="form-section-title">Quick Links</h4>
                <p class="text-muted mb-3">Manage the links shown in the Quick Links section of the footer.</p>
                <?php
                    $defaultQuickLinks = '[{"title":"Home","url":"/","icon":"fa-home"},{"title":"About Us","url":"/about","icon":"fa-info-circle"},{"title":"Shop","url":"/shop","icon":"fa-store"},{"title":"Categories","url":"/categories","icon":"fa-list"},{"title":"Contact Us","url":"/contact","icon":"fa-envelope"}]';
                    $savedQuickLinks = $settings->firstWhere('key', 'footer_quick_links')?->value;
                    $quickLinks = $savedQuickLinks ? json_decode($savedQuickLinks, true) : json_decode($defaultQuickLinks, true);
                ?>
                <div id="quickLinksContainer">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $quickLinks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <div class="link-row row g-2 mb-2 align-items-end">
                            <div class="col-md-4">
                                <input type="text" name="footer_quick_links[<?php echo e($index); ?>][title]" value="<?php echo e($link['title']); ?>" class="form-control" placeholder="Link Title">
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="footer_quick_links[<?php echo e($index); ?>][url]" value="<?php echo e($link['url']); ?>" class="form-control" placeholder="URL">
                            </div>
                            <div class="col-md-3">
                                <input type="text" name="footer_quick_links[<?php echo e($index); ?>][icon]" value="<?php echo e($link['icon']); ?>" class="form-control" placeholder="fa-icon-class">
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-outline-danger remove-link" onclick="this.closest('.link-row').remove()">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="addLinkRow('quickLinksContainer')">
                    <i class="fa-solid fa-plus me-1"></i>Add Link
                </button>
                <input type="hidden" name="footer_quick_links_json" id="footerQuickLinksJson">
            </div>

            <div class="form-section mt-4">
                <h4 class="form-section-title">Support Links</h4>
                <p class="text-muted mb-3">Manage the links shown in the Support section of the footer.</p>
                <?php
                    $defaultSupportLinks = '[{"title":"Help Center","url":"#","icon":"fa-headset"},{"title":"Shipping Info","url":"#","icon":"fa-box"},{"title":"Returns","url":"#","icon":"fa-undo"},{"title":"FAQ","url":"#","icon":"fa-question"}]';
                    $savedSupportLinks = $settings->firstWhere('key', 'footer_support_links')?->value;
                    $supportLinks = $savedSupportLinks ? json_decode($savedSupportLinks, true) : json_decode($defaultSupportLinks, true);
                ?>
                <div id="supportLinksContainer">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $supportLinks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <div class="link-row row g-2 mb-2 align-items-end">
                            <div class="col-md-4">
                                <input type="text" name="footer_support_links[<?php echo e($index); ?>][title]" value="<?php echo e($link['title']); ?>" class="form-control" placeholder="Link Title">
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="footer_support_links[<?php echo e($index); ?>][url]" value="<?php echo e($link['url']); ?>" class="form-control" placeholder="URL">
                            </div>
                            <div class="col-md-3">
                                <input type="text" name="footer_support_links[<?php echo e($index); ?>][icon]" value="<?php echo e($link['icon']); ?>" class="form-control" placeholder="fa-icon-class">
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-outline-danger remove-link" onclick="this.closest('.link-row').remove()">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="addLinkRow('supportLinksContainer')">
                    <i class="fa-solid fa-plus me-1"></i>Add Link
                </button>
                <input type="hidden" name="footer_support_links_json" id="footerSupportLinksJson">
            </div>
        </div>
    </div>

    <!-- About Us Settings -->
    <div class="card settings-card mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fa-solid fa-info-circle me-2"></i>About Us Page Settings
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $settings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $setting): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($setting->key == 'about_hero_title'): ?>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Hero Title</label>
                                <input type="text" name="<?php echo e($setting->key); ?>" value="<?php echo e($setting->value); ?>" class="form-control" placeholder="About Us">
                            </div>
                        </div>
                    <?php elseif($setting->key == 'about_hero_subtitle'): ?>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Hero Subtitle</label>
                                <input type="text" name="<?php echo e($setting->key); ?>" value="<?php echo e($setting->value); ?>" class="form-control" placeholder="Your trusted partner...">
                            </div>
                        </div>
                    <?php elseif($setting->key == 'about_story_title'): ?>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label">Story Section Title</label>
                                <input type="text" name="<?php echo e($setting->key); ?>" value="<?php echo e($setting->value); ?>" class="form-control" placeholder="Our Story">
                            </div>
                        </div>
                    <?php elseif($setting->key == 'about_story_content'): ?>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label">Story Content</label>
                                <textarea name="<?php echo e($setting->key); ?>" class="form-control" rows="6" placeholder="Tell your company story..."><?php echo e($setting->value); ?></textarea>
                                <small class="text-muted">Write your company's story and mission</small>
                            </div>
                        </div>
                    <?php elseif($setting->key == 'about_stat_customers'): ?>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">Customers Stat</label>
                                <input type="text" name="<?php echo e($setting->key); ?>" value="<?php echo e($setting->value); ?>" class="form-control" placeholder="1000+">
                            </div>
                        </div>
                    <?php elseif($setting->key == 'about_stat_products'): ?>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">Products Stat</label>
                                <input type="text" name="<?php echo e($setting->key); ?>" value="<?php echo e($setting->value); ?>" class="form-control" placeholder="500+">
                            </div>
                        </div>
                    <?php elseif($setting->key == 'about_stat_authentic'): ?>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">Authentic Stat</label>
                                <input type="text" name="<?php echo e($setting->key); ?>" value="<?php echo e($setting->value); ?>" class="form-control" placeholder="100%">
                            </div>
                        </div>
                    <?php elseif($setting->key == 'about_stat_support'): ?>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">Support Stat</label>
                                <input type="text" name="<?php echo e($setting->key); ?>" value="<?php echo e($setting->value); ?>" class="form-control" placeholder="24/7">
                            </div>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
        </div>
    </div>

    <!-- CV/Resume Settings -->
    <div class="card settings-card mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fa-solid fa-id-card me-2"></i>CV/Resume Settings (About Us Page)
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $settings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $setting): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($setting->key == 'about_cv_enabled'): ?>
                        <div class="col-md-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="<?php echo e($setting->key); ?>" value="1" id="cvEnabled" <?php echo e($setting->value == '1' ? 'checked' : ''); ?>>
                                <label class="form-check-label fw-bold" for="cvEnabled">
                                    Enable CV/Resume Section on About Us Page
                                </label>
                            </div>
                        </div>
                    <?php elseif($setting->key == 'about_cv_photo'): ?>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Profile Photo</label>
                                <input type="file" name="<?php echo e($setting->key); ?>" class="form-control" accept="image/*">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($setting->value): ?>
                                    <div class="mt-2">
                                        <img src="<?php echo e(asset('storage/' . $setting->value)); ?>" alt="CV Photo" class="rounded-circle" style="width: 80px; height: 80px; object-fit: cover;">
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                    <?php elseif($setting->key == 'about_cv_name'): ?>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="<?php echo e($setting->key); ?>" value="<?php echo e($setting->value); ?>" class="form-control" placeholder="Mr. Len Sophara">
                            </div>
                        </div>
                    <?php elseif($setting->key == 'about_cv_position'): ?>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Position/Title</label>
                                <input type="text" name="<?php echo e($setting->key); ?>" value="<?php echo e($setting->value); ?>" class="form-control" placeholder="Software Engineer">
                            </div>
                        </div>
                    <?php elseif($setting->key == 'about_cv_email'): ?>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Email</label>
                                <input type="email" name="<?php echo e($setting->key); ?>" value="<?php echo e($setting->value); ?>" class="form-control" placeholder="email@example.com">
                            </div>
                        </div>
                    <?php elseif($setting->key == 'about_cv_phone'): ?>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Phone</label>
                                <input type="text" name="<?php echo e($setting->key); ?>" value="<?php echo e($setting->value); ?>" class="form-control" placeholder="060276538">
                            </div>
                        </div>
                    <?php elseif($setting->key == 'about_cv_bio'): ?>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label">Bio/About</label>
                                <textarea name="<?php echo e($setting->key); ?>" class="form-control" rows="3" placeholder="Brief introduction..."><?php echo e($setting->value); ?></textarea>
                            </div>
                        </div>
                    <?php elseif($setting->key == 'about_cv_skills'): ?>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label">Skills (comma separated)</label>
                                <input type="text" name="<?php echo e($setting->key); ?>" value="<?php echo e($setting->value); ?>" class="form-control" placeholder="UI/UX, C#, C++, JavaScript, Laravel, React">
                                <small class="text-muted">Separate skills with commas</small>
                            </div>
                        </div>
                    <?php elseif($setting->key == 'about_cv_education'): ?>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label">Education</label>
                                <textarea name="<?php echo e($setting->key); ?>" class="form-control" rows="3" placeholder="University details..."><?php echo e($setting->value); ?></textarea>
                            </div>
                        </div>
                    <?php elseif($setting->key == 'about_cv_experience'): ?>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label">Experience</label>
                                <textarea name="<?php echo e($setting->key); ?>" class="form-control" rows="3" placeholder="Work experience..."><?php echo e($setting->value); ?></textarea>
                            </div>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Telegram Notification Settings -->
    <div class="card settings-card mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fa-brands fa-telegram me-2"></i>Telegram Notification Settings
            </h5>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="fa-solid fa-info-circle me-2"></i>
                <strong>How to setup Telegram notifications:</strong>
                <ol class="mb-0 mt-2">
                    <li>Create a bot using <a href="https://t.me/BotFather" target="_blank">@BotFather</a> on Telegram</li>
                    <li>Copy the Bot Token and paste it below</li>
                    <li>Start a chat with your bot and send any message</li>
                    <li>Get your Chat ID from <a href="https://api.telegram.org/bot<YOUR_BOT_TOKEN>/getUpdates" target="_blank">this URL</a> (replace YOUR_BOT_TOKEN)</li>
                    <li>Copy the Chat ID and paste it below</li>
                </ol>
            </div>
            
            <div class="row g-3">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $settings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $setting): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($setting->key == 'telegram_enabled'): ?>
                        <div class="col-md-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="<?php echo e($setting->key); ?>" value="1" id="telegramEnabled" <?php echo e($setting->value == '1' ? 'checked' : ''); ?>>
                                <label class="form-check-label fw-bold" for="telegramEnabled">
                                    Enable Telegram Notifications
                                </label>
                            </div>
                        </div>
                    <?php elseif($setting->key == 'telegram_bot_token'): ?>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Bot Token</label>
                                <input type="text" name="<?php echo e($setting->key); ?>" value="<?php echo e($setting->value); ?>" class="form-control" placeholder="123456789:ABCdefGHIjklMNOpqrsTUVwxyz">
                            </div>
                        </div>
                    <?php elseif($setting->key == 'telegram_chat_id'): ?>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Chat ID</label>
                                <input type="text" name="<?php echo e($setting->key); ?>" value="<?php echo e($setting->value); ?>" class="form-control" placeholder="123456789">
                            </div>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                
                <div class="col-md-12">
                    <a href="<?php echo e(route('test.telegram')); ?>" class="btn btn-info" target="_blank">
                        <i class="fa-solid fa-paper-plane me-2"></i>Test Telegram Notification
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4 pt-3 border-top">
        <button type="submit" class="btn btn-save">
            <i class="fa-solid fa-floppy-disk me-2"></i>Save Settings
        </button>
    </div>
</form>

<style>
    /* Icon Picker Styles */
    .icon-picker-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(90px, 1fr));
        gap: 0.75rem;
        max-height: 400px;
        overflow-y: auto;
        padding: 1rem;
        background: var(--bs-body-bg);
        border: 1px solid var(--bs-border-color);
        border-radius: 0.75rem;
    }

    .icon-option {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 1rem 0.5rem;
        border: 2px solid var(--bs-border-color);
        border-radius: 0.75rem;
        cursor: pointer;
        transition: all 0.3s ease;
        background: var(--bs-body-bg);
        text-align: center;
    }

    .icon-option:hover {
        border-color: var(--primary-color);
        background: rgba(79, 70, 229, 0.05);
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.15);
    }

    .icon-option.selected {
        border-color: var(--primary-color);
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
    }

    .icon-option i {
        font-size: 2rem;
        color: var(--primary-color);
        margin-bottom: 0.5rem;
        transition: all 0.3s ease;
    }

    .icon-option:hover i {
        transform: scale(1.2);
    }

    .icon-option span {
        font-size: 0.75rem;
        color: var(--text-muted);
        font-weight: 500;
    }

    .icon-option.selected span {
        color: var(--primary-color);
        font-weight: 600;
    }

    /* Scrollbar styling */
    .icon-picker-grid::-webkit-scrollbar {
        width: 8px;
    }

    .icon-picker-grid::-webkit-scrollbar-track {
        background: var(--bs-tertiary-bg);
        border-radius: 4px;
    }

    .icon-picker-grid::-webkit-scrollbar-thumb {
        background: var(--primary-color);
        border-radius: 4px;
    }

    .icon-picker-grid::-webkit-scrollbar-thumb:hover {
        background: var(--primary-hover);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabButtons = document.querySelectorAll('.nav-link');
        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                tabButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
            });
        });

        // Icon Picker Functionality
        const iconOptions = document.querySelectorAll('.icon-option');
        const iconInput = document.getElementById('storeIconInput');
        const iconPreview = document.getElementById('iconPreview');
        const currentIcon = iconInput.value;

        // Function to update icon preview
        function updateIconPreview(iconClass) {
            // Remove all existing classes
            iconPreview.className = '';
            // Add new classes
            iconPreview.className = 'fa-solid ' + iconClass + ' me-2';
            iconPreview.style.fontSize = '2rem';
            iconPreview.style.color = 'var(--primary-color)';
            iconPreview.style.transition = 'all 0.2s ease';
            
            // Add animation
            iconPreview.style.transform = 'scale(1.2)';
            setTimeout(() => {
                iconPreview.style.transform = 'scale(1)';
            }, 200);
        }

        // Set initial selected state
        iconOptions.forEach(option => {
            if (option.dataset.icon === currentIcon) {
                option.classList.add('selected');
            }

            option.addEventListener('click', function() {
                // Remove selected class from all options
                iconOptions.forEach(opt => opt.classList.remove('selected'));
                
                // Add selected class to clicked option
                this.classList.add('selected');
                
                // Update input value
                const selectedIcon = this.dataset.icon;
                iconInput.value = selectedIcon;
                
                // Update preview
                updateIconPreview(selectedIcon);
            });
        });

        // Listen for manual input changes (typing)
        iconInput.addEventListener('input', function() {
            let iconClass = this.value.trim();
            
            // Remove 'fa-solid' or 'fas' if user types it
            iconClass = iconClass.replace(/^(fa-solid|fas)\s+/, '');
            
            // Ensure it starts with 'fa-' if not empty
            if (iconClass && !iconClass.startsWith('fa-')) {
                iconClass = 'fa-' + iconClass;
            }
            
            // Update the input to clean format
            this.value = iconClass;
            
            // Update preview
            if (iconClass) {
                updateIconPreview(iconClass);
            }
            
            // Update selected state in grid
            iconOptions.forEach(opt => {
                if (opt.dataset.icon === iconClass) {
                    opt.classList.add('selected');
                } else {
                    opt.classList.remove('selected');
                }
            });
        });

        // Also listen for paste events
        iconInput.addEventListener('paste', function(e) {
            setTimeout(() => {
                this.dispatchEvent(new Event('input'));
            }, 10);
        });

        // Logo Upload Preview
        const logoUpload = document.getElementById('logoUpload');
        const newLogoPreview = document.getElementById('newLogoPreview');
        const newLogoImage = document.getElementById('newLogoImage');
        const removeLogo = document.getElementById('removeLogo');
        const removeLogoInput = document.getElementById('removeLogoInput');
        const logoPreviewContainer = document.getElementById('logoPreviewContainer');

        if (logoUpload) {
            logoUpload.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    // Validate file size (2MB)
                    if (file.size > 2 * 1024 * 1024) {
                        alert('File size must be less than 2MB');
                        this.value = '';
                        return;
                    }

                    // Validate file type
                    const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/svg+xml', 'image/webp'];
                    if (!validTypes.includes(file.type)) {
                        alert('Please upload a valid image file (JPG, PNG, SVG, WebP)');
                        this.value = '';
                        return;
                    }

                    // Show preview
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        newLogoImage.src = e.target.result;
                        newLogoPreview.style.display = 'block';
                        
                        // Add animation
                        newLogoPreview.style.opacity = '0';
                        setTimeout(() => {
                            newLogoPreview.style.transition = 'opacity 0.3s ease';
                            newLogoPreview.style.opacity = '1';
                        }, 10);
                    };
                    reader.readAsDataURL(file);
                    
                    // Reset remove flag
                    removeLogoInput.value = '0';
                }
            });
        }

        if (removeLogo) {
            removeLogo.addEventListener('click', function() {
                if (confirm('Are you sure you want to remove the current logo?')) {
                    // Hide current logo preview
                    logoPreviewContainer.style.display = 'none';
                    
                    // Set remove flag
                    removeLogoInput.value = '1';
                    
                    // Clear file input
                    if (logoUpload) {
                        logoUpload.value = '';
                    }
                    
                    // Hide new preview
                    newLogoPreview.style.display = 'none';
                }
            });
        }
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Practice\phoneshop\phoneshop\resources\views/settings/index.blade.php ENDPATH**/ ?>