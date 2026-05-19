@extends(Auth::user()->role === 'admin' ? 'layouts.app' : 'frontend.layouts.app')

@section('content')
<style>
    .profile-hero {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 2rem;
        padding: 3rem 2rem;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
        animation: slideIn 0.6s ease-out;
    }

    .profile-hero::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200px;
        height: 200px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        animation: float 6s ease-in-out infinite;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px) translateX(0px); }
        50% { transform: translateY(-20px) translateX(20px); }
    }

    .profile-avatar {
        width: 140px;
        height: 140px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 2rem;
        background: rgba(255, 255, 255, 0.2);
        border: 4px solid white;
        font-size: 3.5rem;
        color: white;
        font-weight: 700;
        object-fit: cover;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
        animation: pulse 2s ease-in-out infinite;
    }

    .profile-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
    }

    @keyframes pulse {
        0%, 100% { box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2); }
        50% { box-shadow: 0 8px 48px rgba(0, 0, 0, 0.4); }
    }

    .profile-name {
        color: white;
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        text-align: center;
    }

    .profile-role {
        color: rgba(255, 255, 255, 0.9);
        text-align: center;
        font-size: 0.95rem;
        letter-spacing: 0.05em;
        text-transform: uppercase;
    }

    .profile-card {
        border: none;
        border-radius: 1.5rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        animation: slideIn 0.6s ease-out 0.2s both;
    }

    .profile-section {
        padding: 2rem;
        border-bottom: 1px solid var(--bs-border-color);
        animation: fadeInUp 0.5s ease-out backwards;
    }

    .profile-section:last-child {
        border-bottom: none;
    }

    .profile-section:nth-child(1) { animation-delay: 0.1s; }
    .profile-section:nth-child(2) { animation-delay: 0.2s; }
    .profile-section:nth-child(3) { animation-delay: 0.3s; }

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

    .info-label {
        color: var(--text-muted);
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.75rem;
    }

    .info-value {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--text-dark);
    }

    .profile-badge {
        display: inline-block;
        padding: 0.5rem 1.25rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.85rem;
        letter-spacing: 0.05em;
    }

    .profile-badge.admin {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .profile-badge.customer {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
    }

    .btn-group-profile {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-top: 2rem;
    }

    .btn-group-profile .btn {
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        border-radius: 0.75rem;
        transition: all 0.3s ease;
    }

    .btn-group-profile .btn:hover {
        transform: translateY(-2px);
    }

    .change-picture-link {
        color: rgba(255, 255, 255, 0.9);
        text-decoration: none;
        font-size: 0.9rem;
        display: inline-block;
        margin-top: 1rem;
        transition: all 0.3s ease;
        position: relative;
        padding-bottom: 0.25rem;
    }

    .change-picture-link::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 0;
        height: 2px;
        background: white;
        transition: width 0.3s ease;
    }

    .change-picture-link:hover {
        color: white;
    }

    .change-picture-link:hover::after {
        width: 100%;
    }

    @media (max-width: 768px) {
        .profile-hero {
            padding: 2rem 1.5rem;
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            font-size: 3rem;
        }

        .profile-name {
            font-size: 1.5rem;
        }

        .btn-group-profile {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="profile-hero">
                <div class="profile-avatar">
                    @if($user->profile_image)
                        <img src="{{ asset('storage/' . $user->profile_image) }}" alt="{{ $user->name }}">
                    @else
                        {{ substr($user->name, 0, 1) }}
                    @endif
                </div>
                <h1 class="profile-name">{{ $user->name }}</h1>
                <p class="profile-role">
                    @if($user->role === 'admin')
                        Administrator
                    @else
                        Customer
                    @endif
                </p>
                <div class="text-center">
                    <a href="{{ route('profile.edit') }}" class="change-picture-link">
                        <i class="fa-solid fa-camera me-1"></i>Change Picture
                    </a>
                </div>
            </div>

            <div class="profile-card">
                <div class="profile-section">
                    <div class="info-label">Email Address</div>
                    <div class="info-value">{{ $user->email }}</div>
                </div>

                <div class="profile-section">
                    <div class="info-label">Account Type</div>
                    <div>
                        <span class="profile-badge {{ $user->role === 'admin' ? 'admin' : 'customer' }}">
                            @if($user->role === 'admin')
                                <i class="fa-solid fa-shield me-1"></i>Admin
                            @else
                                <i class="fa-solid fa-user me-1"></i>Customer
                            @endif
                        </span>
                    </div>
                </div>

                @if($user->role !== 'admin')
                <div class="profile-section">
                    <div class="info-label">Quick Actions</div>
                    <div class="d-grid gap-2">
                        <a href="{{ route('customer.orders') }}" class="btn btn-outline-primary btn-lg">
                            <i class="fa-solid fa-shopping-bag me-2"></i>My Orders
                            <span class="badge bg-primary ms-2">{{ \App\Models\Sale::where('user_id', $user->id)->count() }}</span>
                        </a>
                        <a href="{{ route('shop.cart') }}" class="btn btn-outline-secondary">
                            <i class="fa-solid fa-shopping-cart me-2"></i>View Cart
                        </a>
                    </div>
                </div>
                @endif

                <div class="profile-section">
                    <div class="btn-group-profile">
                        <a href="{{ route('profile.edit') }}" class="btn btn-primary fw-semibold">
                            <i class="fa-solid fa-pen me-2"></i>Edit Profile
                        </a>
                        <a href="{{ Auth::user()->role === 'admin' ? route('dashboard') : route('shop.home') }}" class="btn btn-outline-secondary fw-semibold">
                            <i class="fa-solid fa-arrow-left me-2"></i>Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
