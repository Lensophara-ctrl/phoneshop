@extends('layouts.app')

@section('content')
<style>
    .dashboard-header {
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
        padding: 2rem;
        border-radius: 20px;
        margin-bottom: 2rem;
        border: 1px solid rgba(99, 102, 241, 0.1);
    }
    
    [data-bs-theme="dark"] .dashboard-header {
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
        border: 1px solid rgba(99, 102, 241, 0.2);
    }
    
    .dashboard-title {
        font-size: 32px;
        font-weight: 700;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin: 0;
    }
    
    .stat-card {
        background: var(--bs-body-bg);
        border-radius: 20px;
        padding: 1.75rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid rgba(0, 0, 0, 0.05);
        position: relative;
        overflow: hidden;
    }

    [data-bs-theme="dark"] .stat-card {
        border: 1px solid rgba(255, 255, 255, 0.05);
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--gradient-start), var(--gradient-end));
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    .stat-card:hover::before {
        opacity: 1;
    }

    .stat-card.blue {
        --gradient-start: #3b82f6;
        --gradient-end: #2563eb;
    }

    .stat-card.green {
        --gradient-start: #10b981;
        --gradient-end: #059669;
    }

    .stat-card.orange {
        --gradient-start: #f59e0b;
        --gradient-end: #d97706;
    }

    .stat-card.purple {
        --gradient-start: #8b5cf6;
        --gradient-end: #7c3aed;
    }
    
    .stat-icon {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
        position: relative;
    }

    .stat-card:hover .stat-icon {
        transform: scale(1.1) rotate(5deg);
    }
    
    .stat-icon.blue {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        box-shadow: 0 8px 16px rgba(59, 130, 246, 0.3);
    }
    
    .stat-icon.green {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        box-shadow: 0 8px 16px rgba(16, 185, 129, 0.3);
    }
    
    .stat-icon.orange {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
        box-shadow: 0 8px 16px rgba(245, 158, 11, 0.3);
    }
    
    .stat-icon.purple {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
        color: white;
        box-shadow: 0 8px 16px rgba(139, 92, 246, 0.3);
    }
    
    .stat-label {
        font-size: 13px;
        color: var(--bs-secondary-color);
        margin-bottom: 0.5rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .stat-value {
        font-size: 32px;
        font-weight: 800;
        color: var(--bs-body-color);
        margin-bottom: 0.5rem;
        line-height: 1;
    }
    
    .stat-change {
        font-size: 13px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 10px;
        border-radius: 20px;
        background: rgba(16, 185, 129, 0.1);
        color: #059669;
    }
    
    .stat-change.negative {
        background: rgba(239, 68, 68, 0.1);
        color: #dc2626;
    }

    .stat-change i {
        font-size: 11px;
    }
    
    .chart-card {
        background: var(--bs-body-bg);
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        border: 1px solid rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }

    [data-bs-theme="dark"] .chart-card {
        border: 1px solid rgba(255, 255, 255, 0.05);
    }

    .chart-card:hover {
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    
    .chart-title {
        font-size: 20px;
        font-weight: 700;
        color: var(--bs-body-color);
        margin-bottom: 1.5rem;
    }
    
    .btn-primary-custom {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 12px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
    }
    
    .btn-primary-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(99, 102, 241, 0.4);
        color: white;
    }
    
    .user-info {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 0.5rem 1rem;
        background: rgba(99, 102, 241, 0.05);
        border-radius: 12px;
        transition: all 0.3s ease;
    }

    .user-info:hover {
        background: rgba(99, 102, 241, 0.1);
    }
    
    .user-avatar {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 20px;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
    }
    
    .user-details h6 {
        margin: 0;
        font-size: 15px;
        font-weight: 700;
        color: var(--bs-body-color);
    }
    
    .user-details p {
        margin: 0;
        font-size: 12px;
        color: var(--bs-secondary-color);
        font-weight: 500;
    }

    .progress {
        height: 10px;
        border-radius: 10px;
        background: rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    [data-bs-theme="dark"] .progress {
        background: rgba(255, 255, 255, 0.05);
    }

    .progress-bar {
        border-radius: 10px;
        transition: width 1s ease;
    }

    .category-item {
        padding: 1rem 0;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        transition: all 0.2s ease;
    }

    [data-bs-theme="dark"] .category-item {
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    .category-item:last-child {
        border-bottom: none;
    }

    .category-item:hover {
        transform: translateX(4px);
    }

    .category-label {
        font-size: 14px;
        font-weight: 600;
        color: var(--bs-body-color);
    }

    .category-percentage {
        font-weight: 700;
        font-size: 15px;
    }

    .table {
        margin-bottom: 0;
    }

    .table thead th {
        background: rgba(99, 102, 241, 0.05);
        border: none;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: 1px;
        padding: 1rem 1.25rem;
        color: var(--bs-secondary-color);
    }

    .table tbody tr {
        transition: all 0.2s ease;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    [data-bs-theme="dark"] .table tbody tr {
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    .table tbody tr:hover {
        background: rgba(99, 102, 241, 0.03);
        transform: scale(1.01);
    }

    .table tbody td {
        padding: 1rem 1.25rem;
        vertical-align: middle;
        font-size: 14px;
    }

    .badge {
        padding: 0.5rem 1rem;
        border-radius: 10px;
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .activity-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-right: 12px;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .stat-card, .chart-card {
        animation: slideUp 0.5s ease forwards;
    }

    .stat-card:nth-child(1) { animation-delay: 0.1s; }
    .stat-card:nth-child(2) { animation-delay: 0.2s; }
    .stat-card:nth-child(3) { animation-delay: 0.3s; }
    .stat-card:nth-child(4) { animation-delay: 0.4s; }
</style>

<div class="container-fluid">
    <!-- Header -->
    <div class="dashboard-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 class="dashboard-title">Dashboard</h1>
                <p class="text-muted mb-0">Welcome back, {{ auth()->user()->name }}</p>
            </div>
            <div class="d-flex gap-3 align-items-center">
                <a href="{{ route('sales.create') }}" class="btn-primary-custom">
                    <i class="fa-solid fa-plus me-2"></i>New Sale
                </a>
                <div class="user-info">
                    <div class="user-avatar">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="user-details">
                        <h6>{{ auth()->user()->name }}</h6>
                        <p>{{ ucfirst(auth()->user()->role) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-6 col-lg-3">
            <div class="stat-card blue">
                <div class="stat-icon blue">
                    <i class="fa-solid fa-dollar-sign"></i>
                </div>
                <div class="stat-label">Total Revenue</div>
                <div class="stat-value">${{ number_format($totalRevenue ?? 0, 2) }}</div>
                <div class="stat-change">
                    <i class="fa-solid fa-arrow-up"></i>
                    <span>12% from last month</span>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="stat-card green">
                <div class="stat-icon green">
                    <i class="fa-solid fa-shopping-cart"></i>
                </div>
                <div class="stat-label">Total Orders</div>
                <div class="stat-value">{{ $totalOrders ?? 0 }}</div>
                <div class="stat-change">
                    <i class="fa-solid fa-arrow-up"></i>
                    <span>8 new today</span>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="stat-card orange">
                <div class="stat-icon orange">
                    <i class="fa-solid fa-box"></i>
                </div>
                <div class="stat-label">Products</div>
                <div class="stat-value">{{ $phones ?? 0 }}</div>
                <div class="stat-change">
                    <i class="fa-solid fa-layer-group"></i>
                    <span>{{ $categories ?? 0 }} categories</span>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="stat-card purple">
                <div class="stat-icon purple">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div class="stat-label">Users</div>
                <div class="stat-value">{{ $users ?? 0 }}</div>
                <div class="stat-change">
                    <i class="fa-solid fa-arrow-up"></i>
                    <span>5 new users</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4">
        <!-- Sales This Month -->
        <div class="col-lg-8">
            <div class="chart-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="chart-title mb-0">Sales This Month</h3>
                    <span class="badge" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">{{ date('F Y') }}</span>
                </div>
                <div style="position: relative; height: 320px;">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Top Categories -->
        <div class="col-lg-4">
            <div class="chart-card">
                <h3 class="chart-title">Top Categories</h3>
                <div class="category-item">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="category-label">Smartphones</span>
                        <span class="category-percentage" style="color: #3b82f6;">65%</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar" style="width: 65%; background: linear-gradient(90deg, #3b82f6, #2563eb);"></div>
                    </div>
                </div>
                <div class="category-item">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="category-label">Accessories</span>
                        <span class="category-percentage" style="color: #8b5cf6;">28%</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar" style="width: 28%; background: linear-gradient(90deg, #8b5cf6, #7c3aed);"></div>
                    </div>
                </div>
                <div class="category-item">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="category-label">Tablets</span>
                        <span class="category-percentage" style="color: #10b981;">5%</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar" style="width: 5%; background: linear-gradient(90deg, #10b981, #059669);"></div>
                    </div>
                </div>
                <div class="category-item">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="category-label">Others</span>
                        <span class="category-percentage" style="color: #f59e0b;">2%</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar" style="width: 2%; background: linear-gradient(90deg, #f59e0b, #d97706);"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row g-4 mt-1">
        <div class="col-12">
            <div class="chart-card">
                <h3 class="chart-title">Recent Activity</h3>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Activity</th>
                                <th>Details</th>
                                <th>Time</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="activity-icon" style="background: rgba(16, 185, 129, 0.1); color: #059669;">
                                            <i class="fa-solid fa-check-circle"></i>
                                        </div>
                                        <span class="fw-semibold">New Order</span>
                                    </div>
                                </td>
                                <td class="text-muted">Order #INV-12345</td>
                                <td class="text-muted">2 minutes ago</td>
                                <td><span class="badge bg-success">Completed</span></td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="activity-icon" style="background: rgba(59, 130, 246, 0.1); color: #2563eb;">
                                            <i class="fa-solid fa-box"></i>
                                        </div>
                                        <span class="fw-semibold">Stock Update</span>
                                    </div>
                                </td>
                                <td class="text-muted">iPhone 15 Pro Max</td>
                                <td class="text-muted">15 minutes ago</td>
                                <td><span class="badge bg-info">Updated</span></td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="activity-icon" style="background: rgba(245, 158, 11, 0.1); color: #d97706;">
                                            <i class="fa-solid fa-user-plus"></i>
                                        </div>
                                        <span class="fw-semibold">New Customer</span>
                                    </div>
                                </td>
                                <td class="text-muted">John Doe registered</td>
                                <td class="text-muted">1 hour ago</td>
                                <td><span class="badge bg-warning">New</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const salesData = {!! json_encode($salesData ?? [12500, 18200, 15800, 22400]) !!};
        
        const ctx = document.getElementById('salesChart');
        if (ctx) {
            const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 320);
            gradient.addColorStop(0, 'rgba(102, 126, 234, 0.4)');
            gradient.addColorStop(1, 'rgba(118, 75, 162, 0.05)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                    datasets: [{
                        label: 'Sales Revenue ($)',
                        data: salesData,
                        borderColor: '#667eea',
                        backgroundColor: gradient,
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#667eea',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 3,
                        pointRadius: 6,
                        pointHoverRadius: 8,
                        pointHoverBackgroundColor: '#764ba2',
                        pointHoverBorderWidth: 3,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        intersect: false,
                        mode: 'index',
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            align: 'end',
                            labels: {
                                color: getComputedStyle(document.documentElement).getPropertyValue('--bs-body-color') || '#666',
                                font: {
                                    size: 13,
                                    weight: '600',
                                    family: 'Inter, sans-serif'
                                },
                                padding: 20,
                                usePointStyle: true,
                                pointStyle: 'circle',
                                boxWidth: 8,
                                boxHeight: 8
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(15, 23, 42, 0.95)',
                            padding: 16,
                            titleColor: '#fff',
                            titleFont: {
                                size: 14,
                                weight: '700',
                                family: 'Inter, sans-serif'
                            },
                            bodyColor: '#e2e8f0',
                            bodyFont: {
                                size: 13,
                                weight: '500',
                                family: 'Inter, sans-serif'
                            },
                            borderColor: '#667eea',
                            borderWidth: 2,
                            displayColors: true,
                            cornerRadius: 12,
                            caretSize: 8,
                            callbacks: {
                                label: function(context) {
                                    return 'Revenue: $' + context.parsed.y.toLocaleString('en-US', {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    });
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(99, 102, 241, 0.08)',
                                drawBorder: false,
                                lineWidth: 1
                            },
                            border: {
                                display: false
                            },
                            ticks: {
                                color: getComputedStyle(document.documentElement).getPropertyValue('--bs-secondary-color') || '#94a3b8',
                                font: {
                                    size: 12,
                                    weight: '500',
                                    family: 'Inter, sans-serif'
                                },
                                padding: 12,
                                callback: function(value) {
                                    return '$' + value.toLocaleString();
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false,
                                drawBorder: false
                            },
                            border: {
                                display: false
                            },
                            ticks: {
                                color: getComputedStyle(document.documentElement).getPropertyValue('--bs-secondary-color') || '#94a3b8',
                                font: {
                                    size: 12,
                                    weight: '600',
                                    family: 'Inter, sans-serif'
                                },
                                padding: 12
                            }
                        }
                    }
                }
            });
        }

        // Animate progress bars on load
        const progressBars = document.querySelectorAll('.progress-bar');
        progressBars.forEach(bar => {
            const width = bar.style.width;
            bar.style.width = '0';
            setTimeout(() => {
                bar.style.width = width;
            }, 300);
        });
    });
</script>

@endsection
