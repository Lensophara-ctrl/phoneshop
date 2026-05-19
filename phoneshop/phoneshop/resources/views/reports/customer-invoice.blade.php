<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Customer Invoice - {{ $customer->name }}</title>
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
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 15px;
        }
        .info-item {
            padding: 10px;
            background: #f9f9f9;
            border-radius: 5px;
        }
        .info-label {
            font-weight: bold;
            color: #666;
            font-size: 12px;
            text-transform: uppercase;
        }
        .info-value {
            font-size: 16px;
            margin-top: 5px;
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
        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📄 Customer Invoice</h1>
        <p>Generated on {{ now()->format('F d, Y') }}</p>
    </div>

    <div class="info-section">
        <h3>Customer Information</h3>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Customer Name</div>
                <div class="info-value">{{ $customer->name }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Email</div>
                <div class="info-value">{{ $customer->email }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Report Period</div>
                <div class="info-value">{{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Total Orders</div>
                <div class="info-value">{{ $totalOrders }}</div>
            </div>
        </div>
    </div>

    <div class="info-section">
        <h3>Order History</h3>
        <table>
            <thead>
                <tr>
                    <th>Bill No</th>
                    <th>Date</th>
                    <th>Items</th>
                    <th>Payment Method</th>
                    <th>Status</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sales as $sale)
                <tr>
                    <td><strong>{{ $sale->bill_no }}</strong></td>
                    <td>{{ $sale->created_at->format('M d, Y') }}</td>
                    <td>{{ $sale->items->count() }} item(s)</td>
                    <td>{{ strtoupper(str_replace('_', ' ', $sale->payment_method)) }}</td>
                    <td>
                        <span class="badge badge-{{ $sale->status === 'completed' ? 'success' : 'warning' }}">
                            {{ ucfirst($sale->status) }}
                        </span>
                    </td>
                    <td>${{ number_format($sale->total_price, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="summary">
        <div class="summary-row">
            <span>Total Orders:</span>
            <span>{{ $totalOrders }}</span>
        </div>
        <div class="summary-row total">
            <span>Total Amount:</span>
            <span>${{ number_format($totalRevenue, 2) }}</span>
        </div>
    </div>

    <div class="footer">
        <p><strong>PhoneShop</strong> - Customer Invoice Report</p>
        <p>This is a computer-generated document. No signature is required.</p>
        <p>Report locked and generated on {{ now()->format('F d, Y H:i:s') }}</p>
    </div>
</body>
</html>
