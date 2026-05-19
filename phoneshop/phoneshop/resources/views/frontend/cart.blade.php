@extends('frontend.layouts.app')

@section('content')

<div class="row mb-5">
    <div class="col-lg-8">
        <div class="d-flex align-items-center mb-4">
            <h3 class="fw-bold mb-0">Shopping Cart</h3>
            <span class="badge bg-primary rounded-pill ms-3 px-3 py-2">{{ session('cart') ? count(session('cart')) : 0 }} Items</span>
        </div>

        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="card-body p-0">
                @if(session('cart') && count(session('cart')) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted small text-uppercase">
                                <tr>
                                    <th class="px-4 py-3">Product</th>
                                    <th class="py-3">Price</th>
                                    <th class="py-3">Quantity</th>
                                    <th class="py-3 text-end px-4">Subtotal</th>
                                    <th class="py-3"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $total = 0 @endphp
                                @foreach(session('cart') as $id => $details)
                                    @php 
                                        $total += $details['price'] * $details['qty'];
                                        $phone = \App\Models\Phone::find($id);
                                        $maxQty = $phone ? $phone->qty : ($details['stock'] ?? $details['qty']);
                                    @endphp
                                    <tr>
                                        <td class="px-4 py-4">
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-3 overflow-hidden me-3 shadow-sm" style="width: 70px; height: 70px;">
                                                    @if($details['image'])
                                                        <img src="{{ asset('storage/'.$details['image']) }}" class="w-100 h-100" style="object-fit: cover;">
                                                    @else
                                                        <div class="bg-light w-100 h-100 d-flex align-items-center justify-content-center">
                                                            <i class="fa-solid fa-mobile-screen text-muted"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <h6 class="fw-bold mb-0">{{ $details['name'] }}</h6>
                                                    <small class="text-muted">{{ $maxQty > 0 ? 'In Stock' : 'Out of Stock' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-4 text-dark fw-medium">${{ number_format($details['price'], 2) }}</td>
                                        <td class="py-4">
                                            <form action="{{ route('shop.update-cart') }}" method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="product_id" value="{{ $id }}">
                                                <div class="input-group" style="width: 100px;">
                                                    <input type="number" name="quantity" value="{{ $details['qty'] }}" min="1" max="{{ $maxQty }}" class="form-control form-control-sm text-center" onchange="this.form.submit()">
                                                </div>
                                                <small class="d-block text-muted mt-1">Max: {{ $maxQty }}</small>
                                            </form>
                                        </td>
                                        <td class="py-4 text-end px-4 fw-bold text-primary">${{ number_format($details['price'] * $details['qty'], 2) }}</td>
                                        <td class="py-4 text-center">
                                            <a href="{{ route('shop.remove', $id) }}" class="btn btn-link text-danger p-0">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <div class="mb-4">
                            <i class="fa-solid fa-cart-shopping opacity-10" style="font-size: 5rem;"></i>
                        </div>
                        <h4 class="fw-bold text-muted">Your cart is empty</h4>
                        <p class="text-muted mb-4">Looks like you haven't added anything to your cart yet.</p>
                        <a href="{{ route('shop.home') }}" class="btn btn-primary px-5 py-3 rounded-pill">Start Shopping</a>
                    </div>
                @endif
            </div>
        </div>
        
        @if(session('cart') && count(session('cart')) > 0)
            <div class="mt-4">
                <a href="{{ route('shop.home') }}" class="btn btn-light rounded-pill px-4 py-2">
                    <i class="fa-solid fa-arrow-left me-2"></i>Continue Shopping
                </a>
            </div>
        @endif
    </div>

    @if(session('cart') && count(session('cart')) > 0)
    <div class="col-lg-4 mt-4 mt-lg-0">
        <div class="card border-0 shadow-sm sticky-top" style="top: 100px;">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4">Order Summary</h5>
                
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Subtotal</span>
                    <span class="fw-bold">${{ number_format($total, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Shipping</span>
                    <span class="text-success fw-bold">Free</span>
                </div>
                <hr class="my-4 opacity-50">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <span class="h5 fw-bold mb-0 text-dark">Total</span>
                    <span class="h4 fw-bold mb-0 text-primary">${{ number_format($total, 2) }}</span>
                </div>

                <a href="{{ route('shop.checkout') }}" class="btn btn-primary w-100 py-3 rounded-3 fw-bold" onclick="return confirm('Proceed to secure Bakong payment?')">
                    <i class="fa-solid fa-lock me-2"></i>Proceed to Secure Checkout
                </a>
                
                <div class="mt-4 text-center">
                    <div class="d-flex justify-content-center gap-3 opacity-50">
                        <i class="fa-brands fa-cc-visa fs-4"></i>
                        <i class="fa-brands fa-cc-mastercard fs-4"></i>
                        <i class="fa-solid fa-qrcode fs-4"></i>
                    </div>
                    <small class="text-muted d-block mt-2">Secure checkout with Bakong KHQR</small>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@endsection
