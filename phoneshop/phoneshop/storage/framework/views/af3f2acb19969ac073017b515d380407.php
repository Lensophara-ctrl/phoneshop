

<?php $__env->startSection('content'); ?>
<style>
    .order-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        overflow: hidden;
        transition: all 0.3s;
        margin-bottom: 20px;
    }
    
    .order-card:hover {
        box-shadow: 0 4px 16px rgba(0,0,0,0.12);
    }
    
    .order-header {
        padding: 20px;
        border-bottom: 1px solid #f0f0f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .order-badges {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }
    
    .badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
    }
    
    .badge.pending {
        background: #fff3cd;
        color: #856404;
    }
    
    .badge.approved {
        background: #d4edda;
        color: #155724;
    }
    
    .badge.rejected {
        background: #f8d7da;
        color: #721c24;
    }
    
    .badge.completed {
        background: #d1ecf1;
        color: #0c5460;
    }
    
    .order-body {
        padding: 20px;
    }
    
    .order-details {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-bottom: 15px;
    }
    
    .detail-item {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }
    
    .detail-label {
        font-size: 12px;
        color: #666;
        font-weight: 500;
    }
    
    .detail-value {
        font-size: 14px;
        color: #333;
        font-weight: 600;
    }
    
    .order-actions {
        display: flex;
        gap: 10px;
        padding: 20px;
        border-top: 1px solid #f0f0f0;
        background: #fafafa;
    }
    
    .receipt-preview {
        margin-top: 15px;
        text-align: center;
    }
    
    .receipt-preview img {
        max-width: 100%;
        max-height: 300px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .stat-card {
        background: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        transition: transform 0.2s;
    }
    
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.12);
    }
    
    .stat-label {
        font-size: 13px;
        color: #666;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }
    
    .stat-value {
        font-size: 32px;
        font-weight: 700;
        color: #667eea;
    }
</style>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">📋 Order Approval System</h2>
            <p class="text-muted mb-0">Review and approve customer orders</p>
        </div>
    </div>
    
    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-label">Pending</div>
                <div class="stat-value" id="stat-pending"><?php echo e($orders->where('approval_status', 'pending')->count()); ?></div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-label">Approved</div>
                <div class="stat-value" id="stat-approved"><?php echo e($orders->where('approval_status', 'approved')->count()); ?></div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-label">Rejected</div>
                <div class="stat-value" id="stat-rejected"><?php echo e($orders->where('approval_status', 'rejected')->count()); ?></div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-label">Total Orders</div>
                <div class="stat-value" id="stat-total"><?php echo e($orders->total()); ?></div>
            </div>
        </div>
    </div>
    
    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="<?php echo e(route('orders.approval')); ?>" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Approval Status</label>
                    <select name="approval_status" class="form-select">
                        <option value="">All</option>
                        <option value="pending" <?php echo e(request('approval_status') == 'pending' ? 'selected' : ''); ?>>Pending</option>
                        <option value="approved" <?php echo e(request('approval_status') == 'approved' ? 'selected' : ''); ?>>Approved</option>
                        <option value="rejected" <?php echo e(request('approval_status') == 'rejected' ? 'selected' : ''); ?>>Rejected</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Payment Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        <option value="pending" <?php echo e(request('status') == 'pending' ? 'selected' : ''); ?>>Pending</option>
                        <option value="completed" <?php echo e(request('status') == 'completed' ? 'selected' : ''); ?>>Completed</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fa-solid fa-filter me-2"></i>Apply Filters
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Orders List -->
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($orders->count() > 0): ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
        <div class="order-card">
            <div class="order-header">
                <div class="order-info">
                    <h5 class="mb-1"><?php echo e($order->bill_no); ?></h5>
                    <p class="text-muted mb-0"><?php echo e($order->customer_name ?? $order->user->name ?? 'Guest'); ?> • <?php echo e($order->created_at->format('M d, Y H:i')); ?></p>
                </div>
                <div class="order-badges">
                    <span class="badge <?php echo e($order->approval_status); ?>"><?php echo e(ucfirst($order->approval_status)); ?></span>
                    <span class="badge <?php echo e($order->status); ?>"><?php echo e(ucfirst($order->status)); ?></span>
                </div>
            </div>
            <div class="order-body">
                <div class="order-details">
                    <div class="detail-item">
                        <span class="detail-label">Total Amount</span>
                        <span class="detail-value"><?php echo e($order->currency); ?> <?php echo e(number_format($order->total_price, 2)); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Payment Method</span>
                        <span class="detail-value"><?php echo e(strtoupper(str_replace('_', ' ', $order->payment_method))); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Items</span>
                        <span class="detail-value"><?php echo e($order->items->count()); ?> item(s)</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Email</span>
                        <span class="detail-value"><?php echo e($order->customer_email ?? $order->user->email ?? 'N/A'); ?></span>
                    </div>
                </div>
                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->receipt_path): ?>
                <div class="receipt-preview">
                    <img src="<?php echo e(asset('storage/' . $order->receipt_path)); ?>" alt="Receipt">
                    <a href="<?php echo e(asset('storage/' . $order->receipt_path)); ?>" target="_blank" class="d-block mt-2 text-primary">View Full Receipt</a>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->rejection_reason): ?>
                <div class="alert alert-danger mt-3">
                    <strong>Rejection Reason:</strong> <?php echo e($order->rejection_reason); ?>

                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <div class="order-actions">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->approval_status === 'pending'): ?>
                    <button class="btn btn-success flex-fill" onclick="approveOrder(<?php echo e($order->id); ?>)">
                        <i class="fa-solid fa-check me-2"></i>Approve
                    </button>
                    <button class="btn btn-danger flex-fill" onclick="openRejectModal(<?php echo e($order->id); ?>)">
                        <i class="fa-solid fa-times me-2"></i>Reject
                    </button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <a href="<?php echo e(route('sales.show', $order->id)); ?>" class="btn btn-primary flex-fill">
                    <i class="fa-solid fa-eye me-2"></i>View Details
                </a>
            </div>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        
        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            <?php echo e($orders->links()); ?>

        </div>
    <?php else: ?>
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fa-solid fa-inbox fa-3x text-muted mb-3"></i>
                <h4>No orders found</h4>
                <p class="text-muted">There are no orders matching your filters</p>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>

<!-- Rejection Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="rejectForm">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" id="rejectOrderId">
                    <div class="mb-3">
                        <label class="form-label">Rejection Reason</label>
                        <textarea class="form-control" id="rejectionReason" rows="4" placeholder="Enter rejection reason..." required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="confirmReject()">Confirm Rejection</button>
            </div>
        </div>
    </div>
</div>

<script>
let rejectModal;

document.addEventListener('DOMContentLoaded', function() {
    rejectModal = new bootstrap.Modal(document.getElementById('rejectModal'));
});

async function approveOrder(orderId) {
    if (!confirm('Are you sure you want to approve this order?')) return;
    
    try {
        const response = await fetch(`/orders/${orderId}/approve`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('✅ Order approved successfully!');
            window.location.reload();
        } else {
            alert('❌ ' + data.message);
        }
    } catch (error) {
        alert('❌ Failed to approve order');
        console.error(error);
    }
}

function openRejectModal(orderId) {
    document.getElementById('rejectOrderId').value = orderId;
    document.getElementById('rejectionReason').value = '';
    rejectModal.show();
}

async function confirmReject() {
    const orderId = document.getElementById('rejectOrderId').value;
    const reason = document.getElementById('rejectionReason').value.trim();
    
    if (!reason) {
        alert('Please provide a rejection reason');
        return;
    }
    
    try {
        const response = await fetch(`/orders/${orderId}/reject`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
            },
            body: JSON.stringify({ reason })
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('✅ Order rejected successfully!');
            rejectModal.hide();
            window.location.reload();
        } else {
            alert('❌ ' + data.message);
        }
    } catch (error) {
        alert('❌ Failed to reject order');
        console.error(error);
    }
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Practice\phoneshop\phoneshop\resources\views/orders/approval.blade.php ENDPATH**/ ?>