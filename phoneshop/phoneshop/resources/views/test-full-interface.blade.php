@extends('layouts.app')

@section('content')
<div class="row g-4">
    <!-- Welcome Card -->
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between text-white">
                    <div>
                        <h2 class="mb-2 fw-bold">Welcome back, {{ Auth::user()->name }}! 👋</h2>
                        <p class="mb-0 opacity-75">Here's what's happening with your store today.</p>
                    </div>
                    <div class="d-none d-md-block">
                        <i class="fa-solid fa-chart-line" style="font-size: 4rem; opacity: 0.2;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="rounded-circle p-3" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
                        <i class="fa-solid fa-dollar-sign text-white fs-4"></i>
                    </div>
                    <span class="badge bg-success">+12.5%</span>
                </div>
                <h6 class="text-muted mb-2">Total Revenue</h6>
                <h3 class="mb-0 fw-bold">$45,231</h3>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="rounded-circle p-3" style="background: linear-gradient(135deg, #06b6d4 0%, #3b82f6 100%);">
                        <i class="fa-solid fa-shopping-cart text-white fs-4"></i>
                    </div>
                    <span class="badge bg-info">+8.2%</span>
                </div>
                <h6 class="text-muted mb-2">Total Orders</h6>
                <h3 class="mb-0 fw-bold">1,234</h3>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="rounded-circle p-3" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                        <i class="fa-solid fa-box text-white fs-4"></i>
                    </div>
                    <span class="badge bg-warning">-3.1%</span>
                </div>
                <h6 class="text-muted mb-2">Products</h6>
                <h3 class="mb-0 fw-bold">567</h3>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="rounded-circle p-3" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                        <i class="fa-solid fa-users text-white fs-4"></i>
                    </div>
                    <span class="badge bg-primary">+15.3%</span>
                </div>
                <h6 class="text-muted mb-2">Customers</h6>
                <h3 class="mb-0 fw-bold">8,921</h3>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 d-flex align-items-center justify-content-between py-3">
                <h5 class="mb-0 fw-bold">Recent Orders</h5>
                <a href="#" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Product</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="badge bg-light text-dark">#ORD-001</span></td>
                                <td>John Doe</td>
                                <td>iPhone 15 Pro</td>
                                <td class="fw-bold">$1,299</td>
                                <td><span class="badge bg-success">Completed</span></td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-light text-dark">#ORD-002</span></td>
                                <td>Jane Smith</td>
                                <td>Samsung Galaxy S24</td>
                                <td class="fw-bold">$999</td>
                                <td><span class="badge bg-warning">Pending</span></td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-light text-dark">#ORD-003</span></td>
                                <td>Mike Johnson</td>
                                <td>Google Pixel 8</td>
                                <td class="fw-bold">$699</td>
                                <td><span class="badge bg-info">Processing</span></td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-light text-dark">#ORD-004</span></td>
                                <td>Sarah Williams</td>
                                <td>OnePlus 12</td>
                                <td class="fw-bold">$799</td>
                                <td><span class="badge bg-success">Completed</span></td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-light text-dark">#ORD-005</span></td>
                                <td>David Brown</td>
                                <td>Xiaomi 14 Pro</td>
                                <td class="fw-bold">$899</td>
                                <td><span class="badge bg-danger">Cancelled</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Products -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 py-3">
                <h5 class="mb-0 fw-bold">Top Products</h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                    <div class="rounded p-2 me-3" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
                        <i class="fa-solid fa-mobile-screen-button text-white"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-0">iPhone 15 Pro</h6>
                        <small class="text-muted">234 sales</small>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold">$1,299</div>
                    </div>
                </div>
                <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                    <div class="rounded p-2 me-3" style="background: linear-gradient(135deg, #06b6d4 0%, #3b82f6 100%);">
                        <i class="fa-solid fa-mobile-screen-button text-white"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-0">Samsung S24</h6>
                        <small class="text-muted">189 sales</small>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold">$999</div>
                    </div>
                </div>
                <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                    <div class="rounded p-2 me-3" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                        <i class="fa-solid fa-mobile-screen-button text-white"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-0">Google Pixel 8</h6>
                        <small class="text-muted">156 sales</small>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold">$699</div>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <div class="rounded p-2 me-3" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                        <i class="fa-solid fa-mobile-screen-button text-white"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-0">OnePlus 12</h6>
                        <small class="text-muted">142 sales</small>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold">$799</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales Chart -->
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 py-3">
                <h5 class="mb-0 fw-bold">Sales Overview</h5>
            </div>
            <div class="card-body">
                <div class="text-center py-5">
                    <i class="fa-solid fa-chart-line text-muted mb-3" style="font-size: 3rem; opacity: 0.3;"></i>
                    <p class="text-muted">Sales chart visualization would appear here</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
