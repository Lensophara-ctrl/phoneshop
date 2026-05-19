<?php $__env->startSection('content'); ?>
<style>
    .chat-sidebar {
        height: calc(100vh - 200px);
        overflow-y: auto;
        border-right: 1px solid var(--bs-border-color);
    }
    .chat-main {
        height: calc(100vh - 200px);
        display: flex;
        flex-direction: column;
    }
    .chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 1rem;
    }
    .chat-conversation-item {
        cursor: pointer;
        transition: all 0.2s;
        border-radius: 0.5rem;
        margin-bottom: 0.25rem;
    }
    .chat-conversation-item:hover {
        background: var(--bs-tertiary-bg);
    }
    .chat-conversation-item.active {
        background: var(--bs-primary-bg-subtle);
        border-left: 3px solid var(--bs-primary);
    }
    .chat-bubble {
        max-width: 80%;
        padding: 0.75rem 1rem;
        border-radius: 1rem;
        margin-bottom: 0.75rem;
        word-wrap: break-word;
    }
    .chat-bubble.customer {
        background: var(--bs-primary);
        color: white;
        border-bottom-left-radius: 0.25rem;
        align-self: flex-start;
    }
    .chat-bubble.admin {
        background: var(--bs-tertiary-bg);
        border-bottom-right-radius: 0.25rem;
        align-self: flex-end;
    }
    .status-badge {
        font-size: 0.7rem;
        padding: 0.2rem 0.5rem;
    }
    .unread-dot {
        width: 8px;
        height: 8px;
        background: var(--bs-danger);
        border-radius: 50%;
        display: inline-block;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">
        <i class="fa-solid fa-comments me-2"></i>Live Chat
    </h2>
    <span class="badge bg-danger rounded-pill" id="unreadBadge"><?php echo e($unreadCount); ?> unread</span>
</div>

<div class="card shadow-sm border-0">
    <div class="row g-0">
        <div class="col-md-4">
            <div class="chat-sidebar p-2">
                <input type="text" class="form-control mb-3" id="searchChat" placeholder="Search conversations...">
                <div id="conversationsList">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $conversations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $conv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <a href="<?php echo e(route('admin.chat.show', $conv)); ?>" class="text-decoration-none text-body chat-conversation-item p-3 d-flex align-items-center <?php echo e(request()->route('conversation')?->id == $conv->id ? 'active' : ''); ?>">
                            <div class="flex-shrink-0 me-3">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <?php echo e(strtoupper(substr($conv->customer_name, 0, 1))); ?>

                                </div>
                            </div>
                            <div class="flex-grow-1 min-width-0">
                                <div class="d-flex justify-content-between">
                                    <strong class="text-truncate"><?php echo e($conv->customer_name); ?></strong>
                                    <small class="text-muted ms-2"><?php echo e($conv->messages->first()?->created_at?->diffForHumans()); ?></small>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted text-truncate d-block"><?php echo e($conv->messages->first()?->message ?? 'No messages'); ?></small>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($conv->messages->where('sender_type', 'customer')->where('is_read', false)->count()): ?>
                                        <span class="unread-dot ms-2"></span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <div>
                                    <span class="badge bg-<?php echo e($conv->status == 'active' ? 'success' : ($conv->status == 'resolved' ? 'warning' : 'secondary')); ?> status-badge"><?php echo e(ucfirst($conv->status)); ?></span>
                                </div>
                            </div>
                        </a>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <p class="text-muted text-center py-4">No conversations yet</p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="chat-main">
                <div class="text-center text-muted py-5">
                    <i class="fa-solid fa-comment-dots fa-3x mb-3"></i>
                    <p>Select a conversation to view messages</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('searchChat')?.addEventListener('input', function() {
        const q = this.value.toLowerCase();
        document.querySelectorAll('.chat-conversation-item').forEach(el => {
            el.style.display = el.textContent.toLowerCase().includes(q) ? 'flex' : 'none';
        });
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Practice\phoneshop\phoneshop\resources\views/admin/chat/index.blade.php ENDPATH**/ ?>