@extends('layouts.app')

@section('content')

<div class="card shadow-sm">
    <div class="card-header bg-warning text-dark">
        <h5 class="mb-0">Edit Phone</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('phones.update', $phone) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Phone Name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $phone->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Category</label>
                    <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ (old('category_id') ?? $phone->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Price</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" name="price" step="0.01" class="form-control @error('price') is-invalid @enderror" value="{{ old('price', $phone->price) }}" required>
                    </div>
                    @error('price')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Quantity</label>
                    <input type="number" name="qty" class="form-control @error('qty') is-invalid @enderror" value="{{ old('qty', $phone->qty) }}" required>
                    @error('qty')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Phone Image</label>
                @if($phone->image)
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $phone->image) }}" width="100" class="rounded border">
                    </div>
                @endif
                <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*" id="main-image-input">
                <small class="text-muted">Leave empty to keep current image</small>
                @error('image')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div id="main-image-preview" class="mt-2"></div>
            </div>

            <div class="mb-3">
                <label class="form-label">Detail Images (Gallery)</label>
                
                @if($phone->detail_images && count($phone->detail_images) > 0)
                    <div class="mb-3">
                        <label class="form-label small text-muted">Current Detail Images:</label>
                        <div class="d-flex flex-wrap gap-2" id="current-images">
                            @foreach($phone->detail_images as $index => $detailImage)
                                <div class="position-relative detail-image-item" data-image="{{ $detailImage }}">
                                    <img src="{{ asset('storage/' . $detailImage) }}" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 remove-image-btn" style="padding: 2px 6px; font-size: 0.7rem;">
                                        <i class="fa-solid fa-times"></i>
                                    </button>
                                    <span class="badge bg-primary position-absolute bottom-0 start-0 m-1">{{ $index + 1 }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <input type="file" name="detail_images[]" class="form-control @error('detail_images.*') is-invalid @enderror" accept="image/*" multiple id="detail-images-input">
                @error('detail_images.*')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted">Add more detail images (Max 2MB each)</small>
                <div id="preview-container" class="mt-3 d-flex flex-wrap gap-2"></div>
                
                <!-- Hidden inputs for images to remove -->
                <div id="remove-images-container"></div>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4" placeholder="Enter phone description...">{{ old('description', $phone->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-warning">Update Phone</button>
            <a href="{{ route('phones.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Preview main image before upload
    const mainImageInput = document.getElementById('main-image-input');
    if (mainImageInput) {
        mainImageInput.addEventListener('change', function(e) {
            const previewContainer = document.getElementById('main-image-preview');
            previewContainer.innerHTML = '';
            
            const file = e.target.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'position-relative d-inline-block';
                    div.innerHTML = `
                        <img src="${e.target.result}" class="img-thumbnail" style="width: 150px; height: 150px; object-fit: cover;">
                        <span class="badge bg-success position-absolute top-0 start-0 m-1">New Preview</span>
                    `;
                    previewContainer.appendChild(div);
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Handle removing existing images
    document.querySelectorAll('.remove-image-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const imageItem = this.closest('.detail-image-item');
            const imagePath = imageItem.dataset.image;
            
            // Add hidden input to mark for removal
            const removeContainer = document.getElementById('remove-images-container');
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'remove_detail_images[]';
            input.value = imagePath;
            removeContainer.appendChild(input);
            
            // Remove from display
            imageItem.remove();
        });
    });

    // Preview new detail images before upload
    document.getElementById('detail-images-input').addEventListener('change', function(e) {
        const previewContainer = document.getElementById('preview-container');
        previewContainer.innerHTML = '';
        
        const files = Array.from(e.target.files);
        files.forEach((file, index) => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'position-relative';
                    div.innerHTML = `
                        <img src="${e.target.result}" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                        <span class="badge bg-success position-absolute top-0 start-0 m-1">New ${index + 1}</span>
                    `;
                    previewContainer.appendChild(div);
                };
                reader.readAsDataURL(file);
            }
        });
    });
</script>
@endpush
