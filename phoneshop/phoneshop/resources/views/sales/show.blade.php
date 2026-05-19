@extends('layouts.app')

@php
    $formatMoney = function($amount, $currency = 'USD') {
        if ($currency === 'KHR') {
            return number_format($amount, 0) . ' ៛';
        }
        return '$' . number_format($amount, 2);
    };
    
    // Get store settings
    $storeName = $settings['store_name'] ?? 'Shop';
    $storePhone = $settings['store_phone'] ?? '';
    $storeEmail = $settings['store_email'] ?? '';
    $storeLogo = $settings['store_logo'] ?? null;
    $storeIcon = $settings['store_icon'] ?? 'fa-store';
@endphp

@section('content')

<style>
    /* Modern Invoice Styles */
    .invoice-container {
        animation: fadeInUp 0.6s ease-out;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .invoice-card {
        background: var(--bs-body-bg);
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        position: relative;
    }

    .invoice-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 3rem 2rem;
        position: relative;
        overflow: hidden;
    }

    .invoice-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
    }

    .invoice-header::after {
        content: '';
        position: absolute;
        bottom: -30%;
        left: -5%;
        width: 200px;
        height: 200px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 50%;
    }

    .invoice-body {
        padding: 2.5rem;
    }

    .invoice-badge {
        display: inline-block;
        padding: 8px 20px;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 600;
        margin-bottom: 1rem;
    }

    .invoice-title {
        font-size: 2.5rem;
        font-weight: 900;
        margin: 0;
        text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    }

    .info-card {
        background: rgba(102, 126, 234, 0.05);
        border-radius: 16px;
        padding: 1.5rem;
        border: 1px solid rgba(102, 126, 234, 0.1);
    }

    [data-bs-theme="dark"] .info-card {
        background: rgba(102, 126, 234, 0.1);
    }

    .info-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-weight: 700;
        color: #667eea;
        margin-bottom: 0.5rem;
    }

    .info-value {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--bs-body-color);
    }

    .items-table {
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid var(--bs-border-color);
    }

    .items-table thead {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .items-table thead th {
        padding: 1rem;
        font-weight: 700;
        border: none;
    }

    .items-table tbody tr {
        border-bottom: 1px solid var(--bs-border-color);
        transition: background 0.2s ease;
    }

    .items-table tbody tr:hover {
        background: rgba(102, 126, 234, 0.05);
    }

    .items-table tbody td {
        padding: 1.25rem 1rem;
    }

    .total-section {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
        border-radius: 16px;
        padding: 1.5rem;
        border: 2px solid rgba(102, 126, 234, 0.2);
    }

    .grand-total {
        font-size: 2rem;
        font-weight: 900;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .status-badge {
        padding: 8px 20px;
        border-radius: 50px;
        font-weight: 700;
        font-size: 0.9rem;
        display: inline-block;
    }

    .status-paid {
        background: rgba(16, 185, 129, 0.1);
        color: #10b981;
        border: 2px solid #10b981;
    }

    .btn-modern {
        padding: 12px 32px;
        border-radius: 50px;
        font-weight: 700;
        transition: all 0.3s ease;
        border: none;
    }

    .btn-print {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .btn-print:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        color: white;
    }

    .language-selector {
        display: flex;
        gap: 8px;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        padding: 6px;
        border-radius: 50px;
    }

    .lang-btn {
        padding: 8px 16px;
        border-radius: 50px;
        border: none;
        background: transparent;
        color: white;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .lang-btn.active {
        background: white;
        color: #667eea;
    }

    .lang-btn:hover:not(.active) {
        background: rgba(255, 255, 255, 0.1);
    }

    /* Print Styles */
    @media print {
        .no-print { display: none !important; }
        .sidebar, .top-nav { display: none !important; }
        .main-content { margin: 0 !important; padding: 0 !important; }
        .invoice-card { box-shadow: none !important; border: 1px solid #ddd; }
        body { background: white !important; }
        .invoice-header { background: #667eea !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .items-table thead { background: #667eea !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    }
</style>

<div class="invoice-container">
    <!-- Action Buttons -->
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <h3 class="mb-0 fw-bold">
            <i class="fa-solid fa-file-invoice me-2 text-primary"></i>
            <span data-translate="invoice_details">Invoice Details</span>
        </h3>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-modern btn-print">
                <i class="fas fa-print me-2"></i><span data-translate="print">Print</span>
            </button>
            <a href="{{ route('sales.index') }}" class="btn btn-modern btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-2"></i><span data-translate="back">Back</span>
            </a>
        </div>
    </div>

    <!-- Invoice Card -->
    <div class="invoice-card">
        <!-- Header -->
        <div class="invoice-header">
            <div class="row align-items-center position-relative">
                <div class="col-md-6">
                    <div class="invoice-badge">
                        <i class="fa-solid fa-receipt me-2"></i><span data-translate="invoice">INVOICE</span>
                    </div>
                    <h1 class="invoice-title">
                        @if($storeLogo)
                            <img src="{{ asset('storage/' . $storeLogo) }}" alt="{{ $storeName }}" style="height: 50px; width: auto; object-fit: contain; margin-right: 1rem;">
                        @else
                            <i class="fa-solid {{ $storeIcon }} me-3"></i>
                        @endif
                        {{ $storeName }}
                    </h1>
                    @if($storePhone || $storeEmail)
                        <div class="mt-3 opacity-90">
                            @if($storePhone)
                                <div><i class="fa-solid fa-phone me-2"></i>{{ $storePhone }}</div>
                            @endif
                            @if($storeEmail)
                                <div><i class="fa-solid fa-envelope me-2"></i>{{ $storeEmail }}</div>
                            @endif
                        </div>
                    @endif
                </div>
                <div class="col-md-6 text-md-end mt-4 mt-md-0">
                    <!-- Language Selector -->
                    <div class="language-selector d-inline-flex mb-3 no-print">
                        <button class="lang-btn active" onclick="changeLanguage('en')" data-lang="en">English</button>
                        <button class="lang-btn" onclick="changeLanguage('km')" data-lang="km">ខ្មែរ</button>
                        <button class="lang-btn" onclick="changeLanguage('zh')" data-lang="zh">中文</button>
                    </div>
                    <div class="mt-3">
                        <div class="h5 mb-2"><span data-translate="bill_no">Bill No</span>: #{{ $sale->bill_no }}</div>
                        <div><span data-translate="date">Date</span>: {{ $sale->created_at->format('M d, Y h:i A') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Body -->
        <div class="invoice-body">
            <!-- Customer & Payment Info -->
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="info-card h-100">
                        <div class="info-label"><i class="fa-solid fa-user me-2"></i><span data-translate="customer_info">Customer Information</span></div>
                        @if($sale->customer_name || $sale->user)
                            <div class="info-value">{{ $sale->customer_name ?? $sale->user->name }}</div>
                            <div class="text-muted mt-1">
                                @if($sale->customer_email)
                                    <i class="fa-solid fa-envelope me-1"></i>{{ $sale->customer_email }}<br>
                                @elseif($sale->user)
                                    <i class="fa-solid fa-envelope me-1"></i>{{ $sale->user->email }}<br>
                                @endif
                                @if($sale->customer_phone)
                                    <i class="fa-solid fa-phone me-1"></i>{{ $sale->customer_phone }}<br>
                                @endif
                                @if($sale->customer_address)
                                    <i class="fa-solid fa-location-dot me-1"></i>{{ $sale->customer_address }}
                                    @if($sale->customer_city), {{ $sale->customer_city }}@endif
                                    @if($sale->customer_postal_code) {{ $sale->customer_postal_code }}@endif
                                @endif
                            </div>
                            @if($sale->order_notes)
                                <div class="mt-2 p-2 bg-light rounded">
                                    <small class="text-muted"><i class="fa-solid fa-note-sticky me-1"></i><strong>Notes:</strong></small><br>
                                    <small>{{ $sale->order_notes }}</small>
                                </div>
                            @endif
                        @else
                            <div class="info-value text-muted"><span data-translate="guest">Guest Customer</span></div>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-card h-100">
                        <div class="info-label"><i class="fa-solid fa-credit-card me-2"></i><span data-translate="payment_info">Payment Information</span></div>
                        <div class="info-value text-uppercase">{{ str_replace('_', ' ', $sale->payment_method) }}</div>
                        <div class="mt-2">
                            <span class="status-badge status-paid">
                                <i class="fa-solid fa-circle-check me-1"></i><span data-translate="paid">PAID</span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <div class="table-responsive mb-4">
                <table class="table items-table mb-0">
                    <thead>
                        <tr>
                            <th><span data-translate="item">Item</span></th>
                            <th class="text-center"><span data-translate="price">Price</span></th>
                            <th class="text-center"><span data-translate="qty">Qty</span></th>
                            <th class="text-end"><span data-translate="total">Total</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sale->items as $item)
                        @php
                            $displayPrice = $sale->currency === 'KHR' ? $item->price * $sale->exchange_rate : $item->price;
                            $displaySubtotal = $sale->currency === 'KHR' ? $item->subtotal * $sale->exchange_rate : $item->subtotal;
                        @endphp
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $item->phone->name }}</div>
                                <small class="text-muted">{{ $item->phone->category->name }}</small>
                            </td>
                            <td class="text-center">{{ $formatMoney($displayPrice, $sale->currency) }}</td>
                            <td class="text-center"><span class="badge bg-primary">{{ $item->qty }}</span></td>
                            <td class="text-end fw-bold">{{ $formatMoney($displaySubtotal, $sale->currency) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Total Section -->
            <div class="row justify-content-end">
                <div class="col-md-5">
                    <div class="total-section">
                        @php
                            $subtotal = $sale->currency === 'KHR' ? $sale->subtotal * $sale->exchange_rate : $sale->subtotal;
                            $tax = $sale->currency === 'KHR' ? $sale->tax * $sale->exchange_rate : $sale->tax;
                            $total = $sale->currency === 'KHR' ? $sale->total_price * $sale->exchange_rate : $sale->total_price;
                        @endphp
                        <div class="d-flex justify-content-between mb-2 pb-2">
                            <span class="text-muted"><span data-translate="subtotal">Subtotal</span>:</span>
                            <span class="fw-bold">{{ $formatMoney($subtotal, $sale->currency) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                            <span class="text-muted"><span data-translate="tax">Tax</span>:</span>
                            <span class="fw-bold">{{ $formatMoney($tax, $sale->currency) }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="h5 mb-0 fw-bold"><span data-translate="grand_total">Grand Total</span>:</span>
                            <span class="grand-total">{{ $formatMoney($total, $sale->currency) }}</span>
                        </div>
                        
                        @if($sale->currency === 'KHR')
                            <div class="text-end small text-muted mt-2">
                                <span data-translate="equivalent">Equivalent</span>: ${{ number_format($sale->total_price, 2) }}
                            </div>
                        @else
                            <div class="text-end small text-muted mt-2">
                                <span data-translate="equivalent">Equivalent</span>: {{ number_format($sale->total_price * $sale->exchange_rate, 0) }} ៛
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center mt-5 pt-4 border-top">
                <p class="h5 mb-2">
                    <i class="fa-solid fa-heart text-danger me-2"></i>
                    <span data-translate="thank_you">Thank you for your business!</span>
                </p>
                <p class="text-muted small mb-0">
                    <span data-translate="terms">Terms: Goods once sold are not returnable.</span>
                </p>
            </div>
        </div>
    </div>
</div>

<script>
    // Translation data
    const translations = {
        en: {
            invoice_details: 'Invoice Details',
            print: 'Print',
            back: 'Back',
            invoice: 'INVOICE',
            bill_no: 'Bill No',
            date: 'Date',
            customer_info: 'Customer Information',
            guest: 'Guest Customer',
            payment_info: 'Payment Information',
            paid: 'PAID',
            item: 'Item',
            price: 'Price',
            qty: 'Qty',
            total: 'Total',
            subtotal: 'Subtotal',
            tax: 'Tax',
            grand_total: 'Grand Total',
            equivalent: 'Equivalent',
            thank_you: 'Thank you for your business!',
            terms: 'Terms: Goods once sold are not returnable.'
        },
        km: {
            invoice_details: 'ព័ត៌មានវិក្កយបត្រ',
            print: 'បោះពុម្ព',
            back: 'ត្រឡប់',
            invoice: 'វិក្កយបត្រ',
            bill_no: 'លេខវិក្កយបត្រ',
            date: 'កាលបរិច្ឆេទ',
            customer_info: 'ព័ត៌មានអតិថិជន',
            guest: 'ភ្ញៀវ',
            payment_info: 'ព័ត៌មានការទូទាត់',
            paid: 'បានទូទាត់',
            item: 'ផលិតផល',
            price: 'តម្លៃ',
            qty: 'បរិមាណ',
            total: 'សរុប',
            subtotal: 'សរុបរង',
            tax: 'ពន្ធ',
            grand_total: 'សរុបទាំងអស់',
            equivalent: 'ស្មើនឹង',
            thank_you: 'សូមអរគុណសម្រាប់ការទិញ!',
            terms: 'លក្ខខណ្ឌ: ទំនិញដែលបានលក់រួចមិនអាចប្តូរវិញបានទេ។'
        },
        zh: {
            invoice_details: '发票详情',
            print: '打印',
            back: '返回',
            invoice: '发票',
            bill_no: '账单号',
            date: '日期',
            customer_info: '客户信息',
            guest: '访客',
            payment_info: '付款信息',
            paid: '已付款',
            item: '商品',
            price: '价格',
            qty: '数量',
            total: '总计',
            subtotal: '小计',
            tax: '税',
            grand_total: '总金额',
            equivalent: '等值',
            thank_you: '感谢您的惠顾！',
            terms: '条款：商品售出后不可退换。'
        }
    };

    let currentLang = 'en';

    function changeLanguage(lang) {
        currentLang = lang;
        
        // Update active button
        document.querySelectorAll('.lang-btn').forEach(btn => {
            btn.classList.remove('active');
            if (btn.dataset.lang === lang) {
                btn.classList.add('active');
            }
        });

        // Update all translatable elements
        document.querySelectorAll('[data-translate]').forEach(element => {
            const key = element.dataset.translate;
            if (translations[lang] && translations[lang][key]) {
                element.textContent = translations[lang][key];
            }
        });
    }

    // Initialize with English
    document.addEventListener('DOMContentLoaded', function() {
        changeLanguage('en');
    });
</script>

@endsection
