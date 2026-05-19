@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <h2 class="fw-bold mb-1">Product Inventory</h2>
        <p class="text-muted mb-0">Manage your products and stock levels.</p>
    </div>
    @permission('create_phones')
        <a href="{{ route('phones.create') }}" class="btn btn-primary rounded-pill px-4">
            <i class="fa-solid fa-plus me-2"></i>Add Product
        </a>
    @endpermission
</div>

<!-- Search Bar -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <div class="position-relative">
                    <i class="fa-solid fa-search position-absolute" style="left: 15px; top: 50%; transform: translateY(-50%); color: #6c757d;"></i>
                    <input type="text" id="searchInput" class="form-control ps-5 rounded-pill" placeholder="Search products by name, category, or price..." style="border: 2px solid #e9ecef;">
                </div>
            </div>
            <div class="col-md-3">
                <select id="categoryFilter" class="form-select rounded-pill" style="border: 2px solid #e9ecef;">
                    <option value="">All Categories</option>
                    @foreach($phones->pluck('category')->unique() as $category)
                        <option value="{{ $category->name }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select id="stockFilter" class="form-select rounded-pill" style="border: 2px solid #e9ecef;">
                    <option value="">All Stock Status</option>
                    <option value="in-stock">In Stock</option>
                    <option value="low-stock">Low Stock</option>
                    <option value="out-of-stock">Out of Stock</option>
                </select>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm overflow-hidden">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase fw-bold">
                    <tr>
                        <th class="px-4 py-3 border-0">Device</th>
                        <th class="py-3 border-0">Category</th>
                        <th class="py-3 border-0">Price</th>
                        <th class="py-3 border-0">Stock Status</th>
                        <th class="px-4 py-3 border-0 text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($phones as $phone)
                    <tr class="product-row" 
                        data-name="{{ strtolower($phone->name) }}" 
                        data-category="{{ strtolower($phone->category->name) }}" 
                        data-price="{{ $phone->price }}"
                        data-stock="{{ $phone->qty > 10 ? 'in-stock' : ($phone->qty > 0 ? 'low-stock' : 'out-of-stock') }}">
                        <td class="px-4 py-3">
                            <div class="d-flex align-items-center">
                                <div class="rounded-3 overflow-hidden shadow-sm border me-3" style="width: 50px; height: 50px;">
                                    @if($phone->image && file_exists(public_path('storage/'.$phone->image)))
                                        <img src="{{ asset('storage/'.$phone->image) }}" class="w-100 h-100" style="object-fit: cover;" alt="{{ $phone->name }}">
                                    @elseif($phone->image)
                                        <img src="{{ asset('storage/'.$phone->image) }}" class="w-100 h-100" style="object-fit: cover;" alt="{{ $phone->name }}" onerror="this.parentElement.innerHTML='<div class=\'bg-light w-100 h-100 d-flex align-items-center justify-content-center text-muted\'><i class=\'fa-solid fa-mobile-screen small\'></i></div>'">
                                    @else
                                        <div class="bg-light w-100 h-100 d-flex align-items-center justify-content-center text-muted">
                                            <i class="fa-solid fa-mobile-screen small"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="fw-bold text-dark">{{ $phone->name }}</div>
                            </div>
                        </td>
                        <td class="py-3">
                            <span class="badge bg-light text-muted border fw-normal px-2 py-1">
                                {{ $phone->category->name }}
                            </span>
                        </td>
                        <td class="py-3 fw-bold text-dark">${{ number_format($phone->price, 2) }}</td>
                        <td class="py-3">
                            @if($phone->qty > 10)
                                <span class="badge rounded-pill bg-success bg-opacity-10 text-success px-3">{{ $phone->qty }} In Stock</span>
                            @elseif($phone->qty > 0)
                                <span class="badge rounded-pill bg-warning bg-opacity-10 text-warning px-3">{{ $phone->qty }} Low Stock</span>
                            @else
                                <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger px-3">Out of Stock</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-end">
                            <div class="d-flex justify-content-end gap-2">
                                @permission('edit_phones')
                                    <a href="{{ route('phones.edit', $phone) }}" class="btn btn-light btn-sm rounded-pill px-3 border shadow-sm">
                                        <i class="fa-solid fa-pen-to-square text-warning me-1"></i>Edit
                                    </a>
                                @endpermission
                                @permission('delete_phones')
                                    <form action="{{ route('phones.destroy', $phone) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-light btn-sm rounded-pill px-3 border shadow-sm" onclick="return confirm('Delete this phone?')">
                                            <i class="fa-solid fa-trash text-danger me-1"></i>Delete
                                        </button>
                                    </form>
                                @endpermission
                                @if(!auth()->user()->hasPermission('edit_phones') && !auth()->user()->hasPermission('delete_phones'))
                                    <span class="badge bg-secondary">View Only</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // Live search and filter functionality
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const stockFilter = document.getElementById('stockFilter');
    const productRows = document.querySelectorAll('.product-row');
    
    function filterProducts() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedCategory = categoryFilter.value.toLowerCase();
        const selectedStock = stockFilter.value;
        
        let visibleCount = 0;
        
        productRows.forEach(row => {
            const name = row.dataset.name;
            const category = row.dataset.category;
            const price = row.dataset.price;
            const stock = row.dataset.stock;
            
            // Check if row matches all filters
            const matchesSearch = name.includes(searchTerm) || 
                                category.includes(searchTerm) || 
                                price.includes(searchTerm);
            const matchesCategory = !selectedCategory || category === selectedCategory;
            const matchesStock = !selectedStock || stock === selectedStock;
            
            if (matchesSearch && matchesCategory && matchesStock) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        // Show "no results" message if needed
        showNoResultsMessage(visibleCount);
    }
    
    function showNoResultsMessage(count) {
        let noResultsRow = document.getElementById('noResultsRow');
        
        if (count === 0) {
            if (!noResultsRow) {
                const tbody = document.querySelector('tbody');
                noResultsRow = document.createElement('tr');
                noResultsRow.id = 'noResultsRow';
                noResultsRow.innerHTML = `
                    <td colspan="5" class="text-center py-5">
                        <i class="fa-solid fa-search fa-3x text-muted mb-3"></i>
                        <p class="text-muted mb-0">No products found matching your search criteria.</p>
                    </td>
                `;
                tbody.appendChild(noResultsRow);
            }
        } else {
            if (noResultsRow) {
                noResultsRow.remove();
            }
        }
    }
    
    // Add event listeners
    searchInput.addEventListener('input', filterProducts);
    categoryFilter.addEventListener('change', filterProducts);
    stockFilter.addEventListener('change', filterProducts);
    
    // Add search icon animation
    searchInput.addEventListener('focus', function() {
        this.style.borderColor = '#0d6efd';
    });
    
    searchInput.addEventListener('blur', function() {
        this.style.borderColor = '#e9ecef';
    });
</script>

@endsection
