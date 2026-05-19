@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-5">
                    <h2 class="fw-bold mb-4">Edit User</h2>

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fa-solid fa-circle-exclamation me-2"></i>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('users.update', $user) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3 text-center">
                            <div class="position-relative d-inline-block">
                                <img id="profilePreview" src="{{ $user->profile_image ? asset('storage/' . $user->profile_image) : asset('storage/default-profile.png') }}" alt="Profile Preview" class="rounded-circle" style="width: 120px; height: 120px; object-fit: cover; border: 3px solid #e9ecef;">
                                <label for="profile_image" class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle p-2 cursor-pointer" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; cursor: pointer;">
                                    <i class="fa-solid fa-camera fa-sm"></i>
                                </label>
                            </div>
                            <input type="file" class="d-none @error('profile_image') is-invalid @enderror" id="profile_image" name="profile_image" accept="image/*">
                            @error('profile_image')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Full Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required autofocus>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">Email Address</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="role" class="form-label fw-semibold">Role</label>
                            <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required {{ (auth()->user()->role === 'staff' && $user->role === 'admin') ? 'disabled' : '' }}>
                                <option value="">Select Role</option>
                                @if(auth()->user()->role === 'admin')
                                    <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                                @elseif($user->role === 'admin')
                                    <option value="admin" selected>Admin (Cannot be changed by staff)</option>
                                @endif
                                <option value="staff" {{ old('role', $user->role) == 'staff' ? 'selected' : '' }}>Staff</option>
                                <option value="customer" {{ old('role', $user->role) == 'customer' ? 'selected' : '' }}>Customer</option>
                            </select>
                            @if(auth()->user()->role === 'staff')
                                <small class="text-muted">Only admins can change user roles to admin</small>
                            @endif
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if(auth()->user()->role === 'staff' && $user->role === 'admin')
                                <input type="hidden" name="role" value="admin">
                            @endif
                        </div>

                        <div id="permissionsSection" class="mb-3" style="display: {{ old('role', $user->role) == 'staff' ? 'block' : 'none' }};">
                            <label class="form-label fw-semibold">Permissions</label>
                            <div class="card">
                                <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                                    @php
                                        $userPermissions = $user->permissions->pluck('id')->toArray();
                                    @endphp
                                    @foreach($permissions as $group => $groupPermissions)
                                        <div class="mb-3">
                                            <h6 class="text-uppercase text-muted mb-2" style="font-size: 0.75rem;">{{ ucfirst($group) }}</h6>
                                            @foreach($groupPermissions as $permission)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->id }}" id="perm_{{ $permission->id }}" {{ in_array($permission->id, old('permissions', $userPermissions)) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="perm_{{ $permission->id }}">
                                                        {{ $permission->display_name }}
                                                        @if($permission->description)
                                                            <small class="text-muted d-block">{{ $permission->description }}</small>
                                                        @endif
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary btn-lg fw-semibold">
                                <i class="fa-solid fa-save me-2"></i>Update User
                            </button>
                            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('profile_image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                document.getElementById('profilePreview').src = event.target.result;
            };
            reader.readAsDataURL(file);
        }
    });

    document.getElementById('profilePreview').addEventListener('click', function() {
        document.getElementById('profile_image').click();
    });

    // Show/hide permissions based on role selection
    document.getElementById('role').addEventListener('change', function() {
        const permissionsSection = document.getElementById('permissionsSection');
        if (this.value === 'staff') {
            permissionsSection.style.display = 'block';
        } else {
            permissionsSection.style.display = 'none';
        }
    });
</script>
@endsection
