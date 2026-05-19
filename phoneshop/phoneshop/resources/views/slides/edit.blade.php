@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold mb-0">Edit Slide</h3>
    <a href="{{ route('slides.index') }}" class="btn btn-outline-secondary">
        <i class="fa-solid fa-arrow-left me-2"></i>Back to List
    </a>
</div>

<div class="row g-4">
    <!-- Form Section -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('slides.update', $slide->id) }}" method="POST" enctype="multipart/form-data" id="slideForm">
                    @csrf
                    @method('PUT')
                    <div class="row g-4">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Title</label>
                            <input type="text" name="title" id="slideTitle" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $slide->title) }}" required>
                            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label fw-semibold">Order</label>
                            <input type="number" name="order" class="form-control" value="{{ old('order', $slide->order) }}">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" id="slideDescription" class="form-control" rows="3">{{ old('description', $slide->description) }}</textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Button Text</label>
                            <input type="text" name="button_text" id="slideButtonText" class="form-control" value="{{ old('button_text', $slide->button_text) }}">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Button Link</label>
                            <input type="text" name="button_link" id="slideButtonLink" class="form-control" value="{{ old('button_link', $slide->button_link) }}">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Slide Image</label>
                            @if($slide->image)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/'.$slide->image) }}" id="currentImage" class="rounded shadow-sm" style="height: 100px;">
                                </div>
                            @endif
                            <input type="file" name="image" id="slideImage" class="form-control @error('image') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp">
                            <small class="text-muted">Recommended: 1920x600px or higher (Max 10MB)</small>
                            @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" {{ $slide->is_active ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="is_active">Active Status</label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fa-solid fa-save me-2"></i>Update Slide
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Live Preview Section -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fa-solid fa-eye me-2"></i>Live Preview</h5>
            </div>
            <div class="card-body p-0">
                <div class="slide-preview-container">
                    <div class="slide-preview-card">
                        <img id="previewImage" src="{{ $slide->image ? asset('storage/'.$slide->image) : 'https://via.placeholder.com/800x480/4f46e5/ffffff?text=Upload+Image' }}" alt="Preview">
                        <div class="slide-preview-content">
                            <h2 id="previewTitle">{{ $slide->title }}</h2>
                            <p id="previewDescription">{{ $slide->description }}</p>
                            <button id="previewButton" class="preview-btn" style="{{ $slide->button_text ? '' : 'display:none;' }}">
                                <span id="previewButtonText">{{ $slide->button_text }}</span>
                                <i class="fa-solid fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .slide-preview-container {
        background: #0a1628;
        padding: 40px 20px;
        min-height: 400px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .slide-preview-card {
        width: 100%;
        max-width: 600px;
        height: 360px;
        background: #1e293b;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 25px 60px rgba(0, 0, 0, 0.6);
        position: relative;
        transition: transform 0.3s ease;
    }

    .slide-preview-card:hover {
        transform: scale(1.02);
    }

    .slide-preview-card img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        filter: brightness(0.7);
    }

    .slide-preview-content {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 30px;
        background: linear-gradient(to top, rgba(0, 0, 0, 0.95) 0%, rgba(0, 0, 0, 0.7) 70%, transparent 100%);
        color: white;
    }

    .slide-preview-content h2 {
        font-size: 2rem;
        font-weight: 800;
        margin-bottom: 10px;
        line-height: 1.2;
    }

    .slide-preview-content p {
        font-size: 0.95rem;
        opacity: 0.9;
        margin-bottom: 15px;
        line-height: 1.5;
    }

    .preview-btn {
        display: inline-block;
        background: white;
        color: #4f46e5;
        padding: 10px 24px;
        border-radius: 50px;
        font-weight: 700;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(255, 255, 255, 0.3);
    }

    .preview-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(255, 255, 255, 0.4);
    }

    @media (max-width: 991px) {
        .slide-preview-card {
            height: 300px;
        }
        
        .slide-preview-content h2 {
            font-size: 1.5rem;
        }
        
        .slide-preview-content p {
            font-size: 0.85rem;
        }
    }
</style>

<script>
    // Live Preview Updates
    document.getElementById('slideTitle').addEventListener('input', function(e) {
        document.getElementById('previewTitle').textContent = e.target.value || 'Your Title Here';
    });

    document.getElementById('slideDescription').addEventListener('input', function(e) {
        document.getElementById('previewDescription').textContent = e.target.value || 'Your description here';
    });

    document.getElementById('slideButtonText').addEventListener('input', function(e) {
        const btn = document.getElementById('previewButton');
        const text = document.getElementById('previewButtonText');
        if (e.target.value) {
            text.textContent = e.target.value;
            btn.style.display = 'inline-block';
        } else {
            btn.style.display = 'none';
        }
    });

    document.getElementById('slideImage').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                document.getElementById('previewImage').src = event.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
</script>
@endsection
