<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }
        .email-header p {
            margin: 10px 0 0;
            font-size: 16px;
            opacity: 0.9;
        }
        .email-body {
            padding: 30px 20px;
        }
        .order-info {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
        }
        .order-info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .order-info-row:last-child {
            border-bottom: none;
        }
        .order-info-label {
            font-weight: 600;
            color: #6c757d;
        }
        .order-info-value {
            font-weight: 700;
            color: #7c3aed;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .items-table th {
            background: #f8f9fa;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: #495057;
            border-bottom: 2px solid #dee2e6;
        }
        .items-table td {
            padding: 12px;
            border-bottom: 1px solid #e9ecef;
        }
        .items-table tr:last-child td {
            border-bottom: none;
        }
        .total-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
        }
        .total-row.grand-total {
            border-top: 2px solid #7c3aed;
            padding-top: 15px;
            margin-top: 10px;
            font-size: 20px;
            font-weight: 700;
            color: #7c3aed;
        }
        .payment-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }
        .payment-bakong {
            background: #e0d4fc;
            color: #7c3aed;
        }
        .payment-cash {
            background: #d1f4e0;
            color: #10b981;
        }
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            background: #d1f4e0;
            color: #10b981;
        }
        .email-footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin: 20px 0;
        }
        .icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <div class="icon">✅</div>
            <h1>Order Confirmed!</h1>
            <p>Thank you for your purchase</p>
        </div>

        <!-- Body -->
        <div class="email-body">
            <p>Hi <strong><?php echo e($sale->user->name); ?></strong>,</p>
            <p>Your order has been successfully confirmed and is being processed. Here are your order details:</p>

            <!-- Order Info -->
            <div class="order-info">
                <div class="order-info-row">
                    <span class="order-info-label">Order Number:</span>
                    <span class="order-info-value"><?php echo e($sale->bill_no); ?></span>
                </div>
                <div class="order-info-row">
                    <span class="order-info-label">Order Date:</span>
                    <span><?php echo e($sale->created_at->format('M d, Y h:i A')); ?></span>
                </div>
                <div class="order-info-row">
                    <span class="order-info-label">Payment Method:</span>
                    <span class="payment-badge <?php echo e($sale->payment_method === 'bakong' ? 'payment-bakong' : 'payment-cash'); ?>">
                        <?php echo e(strtoupper(str_replace('_', ' ', $sale->payment_method))); ?>

                    </span>
                </div>
                <div class="order-info-row">
                    <span class="order-info-label">Status:</span>
                    <span class="status-badge"><?php echo e(strtoupper($sale->status)); ?></span>
                </div>
            </div>

            <!-- Items Table -->
            <h3 style="color: #333; margin-top: 30px;">Order Items</h3>
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th style="text-align: center;">Qty</th>
                        <th style="text-align: right;">Price</th>
                        <th style="text-align: right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $sale->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                    <tr>
                        <td>
                            <strong><?php echo e($item->phone->name); ?></strong><br>
                            <small style="color: #6c757d;"><?php echo e($item->phone->category->name); ?></small>
                        </td>
                        <td style="text-align: center;"><?php echo e($item->qty); ?></td>
                        <td style="text-align: right;">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sale->currency === 'KHR'): ?>
                                <?php echo e(number_format($item->price * $sale->exchange_rate, 0)); ?> ៛
                            <?php else: ?>
                                $<?php echo e(number_format($item->price, 2)); ?>

                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                        <td style="text-align: right; font-weight: 600;">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sale->currency === 'KHR'): ?>
                                <?php echo e(number_format($item->subtotal * $sale->exchange_rate, 0)); ?> ៛
                            <?php else: ?>
                                $<?php echo e(number_format($item->subtotal, 2)); ?>

                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                    </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </tbody>
            </table>

            <!-- Total Section -->
            <div class="total-section">
                <div class="total-row">
                    <span>Subtotal:</span>
                    <span>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sale->currency === 'KHR'): ?>
                            <?php echo e(number_format($sale->subtotal * $sale->exchange_rate, 0)); ?> ៛
                        <?php else: ?>
                            $<?php echo e(number_format($sale->subtotal, 2)); ?>

                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </span>
                </div>
                <div class="total-row">
                    <span>Tax:</span>
                    <span>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sale->currency === 'KHR'): ?>
                            <?php echo e(number_format($sale->tax * $sale->exchange_rate, 0)); ?> ៛
                        <?php else: ?>
                            $<?php echo e(number_format($sale->tax, 2)); ?>

                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </span>
                </div>
                <div class="total-row grand-total">
                    <span>Total:</span>
                    <span>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sale->currency === 'KHR'): ?>
                            <?php echo e(number_format($sale->total_price * $sale->exchange_rate, 0)); ?> ៛
                        <?php else: ?>
                            $<?php echo e(number_format($sale->total_price, 2)); ?>

                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </span>
                </div>
            </div>

            <!-- Call to Action -->
            <div style="text-align: center; margin-top: 30px;">
                <a href="<?php echo e(route('sales.show', $sale->id)); ?>" class="button">View Order Details</a>
            </div>

            <p style="margin-top: 30px; color: #6c757d; font-size: 14px;">
                If you have any questions about your order, please don't hesitate to contact us.
            </p>
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <p style="margin: 0;"><strong><?php echo e($settings['store_name'] ?? 'Shop'); ?></strong></p>
            <p style="margin: 5px 0;"><?php echo e($settings['store_address'] ?? ''); ?></p>
            <p style="margin: 5px 0;">Phone: <?php echo e($settings['store_phone'] ?? ''); ?></p>
            <p style="margin: 15px 0 0; font-size: 12px;">
                © <?php echo e(date('Y')); ?> <?php echo e($settings['store_name'] ?? 'Shop'); ?>. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\laragon\www\Practice\phoneshop\phoneshop\resources\views/emails/order-confirmation.blade.php ENDPATH**/ ?>