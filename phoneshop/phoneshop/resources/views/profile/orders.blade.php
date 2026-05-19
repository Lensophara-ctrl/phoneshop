@extends('frontend.layouts.app')

@section('content')
<style>
    .orders-container {
        max-width: 1200px;
        margin: 40px auto;
        padding: 0 20px;
    }
    
    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 40px;
        border-radius: 12px;
        margin-bottom: 30px;
        text-align: center;
    }
    
    .page-header h1 {
        margin: 0;
        font-size: 32px;
    }
    
    .page-header p {
        margin: 10px 0 0 0;
        opacity: 0.9;
    }
    
    .order-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        margin-bottom: 20px;
        overflow: hidden;
        transition: all 0.3s;
    }
    
    .order-card:hover {
        box-shadow: 0 4px 16px rgba(0,0,0,0.15);
        transform: translateY(-2px);
    }
    
    .order-header {
        padding: 20px;
        border-bottom: 1px solid #f0f0f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }
    
    .order-info h3 {
        margin: 0 0 5px 0;
        font-size: 20px;
        color: #333;
    }
    
    .order-info p {
        margin: 0;
        color: #666;
        font-size: 14px;
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
    
    .badge-pending {
        background: #fff3cd;
        color: #856404;
    }
    
    .badge-completed {
        background: #d4edda;
        color: #155724;
    }
    
    .badge-approved {
        background: #d1ecf1;
        color: #0c5460;
    }
    
    .badge-locked {
        background: #f8d7da;
        color: #721c24;
    }
    
    .order-body {
        padding: 20px;
    }
    
    .order-details {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-bottom: 20px;
    }
    
    .detail-item {
        padding: 15px;
        background: #f9f9f9;
        border-radius: 8px;
    }
    
    .detail-label {
        font-size: 12px;
        color: #666;
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: 5px;
    }
    
    .detail-value {
        font-size: 16px;
        color: #333;
        font-weight: 600;
    }
    
    .order-items {
        margin-top: 20px;
    }
    
    .order-items h4 {
        font-size: 16px;
        margin-bottom: 15px;
        color: #333;
    }
    
    .item-row {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .item-row:last-child {
        border-bottom: none;
    }
    
    .item-name {
        font-weight: 500;
        color: #333;
    }
    
    .item-qty {
        color: #666;
        font-size: 14px;
    }
    
    .item-price {
        font-weight: 600;
        color: #667eea;
    }
    
    .order-actions {
        display: flex;
        gap: 10px;
        padding: 20px;
        border-top: 1px solid #f0f0f0;
        background: #fafafa;
        flex-wrap: wrap;
    }
    
    .btn {
        padding: 10px 20px;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .btn-primary {
        background: #667eea;
        color: white;
    }
    
    .btn-primary:hover {
        background: #5568d3;
        transform: translateY(-1px);
    }
    
    .btn-success {
        background: #28a745;
        color: white;
    }
    
    .btn-success:hover {
        background: #218838;
    }
    
    .btn-warning {
        background: #ffc107;
        color: #333;
    }
    
    .btn-warning:hover {
        background: #e0a800;
    }
    
    .btn-secondary {
        background: #6c757d;
        color: white;
    }
    
    .btn-secondary:hover {
        background: #5a6268;
    }
    
    .btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .empty-state i {
        font-size: 64px;
        color: #ddd;
        margin-bottom: 20px;
    }
    
    .empty-state h3 {
        color: #333;
        margin-bottom: 10px;
    }
    
    .empty-state p {
        color: #666;
        margin-bottom: 20px;
    }
    
    .lock-notice {
        background: #fff3cd;
        border: 1px solid #ffc107;
        padding: 12px;
        border-radius: 8px;
        margin-top: 15px;
        font-size: 13px;
        color: #856404;
    }
    
    .lock-notice i {
        margin-right: 8px;
    }
</style>

<div class="orders-container">
    <div class="page-header">
        <h1>📦 My Orders</h1>
        <p>View your order history, lock reports, and download invoices</p>
    </div>
    
    @if($orders->count() > 0)
        @foreach($orders as $order)
        <div class="order-card">
            <div class="order-header">
                <div class="order-info">
                    <h3>Order #{{ $order->bill_no }}</h3>
                    <p>Placed on {{ $order->created_at->format('F d, Y \a\t H:i A') }}</p>
                </div>
                <div class="order-badges">
                    <span class="badge badge-{{ $order->status }}">{{ ucfirst($order->status) }}</span>
                    <span class="badge badge-{{ $order->approval_status }}">{{ ucfirst($order->approval_status) }}</span>
                    @if($order->is_locked)
                        <span class="badge badge-locked">🔒 Locked</span>
                    @endif
                </div>
            </div>
            
            <div class="order-body">
                <div class="order-details">
                    <div class="detail-item">
                        <div class="detail-label">Total Amount</div>
                        <div class="detail-value">{{ $order->currency }} {{ number_format($order->total_price, 2) }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Payment Method</div>
                        <div class="detail-value">{{ strtoupper(str_replace('_', ' ', $order->payment_method)) }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Items</div>
                        <div class="detail-value">{{ $order->items->count() }} item(s)</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Delivery Status</div>
                        <div class="detail-value">{{ $order->delivery_status ? ucfirst($order->delivery_status) : 'Pending' }}</div>
                    </div>
                </div>
                
                <div class="order-items">
                    <h4>Order Items</h4>
                    @foreach($order->items as $item)
                    <div class="item-row">
                        <div>
                            <div class="item-name">{{ $item->phone->name ?? 'Product' }}</div>
                            <div class="item-qty">Quantity: {{ $item->qty }}</div>
                        </div>
                        <div class="item-price">{{ $order->currency }} {{ number_format($item->subtotal, 2) }}</div>
                    </div>
                    @endforeach
                </div>
                
                @if($order->is_locked)
                <div class="lock-notice">
                    <i class="fa-solid fa-lock"></i>
                    <strong>This order report is locked.</strong> 
                    Locked on {{ $order->locked_at ? $order->locked_at->format('F d, Y \a\t H:i A') : 'N/A' }}. 
                    No further modifications can be made.
                </div>
                @endif
            </div>
            
            <div class="order-actions">
                @if(!$order->is_locked && $order->status === 'completed')
                    <button class="btn btn-warning" onclick="lockOrder({{ $order->id }})">
                        <i class="fa-solid fa-lock"></i>
                        Lock Report
                    </button>
                @endif
                
                <a href="{{ route('customer.order.invoice', $order->id) }}" class="btn btn-primary" target="_blank">
                    <i class="fa-solid fa-download"></i>
                    Download Invoice
                </a>
                
                @if($order->receipt_path)
                    <a href="{{ asset('storage/' . $order->receipt_path) }}" class="btn btn-secondary" target="_blank">
                        <i class="fa-solid fa-receipt"></i>
                        View Receipt
                    </a>
                @endif
                
                @if($order->status === 'pending')
                    <a href="{{ route('shop.checkout.return') }}?bill_no={{ $order->bill_no }}" class="btn btn-success">
                        <i class="fa-solid fa-credit-card"></i>
                        Complete Payment
                    </a>
                @endif
            </div>
        </div>
        @endforeach
        
        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $orders->links() }}
        </div>
    @else
        <div class="empty-state">
            <i class="fa-solid fa-shopping-bag"></i>
            <h3>No Orders Yet</h3>
            <p>You haven't placed any orders yet. Start shopping to see your orders here!</p>
            <a href="{{ route('shop.home') }}" class="btn btn-primary">
                <i class="fa-solid fa-shop"></i>
                Start Shopping
            </a>
        </div>
    @endif
</div>

<script>
async function lockOrder(orderId) {
    if (!confirm('Are you sure you want to lock this order report?\n\nOnce locked, this action cannot be undone and no modifications can be made to this order.')) {
        return;
    }
    
    try {
        const response = await fetch(`/customer/orders/${orderId}/lock`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('✅ Order report locked successfully!\n\nYou can now download the invoice as a permanent record.');
            window.location.reload();
        } else {
            alert('❌ Failed to lock order: ' + data.message);
        }
    } catch (error) {
        alert('❌ An error occurred while locking the order');
        console.error(error);
    }
}
</script>
@endsection
