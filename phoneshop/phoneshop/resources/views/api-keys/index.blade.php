@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2><i class="fa-solid fa-key me-2"></i>API Keys Management</h2>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createApiKeyModal">
                    <i class="fa-solid fa-plus me-2"></i>Create New API Key
                </button>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fa-solid fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('new_key'))
        <div class="alert alert-warning alert-dismissible fade show">
            <h5><i class="fa-solid fa-exclamation-triangle me-2"></i>Important: Save Your API Key</h5>
            <p class="mb-2">This is the only time you'll see this key. Copy it now:</p>
            <div class="input-group mb-3">
                <input type="text" class="form-control font-monospace" id="newApiKey" value="{{ session('new_key') }}" readonly>
                <button class="btn btn-outline-secondary" type="button" onclick="copyApiKey()">
                    <i class="fa-solid fa-copy"></i> Copy
                </button>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            @if($apiKeys->count() > 0)
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Key Preview</th>
                                <th>Permissions</th>
                                <th>Last Used</th>
                                <th>Expires</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($apiKeys as $key)
                                <tr>
                                    <td>
                                        <strong>{{ $key->name }}</strong>
                                        <br>
                                        <small class="text-muted">Created {{ $key->created_at->diffForHumans() }}</small>
                                    </td>
                                    <td>
                                        <code class="small user-select-all">sk_••••••••••••{{ substr($key->key, -8) }}</code>
                                    </td>
                                    <td>
                                        @if($key->permissions)
                                            @foreach($key->permissions as $permission)
                                                <span class="badge bg-info">{{ $permission }}</span>
                                            @endforeach
                                        @else
                                            <span class="badge bg-secondary">None</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($key->last_used_at)
                                            <small>{{ $key->last_used_at->diffForHumans() }}</small>
                                        @else
                                            <small class="text-muted">Never</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($key->expires_at)
                                            <small class="{{ $key->expires_at->isPast() ? 'text-danger' : '' }}">
                                                {{ $key->expires_at->format('M d, Y') }}
                                            </small>
                                        @else
                                            <small class="text-muted">Never</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($key->isValid())
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <form action="{{ route('api-keys.toggle', $key) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-secondary" title="Toggle Status">
                                                <i class="fa-solid fa-{{ $key->is_active ? 'pause' : 'play' }}"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('api-keys.destroy', $key) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this API key? This action cannot be undone.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fa-solid fa-key fa-4x text-muted mb-3"></i>
                    <h4>No API Keys Yet</h4>
                    <p class="text-muted">Create your first API key to get started</p>
                </div>
            @endif
        </div>
    </div>

    <div class="card shadow-sm mt-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fa-solid fa-book me-2"></i>How to Use API Keys</h5>
        </div>
        <div class="card-body">
            <h6>1. Include API Key in Request Header:</h6>
            <pre class="bg-light p-3 rounded"><code>X-API-Key: your_api_key_here</code></pre>

            <h6 class="mt-3">2. Or as Query Parameter:</h6>
            <pre class="bg-light p-3 rounded"><code>GET /api/products?api_key=your_api_key_here</code></pre>

            <h6 class="mt-3">3. Example with cURL:</h6>
            <pre class="bg-light p-3 rounded"><code>curl -H "X-API-Key: your_api_key_here" {{ url('/api/products') }}</code></pre>
        </div>
    </div>
</div>

<!-- Create API Key Modal -->
<div class="modal fade" id="createApiKeyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('api-keys.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fa-solid fa-plus me-2"></i>Create New API Key</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required placeholder="e.g., Mobile App API">
                        <small class="text-muted">A descriptive name to identify this API key</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Permissions</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="permissions[]" value="*" id="perm_all" checked>
                            <label class="form-check-label" for="perm_all">
                                <strong>All Permissions</strong>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="permissions[]" value="products.read" id="perm_products_read">
                            <label class="form-check-label" for="perm_products_read">
                                Read Products
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="permissions[]" value="orders.read" id="perm_orders_read">
                            <label class="form-check-label" for="perm_orders_read">
                                Read Orders
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="permissions[]" value="orders.create" id="perm_orders_create">
                            <label class="form-check-label" for="perm_orders_create">
                                Create Orders
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="expires_at" class="form-label">Expiration Date (Optional)</label>
                        <input type="date" class="form-control" id="expires_at" name="expires_at" min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                        <small class="text-muted">Leave empty for no expiration</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-check me-2"></i>Create API Key
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function copyApiKey() {
    const input = document.getElementById('newApiKey');
    input.select();
    document.execCommand('copy');
    
    const btn = event.target.closest('button');
    const originalHTML = btn.innerHTML;
    btn.innerHTML = '<i class="fa-solid fa-check"></i> Copied!';
    btn.classList.remove('btn-outline-secondary');
    btn.classList.add('btn-success');
    
    setTimeout(() => {
        btn.innerHTML = originalHTML;
        btn.classList.remove('btn-success');
        btn.classList.add('btn-outline-secondary');
    }, 2000);
}
</script>
@endsection


<style>
    /* Prevent accidental clicks on table rows */
    .table tbody tr {
        cursor: default;
    }
    
    .table tbody tr:hover {
        background-color: transparent;
    }
    
    /* Make buttons more prominent */
    .table .btn {
        cursor: pointer;
    }
</style>
