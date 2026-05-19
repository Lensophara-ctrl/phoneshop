

<?php $__env->startSection('content'); ?>

<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <h2 class="fw-bold mb-1">Product Inventory</h2>
        <p class="text-muted mb-0">Manage your products and stock levels.</p>
    </div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('permission', 'create_phones')): ?>
        <a href="<?php echo e(route('phones.create')); ?>" class="btn btn-primary rounded-pill px-4">
            <i class="fa-solid fa-plus me-2"></i>Add Product
        </a>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>

<!-- Search Bar -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <div class="position-relative">
                    <i class="fa-solid fa-search position-absolute" style="left: 15px; top: 50%; transform: translateY(-50%); color: #6c757d;"></i>
                    <input type="text" id="searchInput" class="form-control ps-5 rounded-pill" placeholder="Search products by name, category, or price..." style="border: 2px solid #e9ecef;">
                </div>
            </div>
            <div class="col-md-3">
                <select id="categoryFilter" class="form-select rounded-pill" style="border: 2px solid #e9ecef;">
                    <option value="">All Categories</option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $phones->pluck('category')->unique(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <option value="<?php echo e($category->name); ?>"><?php echo e($category->name); ?></option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </select>
            </div>
            <div class="col-md-3">
                <select id="stockFilter" class="form-select rounded-pill" style="border: 2px solid #e9ecef;">
                    <option value="">All Stock Status</option>
                    <option value="in-stock">In Stock</option>
                    <option value="low-stock">Low Stock</option>
                    <option value="out-of-stock">Out of Stock</option>
                </select>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm overflow-hidden">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase fw-bold">
                    <tr>
                        <th class="px-4 py-3 border-0">Device</th>
                        <th class="py-3 border-0">Category</th>
                        <th class="py-3 border-0">Price</th>
                        <th class="py-3 border-0">Stock Status</th>
                        <th class="px-4 py-3 border-0 text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $phones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $phone): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                    <tr class="product-row" 
                        data-name="<?php echo e(strtolower($phone->name)); ?>" 
                        data-category="<?php echo e(strtolower($phone->category->name)); ?>" 
                        data-price="<?php echo e($phone->price); ?>"
                        data-stock="<?php echo e($phone->qty > 10 ? 'in-stock' : ($phone->qty > 0 ? 'low-stock' : 'out-of-stock')); ?>">
                        <td class="px-4 py-3">
                            <div class="d-flex align-items-center">
                                <div class="rounded-3 overflow-hidden shadow-sm border me-3" style="width: 50px; height: 50px;">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($phone->image && file_exists(public_path('storage/'.$phone->image))): ?>
                                        <img src="<?php echo e(asset('storage/'.$phone->image)); ?>" class="w-100 h-100" style="object-fit: cover;" alt="<?php echo e($phone->name); ?>">
                                    <?php elseif($phone->image): ?>
                                        <img src="<?php echo e(asset('storage/'.$phone->image)); ?>" class="w-100 h-100" style="object-fit: cover;" alt="<?php echo e($phone->name); ?>" onerror="this.parentElement.innerHTML='<div class=\'bg-light w-100 h-100 d-flex align-items-center justify-content-center text-muted\'><i class=\'fa-solid fa-mobile-screen small\'></i></div>'">
                                    <?php else: ?>
                                        <div class="bg-light w-100 h-100 d-flex align-items-center justify-content-center text-muted">
                                            <i class="fa-solid fa-mobile-screen small"></i>
                                        </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <div class="fw-bold text-dark"><?php echo e($phone->name); ?></div>
                            </div>
                        </td>
                        <td class="py-3">
                            <span class="badge bg-light text-muted border fw-normal px-2 py-1">
                                <?php echo e($phone->category->name); ?>

                            </span>
                        </td>
                        <td class="py-3 fw-bold text-dark">$<?php echo e(number_format($phone->price, 2)); ?></td>
                        <td class="py-3">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($phone->qty > 10): ?>
                                <span class="badge rounded-pill bg-success bg-opacity-10 text-success px-3"><?php echo e($phone->qty); ?> In Stock</span>
                            <?php elseif($phone->qty > 0): ?>
                                <span class="badge rounded-pill bg-warning bg-opacity-10 text-warning px-3"><?php echo e($phone->qty); ?> Low Stock</span>
                            <?php else: ?>
                                <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger px-3">Out of Stock</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('permission', 'edit_phones')): ?>
                                    <a href="<?php echo e(route('phones.edit', $phone)); ?>" class="btn btn-light btn-sm rounded-pill px-3 border shadow-sm">
                                        <i class="fa-solid fa-pen-to-square text-warning me-1"></i>Edit
                                    </a>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('permission', 'delete_phones')): ?>
                                    <form action="<?php echo e(route('phones.destroy', $phone)); ?>" method="POST" class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button class="btn btn-light btn-sm rounded-pill px-3 border shadow-sm" onclick="return confirm('Delete this phone?')">
                                            <i class="fa-solid fa-trash text-danger me-1"></i>Delete
                                        </button>
                                    </form>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!auth()->user()->hasPermission('edit_phones') && !auth()->user()->hasPermission('delete_phones')): ?>
                                    <span class="badge bg-secondary">View Only</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // Live search and filter functionality
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const stockFilter = document.getElementById('stockFilter');
    const productRows = document.querySelectorAll('.product-row');
    
    function filterProducts() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedCategory = categoryFilter.value.toLowerCase();
        const selectedStock = stockFilter.value;
        
        let visibleCount = 0;
        
        productRows.forEach(row => {
            const name = row.dataset.name;
            const category = row.dataset.category;
            const price = row.dataset.price;
            const stock = row.dataset.stock;
            
            // Check if row matches all filters
            const matchesSearch = name.includes(searchTerm) || 
                                category.includes(searchTerm) || 
                                price.includes(searchTerm);
            const matchesCategory = !selectedCategory || category === selectedCategory;
            const matchesStock = !selectedStock || stock === selectedStock;
            
            if (matchesSearch && matchesCategory && matchesStock) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        // Show "no results" message if needed
        showNoResultsMessage(visibleCount);
    }
    
    function showNoResultsMessage(count) {
        let noResultsRow = document.getElementById('noResultsRow');
        
        if (count === 0) {
            if (!noResultsRow) {
                const tbody = document.querySelector('tbody');
                noResultsRow = document.createElement('tr');
                noResultsRow.id = 'noResultsRow';
                noResultsRow.innerHTML = `
                    <td colspan="5" class="text-center py-5">
                        <i class="fa-solid fa-search fa-3x text-muted mb-3"></i>
                        <p class="text-muted mb-0">No products found matching your search criteria.</p>
                    </td>
                `;
                tbody.appendChild(noResultsRow);
            }
        } else {
            if (noResultsRow) {
                noResultsRow.remove();
            }
        }
    }
    
    // Add event listeners
    searchInput.addEventListener('input', filterProducts);
    categoryFilter.addEventListener('change', filterProducts);
    stockFilter.addEventListener('change', filterProducts);
    
    // Add search icon animation
    searchInput.addEventListener('focus', function() {
        this.style.borderColor = '#0d6efd';
    });
    
    searchInput.addEventListener('blur', function() {
        this.style.borderColor = '#e9ecef';
    });
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Practice\phoneshop\phoneshop\resources\views/phones/index.blade.php ENDPATH**/ ?>