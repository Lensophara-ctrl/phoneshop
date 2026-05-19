

<?php $__env->startSection('content'); ?>
<style>
    .profile-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 2rem;
        padding: 3rem 2rem;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
        animation: slideDown 0.6s ease-out;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .profile-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200px;
        height: 200px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        animation: float 6s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px) translateX(0px); }
        50% { transform: translateY(-20px) translateX(20px); }
    }

    .profile-avatar-large {
        width: 160px;
        height: 160px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 2rem;
        background: rgba(255, 255, 255, 0.2);
        border: 4px solid white;
        font-size: 4rem;
        color: white;
        font-weight: 700;
        object-fit: cover;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
        animation: pulse 2s ease-in-out infinite;
    }

    .profile-avatar-large img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
    }

    @keyframes pulse {
        0%, 100% { box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2); }
        50% { box-shadow: 0 8px 48px rgba(0, 0, 0, 0.4); }
    }

    .profile-info-title {
        color: white;
        font-size: 2rem;
        font-weight: 700;
        text-align: center;
        margin-bottom: 0.5rem;
    }

    .profile-info-subtitle {
        color: rgba(255, 255, 255, 0.9);
        text-align: center;
        font-size: 0.95rem;
        letter-spacing: 0.05em;
        text-transform: uppercase;
    }

    .profile-info-badge {
        display: inline-block;
        margin-top: 1rem;
        padding: 0.6rem 1.5rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.85rem;
        letter-spacing: 0.05em;
        background: rgba(255, 255, 255, 0.2);
        color: white;
        backdrop-filter: blur(10px);
    }

    .details-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
        animation: fadeInUp 0.6s ease-out 0.2s both;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .detail-card {
        background: var(--bs-body-bg);
        border: 1px solid var(--bs-border-color);
        border-radius: 1.5rem;
        padding: 2rem;
        transition: all 0.3s ease;
    }

    .detail-card:hover {
        border-color: var(--primary-color);
        box-shadow: 0 4px 15px rgba(79, 70, 229, 0.1);
        transform: translateY(-2px);
    }

    .detail-label {
        color: var(--text-muted);
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .detail-label i {
        color: var(--primary-color);
    }

    .detail-value {
        font-size: 1.15rem;
        font-weight: 600;
        color: var(--text-dark);
        word-break: break-all;
    }

    .action-buttons {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1rem;
        margin-top: 2rem;
        animation: fadeInUp 0.6s ease-out 0.4s both;
    }

    .btn-custom {
        padding: 0.75rem 1.5rem;
        border-radius: 0.75rem;
        font-weight: 600;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .btn-custom:hover {
        transform: translateY(-2px);
    }

    .btn-primary-custom {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .btn-primary-custom:hover {
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    }

    .btn-secondary-custom {
        background: var(--bs-tertiary-bg);
        color: var(--text-dark);
        border: 1px solid var(--bs-border-color);
    }

    .btn-secondary-custom:hover {
        background: var(--bs-secondary-bg);
    }

    .btn-danger-custom {
        background: #ef4444;
        color: white;
    }

    .btn-danger-custom:hover {
        background: #dc2626;
        box-shadow: 0 4px 15px rgba(239, 68, 68, 0.4);
    }

    .status-badge {
        display: inline-block;
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.85rem;
    }

    .status-admin {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .status-customer {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
    }

    .back-button {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
        color: var(--primary-color);
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .back-button:hover {
        gap: 1rem;
        color: var(--primary-hover);
    }

    .meta-info {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1rem;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid rgba(255, 255, 255, 0.2);
    }

    .meta-item {
        text-align: center;
    }

    .meta-value {
        color: white;
        font-size: 1.25rem;
        font-weight: 700;
    }

    .meta-label {
        color: rgba(255, 255, 255, 0.8);
        font-size: 0.8rem;
        margin-top: 0.25rem;
    }

    @media (max-width: 768px) {
        .profile-header {
            padding: 2rem 1.5rem;
        }

        .profile-avatar-large {
            width: 130px;
            height: 130px;
            font-size: 3rem;
        }

        .profile-info-title {
            font-size: 1.5rem;
        }

        .details-grid {
            grid-template-columns: 1fr;
        }

        .action-buttons {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="container">
    <a href="<?php echo e(route('users.index')); ?>" class="back-button">
        <i class="fa-solid fa-arrow-left"></i>Back to Users
    </a>

    <div class="profile-header">
        <div class="profile-avatar-large">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->profile_image): ?>
                <img src="<?php echo e(asset('storage/' . $user->profile_image)); ?>" alt="<?php echo e($user->name); ?>">
            <?php else: ?>
                <?php echo e(substr($user->name, 0, 1)); ?>

            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <h1 class="profile-info-title"><?php echo e($user->name); ?></h1>
        <p class="profile-info-subtitle"><?php echo e($user->email); ?></p>
        <div style="text-align: center;">
            <span class="profile-info-badge">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->role === 'admin'): ?>
                    <i class="fa-solid fa-shield-halved me-1"></i>Administrator
                <?php else: ?>
                    <i class="fa-solid fa-user me-1"></i>Customer
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </span>
        </div>
        <div class="meta-info">
            <div class="meta-item">
                <div class="meta-value">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->role === 'admin'): ?>
                        Admin
                    <?php else: ?>
                        Customer
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div class="meta-label">Account Type</div>
            </div>
            <div class="meta-item">
                <div class="meta-value"><?php echo e($user->created_at->format('Y')); ?></div>
                <div class="meta-label">Member Since</div>
            </div>
        </div>
    </div>

    <div class="details-grid">
        <div class="detail-card">
            <div class="detail-label">
                <i class="fa-solid fa-envelope"></i>Email Address
            </div>
            <div class="detail-value"><?php echo e($user->email); ?></div>
        </div>

        <div class="detail-card">
            <div class="detail-label">
                <i class="fa-solid fa-user-tag"></i>Account Status
            </div>
            <div>
                <span class="status-badge <?php echo e($user->role === 'admin' ? 'status-admin' : 'status-customer'); ?>">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->role === 'admin'): ?>
                        <i class="fa-solid fa-star me-1"></i>Admin User
                    <?php else: ?>
                        <i class="fa-solid fa-check me-1"></i>Active Customer
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </span>
            </div>
        </div>

        <div class="detail-card">
            <div class="detail-label">
                <i class="fa-solid fa-calendar"></i>Member Since
            </div>
            <div class="detail-value"><?php echo e($user->created_at->format('F d, Y')); ?></div>
        </div>
    </div>

    <div class="action-buttons">
        <a href="<?php echo e(route('users.reset-password', $user)); ?>" class="btn-custom btn-primary-custom">
            <i class="fa-solid fa-key"></i>Reset Password
        </a>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->id !== auth()->id()): ?>
            <form method="POST" action="<?php echo e(route('users.destroy', $user)); ?>" onsubmit="return confirm('Are you sure you want to delete this user?');" style="width: 100%;">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <button type="submit" class="btn-custom btn-danger-custom" style="width: 100%;">
                    <i class="fa-solid fa-trash"></i>Delete User
                </button>
            </form>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <a href="<?php echo e(route('users.index')); ?>" class="btn-custom btn-secondary-custom">
            <i class="fa-solid fa-arrow-left"></i>Back
        </a>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Practice\phoneshop\phoneshop\resources\views/users/show.blade.php ENDPATH**/ ?>