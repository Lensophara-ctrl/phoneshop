@extends('layouts.app')

@section('content')
<style>
    .report-card {
        background: var(--bs-body-bg);
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        border: 2px solid transparent;
        height: 100%;
        position: relative;
        overflow: hidden;
    }

    [data-bs-theme="dark"] .report-card {
        border: 1px solid rgba(255, 255, 255, 0.05);
    }

    .report-card::before {
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
    
    .report-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        border-color: transparent;
    }

    .report-card:hover::before {
        opacity: 1;
    }
    
    .report-card.active {
        border-color: #667eea;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
    }

    .report-card.active::before {
        opacity: 1;
    }

    .report-card.blue { --gradient-start: #3b82f6; --gradient-end: #2563eb; }
    .report-card.green { --gradient-start: #10b981; --gradient-end: #059669; }
    .report-card.orange { --gradient-start: #f59e0b; --gradient-end: #d97706; }
    .report-card.purple { --gradient-start: #8b5cf6; --gradient-end: #7c3aed; }
    .report-card.pink { --gradient-start: #ec4899; --gradient-end: #db2777; }
    .report-card.indigo { --gradient-start: #6366f1; --gradient-end: #4f46e5; }
    
    .report-icon {
        font-size: 48px;
        margin-bottom: 1.25rem;
        transition: all 0.3s ease;
    }

    .report-card:hover .report-icon {
        transform: scale(1.1) rotate(5deg);
    }
    
    .report-title {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 0.75rem;
        color: var(--bs-body-color);
    }
    
    .report-description {
        font-size: 0.875rem;
        color: var(--bs-secondary-color);
        line-height: 1.6;
    }
    
    .report-content {
        background: var(--bs-body-bg);
        border-radius: 20px;
        padding: 2.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        display: none;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    [data-bs-theme="dark"] .report-content {
        border: 1px solid rgba(255, 255, 255, 0.05);
    }
    
    .report-content.active {
        display: block;
        animation: fadeIn 0.4s ease;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .data-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 1.5rem;
    }
    
    .data-table th {
        background: rgba(99, 102, 241, 0.05);
        padding: 1rem 1.25rem;
        text-align: left;
        font-size: 11px;
        font-weight: 700;
        color: var(--bs-secondary-color);
        text-transform: uppercase;
        letter-spacing: 1px;
        border: none;
    }
    
    .data-table td {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        font-size: 14px;
        vertical-align: middle;
    }

    [data-bs-theme="dark"] .data-table td {
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }
    
    .data-table tr:hover {
        background: rgba(99, 102, 241, 0.03);
        transform: scale(1.01);
        transition: all 0.2s ease;
    }
    
    .stat-box {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.75rem;
        border-radius: 16px;
        box-shadow: 0 8px 16px rgba(102, 126, 234, 0.3);
        transition: all 0.3s ease;
    }

    .stat-box:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(102, 126, 234, 0.4);
    }
    
    .stat-label {
        font-size: 13px;
        opacity: 0.9;
        margin-bottom: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .stat-value {
        font-size: 32px;
        font-weight: 800;
        line-height: 1;
    }
    
    .btn-export {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .btn-export:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
    }

    .btn-export.pdf {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
    }

    .btn-export.pdf:hover {
        box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4);
    }
    
    .btn-download {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        padding: 0.5rem 1rem;
        border: none;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    
    .btn-download:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }

    .page-header {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
        padding: 2rem;
        border-radius: 20px;
        margin-bottom: 2rem;
        border: 1px solid rgba(99, 102, 241, 0.1);
    }

    [data-bs-theme="dark"] .page-header {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
        border: 1px solid rgba(99, 102, 241, 0.2);
    }

    .page-title {
        font-size: 32px;
        font-weight: 800;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin: 0;
    }

    .spinner-border {
        width: 3rem;
        height: 3rem;
        border-width: 0.3rem;
    }
</style>

<div class="container-fluid">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="page-title mb-2">📊 Reports Dashboard</h2>
                <p class="text-muted mb-0">Comprehensive business analytics and insights</p>
            </div>
        </div>
    </div>
    
    <!-- Date Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Start Date</label>
                    <input type="date" class="form-control" id="startDate">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">End Date</label>
                    <input type="date" class="form-control" id="endDate">
                </div>
                <div class="col-md-3">
                    <button class="btn btn-primary" onclick="applyFilters()">
                        <i class="fa-solid fa-filter me-2"></i>Apply Filters
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Reports Grid -->
    <div class="row g-4 mb-4">
        <div class="col-md-6 col-lg-4 col-xl-3">
            <div class="report-card blue" onclick="loadReport('sales-summary')">
                <div class="report-icon">💰</div>
                <div class="report-title">Sales Summary</div>
                <div class="report-description">Overview of total sales, orders, and revenue metrics</div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-4 col-xl-3">
            <div class="report-card orange" onclick="loadReport('top-selling')">
                <div class="report-icon">🏆</div>
                <div class="report-title">Top Selling Products</div>
                <div class="report-description">Best performing products by quantity and revenue</div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-4 col-xl-3">
            <div class="report-card green" onclick="loadReport('revenue-category')">
                <div class="report-icon">📈</div>
                <div class="report-title">Revenue by Category</div>
                <div class="report-description">Sales performance breakdown by product categories</div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-4 col-xl-3">
            <div class="report-card purple" onclick="loadReport('daily-sales')">
                <div class="report-icon">📅</div>
                <div class="report-title">Daily Sales</div>
                <div class="report-description">Day-by-day sales analysis and trends</div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-4 col-xl-3">
            <div class="report-card indigo" onclick="loadReport('customers')">
                <div class="report-icon">👥</div>
                <div class="report-title">Customer Report</div>
                <div class="report-description">Top customers and purchasing behavior analysis</div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-4 col-xl-3">
            <div class="report-card pink" onclick="loadReport('inventory')">
                <div class="report-icon">📦</div>
                <div class="report-title">Inventory Report</div>
                <div class="report-description">Stock levels, values, and inventory status</div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-4 col-xl-3">
            <div class="report-card blue" onclick="loadReport('payment-methods')">
                <div class="report-icon">💳</div>
                <div class="report-title">Payment Methods</div>
                <div class="report-description">Revenue distribution by payment method</div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-4 col-xl-3">
            <div class="report-card green" onclick="loadReport('monthly-comparison')">
                <div class="report-icon">📊</div>
                <div class="report-title">Monthly Comparison</div>
                <div class="report-description">Month-over-month performance comparison</div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-4 col-xl-3">
            <div class="report-card orange" onclick="loadReport('order-status')">
                <div class="report-icon">✅</div>
                <div class="report-title">Order Status</div>
                <div class="report-description">Order fulfillment and approval status breakdown</div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-4 col-xl-3">
            <div class="report-card purple" onclick="loadReport('profit-analysis')">
                <div class="report-icon">💵</div>
                <div class="report-title">Profit Analysis</div>
                <div class="report-description">Detailed profit margins and financial analysis</div>
            </div>
        </div>
    </div>
    
    <!-- Report Content -->
    <div class="report-content" id="reportContent">
        <div class="text-center py-5">
            <i class="fa-solid fa-chart-line fa-4x text-muted mb-4" style="opacity: 0.3;"></i>
            <h4 class="fw-bold">Select a report to view</h4>
            <p class="text-muted">Click on any report card above to view detailed analytics</p>
        </div>
    </div>
</div>

<script>
let currentReport = null;
let startDate = '';
let endDate = '';

// Initialize dates
function initDates() {
    const today = new Date();
    const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
    
    document.getElementById('startDate').value = firstDay.toISOString().split('T')[0];
    document.getElementById('endDate').value = today.toISOString().split('T')[0];
    
    startDate = firstDay.toISOString().split('T')[0];
    endDate = today.toISOString().split('T')[0];
}

function applyFilters() {
    startDate = document.getElementById('startDate').value;
    endDate = document.getElementById('endDate').value;
    
    if (currentReport) {
        loadReport(currentReport);
    }
}

// Map report types to API endpoints
const reportEndpoints = {
    'sales-summary': 'sales-summary',
    'top-selling': 'top-selling-products',
    'revenue-category': 'revenue-by-category',
    'daily-sales': 'daily-sales',
    'customers': 'customer-report',
    'inventory': 'inventory',
    'payment-methods': 'payment-methods',
    'monthly-comparison': 'monthly-comparison',
    'order-status': 'order-status',
    'profit-analysis': 'profit-analysis'
};

async function loadReport(reportType) {
    currentReport = reportType;
    
    // Update active card
    document.querySelectorAll('.report-card').forEach(card => {
        card.classList.remove('active');
    });
    event.target.closest('.report-card').classList.add('active');
    
    const content = document.getElementById('reportContent');
    content.classList.add('active');
    content.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div><p class="mt-3 fw-semibold">Loading report...</p></div>';
    
    try {
        const params = new URLSearchParams();
        if (startDate) params.append('start_date', startDate);
        if (endDate) params.append('end_date', endDate);
        
        const endpoint = reportEndpoints[reportType] || reportType;
        const response = await fetch(`/reports/${endpoint}?${params.toString()}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            renderReport(data);
        } else {
            content.innerHTML = '<div class="alert alert-danger"><i class="fa-solid fa-exclamation-triangle me-2"></i>Failed to load report</div>';
        }
    } catch (error) {
        console.error('Error loading report:', error);
        content.innerHTML = '<div class="alert alert-danger"><i class="fa-solid fa-exclamation-triangle me-2"></i>Error loading report. Please try again.</div>';
    }
}

function renderReport(data) {
    const content = document.getElementById('reportContent');
    let html = `
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
            <div>
                <h3 class="mb-1">${data.report_name}</h3>
                ${data.period ? `<p class="text-muted mb-0">${data.period.start_date} - ${data.period.end_date}</p>` : ''}
            </div>
            <div>
                <button class="btn-export me-2" onclick="exportToCSV()">
                    <i class="fa-solid fa-file-csv me-2"></i>Export CSV
                </button>
                <button class="btn-export" onclick="exportToPDF()">
                    <i class="fa-solid fa-file-pdf me-2"></i>Export PDF
                </button>
            </div>
        </div>
    `;
    
    // Render based on report type
    if (currentReport === 'sales-summary') {
        html += renderSalesSummary(data.data);
    } else if (currentReport === 'top-selling') {
        html += renderTopSelling(data.data);
    } else if (currentReport === 'revenue-category') {
        html += renderRevenueCategory(data.data);
    } else if (currentReport === 'daily-sales') {
        html += renderDailySales(data.data);
    } else if (currentReport === 'customers') {
        html += renderCustomers(data.data);
    } else if (currentReport === 'inventory') {
        html += renderInventory(data.data);
    } else if (currentReport === 'payment-methods') {
        html += renderPaymentMethods(data.data);
    } else if (currentReport === 'monthly-comparison') {
        html += renderMonthlyComparison(data.data);
    } else if (currentReport === 'order-status') {
        html += renderOrderStatus(data.data);
    } else if (currentReport === 'profit-analysis') {
        html += renderProfitAnalysis(data.data);
    }
    
    content.innerHTML = html;
}

function renderSalesSummary(data) {
    return `
        <div class="row g-3 mb-4">
            <div class="col-md-6 col-lg-3">
                <div class="stat-box">
                    <div class="stat-label">Total Sales</div>
                    <div class="stat-value">$${parseFloat(data.total_sales).toFixed(2)}</div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="stat-box">
                    <div class="stat-label">Total Orders</div>
                    <div class="stat-value">${data.total_orders}</div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="stat-box">
                    <div class="stat-label">Average Order Value</div>
                    <div class="stat-value">$${parseFloat(data.average_order_value || 0).toFixed(2)}</div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="stat-box">
                    <div class="stat-label">Items Sold</div>
                    <div class="stat-value">${data.total_items_sold}</div>
                </div>
            </div>
        </div>
    `;
}

function renderTopSelling(data) {
    if (!data || data.length === 0) {
        return '<div class="alert alert-info">No data available</div>';
    }
    
    return `
        <table class="data-table">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Quantity Sold</th>
                    <th>Revenue</th>
                    <th>Current Stock</th>
                </tr>
            </thead>
            <tbody>
                ${data.map((item, index) => `
                    <tr>
                        <td><strong>#${index + 1}</strong></td>
                        <td>${item.product_name}</td>
                        <td>${item.category}</td>
                        <td>${item.quantity_sold}</td>
                        <td>$${item.total_revenue}</td>
                        <td>${item.current_stock}</td>
                    </tr>
                `).join('')}
            </tbody>
        </table>
    `;
}

function renderRevenueCategory(data) {
    if (!data || data.length === 0) {
        return '<div class="alert alert-info">No data available</div>';
    }
    
    return `
        <table class="data-table">
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Total Revenue</th>
                    <th>Quantity Sold</th>
                    <th>Order Count</th>
                </tr>
            </thead>
            <tbody>
                ${data.map(item => `
                    <tr>
                        <td><strong>${item.category_name}</strong></td>
                        <td>$${item.total_revenue}</td>
                        <td>${item.total_quantity}</td>
                        <td>${item.order_count}</td>
                    </tr>
                `).join('')}
            </tbody>
        </table>
    `;
}

function renderDailySales(data) {
    if (!data || data.length === 0) {
        return '<div class="alert alert-info">No data available</div>';
    }
    
    return `
        <table class="data-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Orders</th>
                    <th>Revenue</th>
                    <th>Avg Order Value</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                ${data.map(item => `
                    <tr>
                        <td>${item.date}</td>
                        <td>${item.order_count}</td>
                        <td>$${item.total_revenue}</td>
                        <td>$${item.average_order_value}</td>
                        <td>
                            <button class="btn-download" onclick="downloadDailyInvoice('${item.date}')">
                                <i class="fa-solid fa-download me-1"></i>Invoice
                            </button>
                        </td>
                    </tr>
                `).join('')}
            </tbody>
        </table>
    `;
}

function renderCustomers(data) {
    if (!data || data.length === 0) {
        return '<div class="alert alert-info">No data available</div>';
    }
    
    return `
        <table class="data-table">
            <thead>
                <tr>
                    <th>Customer Name</th>
                    <th>Email</th>
                    <th>Total Orders</th>
                    <th>Total Spent</th>
                    <th>Avg Order Value</th>
                    <th>Last Order</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                ${data.map(item => `
                    <tr>
                        <td><strong>${item.customer_name}</strong></td>
                        <td>${item.customer_email}</td>
                        <td>${item.total_orders}</td>
                        <td>$${item.total_spent}</td>
                        <td>$${item.average_order_value}</td>
                        <td>${item.last_order_date}</td>
                        <td>
                            <button class="btn-download" onclick="lockCustomerReport(${item.customer_id})">
                                <i class="fa-solid fa-lock me-1"></i>Lock
                            </button>
                            <button class="btn-download" onclick="downloadCustomerInvoice(${item.customer_id})">
                                <i class="fa-solid fa-download me-1"></i>Invoice
                            </button>
                        </td>
                    </tr>
                `).join('')}
            </tbody>
        </table>
    `;
}

function renderInventory(data) {
    if (!data || data.length === 0) {
        return '<div class="alert alert-info">No data available</div>';
    }
    
    return `
        <table class="data-table">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Current Stock</th>
                    <th>Price</th>
                    <th>Stock Value</th>
                    <th>Total Sold</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                ${data.map(item => `
                    <tr>
                        <td><strong>${item.product_name}</strong></td>
                        <td>${item.category}</td>
                        <td>${item.current_stock}</td>
                        <td>$${item.price}</td>
                        <td>$${item.stock_value}</td>
                        <td>${item.total_sold}</td>
                        <td><span style="color: ${item.status === 'Out of Stock' ? 'red' : item.status === 'Low Stock' ? 'orange' : 'green'}">${item.status}</span></td>
                    </tr>
                `).join('')}
            </tbody>
        </table>
    `;
}

function renderPaymentMethods(data) {
    if (!data || data.length === 0) {
        return '<div class="alert alert-info">No data available</div>';
    }
    
    return `
        <table class="data-table">
            <thead>
                <tr>
                    <th>Payment Method</th>
                    <th>Transactions</th>
                    <th>Total Revenue</th>
                    <th>Avg Transaction</th>
                    <th>Percentage</th>
                </tr>
            </thead>
            <tbody>
                ${data.map(item => `
                    <tr>
                        <td><strong>${item.payment_method}</strong></td>
                        <td>${item.transaction_count}</td>
                        <td>$${item.total_revenue}</td>
                        <td>$${item.average_transaction}</td>
                        <td>${item.percentage}%</td>
                    </tr>
                `).join('')}
            </tbody>
        </table>
    `;
}

function renderMonthlyComparison(data) {
    if (!data || data.length === 0) {
        return '<div class="alert alert-info">No data available</div>';
    }
    
    return `
        <table class="data-table">
            <thead>
                <tr>
                    <th>Month</th>
                    <th>Orders</th>
                    <th>Revenue</th>
                    <th>Avg Order Value</th>
                    <th>New Customers</th>
                </tr>
            </thead>
            <tbody>
                ${data.map(item => `
                    <tr>
                        <td><strong>${item.month}</strong></td>
                        <td>${item.total_orders}</td>
                        <td>$${item.total_revenue}</td>
                        <td>$${item.average_order_value}</td>
                        <td>${item.new_customers}</td>
                    </tr>
                `).join('')}
            </tbody>
        </table>
    `;
}

function renderOrderStatus(data) {
    return `
        <div class="row g-3 mb-4">
            <div class="col-md-6 col-lg-2">
                <div class="stat-box">
                    <div class="stat-label">Total Orders</div>
                    <div class="stat-value">${data.summary.total_orders}</div>
                </div>
            </div>
            <div class="col-md-6 col-lg-2">
                <div class="stat-box">
                    <div class="stat-label">Completed</div>
                    <div class="stat-value">${data.summary.completed_orders}</div>
                </div>
            </div>
            <div class="col-md-6 col-lg-2">
                <div class="stat-box">
                    <div class="stat-label">Pending</div>
                    <div class="stat-value">${data.summary.pending_orders}</div>
                </div>
            </div>
            <div class="col-md-6 col-lg-2">
                <div class="stat-box">
                    <div class="stat-label">Approved</div>
                    <div class="stat-value">${data.summary.approved_orders}</div>
                </div>
            </div>
            <div class="col-md-6 col-lg-2">
                <div class="stat-box">
                    <div class="stat-label">Pending Approval</div>
                    <div class="stat-value">${data.summary.pending_approval}</div>
                </div>
            </div>
            <div class="col-md-6 col-lg-2">
                <div class="stat-box">
                    <div class="stat-label">Rejected</div>
                    <div class="stat-value">${data.summary.rejected_orders}</div>
                </div>
            </div>
        </div>
    `;
}

function renderProfitAnalysis(data) {
    return `
        <div class="row g-3 mb-4">
            <div class="col-md-6 col-lg-3">
                <div class="stat-box">
                    <div class="stat-label">Total Revenue</div>
                    <div class="stat-value">$${data.summary.total_revenue}</div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="stat-box">
                    <div class="stat-label">Tax Collected</div>
                    <div class="stat-value">$${data.summary.total_tax_collected}</div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="stat-box">
                    <div class="stat-label">Net Revenue</div>
                    <div class="stat-value">$${data.summary.net_revenue}</div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="stat-box">
                    <div class="stat-label">Avg Order Value</div>
                    <div class="stat-value">$${data.summary.average_order_value}</div>
                </div>
            </div>
        </div>
        
        <h5 class="mt-4 mb-3">Revenue by Category</h5>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Revenue</th>
                    <th>Quantity Sold</th>
                </tr>
            </thead>
            <tbody>
                ${data.by_category.map(item => `
                    <tr>
                        <td><strong>${item.category}</strong></td>
                        <td>$${item.revenue}</td>
                        <td>${item.quantity_sold}</td>
                    </tr>
                `).join('')}
            </tbody>
        </table>
    `;
}

// Lock customer report
async function lockCustomerReport(customerId) {
    if (!confirm('Lock this customer report? This will prevent further modifications.')) return;
    
    try {
        const response = await fetch(`/reports/customer/${customerId}/lock`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('✅ Customer report locked successfully!');
            loadReport('customers');
        } else {
            alert('❌ Failed to lock report: ' + data.message);
        }
    } catch (error) {
        alert('❌ Error locking report');
        console.error(error);
    }
}

// Download customer invoice
function downloadCustomerInvoice(customerId) {
    const params = new URLSearchParams();
    if (startDate) params.append('start_date', startDate);
    if (endDate) params.append('end_date', endDate);
    
    window.open(`/reports/customer/${customerId}/invoice?${params.toString()}`, '_blank');
}

// Download daily invoice
function downloadDailyInvoice(date) {
    window.open(`/reports/daily-invoice/${date}`, '_blank');
}

// Export functions
function exportToCSV() {
    alert('CSV export functionality - Coming soon!');
}

function exportToPDF() {
    alert('PDF export functionality - Coming soon!');
}

// Initialize
initDates();
</script>
@endsection
