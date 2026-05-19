@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <h2 class="fw-bold mb-1">Categories</h2>
        <p class="text-muted mb-0">Organize your products into collections.</p>
    </div>
    <a href="{{ route('categories.create') }}" class="btn btn-primary rounded-pill px-4">
        <i class="fa-solid fa-plus me-2"></i>Add Category
    </a>
</div>

<div class="card border-0 shadow-sm overflow-hidden">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase fw-bold">
                    <tr>
                        <th class="px-4 py-3 border-0">Image</th>
                        <th class="py-3 border-0">Category Name</th>
                        <th class="py-3 border-0">Total Products</th>
                        <th class="px-4 py-3 border-0 text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $category)
                    <tr>
                        <td class="px-4 py-3">
                            <div class="rounded-3 overflow-hidden shadow-sm border" style="width: 60px; height: 60px;">
                                @if($category->image)
                                    <img src="{{ asset('storage/'.$category->image) }}" class="w-100 h-100" style="object-fit: cover;">
                                @else
                                    <div class="bg-light w-100 h-100 d-flex align-items-center justify-content-center">
                                        <i class="fa-solid fa-layer-group text-muted small"></i>
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td class="py-3 fw-bold text-dark">{{ $category->name }}</td>
                        <td class="py-3 text-muted">{{ $category->phones_count ?? 0 }} Items</td>
                        <td class="px-4 py-3 text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('categories.edit', $category) }}" class="btn btn-light btn-sm rounded-pill px-3 border shadow-sm">
                                    <i class="fa-solid fa-pen-to-square text-warning me-1"></i>Edit
                                </a>
                                <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-light btn-sm rounded-pill px-3 border shadow-sm" onclick="return confirm('Delete this category?')">
                                        <i class="fa-solid fa-trash text-danger me-1"></i>Delete
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

@endsection
