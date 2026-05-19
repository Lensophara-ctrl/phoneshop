@extends('frontend.layouts.app')

@section('content')

<style>
    /* Modern Lively Animations */
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-20px); }
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }

    @keyframes shimmer {
        0% { background-position: -1000px 0; }
        100% { background-position: 1000px 0; }
    }

    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fadeInScale {
        from {
            opacity: 0;
            transform: scale(0.9);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    @keyframes gradientShift {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    /* Hero Section with Simple Gradient Background */
    .hero-modern {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        border-radius: 24px;
        overflow: hidden;
        position: relative;
        margin-bottom: 3rem;
    }

    .hero-modern::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        opacity: 0.3;
    }

    .hero-content {
        position: relative;
        z-index: 2;
        animation: slideInUp 0.8s ease-out;
    }

    .hero-image {
        animation: float 6s ease-in-out infinite;
    }

    /* Product Cards with Hover Effects */
    .product-card-modern {
        border-radius: 20px;
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        border: 2px solid transparent;
        background: var(--bs-body-bg);
        position: relative;
    }

    .product-card-modern::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: #4f46e5;
        opacity: 0;
        transition: opacity 0.4s ease;
        z-index: 0;
    }

    .product-card-modern:hover::before {
        opacity: 0.08;
    }

    .product-card-modern:hover {
        transform: translateY(-12px) scale(1.02);
        box-shadow: 0 20px 40px rgba(79, 70, 229, 0.25);
        border-color: #4f46e5;
    }

    .product-card-modern > * {
        position: relative;
        z-index: 1;
    }

    .product-image-wrapper {
        height: 220px;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        position: relative;
    }

    [data-bs-theme="dark"] .product-image-wrapper {
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
    }

    .product-image-wrapper img {
        transition: transform 0.5s ease;
        max-height: 200px;
        object-fit: contain;
    }

    .product-card-modern:hover .product-image-wrapper img {
        transform: scale(1.15) rotate(5deg);
    }

    /* Category Pills with Gradient */
    .category-pill-modern {
        padding: 12px 24px;
        border-radius: 50px;
        font-weight: 600;
        border: 2px solid transparent;
        background: var(--bs-body-bg);
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }

    .category-pill-modern::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        transition: left 0.5s ease;
    }

    .category-pill-modern:hover::before {
        left: 100%;
    }

    .category-pill-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(79, 70, 229, 0.2);
    }

    .category-pill-modern.active {
        background: #4f46e5;
        color: white !important;
        border-color: #4f46e5;
        box-shadow: 0 8px 20px rgba(79, 70, 229, 0.3);
    }

    /* Badges with Glow */
    .badge-glow {
        animation: pulse 2s ease-in-out infinite;
        box-shadow: 0 0 20px currentColor;
    }

    /* Section Headers */
    .section-header-modern {
        position: relative;
        display: inline-block;
        margin-bottom: 2rem;
    }

    .section-header-modern h3 {
        font-size: 2.5rem;
        font-weight: 800;
        color: #4f46e5;
        margin: 0;
    }

    .section-header-modern::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 0;
        width: 60px;
        height: 4px;
        background: #4f46e5;
        border-radius: 2px;
    }

    /* Buttons with Simple Color */
    .btn-gradient {
        background: #4f46e5;
        border: none;
        color: white;
        font-weight: 600;
        padding: 12px 28px;
        border-radius: 50px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .btn-gradient::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s ease;
    }

    .btn-gradient:hover::before {
        left: 100%;
    }

    .btn-gradient:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(79, 70, 229, 0.35);
        background: #4338ca;
        color: white;
    }

    /* Price Tag */
    .price-tag {
        font-size: 1.5rem;
        font-weight: 800;
        color: #4f46e5;
    }

    /* Stock Badge */
    .stock-badge-modern {
        position: absolute;
        top: 12px;
        right: 12px;
        padding: 6px 14px;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 700;
        backdrop-filter: blur(10px);
        z-index: 2;
    }

    /* Top Seller Badge */
    .top-seller-badge {
        position: absolute;
        top: 12px;
        left: 12px;
        background: #ef4444;
        color: white;
        padding: 6px 14px;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 700;
        z-index: 2;
        animation: pulse 2s ease-in-out infinite;
    }

    /* Stagger Animation for Cards */
    .product-card-modern {
        animation: fadeInScale 0.6s ease-out backwards;
    }

    .product-card-modern:nth-child(1) { animation-delay: 0.1s; }
    .product-card-modern:nth-child(2) { animation-delay: 0.2s; }
    .product-card-modern:nth-child(3) { animation-delay: 0.3s; }
    .product-card-modern:nth-child(4) { animation-delay: 0.4s; }
    .product-card-modern:nth-child(5) { animation-delay: 0.5s; }
    .product-card-modern:nth-child(6) { animation-delay: 0.6s; }
    .product-card-modern:nth-child(7) { animation-delay: 0.7s; }
    .product-card-modern:nth-child(8) { animation-delay: 0.8s; }

    /* Search Bar Modern */
    .search-modern {
        border-radius: 50px;
        border: 2px solid transparent;
        background: var(--bs-body-bg);
        padding: 12px 24px;
        transition: all 0.3s ease;
    }

    .search-modern:focus {
        border-color: #4f46e5;
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        transform: translateY(-2px);
    }

    /* Shimmer Effect */
    .shimmer {
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        background-size: 200% 100%;
        animation: shimmer 2s infinite;
    }
</style>

<!-- 3D Carousel Slideshow -->
@if($slides->count() > 0)
<div class="carousel-3d-container mb-5">
    <div class="carousel-3d-scene">
        <div class="carousel-3d-track">
            @foreach($slides as $index => $slide)
                <div class="carousel-3d-card {{ $index == 0 ? 'active' : '' }}" data-index="{{ $index }}">
                    <div class="card-inner">
                        <!-- Background Image -->
                        @if($slide->image)
                            <img src="{{ asset('storage/'.$slide->image) }}" alt="{{ $slide->title }}" class="card-bg-image">
                        @else
                            <div class="card-bg-gradient"></div>
                        @endif
                        
                        <!-- Content Overlay -->
                        <div class="card-content">
                            @if($slide->subtitle)
                                <p class="card-subtitle">{{ $slide->subtitle }}</p>
                            @endif
                            <h2 class="card-title">{{ $slide->title }}</h2>
                            <p class="card-description">{{ $slide->description }}</p>
                            @if($slide->button_text)
                                <a href="{{ $slide->button_link ?? '#' }}" class="card-btn">
                                    {{ $slide->button_text }} <i class="fa-solid fa-arrow-right ms-2"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Navigation Arrows -->
        <button class="carousel-arrow prev" onclick="moveCarousel(-1)">
            <i class="fa-solid fa-chevron-left"></i>
        </button>
        <button class="carousel-arrow next" onclick="moveCarousel(1)">
            <i class="fa-solid fa-chevron-right"></i>
        </button>
        
        <!-- Dot Indicators -->
        <div class="carousel-dots">
            @foreach($slides as $index => $slide)
                <button class="dot {{ $index == 0 ? 'active' : '' }}" onclick="jumpToSlide({{ $index }})"></button>
            @endforeach
        </div>
    </div>
</div>

<style>
    /* 3D Carousel Container */
    .carousel-3d-container {
        margin-bottom: 4rem;
    }
    
    .carousel-3d-scene {
        position: relative;
        height: 600px;
        perspective: 1500px;
        overflow: hidden;
        background: #0a1628;
        border-radius: 20px;
        padding: 60px 0;
    }
    
    /* 3D Track */
    .carousel-3d-track {
        position: relative;
        width: 100%;
        height: 100%;
        transform-style: preserve-3d;
    }
    
    /* 3D Cards */
    .carousel-3d-card {
        position: absolute;
        width: 800px;
        height: 480px;
        left: 50%;
        top: 50%;
        margin-left: -400px;
        margin-top: -240px;
        transition: all 0.6s cubic-bezier(0.4, 0.0, 0.2, 1);
        transform-style: preserve-3d;
        backface-visibility: hidden;
    }
    
    .card-inner {
        width: 100%;
        height: 100%;
        background: #1e293b;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 25px 60px rgba(0, 0, 0, 0.6);
        position: relative;
    }
    
    /* Background Image */
    .card-bg-image {
        position: absolute;
        width: 100%;
        height: 100%;
        object-fit: contain;
        object-position: center;
        filter: brightness(0.7);
        image-rendering: -webkit-optimize-contrast;
        image-rendering: crisp-edges;
    }
    
    .card-bg-gradient {
        position: absolute;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #4f46e5, #7c3aed, #ec4899);
    }
    
    /* Content Overlay */
    .card-content {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 40px;
        background: linear-gradient(to top, rgba(0, 0, 0, 0.95) 0%, rgba(0, 0, 0, 0.7) 70%, transparent 100%);
        color: white;
        z-index: 2;
    }
    
    .card-subtitle {
        font-size: 0.9rem;
        opacity: 0.9;
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    .card-title {
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 12px;
        line-height: 1.2;
    }
    
    .card-description {
        font-size: 1rem;
        opacity: 0.9;
        margin-bottom: 20px;
        line-height: 1.5;
    }
    
    .card-btn {
        display: inline-block;
        background: white;
        color: #4f46e5;
        padding: 12px 28px;
        border-radius: 50px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(255, 255, 255, 0.3);
    }
    
    .card-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(255, 255, 255, 0.4);
        color: #4338ca;
    }
    
    /* 3D Positioning States */
    .carousel-3d-card.active {
        transform: translateZ(0) rotateY(0deg) scale(1);
        opacity: 1;
        z-index: 10;
    }
    
    .carousel-3d-card.prev {
        transform: translateZ(-250px) translateX(-450px) rotateY(40deg) scale(0.75);
        opacity: 0.6;
        z-index: 5;
    }
    
    .carousel-3d-card.next {
        transform: translateZ(-250px) translateX(450px) rotateY(-40deg) scale(0.75);
        opacity: 0.6;
        z-index: 5;
    }
    
    .carousel-3d-card.hidden {
        transform: translateZ(-500px) scale(0.4);
        opacity: 0;
        z-index: 1;
    }
    
    /* Navigation Arrows */
    .carousel-arrow {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 50px;
        height: 50px;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border: 2px solid rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        color: white;
        font-size: 1.2rem;
        cursor: pointer;
        z-index: 100;
        transition: all 0.3s ease;
    }
    
    .carousel-arrow:hover {
        background: rgba(255, 255, 255, 0.2);
        border-color: white;
        transform: translateY(-50%) scale(1.1);
    }
    
    .carousel-arrow.prev {
        left: 30px;
    }
    
    .carousel-arrow.next {
        right: 30px;
    }
    
    /* Dot Indicators */
    .carousel-dots {
        position: absolute;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        gap: 10px;
        z-index: 100;
    }
    
    .dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.4);
        border: 2px solid rgba(255, 255, 255, 0.6);
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .dot.active {
        background: white;
        transform: scale(1.3);
        box-shadow: 0 0 15px rgba(255, 255, 255, 0.8);
    }
    
    .dot:hover {
        background: rgba(255, 255, 255, 0.7);
    }
    
    /* Responsive */
    @media (max-width: 991px) {
        .carousel-3d-scene {
            height: 450px;
        }
        
        .carousel-3d-card {
            width: 500px;
            height: 320px;
            margin-left: -250px;
            margin-top: -160px;
        }
        
        .card-title {
            font-size: 2rem;
        }
        
        .card-description {
            font-size: 0.95rem;
        }
    }
    
    @media (max-width: 767px) {
        .carousel-3d-scene {
            height: 400px;
        }
        
        .carousel-3d-card {
            width: 350px;
            height: 280px;
            margin-left: -175px;
            margin-top: -140px;
        }
        
        .carousel-3d-card.prev,
        .carousel-3d-card.next {
            display: none;
        }
        
        .card-content {
            padding: 25px;
        }
        
        .card-title {
            font-size: 1.5rem;
        }
        
        .card-description {
            font-size: 0.9rem;
        }
        
        .carousel-arrow {
            width: 40px;
            height: 40px;
            font-size: 1rem;
        }
        
        .carousel-arrow.prev {
            left: 15px;
        }
        
        .carousel-arrow.next {
            right: 15px;
        }
    }
</style>

<script>
    let currentIndex = 0;
    const totalCards = {{ $slides->count() }};
    let autoPlayTimer;
    
    function updateCarousel() {
        const cards = document.querySelectorAll('.carousel-3d-card');
        const dots = document.querySelectorAll('.dot');
        
        cards.forEach((card, index) => {
            card.classList.remove('active', 'prev', 'next', 'hidden');
            dots[index].classList.remove('active');
            
            if (index === currentIndex) {
                card.classList.add('active');
                dots[index].classList.add('active');
            } else if (index === (currentIndex - 1 + totalCards) % totalCards) {
                card.classList.add('prev');
            } else if (index === (currentIndex + 1) % totalCards) {
                card.classList.add('next');
            } else {
                card.classList.add('hidden');
            }
        });
    }
    
    function moveCarousel(direction) {
        currentIndex = (currentIndex + direction + totalCards) % totalCards;
        updateCarousel();
        resetAutoPlay();
    }
    
    function jumpToSlide(index) {
        currentIndex = index;
        updateCarousel();
        resetAutoPlay();
    }
    
    function startAutoPlay() {
        autoPlayTimer = setInterval(() => {
            moveCarousel(1);
        }, 5000);
    }
    
    function resetAutoPlay() {
        clearInterval(autoPlayTimer);
        startAutoPlay();
    }
    
    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        updateCarousel();
        startAutoPlay();
        
        // Pause on hover
        const scene = document.querySelector('.carousel-3d-scene');
        if (scene) {
            scene.addEventListener('mouseenter', () => clearInterval(autoPlayTimer));
            scene.addEventListener('mouseleave', () => startAutoPlay());
        }
    });
</script>
@else
<!-- Fallback Hero if no slides -->
<div class="hero-modern shadow-lg mb-5">
    <div class="container py-5">
        <div class="row align-items-center" style="min-height: 500px;">
            <div class="col-lg-6 hero-content">
                <div class="badge bg-white text-primary px-4 py-2 rounded-pill mb-3 shadow-sm">
                    <i class="fa-solid fa-sparkles me-2"></i>{{ __('messages.welcome') }} {{ $settings['store_name'] ?? 'Our Shop' }}
                </div>
                <h1 class="display-3 fw-bold text-white mb-4" style="text-shadow: 0 4px 12px rgba(0,0,0,0.2);">
                    {{ __('messages.discover_amazing') }}
                </h1>
                <p class="lead text-white mb-4" style="font-size: 1.3rem; opacity: 0.95;">
                    {{ __('messages.shop_latest') }}
                </p>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="#latest-phones" class="btn btn-light btn-lg px-5 rounded-pill shadow-lg">
                        <i class="fa-solid fa-shopping-bag me-2"></i>{{ __('messages.shop_now') }}
                    </a>
                    <a href="#top-selling" class="btn btn-outline-light btn-lg px-5 rounded-pill">
                        <i class="fa-solid fa-fire me-2"></i>{{ __('messages.hot_deals') }}
                    </a>
                </div>
            </div>
            <div class="col-lg-6 text-center d-none d-lg-block">
                <div class="hero-image">
                    @php
                        $storeIcon = $settings['store_icon'] ?? 'fa-store';
                    @endphp
                    <i class="fa-solid {{ $storeIcon }} text-white" style="font-size: 18rem; opacity: 0.2; filter: drop-shadow(0 10px 30px rgba(0,0,0,0.3));"></i>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Language Selector Section -->
<div class="language-selector-section mb-5">
    <div class="card border-0 shadow-sm" style="border-radius: 20px; background: linear-gradient(135deg, #1e293b 0%, #334155 100%);">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-4 text-center text-md-start mb-3 mb-md-0">
                    <h5 class="fw-bold text-white mb-1">
                        <i class="fa-solid fa-globe me-2"></i>{{ __('messages.choose_language') }}
                    </h5>
                    <p class="text-white-50 small mb-0">{{ __('messages.select_language') }}</p>
                </div>
                <div class="col-md-8">
                    <div class="row g-3">
                        @php
                            $currentLocale = app()->getLocale();
                        @endphp
                        
                        <!-- English -->
                        <div class="col-md-4">
                            <form action="{{ route('language.switch') }}" method="POST">
                                @csrf
                                <input type="hidden" name="locale" value="en">
                                <button type="submit" class="language-card w-100 {{ $currentLocale === 'en' ? 'active' : '' }}">
                                    <div class="flag-icon mb-2">🇬🇧</div>
                                    <div class="fw-bold">English</div>
                                    <small class="text-muted">GB English</small>
                                </button>
                            </form>
                        </div>
                        
                        <!-- Khmer -->
                        <div class="col-md-4">
                            <form action="{{ route('language.switch') }}" method="POST">
                                @csrf
                                <input type="hidden" name="locale" value="km">
                                <button type="submit" class="language-card w-100 {{ $currentLocale === 'km' ? 'active' : '' }}">
                                    <div class="flag-icon mb-2">🇰🇭</div>
                                    <div class="fw-bold">ខ្មែរ</div>
                                    <small class="text-muted">KH ខ្មែរ</small>
                                </button>
                            </form>
                        </div>
                        
                        <!-- Chinese -->
                        <div class="col-md-4">
                            <form action="{{ route('language.switch') }}" method="POST">
                                @csrf
                                <input type="hidden" name="locale" value="zh">
                                <button type="submit" class="language-card w-100 {{ $currentLocale === 'zh' ? 'active' : '' }}">
                                    <div class="flag-icon mb-2">🇨🇳</div>
                                    <div class="fw-bold">中文</div>
                                    <small class="text-muted">CN 中文</small>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .language-selector-section {
        animation: fadeInScale 0.6s ease-out;
    }
    
    .language-card {
        background: white;
        border: 3px solid transparent;
        border-radius: 16px;
        padding: 20px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .language-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(102, 126, 234, 0.1), transparent);
        transition: left 0.5s ease;
    }
    
    .language-card:hover::before {
        left: 100%;
    }
    
    .language-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        border-color: #667eea;
    }
    
    .language-card.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-color: #667eea;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        transform: scale(1.05);
    }
    
    .language-card.active small {
        color: rgba(255, 255, 255, 0.8) !important;
    }
    
    .flag-icon {
        font-size: 3rem;
        line-height: 1;
    }
    
    [data-bs-theme="dark"] .language-card:not(.active) {
        background: #1e293b;
        color: white;
    }
    
    [data-bs-theme="dark"] .language-card:not(.active):hover {
        background: #334155;
    }
</style>

<!-- New Customer Welcome Section -->
@guest
<div class="new-customer-intro mb-5">
    <div class="card border-0 shadow-lg overflow-hidden" style="border-radius: 24px;">
        <div class="row g-0">
            <!-- Left Side - Introduction -->
            <div class="col-lg-7">
                <div class="p-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="text-white">
                        <div class="badge bg-white text-primary px-3 py-2 rounded-pill mb-3">
                            <i class="fa-solid fa-star me-1"></i>{{ __('messages.new_customer') }}
                        </div>
                        <h2 class="display-5 fw-bold mb-3">{{ __('messages.welcome_message') }} {{ $settings['store_name'] ?? 'PharaShop' }}!</h2>
                        <p class="lead mb-4" style="opacity: 0.95;">
                            {{ __('messages.join_message') }}
                        </p>
                        
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0">
                                        <div class="rounded-circle bg-white bg-opacity-25 p-3">
                                            <i class="fa-solid fa-shield-check fa-lg"></i>
                                        </div>
                                    </div>
                                    <div class="ms-3">
                                        <h6 class="fw-bold mb-1">{{ __('messages.authentic') }}</h6>
                                        <small style="opacity: 0.9;">{{ __('messages.authentic_desc') }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0">
                                        <div class="rounded-circle bg-white bg-opacity-25 p-3">
                                            <i class="fa-solid fa-truck-fast fa-lg"></i>
                                        </div>
                                    </div>
                                    <div class="ms-3">
                                        <h6 class="fw-bold mb-1">{{ __('messages.fast_delivery') }}</h6>
                                        <small style="opacity: 0.9;">{{ __('messages.fast_delivery_desc') }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0">
                                        <div class="rounded-circle bg-white bg-opacity-25 p-3">
                                            <i class="fa-solid fa-tags fa-lg"></i>
                                        </div>
                                    </div>
                                    <div class="ms-3">
                                        <h6 class="fw-bold mb-1">{{ __('messages.member_discounts') }}</h6>
                                        <small style="opacity: 0.9;">{{ __('messages.member_discounts_desc') }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0">
                                        <div class="rounded-circle bg-white bg-opacity-25 p-3">
                                            <i class="fa-solid fa-headset fa-lg"></i>
                                        </div>
                                    </div>
                                    <div class="ms-3">
                                        <h6 class="fw-bold mb-1">{{ __('messages.support_247') }}</h6>
                                        <small style="opacity: 0.9;">{{ __('messages.support_247_desc') }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right Side - Sign Up Form -->
            <div class="col-lg-5">
                <div class="p-5 bg-white" style="min-height: 100%;">
                    <div class="text-center mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10 mb-3" style="width: 70px; height: 70px;">
                            <i class="fa-solid fa-user-plus fa-2x text-primary"></i>
                        </div>
                        <h4 class="fw-bold mb-2">{{ __('messages.create_account') }}</h4>
                        <p class="text-muted small">{{ __('messages.start_shopping') }}</p>
                    </div>
                    
                    <form action="{{ route('register') }}" method="GET">
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">{{ __('messages.email_address') }}</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fa-solid fa-envelope text-primary"></i>
                                </span>
                                <input type="email" name="email" class="form-control border-start-0 ps-0" placeholder="your@email.com" required>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 py-3 fw-bold rounded-pill mb-3">
                            <i class="fa-solid fa-rocket me-2"></i>{{ __('messages.sign_up_free') }}
                        </button>
                    </form>
                    
                    <div class="text-center mb-3">
                        <div class="d-flex align-items-center">
                            <hr class="flex-grow-1">
                            <span class="px-3 text-muted small">{{ __('messages.or') }}</span>
                            <hr class="flex-grow-1">
                        </div>
                    </div>
                    
                    <a href="{{ route('login') }}" class="btn btn-outline-primary w-100 py-3 fw-bold rounded-pill">
                        <i class="fa-solid fa-sign-in-alt me-2"></i>{{ __('messages.sign_in') }}
                    </a>
                    
                    <p class="text-center text-muted small mt-4 mb-0">
                        <i class="fa-solid fa-lock me-1"></i>{{ __('messages.data_safe') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .new-customer-intro {
        animation: fadeInScale 0.8s ease-out;
    }
    
    .new-customer-intro .input-group-text {
        background: transparent;
    }
    
    .new-customer-intro .form-control:focus {
        box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
        border-color: #667eea;
    }
    
    .new-customer-intro .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        transition: all 0.3s ease;
    }
    
    .new-customer-intro .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
    }
    
    .new-customer-intro .btn-outline-primary {
        border-width: 2px;
        transition: all 0.3s ease;
    }
    
    .new-customer-intro .btn-outline-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.2);
    }
    
    [data-bs-theme="dark"] .new-customer-intro .bg-white {
        background: var(--bs-body-bg) !important;
    }
    
    @media (max-width: 991px) {
        .new-customer-intro .col-lg-7 {
            order: 2;
        }
        .new-customer-intro .col-lg-5 {
            order: 1;
        }
    }
</style>
@endguest

<!-- Categories -->
<div class="mb-5">
    <div class="section-header-modern mb-4">
        <h3><i class="fa-solid fa-grid-2 me-2"></i>{{ __('messages.shop_by_category') }}</h3>
    </div>
    <div class="row g-3">
        <div class="col-6 col-md-4 col-lg-2">
            <a href="{{ route('shop.home') }}" class="text-decoration-none">
                <div class="category-pill-modern text-center {{ !request('category') ? 'active' : '' }}">
                    <div class="mb-2">
                        <i class="fa-solid fa-border-all fa-2x"></i>
                    </div>
                    <span class="fw-bold small">{{ __('messages.all_products') }}</span>
                </div>
            </a>
        </div>
        @foreach($categories as $category)
            @php
                $icon = 'fa-box';
                $name = strtolower($category->name);
                if (str_contains($name, 'iphone') || str_contains($name, 'apple')) $icon = 'fa-brands fa-apple';
                elseif (str_contains($name, 'samsung')) $icon = 'fa-mobile-button';
                elseif (str_contains($name, 'tablet') || str_contains($name, 'ipad')) $icon = 'fa-tablet-screen-button';
                elseif (str_contains($name, 'watch')) $icon = 'fa-clock';
                elseif (str_contains($name, 'accessory') || str_contains($name, 'cable')) $icon = 'fa-plug';
                elseif (str_contains($name, 'phone') || str_contains($name, 'mobile')) $icon = 'fa-mobile-screen';
            @endphp
            <div class="col-6 col-md-4 col-lg-2">
                <a href="{{ route('shop.category', $category->id) }}" class="text-decoration-none">
                    <div class="category-pill-modern text-center {{ request()->is('category/'.$category->id) ? 'active' : '' }}">
                        @if($category->image)
                            <img src="{{ asset('storage/'.$category->image) }}" alt="{{ $category->name }}" class="mb-2" style="height: 50px; width: auto; object-fit: contain;">
                        @else
                            <div class="mb-2">
                                <i class="fa-solid {{ $icon }} fa-2x"></i>
                            </div>
                        @endif
                        <span class="fw-bold small d-block">{{ $category->name }}</span>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
</div>

<!-- Top Selling Products -->
@if($topSellingProducts->count() > 0)
<div class="mb-5" id="top-selling">
    <div class="section-header-modern mb-4">
        <h3><i class="fa-solid fa-fire text-danger me-2"></i>{{ __('messages.top_selling') }}</h3>
    </div>
    <div class="row g-4">
        @foreach($topSellingProducts as $product)
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card product-card-modern h-100 border-0 shadow-sm">
                    <div class="top-seller-badge">
                        <i class="fa-solid fa-fire me-1"></i>{{ __('messages.best_seller') }}
                    </div>
                    
                    <div class="stock-badge-modern bg-success text-white">
                        {{ $product->total_sold }} {{ __('messages.sold') }}
                    </div>

                    <div class="product-image-wrapper">
                        @if($product->image)
                            <img src="{{ asset('storage/'.$product->image) }}" class="p-3" alt="{{ $product->name }}">
                        @else
                            <i class="fa-solid fa-box fa-4x text-muted opacity-25"></i>
                        @endif
                    </div>

                    <div class="card-body d-flex flex-column">
                        <span class="badge bg-primary-subtle text-primary mb-2 align-self-start">{{ $product->category->name }}</span>
                        <h6 class="card-title fw-bold mb-2">{{ $product->name }}</h6>
                        <div class="mt-auto">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="price-tag">${{ number_format($product->price, 2) }}</span>
                                @if($product->qty > 0)
                                    <span class="badge bg-success-subtle text-success">{{ __('messages.in_stock') }}</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger">{{ __('messages.out_of_stock') }}</span>
                                @endif
                            </div>
                            <a href="{{ route('shop.product', $product->id) }}" class="btn btn-gradient w-100 btn-sm">
                                <i class="fa-solid fa-eye me-2"></i>{{ __('messages.view_details') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif

<!-- Latest Phones -->
<div id="latest-phones">
    <div class="section-header-modern mb-4">
        <h3><i class="fa-solid fa-sparkles me-2"></i>{{ __('messages.latest_arrivals') }}</h3>
    </div>
    
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <span class="text-muted">{{ __('messages.items_found', ['count' => $phones->count()]) }}</span>
        <div class="btn-group shadow-sm" role="group">
            <a href="{{ route('shop.home', ['sort' => 'latest']) }}" class="btn btn-sm {{ $sort === 'latest' ? 'btn-gradient' : 'btn-outline-secondary' }}">
                <i class="fa-solid fa-clock me-1"></i>Latest
            </a>
            <a href="{{ route('shop.home', ['sort' => 'price-low']) }}" class="btn btn-sm {{ $sort === 'price-low' ? 'btn-gradient' : 'btn-outline-secondary' }}">
                <i class="fa-solid fa-arrow-up-long me-1"></i>Low to High
            </a>
            <a href="{{ route('shop.home', ['sort' => 'price-high']) }}" class="btn btn-sm {{ $sort === 'price-high' ? 'btn-gradient' : 'btn-outline-secondary' }}">
                <i class="fa-solid fa-arrow-down-long me-1"></i>High to Low
            </a>
        </div>
    </div>

    <div class="row g-4">
    @foreach($phones as $phone)
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card product-card-modern h-100 border-0 shadow-sm">
                @if($phone->qty <= 0)
                    <div class="stock-badge-modern bg-danger text-white">
                        {{ __('messages.out_of_stock') }}
                    </div>
                @elseif($phone->qty <= 5)
                    <div class="stock-badge-modern bg-warning text-dark">
                        {{ __('messages.only_left', ['count' => $phone->qty]) }}
                    </div>
                @else
                    <div class="stock-badge-modern bg-success text-white">
                        {{ __('messages.in_stock') }}
                    </div>
                @endif

                <div class="product-image-wrapper">
                    @if($phone->image)
                        <img src="{{ asset('storage/'.$phone->image) }}" class="p-3" alt="{{ $phone->name }}">
                    @else
                        <i class="fa-solid fa-box fa-4x text-muted opacity-25"></i>
                    @endif
                </div>

                <div class="card-body d-flex flex-column">
                    <span class="badge bg-secondary-subtle text-secondary mb-2 align-self-start">{{ $phone->category->name }}</span>
                    <h6 class="fw-bold mb-3 text-truncate">{{ $phone->name }}</h6>
                    
                    <div class="mt-auto">
                        <div class="price-tag mb-3">${{ number_format($phone->price, 2) }}</div>
                        <div class="d-grid gap-2">
                            <a href="{{ route('shop.product', $phone->id) }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fa-solid fa-eye me-1"></i>{{ __('messages.details') }}
                            </a>
                            <a href="{{ route('shop.add', $phone->id) }}" class="btn btn-gradient btn-sm">
                                <i class="fa-solid fa-cart-plus me-1"></i>{{ __('messages.add_to_cart') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar {
        height: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: var(--bs-border-color);
        border-radius: 10px;
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
