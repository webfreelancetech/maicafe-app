@extends('layouts.admin')

@section('title', 'Products - Mai Cafe Admin')

@section('content')
<div class="page-header">
    <h1>Products</h1>
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add Product
    </a>
</div>

<div class="card">
    <table class="data-table">
        <thead>
            <tr>
                <th>Image</th>
                <th>Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $product)
            <tr>
                <td>
                    @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                    @else
                    <div style="width: 50px; height: 50px; background: #f3f4f6; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-image" style="color: #9ca3af;"></i>
                    </div>
                    @endif
                </td>
                <td>
                    {{ $product->name }}
                    @if($product->has_variants)
                    <span class="badge" style="background: #e0e7ff; color: #3730a3; font-size: 10px; margin-left: 6px;">
                        {{ $product->variants->count() }} variants
                    </span>
                    @endif
                </td>
                <td>
                    {{ $product->category->name ?? 'N/A' }}
                    @if($product->category)
                    <span class="badge" style="background: {{ $product->category->type === 'restaurant' ? '#fef3c7' : '#dbeafe' }}; color: {{ $product->category->type === 'restaurant' ? '#92400e' : '#1e40af' }}; font-size: 10px; margin-left: 4px;">
                        {{ ucfirst($product->category->type) }}
                    </span>
                    @endif
                </td>
                <td>
                    @if($product->has_variants && $product->variants->count() > 0)
                        @php
                            $minPrice = $product->variants->min('price');
                            $maxPrice = $product->variants->max('price');
                        @endphp
                        @if($minPrice == $maxPrice)
                            £{{ number_format($minPrice, 2) }}
                        @else
                            £{{ number_format($minPrice, 2) }} - £{{ number_format($maxPrice, 2) }}
                        @endif
                    @else
                        £{{ number_format($product->price, 2) }}
                    @endif
                </td>
                <td>
                    @if($product->has_variants && $product->variants->count() > 0)
                        {{ $product->variants->sum('stock_quantity') }}
                    @else
                        {{ $product->stock_quantity }}
                    @endif
                </td>
                <td>
                    <span class="badge {{ $product->is_active ? 'badge-success' : 'badge-danger' }}">
                        {{ $product->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('admin.products.edit', $product) }}" class="btn" style="padding: 6px 12px; background: #f3f4f6;">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" style="padding: 6px 12px;">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; padding: 40px; color: #6b7280;">No products found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    <div style="margin-top: 20px;">
        {{ $products->links() }}
    </div>
</div>
@endsection


