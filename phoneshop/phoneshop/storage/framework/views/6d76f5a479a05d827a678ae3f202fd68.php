<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>PhoneShop - Modern Mobile Store</title>
    
    <script>
        // Check for saved theme preference or use system preference
        const getStoredTheme = () => localStorage.getItem('theme')
        const getPreferredTheme = () => {
            const storedTheme = getStoredTheme()
            if (storedTheme) {
                return storedTheme
            }
            return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
        }

        const setTheme = theme => {
            if (theme === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                document.documentElement.setAttribute('data-bs-theme', 'dark')
            } else {
                document.documentElement.setAttribute('data-bs-theme', theme)
            }
        }

        setTheme(getPreferredTheme())
    </script>
    
    <!-- Google Fonts: Inter + Noto Sans Khmer + Noto Sans SC -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Noto+Sans+Khmer:wght@300;400;500;600;700&family=Noto+Sans+SC:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #4f46e5;
            --primary-hover: #4338ca;
            --bg-light: #f8fafc;
            --text-dark: #1e293b;
            --text-muted: #64748b;
        }

        [data-bs-theme="dark"] {
            --bg-light: #0f172a;
            --text-dark: #f8fafc;
            --text-muted: #94a3b8;
        }

        body { 
            font-family: 'Inter', 'Noto Sans Khmer', 'Noto Sans SC', sans-serif;
            font-size: 16px;
            line-height: 1.6;
            background-color: var(--bg-light);
            color: var(--text-dark);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        
        /* Better font rendering for all text */
        * {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        
        /* Khmer text specific styling - larger and more spaced */
        [lang="km"], .khmer-text {
            font-family: 'Noto Sans Khmer', sans-serif !important;
            font-size: 1.1em;
            line-height: 1.9;
            letter-spacing: 0.3px;
        }
        
        /* Chinese text specific styling */
        [lang="zh"], .chinese-text {
            font-family: 'Noto Sans SC', sans-serif !important;
            font-size: 1.05em;
            line-height: 1.7;
        }
        
        /* Improve readability for headings */
        h1, h2, h3, h4, h5, h6 {
            font-weight: 600;
            line-height: 1.3;
            margin-bottom: 1rem;
        }
        
        h1 { font-size: 2.5rem; }
        h2 { font-size: 2rem; }
        h3 { font-size: 1.75rem; }
        h4 { font-size: 1.5rem; }
        h5 { font-size: 1.25rem; }
        h6 { font-size: 1.1rem; }
        
        /* Better paragraph spacing */
        p {
            margin-bottom: 1rem;
            line-height: 1.7;
        }
        
        /* Improve button text readability */
        .btn {
            font-weight: 500;
            letter-spacing: 0.3px;
            font-size: 1rem;
        }
        
        /* Better label readability */
        label, .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }
        
        /* Card text improvements */
        .card-title {
            font-weight: 600;
            line-height: 1.4;
        }
        
        .card-text {
            line-height: 1.6;
        }
        
        /* Badge text - bigger and clearer */
        .badge {
            font-weight: 600;
            letter-spacing: 0.3px;
            font-size: 0.85rem;
            padding: 0.4em 0.8em;
        }
        
        /* Navigation text */
        .nav-link {
            font-weight: 500;
            font-size: 1rem;
        }
        
        /* Price text - make it bold and clear */
        .price-tag, .price {
            font-weight: 700;
            letter-spacing: 0.5px;
            font-size: 1.5rem;
        }
        
        /* Small text readability */
        small, .small {
            font-size: 0.9em;
            line-height: 1.5;
        }
        
        /* Input fields - better readability */
        .form-control, .form-select {
            font-size: 1rem;
            line-height: 1.6;
        }
        
        /* Dropdown menu items */
        .dropdown-item {
            font-size: 1rem;
            padding: 0.6rem 1rem;
        }
        
        /* ============================================
           MOBILE RESPONSIVE STYLES
           ============================================ */
        
        /* Tablets and below (768px) */
        @media (max-width: 768px) {
            /* Base font adjustments */
            body {
                font-size: 15px;
            }
            
            /* Heading sizes for mobile */
            h1 { font-size: 1.8rem; }
            h2 { font-size: 1.5rem; }
            h3 { font-size: 1.3rem; }
            h4 { font-size: 1.2rem; }
            h5 { font-size: 1.1rem; }
            h6 { font-size: 1rem; }
            
            /* Navigation adjustments */
            .navbar {
                padding: 0.5rem 1rem;
            }
            
            .navbar-brand {
                font-size: 1.2rem;
            }
            
            /* Search bar full width on mobile */
            .navbar .d-flex {
                width: 100%;
                margin: 0.5rem 0;
            }
            
            /* Product cards - 2 columns on mobile */
            .col-6 {
                padding-left: 0.5rem;
                padding-right: 0.5rem;
            }
            
            /* Product card adjustments */
            .product-card-modern {
                margin-bottom: 1rem;
            }
            
            .product-image-wrapper {
                height: 150px;
            }
            
            .card-body {
                padding: 0.75rem;
            }
            
            .card-title {
                font-size: 0.9rem;
            }
            
            /* Price display */
            .price-tag {
                font-size: 1.2rem;
            }
            
            /* Buttons */
            .btn {
                padding: 0.5rem 1rem;
                font-size: 0.9rem;
            }
            
            .btn-lg {
                padding: 0.75rem 1.5rem;
                font-size: 1rem;
            }
            
            .btn-sm {
                padding: 0.4rem 0.8rem;
                font-size: 0.85rem;
            }
            
            /* Badges */
            .badge {
                font-size: 0.75rem;
                padding: 0.3em 0.6em;
            }
            
            /* Category pills */
            .category-pill-modern {
                padding: 8px 16px;
                font-size: 0.85rem;
            }
            
            /* Section headers */
            .section-header-modern h3 {
                font-size: 1.5rem;
            }
            
            /* Language selector cards */
            .language-card {
                padding: 15px;
            }
            
            .flag-icon {
                font-size: 2rem;
            }
            
            /* New customer section */
            .new-customer-intro .display-5 {
                font-size: 1.5rem;
            }
            
            .new-customer-intro .lead {
                font-size: 1rem;
            }
            
            /* Carousel adjustments */
            .carousel-3d-scene {
                height: 350px;
            }
            
            .card-title {
                font-size: 1.2rem;
            }
            
            .card-description {
                font-size: 0.85rem;
            }
            
            /* Form elements */
            .form-control, .form-select {
                font-size: 0.95rem;
                padding: 0.6rem 0.75rem;
            }
            
            /* Modal adjustments */
            .modal-body {
                padding: 1rem;
            }
            
            /* Footer */
            .footer-link {
                font-size: 0.9rem;
            }
            
            /* Container padding */
            .container {
                padding-left: 1rem;
                padding-right: 1rem;
            }
            
            /* Card spacing */
            .card {
                margin-bottom: 1rem;
            }
            
            /* Spacing utilities */
            .mb-5 {
                margin-bottom: 2rem !important;
            }
            
            .py-5 {
                padding-top: 2rem !important;
                padding-bottom: 2rem !important;
            }
        }
        
        /* Small phones (576px and below) */
        @media (max-width: 576px) {
            /* Even smaller fonts for tiny screens */
            body {
                font-size: 14px;
            }
            
            h1 { font-size: 1.5rem; }
            h2 { font-size: 1.3rem; }
            h3 { font-size: 1.2rem; }
            
            /* Single column for very small screens */
            .col-6 {
                flex: 0 0 100%;
                max-width: 100%;
            }
            
            /* Product images smaller */
            .product-image-wrapper {
                height: 180px;
            }
            
            /* Buttons full width on small screens */
            .btn-group {
                flex-direction: column;
                width: 100%;
            }
            
            .btn-group .btn {
                width: 100%;
                margin-bottom: 0.5rem;
            }
            
            /* Language selector stacked */
            .language-selector-section .col-md-4,
            .language-selector-section .col-md-8 {
                flex: 0 0 100%;
                max-width: 100%;
            }
            
            /* Carousel even smaller */
            .carousel-3d-scene {
                height: 300px;
            }
            
            /* New customer section stacked */
            .new-customer-intro .col-lg-7,
            .new-customer-intro .col-lg-5 {
                flex: 0 0 100%;
                max-width: 100%;
            }
            
            /* Price larger on small screens for visibility */
            .price-tag {
                font-size: 1.4rem;
            }
            
            /* Padding adjustments */
            .p-5 {
                padding: 1.5rem !important;
            }
            
            .p-4 {
                padding: 1rem !important;
            }
            
            /* Navigation */
            .navbar-nav {
                padding: 1rem 0;
            }
            
            .nav-item {
                margin-bottom: 0.5rem;
            }
        }
        
        /* Landscape phones */
        @media (max-width: 768px) and (orientation: landscape) {
            .carousel-3d-scene {
                height: 250px;
            }
            
            .new-customer-intro {
                margin-bottom: 1rem;
            }
        }
        
        /* Touch-friendly improvements */
        @media (hover: none) and (pointer: coarse) {
            /* Larger tap targets for touch devices */
            .btn {
                min-height: 44px;
                min-width: 44px;
            }
            
            .nav-link {
                padding: 0.75rem 1rem;
            }
            
            .dropdown-item {
                padding: 0.75rem 1rem;
            }
            
            /* Remove hover effects on touch devices */
            .product-card-modern:hover {
                transform: none;
            }
            
            .btn:hover {
                transform: none;
            }
        }

        .navbar {
            background-color: rgba(var(--bs-body-bg-rgb), 0.9) !important;
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--bs-border-color);
            padding: 1rem 0;
        }

        [data-bs-theme="dark"] .navbar {
            background-color: rgba(15, 23, 42, 0.9) !important;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary-color) !important;
            letter-spacing: -0.025em;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .navbar-brand:hover {
            transform: scale(1.05);
        }

        .navbar-brand i {
            color: var(--primary-color);
            transition: all 0.3s ease;
        }

        .navbar-brand:hover i {
            transform: rotate(-15deg) scale(1.1);
        }

        .nav-link {
            font-weight: 500;
            color: var(--text-dark) !important;
            margin: 0 0.5rem;
            transition: color 0.2s ease;
        }

        .nav-link:hover {
            color: var(--primary-color) !important;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border: none;
            font-weight: 600;
            padding: 0.6rem 1.5rem;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2);
        }

        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
            font-weight: 600;
            padding: 0.6rem 1.5rem;
            border-radius: 0.5rem;
        }

        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            transition: transform 0.2s ease, box-shadow 0.2s ease, background-color 0.3s ease;
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .alert {
            border-radius: 0.75rem;
            border: none;
        }

        .badge-cart {
            font-size: 0.65rem;
            padding: 0.35em 0.6em;
            background-color: var(--primary-color) !important;
        }

        .footer {
            background: var(--bs-body-bg);
            border-top: 1px solid var(--bs-border-color);
            padding: 3rem 0;
            margin-top: 5rem;
            transition: background-color 0.3s ease;
        }

        .theme-toggle-btn {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
            background: var(--bs-tertiary-bg);
            color: var(--text-dark);
        }

        .theme-toggle-btn:hover {
            background: var(--bs-secondary-bg);
        }

        .footer {
            background: linear-gradient(135deg, var(--bs-body-bg) 0%, var(--bs-body-bg) 100%);
            border-top: 1px solid var(--bs-border-color);
            padding: 4rem 0 2rem;
            margin-top: 5rem;
            position: relative;
            overflow: hidden;
        }

        .footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--primary-color), transparent);
            animation: shimmer 3s ease-in-out infinite;
        }

        @keyframes shimmer {
            0%, 100% { opacity: 0.3; }
            50% { opacity: 1; }
        }

        .footer-section {
            margin-bottom: 2rem;
        }

        .footer-title {
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--text-dark);
            margin-bottom: 1.5rem;
            position: relative;
            padding-bottom: 0.75rem;
        }

        .footer-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 40px;
            height: 3px;
            background: linear-gradient(90deg, var(--primary-color), transparent);
            border-radius: 2px;
        }

        .footer-link {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.95rem;
            margin-bottom: 0.75rem;
            display: inline-block;
            position: relative;
            transition: all 0.3s ease;
        }

        .footer-link::before {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--primary-color), var(--primary-hover));
            transition: width 0.3s ease;
        }

        .footer-link:hover {
            color: var(--primary-color);
        }

        .footer-link:hover::before {
            width: 100%;
        }

        .social-links {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .social-icon {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: var(--bs-tertiary-bg);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            text-decoration: none;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .social-icon::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: var(--primary-color);
            transform: translate(-50%, -50%);
            transition: width 0.4s ease, height 0.4s ease;
            z-index: -1;
        }

        .social-icon:hover {
            color: white;
            transform: translateY(-3px);
        }

        .social-icon:hover::before {
            width: 100%;
            height: 100%;
        }

        .footer-divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--bs-border-color), transparent);
            margin: 2rem 0;
        }

        .footer-bottom {
            text-align: center;
            padding-top: 1.5rem;
            border-top: 1px solid var(--bs-border-color);
            color: var(--text-muted);
            font-size: 0.9rem;
            animation: fadeIn 0.6s ease-out 0.2s both;
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

        .footer-section {
            animation: fadeIn 0.6s ease-out backwards;
        }

        .footer-section:nth-child(1) { animation-delay: 0.1s; }
        .footer-section:nth-child(2) { animation-delay: 0.2s; }
        .footer-section:nth-child(3) { animation-delay: 0.3s; }
        .footer-section:nth-child(4) { animation-delay: 0.4s; }

        .footer-link {
            display: block;
        }
    </style>
    
    <!-- Netflix Carousel Styles -->
    <link rel="stylesheet" href="<?php echo e(asset('css/netflix-carousel.css')); ?>">
</head>
<body>

<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="navbar-brand" href="<?php echo e(route('shop.home')); ?>">
            <?php
                $storeIcon = $settings['store_icon'] ?? 'fa-store';
                $storeName = $settings['store_name'] ?? 'Shop';
            ?>
            <i class="fa-solid <?php echo e($storeIcon); ?> me-2"></i>
            <?php echo e($storeName); ?>

        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo e(route('shop.home')); ?>"><?php echo e(__('messages.home')); ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo e(route('shop.about')); ?>"><?php echo e(__('messages.about_us')); ?></a>
                </li>
            </ul>
            
            <form class="d-flex me-3" action="<?php echo e(route('shop.search')); ?>" method="GET" role="search">
                <div class="input-group">
                    <input class="form-control" type="search" name="q" placeholder="<?php echo e(__('messages.search_products')); ?>" value="<?php echo e(request('q')); ?>" style="border-radius: 0.5rem 0 0 0.5rem;">
                    <button class="btn btn-primary" type="submit" style="border-radius: 0 0.5rem 0.5rem 0;">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </div>
            </form>
            
            <ul class="navbar-nav align-items-center">
                <!-- Language Switcher -->
                <li class="nav-item dropdown me-2">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="languageDropdown" role="button" data-bs-toggle="dropdown">
                        <?php
                            $currentLocale = app()->getLocale();
                            $flags = ['en' => '🇬🇧', 'km' => '🇰🇭', 'zh' => '🇨🇳'];
                            $names = ['en' => 'English', 'km' => 'ខ្មែរ', 'zh' => '中文'];
                        ?>
                        <span class="me-2"><?php echo e($flags[$currentLocale] ?? '🇬🇧'); ?></span>
                        <span class="d-none d-md-inline"><?php echo e($names[$currentLocale] ?? 'English'); ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                        <li>
                            <form action="<?php echo e(route('language.switch')); ?>" method="POST" class="dropdown-item-form">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="locale" value="en">
                                <button type="submit" class="dropdown-item py-2 <?php echo e($currentLocale === 'en' ? 'active' : ''); ?>">
                                    🇬🇧 English
                                </button>
                            </form>
                        </li>
                        <li>
                            <form action="<?php echo e(route('language.switch')); ?>" method="POST" class="dropdown-item-form">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="locale" value="km">
                                <button type="submit" class="dropdown-item py-2 <?php echo e($currentLocale === 'km' ? 'active' : ''); ?>">
                                    🇰🇭 ខ្មែរ
                                </button>
                            </form>
                        </li>
                        <li>
                            <form action="<?php echo e(route('language.switch')); ?>" method="POST" class="dropdown-item-form">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="locale" value="zh">
                                <button type="submit" class="dropdown-item py-2 <?php echo e($currentLocale === 'zh' ? 'active' : ''); ?>">
                                    🇨🇳 中文
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
                
                <li class="nav-item me-2">
                    <button class="theme-toggle-btn" id="themeToggleButton" title="Toggle theme">
                        <i class="fa-solid fa-moon dark-icon"></i>
                        <i class="fa-solid fa-sun light-icon d-none"></i>
                    </button>
                </li>
                <li class="nav-item me-3">
                    <a href="<?php echo e(route('shop.cart')); ?>" class="btn btn-light position-relative rounded-circle p-2" style="width: 42px; height: 42px;">
                        <i class="fa-solid fa-cart-shopping text-dark"></i>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('cart')): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill badge-cart">
                                <?php echo e(count(session('cart'))); ?>

                            </span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </a>
                </li>
                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::user()->profile_image): ?>
                                <img src="<?php echo e(asset('storage/' . Auth::user()->profile_image)); ?>" alt="<?php echo e(Auth::user()->name); ?>" class="rounded-circle me-2" style="width: 32px; height: 32px; object-fit: cover;">
                            <?php else: ?>
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                    <?php echo e(substr(Auth::user()->name, 0, 1)); ?>

                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <span><?php echo e(Auth::user()->name); ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                            <li><a class="dropdown-item py-2" href="<?php echo e(route('profile.show')); ?>"><i class="fa-solid fa-user me-2"></i>My Profile</a></li>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::user()->role !== 'admin'): ?>
                                <li><a class="dropdown-item py-2" href="<?php echo e(route('customer.orders')); ?>"><i class="fa-solid fa-shopping-bag me-2"></i>My Orders</a></li>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::user()->role === 'admin'): ?>
                                <li><a class="dropdown-item py-2" href="<?php echo e(route('dashboard')); ?>"><i class="fa-solid fa-gauge me-2"></i>Admin Panel</a></li>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="<?php echo e(route('logout')); ?>">
                                    <?php echo csrf_field(); ?>
                                    <button class="dropdown-item py-2 text-danger" type="submit"><i class="fa-solid fa-right-from-bracket me-2"></i>Logout</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4 pt-4 min-vh-100">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i><?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fa-solid fa-circle-exclamation me-2"></i><?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php echo $__env->yieldContent('content'); ?>
</div>

<footer class="footer mt-auto">
    <div class="container">
        <?php
            $quickLinks = json_decode($settings['footer_quick_links'] ?? '[]', true);
            $supportLinks = json_decode($settings['footer_support_links'] ?? '[]', true);
            $sectionTitles = json_decode($settings['footer_section_titles'] ?? '{}', true);
        ?>
        <div class="row">
            <div class="col-lg-3 col-md-6 footer-section">
                <h5 class="footer-title"><?php echo e($settings['store_name'] ?? 'About PhoneShop'); ?></h5>
                <p class="text-muted" style="font-size: 0.95rem; line-height: 1.6;">
                    <?php echo $settings['footer_about'] ?? 'Your trusted destination for premium smartphones and accessories with the best prices and quality assurance.'; ?>

                </p>
            </div>

            <div class="col-lg-3 col-md-6 footer-section">
                <h5 class="footer-title"><?php echo e($sectionTitles['quick_links'] ?? 'Quick Links'); ?></h5>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $quickLinks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                    <a href="<?php echo e($link['url'] ?? '#'); ?>" class="footer-link">
                        <i class="fa-solid <?php echo e($link['icon'] ?? 'fa-link'); ?> me-1"></i><?php echo e($link['title'] ?? 'Link'); ?>

                    </a>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>

            <div class="col-lg-3 col-md-6 footer-section">
                <h5 class="footer-title"><?php echo e($sectionTitles['support'] ?? 'Support'); ?></h5>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $supportLinks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                    <a href="<?php echo e($link['url'] ?? '#'); ?>" class="footer-link">
                        <i class="fa-solid <?php echo e($link['icon'] ?? 'fa-link'); ?> me-1"></i><?php echo e($link['title'] ?? 'Link'); ?>

                    </a>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>

            <div class="col-lg-3 col-md-6 footer-section">
                <h5 class="footer-title"><?php echo e($sectionTitles['follow_us'] ?? 'Follow Us'); ?></h5>
                <p class="text-muted" style="font-size: 0.9rem; margin-bottom: 1rem;">Connect with us on social media</p>
                <div class="social-links">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($settings['footer_facebook'] ?? false): ?>
                        <a href="<?php echo e($settings['footer_facebook']); ?>" class="social-icon" title="Facebook" target="_blank" rel="noopener noreferrer">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($settings['footer_twitter'] ?? false): ?>
                        <a href="<?php echo e($settings['footer_twitter']); ?>" class="social-icon" title="Twitter" target="_blank" rel="noopener noreferrer">
                            <i class="fab fa-twitter"></i>
                        </a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($settings['footer_instagram'] ?? false): ?>
                        <a href="<?php echo e($settings['footer_instagram']); ?>" class="social-icon" title="Instagram" target="_blank" rel="noopener noreferrer">
                            <i class="fab fa-instagram"></i>
                        </a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($settings['footer_linkedin'] ?? false): ?>
                        <a href="<?php echo e($settings['footer_linkedin']); ?>" class="social-icon" title="LinkedIn" target="_blank" rel="noopener noreferrer">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>

        <div class="footer-divider"></div>

        <div class="footer-bottom">
            <p class="mb-2">
                <i class="fa-solid fa-copyright me-1"></i><?php echo $settings['footer_copyright'] ?? '&copy; ' . date('Y'); ?> <?php echo e($settings['footer_text'] ?? 'PhoneShop. All rights reserved.'); ?>

            </p>
            <p class="mb-0" style="font-size: 0.85rem;">
                Designed with <i class="fas fa-heart text-danger"></i> for excellence
            </p>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const themeToggleButton = document.getElementById('themeToggleButton');
    const darkIcon = themeToggleButton.querySelector('.dark-icon');
    const lightIcon = themeToggleButton.querySelector('.light-icon');

    const updateToggleButton = (theme) => {
        if (theme === 'dark') {
            darkIcon.classList.add('d-none');
            lightIcon.classList.remove('d-none');
        } else {
            darkIcon.classList.remove('d-none');
            lightIcon.classList.add('d-none');
        }
    }

    // Initial button state
    updateToggleButton(document.documentElement.getAttribute('data-bs-theme'));

    themeToggleButton.addEventListener('click', () => {
        const currentTheme = document.documentElement.getAttribute('data-bs-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        
        document.documentElement.setAttribute('data-bs-theme', newTheme);
        localStorage.setItem('theme', newTheme);
        updateToggleButton(newTheme);
    });
</script>

<!-- Netflix Carousel Script -->
<script src="<?php echo e(asset('js/netflix-carousel.js')); ?>"></script>

<!-- Live Chat Widget -->
<?php echo $__env->make('frontend.partials.live-chat', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>
</html>


<style>
    /* Language Switcher */
    .dropdown-item-form {
        margin: 0;
        padding: 0;
    }
    
    .dropdown-item-form button {
        width: 100%;
        text-align: left;
        background: none;
        border: none;
        cursor: pointer;
    }
    
    .dropdown-item-form button.active {
        background-color: var(--primary-color);
        color: white;
    }
</style>
<?php /**PATH C:\laragon\www\Practice\phoneshop\phoneshop\resources\views/frontend/layouts/app.blade.php ENDPATH**/ ?>