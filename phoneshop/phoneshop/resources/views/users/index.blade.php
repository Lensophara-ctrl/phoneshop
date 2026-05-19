@extends('layouts.app')

@section('content')
<style>
    .user-table-wrapper {
        animation: slideIn 0.5s ease-out;
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

    .user-row {
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .user-row:hover {
        background-color: var(--bs-tertiary-bg);
        transform: translateX(4px);
    }

    .user-avatar {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.9rem;
        color: white;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        object-fit: cover;
    }

    .user-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
    }

    .user-avatar-letter {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.9rem;
        color: white;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .user-avatar::before,
    .user-avatar-letter::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.2);
        transition: left 0.3s ease;
    }

    .user-row:hover .user-avatar::before,
    .user-row:hover .user-avatar-letter::before {
        left: 100%;
    }

    .user-avatar-letter.admin {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .user-avatar-letter.staff {
        background: linear-gradient(135deg, #4ade80 0%, #22c55e 100%);
    }

    .user-avatar-letter.customer {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }

    .user-name {
        font-weight: 600;
        color: var(--text-dark);
        transition: color 0.3s ease;
    }

    .user-row:hover .user-name {
        color: var(--primary-color);
    }

    .badge-custom {
        padding: 0.5rem 0.875rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
    }

    .badge-admin {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .badge-staff {
        background: linear-gradient(135deg, #4ade80 0%, #22c55e 100%);
        color: white;
    }

    .badge-customer {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
    }

    .btn-action {
        width: 36px;
        height: 36px;
        padding: 0 !important;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.5rem;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }

    .btn-action:hover {
        transform: translateY(-2px);
    }

    .btn-action.view {
        background-color: var(--primary-color);
        color: white;
    }

    .btn-action.view:hover {
        background-color: var(--primary-hover);
    }

    .btn-action.reset {
        background-color: #fbbf24;
        color: white;
    }

    .btn-action.reset:hover {
        background-color: #f59e0b;
    }

    .btn-action.delete {
        background-color: #ef4444;
        color: white;
    }

    .btn-action.delete:hover {
        background-color: #dc2626;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        animation: fadeIn 0.6s ease-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    .page-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--text-dark);
    }

    table thead th {
        background-color: var(--bs-tertiary-bg);
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        color: var(--text-muted);
        border: none;
        padding: 1rem;
    }

    .user-card {
        border: none;
        border-radius: 1rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
</style>

<div class="container">
    <div class="page-header">
        <h1 class="page-title">User Management</h1>
        <a href="{{ route('users.create-admin') }}" class="btn btn-primary fw-semibold">
            <i class="fa-solid fa-user-plus me-2"></i>Create New User
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-circle-exclamation me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="user-card user-table-wrapper">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Joined</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr class="user-row">
                            <td class="py-3">
                                <div class="d-flex align-items-center gap-3">
                                    @if($user->profile_image)
                                        <div class="user-avatar">
                                            <img src="{{ asset('storage/' . $user->profile_image) }}" alt="{{ $user->name }}">
                                        </div>
                                    @else
                                        <div class="user-avatar-letter {{ $user->role }}">
                                            {{ substr($user->name, 0, 1) }}
                                        </div>
                                    @endif
                                    <span class="user-name">{{ $user->name }}</span>
                                </div>
                            </td>
                            <td class="py-3">{{ $user->email }}</td>
                            <td class="py-3">
                                <span class="badge-custom badge-{{ $user->role }}">
                                    @if($user->role === 'admin')
                                        <i class="fa-solid fa-shield-halved me-1"></i>Admin
                                    @elseif($user->role === 'staff')
                                        <i class="fa-solid fa-user-tie me-1"></i>Staff
                                    @else
                                        <i class="fa-solid fa-user me-1"></i>Customer
                                    @endif
                                </span>
                            </td>
                            <td class="py-3 text-muted small">{{ $user->created_at->format('M d, Y') }}</td>
                            <td class="py-3 text-center">
                                <div class="d-flex gap-2 justify-content-center">
                                    <a href="{{ route('users.show', $user) }}" class="btn-action view" title="View Profile" data-bs-toggle="tooltip">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    @if(auth()->user()->role === 'admin' || $user->role !== 'admin')
                                        <a href="{{ route('users.edit', $user) }}" class="btn-action reset" title="Edit User" data-bs-toggle="tooltip">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        <a href="{{ route('users.reset-password', $user) }}" class="btn-action reset" title="Reset Password" data-bs-toggle="tooltip">
                                            <i class="fa-solid fa-key"></i>
                                        </a>
                                    @endif
                                    
                                    @if($user->role !== 'admin' && $user->id !== auth()->id())
                                        <form method="POST" action="{{ route('users.destroy', $user) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-action delete" title="Delete User" data-bs-toggle="tooltip">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    @elseif($user->role === 'admin')
                                        <span class="badge bg-secondary px-2 py-1" title="Admin users cannot be deleted" style="font-size: 0.7rem;">
                                            <i class="fa-solid fa-shield-halved"></i> Protected
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-5 text-center text-muted">
                                <i class="fa-solid fa-inbox fa-2x mb-2" style="opacity: 0.5;"></i>
                                <p>No users found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-5">
        {{ $users->links() }}
    </div>
</div>

<script>
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
</script>
@endsection
