@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold mb-0">Slideshow Management</h3>
    <div class="btn-group">
        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#previewModal">
            <i class="fa-solid fa-eye me-2"></i>Preview Slideshow
        </button>
        <a href="{{ route('slides.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus me-2"></i>Add New Slide
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Image</th>
                        <th>Title</th>
                        <th>Order</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($slides as $slide)
                    <tr>
                        <td class="ps-4">
                            @if($slide->image)
                                <img src="{{ asset('storage/'.$slide->image) }}" class="rounded" style="width: 100px; height: 50px; object-fit: cover;">
                            @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 100px; height: 50px;">
                                    <i class="fa-solid fa-image text-muted"></i>
                                </div>
                            @endif
                        </td>
                        <td>
                            <div class="fw-bold">{{ $slide->title }}</div>
                            <small class="text-muted">{{ Str::limit($slide->description, 50) }}</small>
                        </td>
                        <td>{{ $slide->order }}</td>
                        <td>
                            @if($slide->is_active)
                                <span class="badge bg-success-subtle text-success px-3">Active</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger px-3">Inactive</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <div class="btn-group">
                                <a href="{{ route('slides.edit', $slide->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <form action="{{ route('slides.destroy', $slide->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fa-solid fa-eye me-2"></i>Slideshow Preview</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                @if($slides->where('is_active', true)->count() > 0)
                <div class="carousel-3d-container-preview">
                    <div class="carousel-3d-scene-preview">
                        <div class="carousel-3d-track-preview">
                            @foreach($slides->where('is_active', true) as $index => $slide)
                                <div class="carousel-3d-card-preview {{ $index == 0 ? 'active' : '' }}" data-index="{{ $index }}">
                                    <div class="card-inner-preview">
                                        @if($slide->image)
                                            <img src="{{ asset('storage/'.$slide->image) }}" alt="{{ $slide->title }}" class="card-bg-image-preview">
                                        @else
                                            <div class="card-bg-gradient-preview"></div>
                                        @endif
                                        
                                        <div class="card-content-preview">
                                            <h2 class="card-title-preview">{{ $slide->title }}</h2>
                                            <p class="card-description-preview">{{ $slide->description }}</p>
                                            @if($slide->button_text)
                                                <a href="{{ $slide->button_link ?? '#' }}" class="card-btn-preview">
                                                    {{ $slide->button_text }} <i class="fa-solid fa-arrow-right ms-2"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <button class="carousel-arrow-preview prev-preview" onclick="moveCarouselPreview(-1)">
                            <i class="fa-solid fa-chevron-left"></i>
                        </button>
                        <button class="carousel-arrow-preview next-preview" onclick="moveCarouselPreview(1)">
                            <i class="fa-solid fa-chevron-right"></i>
                        </button>
                        
                        <div class="carousel-dots-preview">
                            @foreach($slides->where('is_active', true) as $index => $slide)
                                <button class="dot-preview {{ $index == 0 ? 'active' : '' }}" onclick="jumpToSlidePreview({{ $index }})"></button>
                            @endforeach
                        </div>
                    </div>
                </div>
                @else
                <div class="text-center py-5">
                    <i class="fa-solid fa-images fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No active slides to preview</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    .carousel-3d-container-preview {
        margin: 0;
    }
    
    .carousel-3d-scene-preview {
        position: relative;
        height: 500px;
        perspective: 1500px;
        overflow: hidden;
        background: #0a1628;
        padding: 60px 0;
    }
    
    .carousel-3d-track-preview {
        position: relative;
        width: 100%;
        height: 100%;
        transform-style: preserve-3d;
    }
    
    .carousel-3d-card-preview {
        position: absolute;
        width: 700px;
        height: 380px;
        left: 50%;
        top: 50%;
        margin-left: -350px;
        margin-top: -190px;
        transition: all 0.6s cubic-bezier(0.4, 0.0, 0.2, 1);
        transform-style: preserve-3d;
        backface-visibility: hidden;
    }
    
    .card-inner-preview {
        width: 100%;
        height: 100%;
        background: #1e293b;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 25px 60px rgba(0, 0, 0, 0.6);
        position: relative;
    }
    
    .card-bg-image-preview {
        position: absolute;
        width: 100%;
        height: 100%;
        object-fit: cover;
        filter: brightness(0.7);
    }
    
    .card-bg-gradient-preview {
        position: absolute;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #4f46e5, #7c3aed, #ec4899);
    }
    
    .card-content-preview {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 30px;
        background: linear-gradient(to top, rgba(0, 0, 0, 0.95) 0%, rgba(0, 0, 0, 0.7) 70%, transparent 100%);
        color: white;
        z-index: 2;
    }
    
    .card-title-preview {
        font-size: 2rem;
        font-weight: 800;
        margin-bottom: 10px;
        line-height: 1.2;
    }
    
    .card-description-preview {
        font-size: 0.95rem;
        opacity: 0.9;
        margin-bottom: 15px;
        line-height: 1.5;
    }
    
    .card-btn-preview {
        display: inline-block;
        background: white;
        color: #4f46e5;
        padding: 10px 24px;
        border-radius: 50px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(255, 255, 255, 0.3);
    }
    
    .card-btn-preview:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(255, 255, 255, 0.4);
        color: #4338ca;
    }
    
    .carousel-3d-card-preview.active {
        transform: translateZ(0) rotateY(0deg) scale(1);
        opacity: 1;
        z-index: 10;
    }
    
    .carousel-3d-card-preview.prev {
        transform: translateZ(-200px) translateX(-400px) rotateY(35deg) scale(0.7);
        opacity: 0.5;
        z-index: 5;
    }
    
    .carousel-3d-card-preview.next {
        transform: translateZ(-200px) translateX(400px) rotateY(-35deg) scale(0.7);
        opacity: 0.5;
        z-index: 5;
    }
    
    .carousel-3d-card-preview.hidden {
        transform: translateZ(-400px) scale(0.3);
        opacity: 0;
        z-index: 1;
    }
    
    .carousel-arrow-preview {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 45px;
        height: 45px;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border: 2px solid rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        color: white;
        font-size: 1.1rem;
        cursor: pointer;
        z-index: 100;
        transition: all 0.3s ease;
    }
    
    .carousel-arrow-preview:hover {
        background: rgba(255, 255, 255, 0.2);
        border-color: white;
        transform: translateY(-50%) scale(1.1);
    }
    
    .carousel-arrow-preview.prev-preview {
        left: 30px;
    }
    
    .carousel-arrow-preview.next-preview {
        right: 30px;
    }
    
    .carousel-dots-preview {
        position: absolute;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        gap: 8px;
        z-index: 100;
    }
    
    .dot-preview {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.4);
        border: 2px solid rgba(255, 255, 255, 0.6);
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .dot-preview.active {
        background: white;
        transform: scale(1.3);
        box-shadow: 0 0 15px rgba(255, 255, 255, 0.8);
    }
    
    .dot-preview:hover {
        background: rgba(255, 255, 255, 0.7);
    }
</style>

<script>
    let currentIndexPreview = 0;
    const totalCardsPreview = {{ $slides->where('is_active', true)->count() }};
    
    function updateCarouselPreview() {
        const cards = document.querySelectorAll('.carousel-3d-card-preview');
        const dots = document.querySelectorAll('.dot-preview');
        
        cards.forEach((card, index) => {
            card.classList.remove('active', 'prev', 'next', 'hidden');
            if (dots[index]) dots[index].classList.remove('active');
            
            if (index === currentIndexPreview) {
                card.classList.add('active');
                if (dots[index]) dots[index].classList.add('active');
            } else if (index === (currentIndexPreview - 1 + totalCardsPreview) % totalCardsPreview) {
                card.classList.add('prev');
            } else if (index === (currentIndexPreview + 1) % totalCardsPreview) {
                card.classList.add('next');
            } else {
                card.classList.add('hidden');
            }
        });
    }
    
    function moveCarouselPreview(direction) {
        currentIndexPreview = (currentIndexPreview + direction + totalCardsPreview) % totalCardsPreview;
        updateCarouselPreview();
    }
    
    function jumpToSlidePreview(index) {
        currentIndexPreview = index;
        updateCarouselPreview();
    }
    
    // Initialize when modal opens
    document.getElementById('previewModal')?.addEventListener('shown.bs.modal', function () {
        currentIndexPreview = 0;
        updateCarouselPreview();
    });
</script>
@endsection
