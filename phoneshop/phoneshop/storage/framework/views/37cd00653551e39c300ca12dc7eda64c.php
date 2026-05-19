<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Daily Invoice - <?php echo e($date); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
            border-bottom: 3px solid #667eea;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #667eea;
            margin: 0;
        }
        .info-section {
            margin-bottom: 30px;
        }
        .info-section h3 {
            color: #667eea;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 10px;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin: 20px 0;
        }
        .stat-box {
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 8px;
            text-align: center;
        }
        .stat-label {
            font-size: 12px;
            opacity: 0.9;
            text-transform: uppercase;
        }
        .stat-value {
            font-size: 28px;
            font-weight: bold;
            margin-top: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th {
            background: #667eea;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: 600;
        }
        td {
            padding: 10px 12px;
            border-bottom: 1px solid #e0e0e0;
        }
        tr:hover {
            background: #f9f9f9;
        }
        .summary {
            margin-top: 30px;
            padding: 20px;
            background: #f5f7fa;
            border-radius: 8px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            font-size: 18px;
        }
        .summary-row.total {
            border-top: 2px solid #667eea;
            margin-top: 10px;
            padding-top: 15px;
            font-weight: bold;
            font-size: 24px;
            color: #667eea;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            color: #999;
            font-size: 12px;
            border-top: 1px solid #e0e0e0;
            padding-top: 20px;
        }
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .badge-success {
            background: #d4edda;
            color: #155724;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📅 Daily Sales Invoice</h1>
        <p><?php echo e(\Carbon\Carbon::parse($date)->format('F d, Y')); ?></p>
    </div>

    <div class="stats">
        <div class="stat-box">
            <div class="stat-label">Total Orders</div>
            <div class="stat-value"><?php echo e($totalOrders); ?></div>
        </div>
        <div class="stat-box">
            <div class="stat-label">Total Revenue</div>
            <div class="stat-value">$<?php echo e(number_format($totalRevenue, 2)); ?></div>
        </div>
        <div class="stat-box">
            <div class="stat-label">Avg Order Value</div>
            <div class="stat-value">$<?php echo e($totalOrders > 0 ? number_format($totalRevenue / $totalOrders, 2) : '0.00'); ?></div>
        </div>
    </div>

    <div class="info-section">
        <h3>Order Details</h3>
        <table>
            <thead>
                <tr>
                    <th>Bill No</th>
                    <th>Time</th>
                    <th>Customer</th>
                    <th>Items</th>
                    <th>Payment Method</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                <tr>
                    <td><strong><?php echo e($sale->bill_no); ?></strong></td>
                    <td><?php echo e($sale->created_at->format('H:i A')); ?></td>
                    <td><?php echo e($sale->user->name ?? 'Guest'); ?></td>
                    <td><?php echo e($sale->items->count()); ?> item(s)</td>
                    <td><?php echo e(strtoupper(str_replace('_', ' ', $sale->payment_method))); ?></td>
                    <td>$<?php echo e(number_format($sale->total_price, 2)); ?></td>
                </tr>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="info-section">
        <h3>Items Sold</h3>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $itemsSummary = [];
                    foreach($sales as $sale) {
                        foreach($sale->items as $item) {
                            $productName = $item->phone->name ?? 'Unknown';
                            if (!isset($itemsSummary[$productName])) {
                                $itemsSummary[$productName] = [
                                    'qty' => 0,
                                    'price' => $item->price,
                                    'total' => 0
                                ];
                            }
                            $itemsSummary[$productName]['qty'] += $item->qty;
                            $itemsSummary[$productName]['total'] += $item->subtotal;
                        }
                    }
                ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $itemsSummary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $productName => $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                <tr>
                    <td><strong><?php echo e($productName); ?></strong></td>
                    <td><?php echo e($data['qty']); ?></td>
                    <td>$<?php echo e(number_format($data['price'], 2)); ?></td>
                    <td>$<?php echo e(number_format($data['total'], 2)); ?></td>
                </tr>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="summary">
        <div class="summary-row">
            <span>Total Orders:</span>
            <span><?php echo e($totalOrders); ?></span>
        </div>
        <div class="summary-row">
            <span>Total Items Sold:</span>
            <span><?php echo e(array_sum(array_column($itemsSummary, 'qty'))); ?></span>
        </div>
        <div class="summary-row total">
            <span>Total Revenue:</span>
            <span>$<?php echo e(number_format($totalRevenue, 2)); ?></span>
        </div>
    </div>

    <div class="footer">
        <p><strong>PhoneShop</strong> - Daily Sales Invoice</p>
        <p>This is a computer-generated document. No signature is required.</p>
        <p>Generated on <?php echo e(now()->format('F d, Y H:i:s')); ?></p>
    </div>
</body>
</html>
<?php /**PATH C:\laragon\www\Practice\phoneshop\phoneshop\resources\views/reports/daily-invoice.blade.php ENDPATH**/ ?>