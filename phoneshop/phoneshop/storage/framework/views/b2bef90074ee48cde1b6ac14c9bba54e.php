<?php $__env->startSection('content'); ?>

<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <h2 class="fw-bold mb-1 text-primary">Customer Orders Report</h2>
        <p class="text-muted mb-0">Detailed view of all customer transactions and order status.</p>
    </div>
    <div class="d-flex gap-2 align-items-center">
        <div id="realtime-indicator" class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">
            <i class="fa-solid fa-circle-pulse fa-spin me-1"></i> Live
        </div>
        <button onclick="window.print()" class="btn btn-outline-secondary">
            <i class="fa-solid fa-print me-2"></i>Print Report
        </button>
    </div>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form action="<?php echo e(route('sales.index')); ?>" method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label small fw-bold text-muted">Quick Filter</label>
                <select name="filter" class="form-select border-0 bg-light" onchange="this.form.submit()">
                    <option value="">All Transactions</option>
                    <option value="today" <?php echo e(request('filter') == 'today' ? 'selected' : ''); ?>>Today</option>
                    <option value="this_month" <?php echo e(request('filter') == 'this_month' ? 'selected' : ''); ?>>This Month</option>
                    <option value="this_year" <?php echo e(request('filter') == 'this_year' ? 'selected' : ''); ?>>This Year</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold text-muted">Specific Date</label>
                <input type="date" name="date" class="form-control border-0 bg-light" value="<?php echo e(request('date')); ?>" onchange="this.form.submit()">
            </div>
            <div class="col-md-4 d-flex gap-2">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request()->hasAny(['filter', 'date'])): ?>
                    <a href="<?php echo e(route('sales.index')); ?>" class="btn btn-light w-100 fw-bold">
                        <i class="fa-solid fa-rotate-left me-2"></i>Clear
                    </a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </form>
    </div>
</div>

<div class="row g-4 mb-5">
    <div class="col-md-3">
        <div class="card p-4 border-0 shadow-sm bg-primary text-white">
            <div class="d-flex align-items-center">
                <div class="bg-white bg-opacity-25 rounded-3 p-3 me-3">
                    <i class="fa-solid fa-receipt fs-4"></i>
                </div>
                <div>
                    <div class="text-white-50 small">Total Orders</div>
                    <div class="h4 fw-bold mb-0" id="total-orders"><?php echo e($sales->count()); ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-4 border-0 shadow-sm">
            <div class="d-flex align-items-center">
                <div class="bg-success bg-opacity-10 text-success rounded-3 p-3 me-3">
                    <i class="fa-solid fa-money-bill-trend-up fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small">Total Revenue</div>
                    <div class="h4 fw-bold mb-0" id="total-revenue">$<?php echo e(number_format($sales->sum('total_price'), 2)); ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-4 border-0 shadow-sm">
            <div class="d-flex align-items-center">
                <div class="bg-info bg-opacity-10 text-info rounded-3 p-3 me-3">
                    <i class="fa-solid fa-users fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small">Unique Customers</div>
                    <div class="h4 fw-bold mb-0" id="unique-customers"><?php echo e($sales->unique('user_id')->count()); ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-4 border-0 shadow-sm">
            <div class="d-flex align-items-center">
                <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-3 me-3">
                    <i class="fa-solid fa-box-open fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small">Products Sold</div>
                    <div class="h4 fw-bold mb-0" id="products-sold"><?php echo e($sales->sum(fn($sale) => $sale->items->sum('qty'))); ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="sales-table">
                <thead class="bg-light text-muted small text-uppercase fw-bold">
                    <tr>
                        <th class="px-4 py-3 border-bottom">Bill / Order Info</th>
                        <th class="py-3 border-bottom">Customer</th>
                        <th class="py-3 border-bottom">Items Count</th>
                        <th class="py-3 border-bottom text-end">Total Amount</th>
                        <th class="py-3 border-bottom text-center">Payment</th>
                        <th class="px-4 py-3 border-bottom text-end">Order Date</th>
                        <th class="py-3 border-bottom"></th>
                    </tr>
                </thead>
                <tbody id="sales-tbody">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                    <tr data-id="<?php echo e($sale->id); ?>">
                        <td class="px-4 py-4">
                            <span class="badge bg-secondary-subtle text-secondary border px-3 py-2 rounded-pill small">
                                #<?php echo e($sale->bill_no); ?>

                            </span>
                        </td>
                        <td class="py-4">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sale->user): ?>
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                        <?php echo e(substr($sale->user->name, 0, 1)); ?>

                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark"><?php echo e($sale->user->name); ?></div>
                                        <div class="small text-muted"><?php echo e($sale->user->email); ?></div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <span class="text-muted italic">Guest Customer</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                        <td class="py-4">
                            <div class="fw-semibold"><?php echo e($sale->items->count()); ?> Items</div>
                            <div class="small text-muted">(<?php echo e($sale->items->sum('qty')); ?> units total)</div>
                        </td>
                        <td class="py-4 text-end">
                            <div class="fw-bold text-primary h6 mb-0">$<?php echo e(number_format($sale->total_price, 2)); ?></div>
                        </td>
                        <td class="py-4 text-center text-uppercase">
                            <span class="badge bg-info-subtle text-info border px-2 py-1 small"><?php echo e(str_replace('_', ' ', $sale->payment_method)); ?></span>
                        </td>
                        <td class="px-4 py-4 text-end">
                            <div class="text-dark fw-medium small"><?php echo e($sale->created_at->format('M d, Y')); ?></div>
                            <div class="text-muted x-small" style="font-size: 0.75rem;"><?php echo e($sale->created_at->format('h:i A')); ?></div>
                        </td>
                        <td class="py-4 pe-4 text-end">
                            <a href="<?php echo e(route('sales.show', $sale->id)); ?>" class="btn btn-sm btn-outline-primary">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    @media print {
        .sidebar, .top-nav, .btn, .theme-toggle-btn { display: none !important; }
        .main-content { margin-left: 0 !important; padding: 0 !important; }
        .card { border: none !important; box-shadow: none !important; }
        .table { border: 1px solid #dee2e6 !important; }
        body { background: white !important; }
    }
</style>

<script>
    // Real-time sales polling
    (function() {
        let lastSaleId = <?php echo e($sales->max('id') ?? 0); ?>;
        let pollingInterval = null;
        let isPolling = false;

        // Get current filter params
        const getFilterParams = () => {
            const urlParams = new URLSearchParams(window.location.search);
            const params = {};
            if (urlParams.get('filter')) params.filter = urlParams.get('filter');
            if (urlParams.get('date')) params.date = urlParams.get('date');
            return params;
        };

        // Fetch latest sales data
        const fetchLatestSales = async () => {
            if (isPolling) return;
            isPolling = true;

            try {
                const params = getFilterParams();
                params.after_id = lastSaleId;
                const queryString = new URLSearchParams(params).toString();
                
                const response = await fetch(`<?php echo e(route('sales.latest')); ?>?${queryString}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) throw new Error('Network response was not ok');

                const data = await response.json();

                if (data.success && data.sales.length > 0) {
                    // Update summary cards
                    if (data.summary) {
                        document.getElementById('total-orders').textContent = data.summary.total_orders;
                        document.getElementById('total-revenue').textContent = '$' + data.summary.total_revenue.toFixed(2);
                        document.getElementById('unique-customers').textContent = data.summary.unique_customers;
                        document.getElementById('products-sold').textContent = data.summary.products_sold;
                    }

                    // Check if we have new sales
                    const newSales = data.sales.filter(s => s.id > lastSaleId);
                    if (newSales.length > 0) {
                        // Update lastSaleId
                        lastSaleId = Math.max(...data.sales.map(s => s.id));

                        // Re-render the table with new data
                        renderSalesTable(data.sales);
                        
                        // Show notification for new sales
                        if (newSales.length === 1) {
                            showNewSaleNotification(newSales[0]);
                        } else {
                            showNewSaleNotification({ bill_no: `${newSales.length} orders`, total_price: newSales.reduce((sum, s) => sum + s.total_price, 0) });
                        }
                    }
                }
            } catch (error) {
                console.error('Error fetching sales:', error);
            } finally {
                isPolling = false;
            }
        };

        // Render sales table
        const renderSalesTable = (sales) => {
            const tbody = document.getElementById('sales-tbody');
            if (!tbody) return;

            tbody.innerHTML = sales.map(sale => `
                <tr data-id="${sale.id}">
                    <td class="px-4 py-4">
                        <span class="badge bg-secondary-subtle text-secondary border px-3 py-2 rounded-pill small">
                            #${sale.bill_no}
                        </span>
                    </td>
                    <td class="py-4">
                        ${sale.user ? `
                            <div class="d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                    ${sale.user.name.charAt(0)}
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">${sale.user.name}</div>
                                    <div class="small text-muted">${sale.user.email}</div>
                                </div>
                            </div>
                        ` : '<span class="text-muted italic">Guest Customer</span>'}
                    </td>
                    <td class="py-4">
                        <div class="fw-semibold">${sale.items_count} Items</div>
                        <div class="small text-muted">(${sale.items_total_qty} units total)</div>
                    </td>
                    <td class="py-4 text-end">
                        <div class="fw-bold text-primary h6 mb-0">$${sale.total_price.toFixed(2)}</div>
                    </td>
                    <td class="py-4 text-center text-uppercase">
                        <span class="badge bg-info-subtle text-info border px-2 py-1 small">${sale.payment_method.replace('_', ' ')}</span>
                    </td>
                    <td class="px-4 py-4 text-end">
                        <div class="text-dark fw-medium small">${sale.formatted_date}</div>
                        <div class="text-muted x-small" style="font-size: 0.75rem;">${sale.formatted_time}</div>
                    </td>
                    <td class="py-4 pe-4 text-end">
                        <a href="/sales/${sale.id}" class="btn btn-sm btn-outline-primary">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                    </td>
                </tr>
            `).join('');
        };

        // Show notification for new sale
        const showNewSaleNotification = (sale) => {
            // Create a toast notification
            const toast = document.createElement('div');
            toast.className = 'position-fixed top-0 end-0 m-3 alert alert-success alert-dismissible fade show';
            toast.style.zIndex = '9999';
            toast.innerHTML = `
                <i class="fa-solid fa-bell me-2"></i>
                <strong>New Sale!</strong> ${sale.bill_no} - $${sale.total_price.toFixed(2)}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(toast);

            // Auto remove after 5 seconds
            setTimeout(() => {
                toast.remove();
            }, 5000);
        };

        // Start polling when page loads
        const startPolling = () => {
            // Poll every 5 seconds
            pollingInterval = setInterval(fetchLatestSales, 5000);
        };

        // Stop polling when leaving page
        const stopPolling = () => {
            if (pollingInterval) {
                clearInterval(pollingInterval);
                pollingInterval = null;
            }
        };

        // Initialize
        if (document.getElementById('sales-table')) {
            startPolling();
            
            // Stop polling when navigating away
            window.addEventListener('beforeunload', stopPolling);
            
            // Also stop if the page becomes hidden (user switches tabs)
            document.addEventListener('visibilitychange', () => {
                if (document.hidden) {
                    stopPolling();
                } else {
                    startPolling();
                    fetchLatestSales(); // Immediate fetch when tab becomes visible
                }
            });
        }
    })();
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Practice\phoneshop\phoneshop\resources\views/sales/index.blade.php ENDPATH**/ ?>