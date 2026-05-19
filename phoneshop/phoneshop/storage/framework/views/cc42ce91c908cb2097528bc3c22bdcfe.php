

<?php $__env->startSection('content'); ?>

<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <h2 class="fw-bold mb-1">Categories</h2>
        <p class="text-muted mb-0">Organize your products into collections.</p>
    </div>
    <a href="<?php echo e(route('categories.create')); ?>" class="btn btn-primary rounded-pill px-4">
        <i class="fa-solid fa-plus me-2"></i>Add Category
    </a>
</div>

<div class="card border-0 shadow-sm overflow-hidden">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase fw-bold">
                    <tr>
                        <th class="px-4 py-3 border-0">Image</th>
                        <th class="py-3 border-0">Category Name</th>
                        <th class="py-3 border-0">Total Products</th>
                        <th class="px-4 py-3 border-0 text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                    <tr>
                        <td class="px-4 py-3">
                            <div class="rounded-3 overflow-hidden shadow-sm border" style="width: 60px; height: 60px;">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($category->image): ?>
                                    <img src="<?php echo e(asset('storage/'.$category->image)); ?>" class="w-100 h-100" style="object-fit: cover;">
                                <?php else: ?>
                                    <div class="bg-light w-100 h-100 d-flex align-items-center justify-content-center">
                                        <i class="fa-solid fa-layer-group text-muted small"></i>
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </td>
                        <td class="py-3 fw-bold text-dark"><?php echo e($category->name); ?></td>
                        <td class="py-3 text-muted"><?php echo e($category->phones_count ?? 0); ?> Items</td>
                        <td class="px-4 py-3 text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="<?php echo e(route('categories.edit', $category)); ?>" class="btn btn-light btn-sm rounded-pill px-3 border shadow-sm">
                                    <i class="fa-solid fa-pen-to-square text-warning me-1"></i>Edit
                                </a>
                                <form action="<?php echo e(route('categories.destroy', $category)); ?>" method="POST" class="d-inline">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button class="btn btn-light btn-sm rounded-pill px-3 border shadow-sm" onclick="return confirm('Delete this category?')">
                                        <i class="fa-solid fa-trash text-danger me-1"></i>Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Practice\phoneshop\phoneshop\resources\views/categories/index.blade.php ENDPATH**/ ?>