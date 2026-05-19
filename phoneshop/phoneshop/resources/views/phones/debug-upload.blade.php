@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0">Debug Upload Test</h5>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <strong>Upload Configuration:</strong><br>
            Storage Path: {{ storage_path('app/public/phones') }}<br>
            Public Path: {{ public_path('storage/phones') }}<br>
            APP_URL: {{ config('app.url') }}<br>
            Max Upload: {{ ini_get('upload_max_filesize') }}<br>
            Post Max: {{ ini_get('post_max_size') }}
        </div>

        @if(session('debug'))
            <div class="alert alert-warning">
                <strong>Debug Info:</strong>
                <pre>{{ print_r(session('debug'), true) }}</pre>
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('phones.debug-upload-test') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label class="form-label">Test Image Upload</label>
                <input type="file" name="test_image" class="form-control" accept="image/*" required id="test-image">
                <small class="text-muted">Select an image to test upload</small>
                <div id="preview" class="mt-2"></div>
            </div>
            <button type="submit" class="btn btn-primary">Test Upload</button>
        </form>
    </div>
</div>

<script>
document.getElementById('test-image').addEventListener('change', function(e) {
    const preview = document.getElementById('preview');
    preview.innerHTML = '';
    
    const file = e.target.files[0];
    if (file) {
        console.log('File selected:', file.name, file.size, file.type);
        
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `
                <div class="mt-2">
                    <img src="${e.target.result}" class="img-thumbnail" style="max-width: 200px;">
                    <p class="mt-2 mb-0">
                        <strong>File:</strong> ${file.name}<br>
                        <strong>Size:</strong> ${(file.size / 1024).toFixed(2)} KB<br>
                        <strong>Type:</strong> ${file.type}
                    </p>
                </div>
            `;
        };
        reader.readAsDataURL(file);
    }
});
</script>
@endsection
