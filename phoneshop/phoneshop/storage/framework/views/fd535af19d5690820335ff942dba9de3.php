<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Panel</title>

    <script>
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
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        :root {
            --sidebar-bg: linear-gradient(180deg, #0f172a 0%, #1e1b4b 100%);
            --sidebar-hover: rgba(139, 92, 246, 0.15);
            --primary-color: #8b5cf6;
            --primary-gradient: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
            --accent-gradient: linear-gradient(135deg, #06b6d4 0%, #3b82f6 100%);
            --bg-light: #f8fafc;
            --card-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -1px rgba(0, 0, 0, 0.1);
            --card-shadow-hover: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1);
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --border-color: #e2e8f0;
        }

        [data-bs-theme="dark"] {
            --bg-light: #0f172a;
            --sidebar-bg: linear-gradient(180deg, #0f172a 0%, #1e1b4b 100%);
            --card-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.5), 0 1px 2px -1px rgba(0, 0, 0, 0.5);
            --card-shadow-hover: 0 10px 15px -3px rgba(0, 0, 0, 0.5), 0 4px 6px -4px rgba(0, 0, 0, 0.5);
            --text-primary: #f1f5f9;
            --text-secondary: #94a3b8;
            --border-color: #334155;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body { 
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--bg-light);
            color: var(--text-primary);
            overflow-x: hidden;
            transition: background-color 0.3s ease, color 0.3s ease;
            font-size: 14px;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* Sidebar Styles */
        .sidebar {
            height: 100vh;
            background: var(--sidebar-bg);
            color: #fff;
            position: fixed;
            width: 280px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1000;
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.12);
            overflow-y: auto;
            overflow-x: hidden;
            border-right: 1px solid rgba(139, 92, 246, 0.1);
        }

        .sidebar::-webkit-scrollbar {
            width: 8px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.2);
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(139, 92, 246, 0.4);
            border-radius: 4px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(139, 92, 246, 0.6);
        }

        .sidebar.collapsed {
            width: 85px;
        }

        .sidebar.collapsed .sidebar-link span,
        .sidebar.collapsed .sidebar-header h4 span,
        .sidebar.collapsed .sidebar-header small,
        .sidebar.collapsed .position-absolute .small,
        .sidebar.collapsed .position-absolute .x-small,
        .sidebar.collapsed .position-absolute .btn {
            opacity: 0;
            visibility: hidden;
            width: 0;
            overflow: hidden;
        }

        .sidebar.collapsed .sidebar-link {
            justify-content: center;
            padding: 1rem 0;
            margin: 0.3rem 0.8rem;
            border-radius: 12px;
        }

        .sidebar.collapsed .sidebar-link i {
            margin-right: 0;
            font-size: 1.4rem;
        }

        .sidebar.collapsed .sidebar-header {
            text-align: center;
            padding: 1.5rem 0.5rem;
        }

        .sidebar.collapsed .sidebar-header h4 {
            justify-content: center;
        }

        .sidebar.collapsed .sidebar-header .d-flex {
            flex-direction: column;
            align-items: center !important;
        }

        .sidebar.collapsed .sidebar-header .me-3 {
            margin-right: 0 !important;
            margin-bottom: 0.5rem;
        }

        .sidebar.collapsed .sidebar-header small {
            display: none;
        }

        .sidebar.collapsed .position-absolute .d-flex {
            justify-content: center;
        }

        .sidebar.collapsed .position-absolute .me-3 {
            margin-right: 0 !important;
        }

        /* Sidebar Header */
        .sidebar-header {
            padding: 2rem 1.5rem;
            border-bottom: 1px solid rgba(139, 92, 246, 0.15);
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.08), rgba(124, 58, 237, 0.05));
            backdrop-filter: blur(10px);
            position: relative;
        }

        .sidebar-header::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, rgba(139, 92, 246, 0.6), transparent);
        }

        .sidebar-header::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at top right, rgba(139, 92, 246, 0.1), transparent 60%);
            pointer-events: none;
        }

        .sidebar-header h4 {
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
            font-size: 1.4rem;
            font-weight: 800;
            letter-spacing: -0.03em;
            background: linear-gradient(135deg, #ffffff 0%, #e0e7ff 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            position: relative;
            z-index: 1;
        }

        .sidebar-header h4 span {
            transition: all 0.3s ease;
        }

        .sidebar-header img {
            transition: all 0.3s ease;
        }

        /* Sidebar Menu */
        .sidebar-menu {
            padding: 1.5rem 0.8rem 2rem 0.8rem;
        }

        .menu-section-label {
            padding: 1.5rem 1.2rem 0.6rem 1.2rem;
            margin-top: 1rem;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 2px;
            color: rgba(139, 92, 246, 0.6);
            text-transform: uppercase;
            transition: all 0.3s ease;
            position: relative;
        }

        .menu-section-label::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 1.2rem;
            right: 1.2rem;
            height: 2px;
            background: linear-gradient(90deg, rgba(139, 92, 246, 0.3), transparent);
            border-radius: 2px;
        }

        .sidebar.collapsed .menu-section-label {
            opacity: 0;
            visibility: hidden;
            height: 0;
            padding: 0;
            margin: 0;
            overflow: hidden;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 0.85rem 1.2rem;
            margin: 0.25rem 0.5rem;
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 12px;
            position: relative;
            overflow: hidden;
            font-weight: 500;
            font-size: 14px;
        }

        .sidebar-link::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background: var(--primary-gradient);
            transform: scaleY(0);
            transition: transform 0.25s ease;
            border-radius: 0 4px 4px 0;
        }

        .sidebar-link::after {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at center, rgba(139, 92, 246, 0.15), transparent 70%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .sidebar-link:hover {
            background: var(--sidebar-hover);
            color: #fff;
            transform: translateX(4px);
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.2);
        }

        .sidebar-link:hover::after {
            opacity: 1;
        }

        .sidebar-link.active {
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.2), rgba(124, 58, 237, 0.15));
            color: #fff;
            font-weight: 600;
            box-shadow: 0 4px 16px rgba(139, 92, 246, 0.25);
        }

        .sidebar-link.active::before {
            transform: scaleY(1);
        }

        .sidebar-link i {
            width: 22px;
            font-size: 1.15rem;
            margin-right: 14px;
            transition: all 0.25s ease;
            text-align: center;
            color: rgba(139, 92, 246, 0.8);
        }

        .sidebar-link:hover i {
            transform: scale(1.15) rotate(5deg);
            color: #a78bfa;
        }

        .sidebar-link.active i {
            color: #c4b5fd;
        }

        .sidebar-link span {
            transition: all 0.3s ease;
        }

        /* Main Content */
        .main-content {
            margin-left: 280px;
            padding: 2rem;
            min-height: 100vh;
            transition: margin-left 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .main-content.expanded {
            margin-left: 85px;
        }

        /* Top Navigation */
        .top-nav {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            padding: 1rem 2rem;
            margin: -2rem -2rem 2rem -2rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: flex-end;
            align-items: center;
            transition: all 0.3s ease;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        [data-bs-theme="dark"] .top-nav {
            background: rgba(15, 23, 42, 0.9);
            border-bottom: 1px solid var(--border-color);
        }

        /* Cards */
        .card {
            border: 1px solid var(--border-color);
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            background: var(--bs-body-bg);
        }

        .card:hover {
            box-shadow: var(--card-shadow-hover);
            transform: translateY(-4px);
            border-color: rgba(99, 102, 241, 0.2);
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 1.5rem;
            font-weight: 600;
            font-size: 15px;
        }

        [data-bs-theme="dark"] .card {
            border: 1px solid var(--border-color);
        }

        [data-bs-theme="dark"] .card-header {
            border-bottom: 1px solid var(--border-color);
        }

        /* Buttons */
        .btn {
            border-radius: 8px;
            padding: 0.6rem 1.25rem;
            font-weight: 500;
            transition: all 0.2s ease;
            border: none;
            font-size: 14px;
            letter-spacing: -0.01em;
        }

        .btn-primary {
            background: var(--primary-gradient);
            border: none;
            box-shadow: 0 2px 8px rgba(99, 102, 241, 0.25);
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.35);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        /* Theme Toggle */
        .theme-toggle-btn {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
            background: rgba(99, 102, 241, 0.08);
            color: var(--primary-color);
            margin-right: 1rem;
        }

        .theme-toggle-btn:hover {
            background: rgba(99, 102, 241, 0.15);
            transform: scale(1.05);
        }

        /* Sidebar Toggle Button */
        .sidebar-toggle-btn {
            position: fixed;
            top: 1.25rem;
            left: 290px;
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            background: var(--primary-gradient);
            color: white;
            z-index: 1001;
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.35);
        }

        .sidebar-toggle-btn:hover {
            transform: scale(1.1) rotate(5deg);
            box-shadow: 0 6px 20px rgba(139, 92, 246, 0.45);
        }

        .sidebar.collapsed ~ .sidebar-toggle-btn {
            left: 95px;
        }

        .sidebar-toggle-btn i {
            transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar.collapsed ~ .sidebar-toggle-btn i {
            transform: rotate(180deg);
        }

        .hover-opacity:hover {
            opacity: 0.85;
            transition: opacity 0.2s;
        }

        /* Alerts */
        .alert {
            border-radius: 10px;
            border: none;
            padding: 1rem 1.25rem;
            box-shadow: var(--card-shadow);
            font-size: 14px;
        }

        .alert-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }

        /* Dropdown */
        .dropdown-menu {
            border-radius: 10px;
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
            padding: 0.5rem;
            margin-top: 0.5rem;
        }

        .dropdown-item {
            border-radius: 6px;
            padding: 0.6rem 1rem;
            transition: all 0.2s ease;
            font-size: 14px;
        }

        .dropdown-item:hover {
            background: rgba(99, 102, 241, 0.08);
            transform: translateX(2px);
        }

        /* Mobile Overlay */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(4px);
            z-index: 999;
            transition: all 0.3s ease;
        }
        
        .sidebar-overlay.active {
            display: block;
        }

        .mobile-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--bs-body-color);
            margin-right: auto;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .mobile-toggle:hover {
            transform: scale(1.1);
        }

        /* Tables */
        .table {
            border-radius: 10px;
            overflow: hidden;
        }

        .table thead th {
            background: rgba(99, 102, 241, 0.06);
            border: none;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.8px;
            padding: 1rem;
            color: var(--text-secondary);
        }

        .table tbody tr {
            transition: all 0.2s ease;
            border-bottom: 1px solid var(--border-color);
        }

        .table tbody tr:hover {
            background: rgba(99, 102, 241, 0.03);
        }

        /* Form Controls */
        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid var(--border-color);
            padding: 0.6rem 1rem;
            transition: all 0.2s ease;
            font-size: 14px;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.08);
        }

        [data-bs-theme="dark"] .form-control,
        [data-bs-theme="dark"] .form-select {
            border: 1px solid var(--border-color);
            background: rgba(255, 255, 255, 0.03);
        }

        /* Badges */
        .badge {
            border-radius: 6px;
            padding: 0.35rem 0.75rem;
            font-weight: 500;
            font-size: 12px;
            letter-spacing: 0.02em;
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .sidebar { 
                margin-left: -280px; 
            }
            .main-content { 
                margin-left: 0; 
                padding: 1rem; 
            }
            .main-content.expanded { 
                margin-left: 0; 
            }
            .sidebar.active { 
                margin-left: 0; 
            }
            .mobile-toggle { 
                display: block; 
            }
            .top-nav { 
                padding: 1rem; 
                justify-content: space-between; 
            }
            .sidebar-toggle-btn { 
                display: none; 
            }
        }

        /* Smooth Animations */
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

        .card, .alert {
            animation: fadeIn 0.4s ease;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(99, 102, 241, 0.25);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: rgba(99, 102, 241, 0.4);
        }
    </style>
</head>
<body>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
<div class="sidebar-overlay" id="sidebarOverlay"></div>
<div class="sidebar shadow" id="sidebar">
    <div class="sidebar-header">
        <a href="<?php echo e(route('profile.show')); ?>" class="text-decoration-none hover-opacity">
            <?php
                $storeLogo = $settings['store_logo'] ?? null;
                $storeIcon = $settings['store_icon'] ?? 'fa-store';
                $storeName = $settings['store_name'] ?? 'Admin Panel';
            ?>
            <div class="d-flex align-items-center mb-2">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($storeLogo): ?>
                    <img src="<?php echo e(asset('storage/' . $storeLogo)); ?>" alt="<?php echo e($storeName); ?>" class="me-3" style="height: 45px; width: 45px; object-fit: contain; filter: drop-shadow(0 2px 8px rgba(139, 92, 246, 0.3));">
                <?php else: ?>
                    <div class="d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px; background: var(--primary-gradient); border-radius: 12px; box-shadow: 0 4px 12px rgba(139, 92, 246, 0.35);">
                        <i class="fa-solid <?php echo e($storeIcon); ?>" style="color: white; font-size: 1.5rem;"></i>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <div>
                    <h4 class="mb-0 fw-bold text-white" style="font-size: 1.4rem; letter-spacing: -0.03em;">
                        <?php echo e($storeName); ?>

                    </h4>
                    <small class="text-muted" style="font-size: 11px; opacity: 0.5; letter-spacing: 0.5px;">MANAGEMENT SYSTEM</small>
                </div>
            </div>
        </a>
    </div>
    
    <div class="sidebar-menu">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('permission', 'view_dashboard')): ?>
            <a href="<?php echo e(route('dashboard')); ?>" class="sidebar-link <?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>">
                <i class="fa-solid fa-chart-pie"></i><span>Dashboard</span>
            </a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        
        <!-- Main Menu Section -->
        <div class="menu-section-label"><span>MAIN MENU</span></div>
        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('permission', 'view_categories')): ?>
            <a href="<?php echo e(route('categories.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('categories.*') ? 'active' : ''); ?>">
                <i class="fa-solid fa-layer-group"></i><span>Categories</span>
            </a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('permission', 'view_phones')): ?>
            <a href="<?php echo e(route('phones.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('phones.*') ? 'active' : ''); ?>">
                <i class="fa-solid fa-box"></i><span>Products</span>
            </a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        
        <!-- Sales Section -->
        <div class="menu-section-label"><span>SALES</span></div>
        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('permission', 'create_sales')): ?>
            <a href="<?php echo e(route('sales.create')); ?>" class="sidebar-link <?php echo e(request()->routeIs('sales.create') ? 'active' : ''); ?>">
                <i class="fa-solid fa-cash-register"></i><span>POS System</span>
            </a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('permission', 'view_sales')): ?>
            <a href="<?php echo e(route('sales.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('sales.index') || request()->routeIs('sales.show') ? 'active' : ''); ?>">
                <i class="fa-solid fa-cart-shopping"></i><span>Sales History</span>
            </a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('permission', 'approve_orders')): ?>
            <a href="<?php echo e(route('orders.approval')); ?>" class="sidebar-link <?php echo e(request()->routeIs('orders.approval') ? 'active' : ''); ?>">
                <i class="fa-solid fa-check-circle"></i><span>Order Approval</span>
            </a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        
        <!-- Customer Support Section -->
        <div class="menu-section-label"><span>CUSTOMER SUPPORT</span></div>
        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('permission', 'manage_chat')): ?>
            <a href="<?php echo e(route('admin.chat.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('admin.chat.*') ? 'active' : ''); ?>">
                <i class="fa-solid fa-comments"></i><span>Live Chat</span>
            </a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <!-- Analytics Section -->
        <div class="menu-section-label"><span>ANALYTICS</span></div>
        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('permission', 'view_reports')): ?>
            <a href="<?php echo e(route('reports.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('reports.*') ? 'active' : ''); ?>">
                <i class="fa-solid fa-chart-bar"></i><span>Reports</span>
            </a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        
        <!-- Management Section -->
        <div class="menu-section-label"><span>MANAGEMENT</span></div>
        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('permission', 'view_users')): ?>
            <a href="<?php echo e(route('users.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('users.*') ? 'active' : ''); ?>">
                <i class="fa-solid fa-users"></i><span>Users</span>
            </a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('permission', 'manage_slides')): ?>
            <a href="<?php echo e(route('slides.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('slides.*') ? 'active' : ''); ?>">
                <i class="fa-solid fa-images"></i><span>Slideshow</span>
            </a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        
        <!-- System Section -->
        <div class="menu-section-label"><span>SYSTEM</span></div>
        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('permission', 'view_settings')): ?>
            <a href="<?php echo e(route('settings.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('settings.*') ? 'active' : ''); ?>">
                <i class="fa-solid fa-gear"></i><span>Settings</span>
            </a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <a href="<?php echo e(route('api-keys.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('api-keys.*') ? 'active' : ''); ?>">
            <i class="fa-solid fa-key"></i><span>API Keys</span>
        </a>
        
        <!-- Account Section -->
        <div class="menu-section-label"><span>ACCOUNT</span></div>
        
        <a href="<?php echo e(route('profile.show')); ?>" class="sidebar-link <?php echo e(request()->routeIs('profile.*') ? 'active' : ''); ?>">
            <i class="fa-solid fa-user"></i><span>My Profile</span>
        </a>
        
        <!-- User Profile & Logout -->
        <div class="mt-4 mb-3">
            <a href="<?php echo e(route('profile.show')); ?>" class="d-flex align-items-center mb-3 text-decoration-none hover-opacity px-2">
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 42px; height: 42px; flex-shrink: 0; background: var(--primary-gradient) !important; box-shadow: 0 4px 12px rgba(139, 92, 246, 0.35); border: 2px solid rgba(255, 255, 255, 0.1);">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::user()->profile_image): ?>
                        <img src="<?php echo e(asset('storage/' . Auth::user()->profile_image)); ?>" class="rounded-circle" style="width: 42px; height: 42px; object-fit: cover;">
                    <?php else: ?>
                        <span style="font-size: 16px; font-weight: 600;"><?php echo e(substr(Auth::user()->name, 0, 1)); ?></span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div style="min-width: 0; flex: 1;">
                    <div class="small fw-semibold text-white text-truncate" style="font-size: 13px;"><?php echo e(Auth::user()->name); ?></div>
                    <div class="x-small text-muted text-truncate" style="font-size: 11px; opacity: 0.6;"><?php echo e(Auth::user()->role ?? 'Administrator'); ?></div>
                </div>
            </a>
            <form method="POST" action="<?php echo e(route('logout')); ?>" class="px-2">
                <?php echo csrf_field(); ?>
                <button class="btn btn-outline-danger btn-sm w-100" style="font-size: 13px; padding: 0.5rem;">
                    <i class="fa-solid fa-right-from-bracket me-2"></i>Logout
                </button>
            </form>
        </div>
    </div>
</div>

<button class="sidebar-toggle-btn" id="sidebarToggle" title="Toggle Sidebar">
    <i class="fa-solid fa-chevron-left"></i>
</button>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<main class="main-content" id="mainContent">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
    <div class="top-nav">
        <button class="mobile-toggle" id="mobileToggle">
            <i class="fa-solid fa-bars"></i>
        </button>
        <button class="theme-toggle-btn" id="themeToggleButton" title="Toggle theme">
            <i class="fa-solid fa-moon dark-icon"></i>
            <i class="fa-solid fa-sun light-icon d-none"></i>
        </button>
        <div class="dropdown">
            <button class="btn btn-link text-body text-decoration-none dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="fa-solid fa-bell me-3 text-muted"></i>
                <?php echo e(Auth::user()->name); ?>

            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                <li><a class="dropdown-item" href="<?php echo e(route('profile.show')); ?>"><i class="fa-solid fa-user me-2"></i>My Profile</a></li>
                <li><a class="dropdown-item" href="<?php echo e(route('shop.home')); ?>"><i class="fa-solid fa-shop me-2"></i>View Shop</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="<?php echo e(route('logout')); ?>">
                        <?php echo csrf_field(); ?>
                        <button class="dropdown-item text-danger" type="submit"><i class="fa-solid fa-right-from-bracket me-2"></i>Logout</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="container-fluid">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i><?php echo e(session('success')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php echo $__env->yieldContent('content'); ?>
    </div>
</main>

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

    updateToggleButton(document.documentElement.getAttribute('data-bs-theme'));

    themeToggleButton.addEventListener('click', () => {
        const currentTheme = document.documentElement.getAttribute('data-bs-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        
        document.documentElement.setAttribute('data-bs-theme', newTheme);
        localStorage.setItem('theme', newTheme);
        updateToggleButton(newTheme);
    });

    // Sidebar Mobile Toggle
    const mobileToggle = document.getElementById('mobileToggle');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');

    if (mobileToggle) {
        mobileToggle.addEventListener('click', () => {
            sidebar.classList.add('active');
            overlay.classList.add('active');
        });
    }

    if (overlay) {
        overlay.addEventListener('click', () => {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        });
    }

    // Sidebar Toggle (Desktop)
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mainContent = document.getElementById('mainContent');

    // Load saved sidebar state
    const sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    if (sidebarCollapsed) {
        sidebar.classList.add('collapsed');
        mainContent.classList.add('expanded');
    }

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
            
            // Save state to localStorage
            const isCollapsed = sidebar.classList.contains('collapsed');
            localStorage.setItem('sidebarCollapsed', isCollapsed);
        });
    }
</script>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\laragon\www\Practice\phoneshop\phoneshop\resources\views/layouts/app.blade.php ENDPATH**/ ?>