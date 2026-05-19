<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice - {{ $order->bill_no }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            padding: 40px;
            background: #f5f5f5;
        }
        
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        
        .invoice-header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 3px solid #667eea;
        }
        
        .invoice-header h1 {
            color: #667eea;
            font-size: 36px;
            margin-bottom: 10px;
        }
        
        .invoice-header p {
            color: #666;
            font-size: 14px;
        }
        
        .lock-badge {
            display: inline-block;
            background: #f8d7da;
            color: #721c24;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            margin-top: 10px;
        }
        
        .invoice-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 40px;
        }
        
        .info-section h3 {
            color: #667eea;
            font-size: 14px;
            text-transform: uppercase;
            margin-bottom: 15px;
            letter-spacing: 1px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .info-label {
            color: #666;
            font-size: 14px;
        }
        
        .info-value {
            font-weight: 600;
            color: #333;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0;
        }
        
        .items-table thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .items-table th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .items-table td {
            padding: 15px;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .items-table tbody tr:hover {
            background: #f9f9f9;
        }
        
        .item-name {
            font-weight: 600;
            color: #333;
        }
        
        .item-category {
            font-size: 12px;
            color: #666;
            margin-top: 4px;
        }
        
        .totals-section {
            margin-top: 30px;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 8px;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            font-size: 16px;
        }
        
        .total-row.grand-total {
            border-top: 2px solid #667eea;
            margin-top: 10px;
            padding-top: 15px;
            font-size: 20px;
            font-weight: bold;
            color: #667eea;
        }
        
        .status-badges {
            display: flex;
            gap: 10px;
            margin: 20px 0;
            justify-content: center;
        }
        
        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-badge.completed {
            background: #d4edda;
            color: #155724;
        }
        
        .status-badge.pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-badge.approved {
            background: #d1ecf1;
            color: #0c5460;
        }
        
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 2px solid #f0f0f0;
            text-align: center;
            color: #999;
            font-size: 12px;
        }
        
        .footer p {
            margin: 5px 0;
        }
        
        .lock-notice {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        
        .lock-notice strong {
            color: #856404;
            display: block;
            margin-bottom: 5px;
        }
        
        .lock-notice p {
            color: #856404;
            font-size: 13px;
            margin: 0;
        }
        
        @media print {
            body {
                background: white;
                padding: 0;
            }
            
            .invoice-container {
                box-shadow: none;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="invoice-header">
            <h1>INVOICE</h1>
            <p>Order #{{ $order->bill_no }}</p>
            <p>{{ $order->created_at->format('F d, Y') }}</p>
            
            @if($isLocked)
                <div class="lock-badge">
                    🔒 LOCKED REPORT
                </div>
            @endif
        </div>
        
        <div class="status-badges">
            <span class="status-badge {{ $order->status }}">{{ ucfirst($order->status) }}</span>
            <span class="status-badge {{ $order->approval_status }}">{{ ucfirst($order->approval_status) }}</span>
        </div>
        
        @if($isLocked && $lockedInfo)
        <div class="lock-notice">
            <strong>🔒 This is a Locked Report</strong>
            <p>This order report was locked on {{ \Carbon\Carbon::parse($lockedInfo->locked_at)->format('F d, Y \a\t H:i A') }}. This document serves as an official and permanent record of this transaction.</p>
        </div>
        @endif
        
        <div class="invoice-info">
            <div class="info-section">
                <h3>Customer Information</h3>
                <div class="info-row">
                    <span class="info-label">Name:</span>
                    <span class="info-value">{{ $order->customer_name ?? $order->user->name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Email:</span>
                    <span class="info-value">{{ $order->customer_email ?? $order->user->email }}</span>
                </div>
                @if($order->customer_phone)
                <div class="info-row">
                    <span class="info-label">Phone:</span>
                    <span class="info-value">{{ $order->customer_phone }}</span>
                </div>
                @endif
                @if($order->customer_address)
                <div class="info-row">
                    <span class="info-label">Address:</span>
                    <span class="info-value">{{ $order->customer_address }}</span>
                </div>
                @endif
            </div>
            
            <div class="info-section">
                <h3>Order Details</h3>
                <div class="info-row">
                    <span class="info-label">Order Date:</span>
                    <span class="info-value">{{ $order->created_at->format('M d, Y') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Order Time:</span>
                    <span class="info-value">{{ $order->created_at->format('H:i A') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Payment Method:</span>
                    <span class="info-value">{{ strtoupper(str_replace('_', ' ', $order->payment_method)) }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Currency:</span>
                    <span class="info-value">{{ $order->currency }}</span>
                </div>
            </div>
        </div>
        
        <table class="items-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th style="text-align: center;">Quantity</th>
                    <th style="text-align: right;">Unit Price</th>
                    <th style="text-align: right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>
                        <div class="item-name">{{ $item->phone->name ?? 'Product' }}</div>
                        @if($item->phone && $item->phone->category)
                            <div class="item-category">{{ $item->phone->category->name }}</div>
                        @endif
                    </td>
                    <td style="text-align: center;">{{ $item->qty }}</td>
                    <td style="text-align: right;">{{ $order->currency }} {{ number_format($item->price, 2) }}</td>
                    <td style="text-align: right; font-weight: 600;">{{ $order->currency }} {{ number_format($item->subtotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="totals-section">
            <div class="total-row">
                <span>Subtotal:</span>
                <span>{{ $order->currency }} {{ number_format($order->subtotal, 2) }}</span>
            </div>
            @if($order->tax > 0)
            <div class="total-row">
                <span>Tax:</span>
                <span>{{ $order->currency }} {{ number_format($order->tax, 2) }}</span>
            </div>
            @endif
            <div class="total-row grand-total">
                <span>TOTAL:</span>
                <span>{{ $order->currency }} {{ number_format($order->total_price, 2) }}</span>
            </div>
        </div>
        
        @if($order->order_notes)
        <div style="margin-top: 30px; padding: 15px; background: #f9f9f9; border-radius: 8px;">
            <strong style="color: #667eea;">Order Notes:</strong>
            <p style="margin-top: 8px; color: #666;">{{ $order->order_notes }}</p>
        </div>
        @endif
        
        <div class="footer">
            <p><strong>PhoneShop</strong></p>
            <p>Thank you for your business!</p>
            <p>This is a computer-generated invoice. No signature is required.</p>
            <p>Generated on {{ now()->format('F d, Y \a\t H:i:s A') }}</p>
            @if($isLocked)
                <p style="color: #721c24; font-weight: bold; margin-top: 10px;">
                    🔒 This is a locked and official document
                </p>
            @endif
        </div>
    </div>
</body>
</html>
