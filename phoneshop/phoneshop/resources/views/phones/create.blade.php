@extends('layouts.app')

@section('content')

<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Add New Phone</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('phones.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Phone Name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Category</label>
                    <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
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
                        <input type="number" name="price" step="0.01" class="form-control @error('price') is-invalid @enderror" value="{{ old('price') }}" required>
                    </div>
                    @error('price')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Quantity</label>
                    <input type="number" name="qty" class="form-control @error('qty') is-invalid @enderror" value="{{ old('qty') }}" required>
                    @error('qty')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Phone Image</label>
                <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*" id="main-image-input">
                @error('image')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted">Main product image</small>
                <div id="main-image-preview" class="mt-2"></div>
            </div>

            <div class="mb-3">
                <label class="form-label">Detail Images (Multiple)</label>
                <input type="file" name="detail_images[]" class="form-control @error('detail_images.*') is-invalid @enderror" accept="image/*" multiple id="detail-images-input">
                @error('detail_images.*')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted">You can select multiple images for product gallery (Max 2MB each)</small>
                <div id="preview-container" class="mt-3 d-flex flex-wrap gap-2"></div>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4" placeholder="Enter phone description...">{{ old('description') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Save Phone</button>
            <a href="{{ route('phones.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Preview main image before upload
    document.getElementById('main-image-input').addEventListener('change', function(e) {
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
                    <span class="badge bg-success position-absolute top-0 start-0 m-1">Preview</span>
                `;
                previewContainer.appendChild(div);
            };
            reader.readAsDataURL(file);
        }
    });

    // Preview detail images before upload
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
                        <span class="badge bg-primary position-absolute top-0 start-0 m-1">${index + 1}</span>
                    `;
                    previewContainer.appendChild(div);
                };
                reader.readAsDataURL(file);
            }
        });
    });
</script>
@endpush
