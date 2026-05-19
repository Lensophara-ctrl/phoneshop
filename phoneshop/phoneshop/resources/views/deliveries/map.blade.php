@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fa-solid fa-map"></i> Delivery Map</h2>
        <a href="{{ route('deliveries.index') }}" class="btn btn-secondary">
            <i class="fa-solid fa-list"></i> List View
        </a>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div id="deliveryMap" style="height: calc(100vh - 200px); min-height: 600px;"></div>
        </div>
    </div>

    <div class="legend mt-3">
        <div class="d-flex gap-3 flex-wrap">
            <div class="legend-item">
                <span class="badge bg-info">Processing</span>
            </div>
            <div class="legend-item">
                <span class="badge bg-primary">Shipped</span>
            </div>
            <div class="legend-item">
                <span class="badge bg-warning">Out for Delivery</span>
            </div>
        </div>
    </div>
</div>

<style>
.legend-item {
    display: flex;
    align-items: center;
    gap: 8px;
}
</style>

<script>
let map;
let markers = [];
const deliveries = @json($deliveries);

function initMap() {
    // Default center (Phnom Penh)
    const center = { lat: 11.5564, lng: 104.9282 };
    
    map = new google.maps.Map(document.getElementById('deliveryMap'), {
        center: center,
        zoom: 12,
        mapTypeControl: true,
        streetViewControl: true,
        fullscreenControl: true
    });

    const bounds = new google.maps.LatLngBounds();

    deliveries.forEach(delivery => {
        const position = {
            lat: parseFloat(delivery.delivery_latitude),
            lng: parseFloat(delivery.delivery_longitude)
        };

        // Choose marker color based on status
        let markerColor = 'blue';
        if (delivery.delivery_status === 'processing') markerColor = 'blue';
        if (delivery.delivery_status === 'shipped') markerColor = 'purple';
        if (delivery.delivery_status === 'out_for_delivery') markerColor = 'orange';

        const marker = new google.maps.Marker({
            position: position,
            map: map,
            title: `Order #${delivery.bill_no}`,
            icon: {
                url: `http://maps.google.com/mapfiles/ms/icons/${markerColor}-dot.png`
            }
        });

        const infoWindow = new google.maps.InfoWindow({
            content: `
                <div style="padding: 10px;">
                    <h6><strong>Order #${delivery.bill_no}</strong></h6>
                    <p><strong>Customer:</strong> ${delivery.customer_name || delivery.user?.name || 'N/A'}</p>
                    <p><strong>Phone:</strong> ${delivery.customer_phone || 'N/A'}</p>
                    <p><strong>Status:</strong> <span class="badge bg-${getStatusColor(delivery.delivery_status)}">${delivery.delivery_status}</span></p>
                    <p><strong>Total:</strong> $${parseFloat(delivery.total_price).toFixed(2)}</p>
                    ${delivery.delivery_driver_name ? `<p><strong>Driver:</strong> ${delivery.delivery_driver_name}</p>` : ''}
                    <a href="/deliveries/${delivery.id}" class="btn btn-sm btn-primary mt-2">View Details</a>
                </div>
            `
        });

        marker.addListener('click', () => {
            infoWindow.open(map, marker);
        });

        markers.push(marker);
        bounds.extend(position);
    });

    // Fit map to show all markers
    if (deliveries.length > 0) {
        map.fitBounds(bounds);
    }
}

function getStatusColor(status) {
    const colors = {
        'pending': 'secondary',
        'processing': 'info',
        'shipped': 'primary',
        'out_for_delivery': 'warning',
        'delivered': 'success'
    };
    return colors[status] || 'secondary';
}

// Load Google Maps
const apiKey = "{{ env('GOOGLE_MAPS_API_KEY', '') }}";
if (apiKey && apiKey !== 'your_google_maps_api_key_here') {
    const script = document.createElement('script');
    script.src = `https://maps.googleapis.com/maps/api/js?key=${apiKey}&callback=initMap`;
    script.async = true;
    script.defer = true;
    document.head.appendChild(script);
} else {
    document.getElementById('deliveryMap').innerHTML = '<div style="display: flex; align-items: center; justify-content: center; height: 100%; background: #f3f4f6; padding: 20px; text-align: center;"><div><i class="fa-solid fa-exclamation-triangle" style="font-size: 48px; color: #ef4444; margin-bottom: 16px;"></i><h4>Google Maps Not Configured</h4><p>Please add your Google Maps API key to the .env file.</p></div></div>';
}
</script>
@endsection
