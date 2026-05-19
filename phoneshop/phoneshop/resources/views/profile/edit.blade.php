@extends(Auth::user()->role === 'admin' ? 'layouts.app' : 'frontend.layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-5">
                    <h2 class="fw-bold mb-4">Edit Profile</h2>

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

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

                    <!-- Profile Picture Section -->
                    <div class="mb-5">
                        <h5 class="fw-semibold mb-3">Profile Picture</h5>
                        <div class="text-center mb-4">
                            @if($user->profile_image)
                                <img id="previewImage" src="{{ asset('storage/' . $user->profile_image) }}" alt="{{ $user->name }}" class="rounded-circle shadow" style="width: 150px; height: 150px; object-fit: cover;">
                            @else
                                <div id="previewImage" class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto shadow" style="width: 150px; height: 150px; font-size: 3rem;">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                            @endif
                        </div>

                        <form action="{{ route('profile.update-image') }}" method="POST" enctype="multipart/form-data" class="mb-3">
                            @csrf
                            <div class="mb-3">
                                <label for="profile_image" class="form-label fw-semibold">Choose Picture</label>
                                <input type="file" class="form-control @error('profile_image') is-invalid @enderror" id="profile_image" name="profile_image" accept="image/*" onchange="previewFile()">
                                @error('profile_image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted d-block mt-1">JPG, PNG, GIF, WebP (Max 5MB)</small>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 fw-semibold">
                                <i class="fa-solid fa-upload me-2"></i>Upload Picture
                            </button>
                        </form>
                    </div>

                    <hr>

                    <!-- Profile Information Section -->
                    <div class="mb-4">
                        <h5 class="fw-semibold mb-3">Profile Information</h5>

                        <form action="{{ route('profile.update') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="name" class="form-label fw-semibold">Full Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="email" class="form-label fw-semibold">Email Address</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success fw-semibold">
                                    <i class="fa-solid fa-save me-2"></i>Save Changes
                                </button>
                                <a href="{{ route('profile.show') }}" class="btn btn-outline-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function previewFile() {
        const file = document.getElementById('profile_image').files[0];
        const preview = document.getElementById('previewImage');
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                if (preview.tagName === 'DIV') {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'rounded-circle shadow';
                    img.style.width = '150px';
                    img.style.height = '150px';
                    img.style.objectFit = 'cover';
                    preview.innerHTML = '';
                    preview.appendChild(img);
                } else {
                    preview.src = e.target.result;
                }
            };
            reader.readAsDataURL(file);
        }
    }
</script>
@endsection
