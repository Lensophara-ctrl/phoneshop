@extends('frontend.layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-5 pb-3 border-bottom">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2">
                <li class="breadcrumb-item"><a href="{{ route('shop.home') }}" class="text-decoration-none">Home</a></li>
                <li class="breadcrumb-item active">{{ $category->name }}</li>
            </ol>
        </nav>
        <h2 class="fw-bold mb-0">Category: <span class="text-primary">{{ $category->name }}</span></h2>
    </div>
    <div class="text-muted small">
        {{ $phones->count() }} Products found
    </div>
</div>

<div class="row g-4">
@forelse($phones as $phone)
    <div class="col-6 col-md-4 col-lg-3">
        <div class="card h-100 border-0 shadow-sm overflow-hidden">
            <div class="position-relative overflow-hidden group" style="height: 250px;">
                @if($phone->image)
                    <img src="{{ asset('storage/'.$phone->image) }}" class="card-img-top h-100 w-100" style="object-fit: cover; transition: transform 0.5s ease;">
                @else
                    <div class="bg-body-tertiary h-100 w-100 d-flex flex-column align-items-center justify-content-center text-muted">
                        <i class="fa-solid fa-image mb-2" style="font-size: 2rem;"></i>
                        <span class="small">No Image</span>
                    </div>
                @endif
                <div class="position-absolute top-0 start-0 p-2">
                    @if($phone->qty <= 0)
                        <span class="badge bg-danger shadow-sm rounded-pill py-2 px-3 fw-bold text-uppercase">Out of Stock</span>
                    @elseif($phone->qty <= 5)
                        <span class="badge bg-warning text-dark shadow-sm rounded-pill py-2 px-3 fw-bold text-uppercase">Low Stock: {{ $phone->qty }}</span>
                    @else
                        <span class="badge bg-success shadow-sm rounded-pill py-2 px-3 fw-bold text-uppercase">In Stock: {{ $phone->qty }}</span>
                    @endif
                </div>
                <div class="position-absolute top-0 end-0 p-2">
                    <span class="badge bg-white text-dark shadow-sm rounded-pill py-2 px-3 fw-bold">${{ number_format($phone->price, 2) }}</span>
                </div>
            </div>
            <div class="card-body p-4 text-center">
                <div class="text-muted small mb-1">{{ $category->name }}</div>
                <h6 class="fw-bold mb-3 text-truncate">{{ $phone->name }}</h6>
                
                <div class="d-grid gap-2">
                    <a href="{{ route('shop.product', $phone->id) }}" class="btn btn-outline-secondary btn-sm fw-semibold py-2">Details</a>
                    <a href="{{ route('shop.add', $phone->id) }}" class="btn btn-primary btn-sm fw-semibold py-2">
                        <i class="fa-solid fa-cart-plus me-1"></i>Add to Cart
                    </a>
                </div>
            </div>
        </div>
    </div>
@empty
    <div class="col-12">
        <div class="text-center py-5 bg-white rounded-4 shadow-sm">
            <i class="fa-solid fa-box-open opacity-10 mb-4" style="font-size: 5rem;"></i>
            <h4 class="fw-bold text-muted">No products found</h4>
            <p class="text-muted">There are no products available in this category at the moment.</p>
            <a href="{{ route('shop.home') }}" class="btn btn-primary rounded-pill px-4 mt-3">Back to Home</a>
        </div>
    </div>
@endforelse
</div>

<style>
    .card-img-top:hover {
        transform: scale(1.1);
    }
    [data-bs-theme="dark"] .btn-outline-secondary {
        color: #e2e8f0;
        border-color: #475569;
    }
    [data-bs-theme="dark"] .btn-outline-secondary:hover {
        background-color: #1e293b;
        border-color: #64748b;
    }
</style>

@endsection
