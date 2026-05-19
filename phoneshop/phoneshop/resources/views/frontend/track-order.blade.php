@extends('frontend.layouts.app')

@section('content')
<div class="track-order-page">
    <div class="track-container">
        <div class="track-header">
            <h1><i class="fa-solid fa-truck"></i> Track Your Order</h1>
            <p>Real-time delivery tracking</p>
        </div>

        @if(isset($sale))
        <div class="order-info-card">
            <div class="order-header">
                <div>
                    <h3>Order #{{ $sale->bill_no }}</h3>
                    <p class="text-muted">Placed on {{ $sale->created_at->format('M d, Y h:i A') }}</p>
                </div>
                <div class="status-badge status-{{ $sale->delivery_status }}">
                    {{ ucfirst($sale->delivery_status) }}
                </div>
            </div>

            <div class="delivery-timeline">
                <div class="timeline-item {{ $sale->status == 'completed' ? 'completed' : '' }}">
                    <div class="timeline-icon">
                        <i class="fa-solid fa-check"></i>
                    </div>
                    <div class="timeline-content">
                        <h4>Order Confirmed</h4>
                        <p>{{ $sale->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                </div>

                <div class="timeline-item {{ in_array($sale->delivery_status, ['processing', 'shipped', 'out_for_delivery', 'delivered']) ? 'completed' : '' }}">
                    <div class="timeline-icon">
                        <i class="fa-solid fa-box"></i>
                    </div>
                    <div class="timeline-content">
                        <h4>Processing</h4>
                        <p>Your order is being prepared</p>
                    </div>
                </div>

                <div class="timeline-item {{ in_array($sale->delivery_status, ['shipped', 'out_for_delivery', 'delivered']) ? 'completed' : '' }}">
                    <div class="timeline-icon">
                        <i class="fa-solid fa-shipping-fast"></i>
                    </div>
                    <div class="timeline-content">
                        <h4>Shipped</h4>
                        <p>Order is on the way</p>
                    </div>
                </div>

                <div class="timeline-item {{ in_array($sale->delivery_status, ['out_for_delivery', 'delivered']) ? 'completed' : '' }}">
                    <div class="timeline-icon">
                        <i class="fa-solid fa-truck"></i>
                    </div>
                    <div class="timeline-content">
                        <h4>Out for Delivery</h4>
                        @if($sale->delivery_estimated_at)
                            <p>Estimated: {{ $sale->delivery_estimated_at->format('M d, Y h:i A') }}</p>
                        @endif
                        @if($sale->delivery_driver_name)
                            <p><strong>Driver:</strong> {{ $sale->delivery_driver_name }}</p>
                            <p><strong>Phone:</strong> {{ $sale->delivery_driver_phone }}</p>
                        @endif
                    </div>
                </div>

                <div class="timeline-item {{ $sale->delivery_status == 'delivered' ? 'completed' : '' }}">
                    <div class="timeline-icon">
                        <i class="fa-solid fa-home"></i>
                    </div>
                    <div class="timeline-content">
                        <h4>Delivered</h4>
                        @if($sale->delivery_completed_at)
                            <p>{{ $sale->delivery_completed_at->format('M d, Y h:i A') }}</p>
                        @endif
                    </div>
                </div>
            </div>

            @if($sale->delivery_latitude && $sale->delivery_longitude)
            <div class="delivery-map-section">
                <h3><i class="fa-solid fa-map-marker-alt"></i> Delivery Location</h3>
                <div id="deliveryMap" style="height: 400px; border-radius: 12px; margin-top: 16px;"></div>
            </div>
            @endif

            <div class="order-items">
                <h3><i class="fa-solid fa-shopping-bag"></i> Order Items</h3>
                @foreach($sale->items as $item)
                <div class="item-row">
                    <div class="item-info">
                        <span class="item-name">{{ $item->phone->name }}</span>
                        <span class="item-qty">x{{ $item->qty }}</span>
                    </div>
                    <span class="item-price">${{ number_format($item->subtotal, 2) }}</span>
                </div>
                @endforeach
                <div class="total-row">
                    <span>Total</span>
                    <span>${{ number_format($sale->total_price, 2) }}</span>
                </div>
            </div>

            <div class="delivery-address">
                <h3><i class="fa-solid fa-location-dot"></i> Delivery Address</h3>
                <p>{{ $sale->customer_address }}</p>
                <p>{{ $sale->customer_city }} @if($sale->customer_postal_code){{ $sale->customer_postal_code }}@endif</p>
                <p><i class="fa-solid fa-phone"></i> {{ $sale->customer_phone }}</p>
            </div>
        </div>
        @else
        <div class="search-form">
            <form action="{{ route('shop.track') }}" method="GET">
                <div class="form-group">
                    <label for="bill_no">Enter your order number</label>
                    <input type="text" 
                           id="bill_no" 
                           name="bill_no" 
                           class="form-control" 
                           placeholder="INV-XXXXXXXXX"
                           required>
                </div>
                <button type="submit" class="btn-track">
                    <i class="fa-solid fa-search"></i> Track Order
                </button>
            </form>
        </div>
        @endif
    </div>
</div>

<style>
.track-order-page {
    min-height: 100vh;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 40px 20px;
}

.track-container {
    max-width: 900px;
    margin: 0 auto;
}

.track-header {
    text-align: center;
    color: white;
    margin-bottom: 40px;
}

.track-header h1 {
    font-size: 36px;
    margin-bottom: 8px;
    font-weight: 700;
}

.order-info-card {
    background: white;
    border-radius: 20px;
    padding: 32px;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

.order-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-bottom: 24px;
    border-bottom: 2px solid #e5e7eb;
    margin-bottom: 32px;
}

.order-header h3 {
    font-size: 24px;
    color: #1f2937;
    margin: 0;
}

.status-badge {
    padding: 8px 20px;
    border-radius: 50px;
    font-weight: 700;
    font-size: 14px;
}

.status-pending { background: #fef3c7; color: #92400e; }
.status-processing { background: #dbeafe; color: #1e40af; }
.status-shipped { background: #e0e7ff; color: #4338ca; }
.status-out_for_delivery { background: #fce7f3; color: #9f1239; }
.status-delivered { background: #d1fae5; color: #065f46; }

.delivery-timeline {
    margin: 32px 0;
}

.timeline-item {
    display: flex;
    gap: 20px;
    padding: 20px 0;
    position: relative;
    opacity: 0.4;
}

.timeline-item.completed {
    opacity: 1;
}

.timeline-item:not(:last-child)::after {
    content: '';
    position: absolute;
    left: 19px;
    top: 60px;
    width: 2px;
    height: calc(100% - 20px);
    background: #e5e7eb;
}

.timeline-item.completed:not(:last-child)::after {
    background: #667eea;
}

.timeline-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #e5e7eb;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.timeline-item.completed .timeline-icon {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.timeline-content h4 {
    font-size: 18px;
    color: #1f2937;
    margin: 0 0 4px 0;
}

.timeline-content p {
    color: #6b7280;
    margin: 0;
    font-size: 14px;
}

.delivery-map-section,
.order-items,
.delivery-address {
    margin-top: 32px;
    padding-top: 32px;
    border-top: 2px solid #e5e7eb;
}

.delivery-map-section h3,
.order-items h3,
.delivery-address h3 {
    font-size: 20px;
    color: #1f2937;
    margin-bottom: 16px;
}

.item-row {
    display: flex;
    justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid #e5e7eb;
}

.item-info {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.item-name {
    font-weight: 600;
    color: #1f2937;
}

.item-qty {
    font-size: 13px;
    color: #6b7280;
}

.item-price {
    font-weight: 700;
    color: #667eea;
}

.total-row {
    display: flex;
    justify-content: space-between;
    padding: 16px 0;
    font-size: 20px;
    font-weight: 700;
    color: #1f2937;
}

.search-form {
    background: white;
    border-radius: 20px;
    padding: 40px;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    font-weight: 600;
    color: #374151;
    margin-bottom: 8px;
}

.form-control {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    font-size: 15px;
}

.btn-track {
    width: 100%;
    padding: 16px 24px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 700;
    cursor: pointer;
}

.btn-track:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 24px -8px rgba(102, 126, 234, 0.5);
}
</style>

@if(isset($sale) && $sale->delivery_latitude && $sale->delivery_longitude)
<script>
function initDeliveryMap() {
    const deliveryLocation = {
        lat: {{ $sale->delivery_latitude }},
        lng: {{ $sale->delivery_longitude }}
    };

    const map = new google.maps.Map(document.getElementById('deliveryMap'), {
        center: deliveryLocation,
        zoom: 15,
        mapTypeControl: true,
        streetViewControl: true
    });

    new google.maps.Marker({
        position: deliveryLocation,
        map: map,
        title: 'Delivery Location',
        icon: {
            url: 'http://maps.google.com/mapfiles/ms/icons/red-dot.png'
        }
    });
}

// Load Google Maps
const apiKey = "{{ env('GOOGLE_MAPS_API_KEY', '') }}";
if (apiKey && apiKey !== 'your_google_maps_api_key_here') {
    const script = document.createElement('script');
    script.src = `https://maps.googleapis.com/maps/api/js?key=${apiKey}&callback=initDeliveryMap`;
    script.async = true;
    script.defer = true;
    document.head.appendChild(script);
} else {
    document.getElementById('deliveryMap').innerHTML = '<div style="display: flex; align-items: center; justify-content: center; height: 100%; background: #f3f4f6; padding: 20px; text-align: center;"><div><i class="fa-solid fa-exclamation-triangle" style="font-size: 48px; color: #ef4444; margin-bottom: 16px;"></i><h4>Google Maps Not Configured</h4><p>Please add your Google Maps API key to the .env file.</p></div></div>';
}
</script>
@endif
@endsection
