@extends('layouts.admin')

@section('title', 'Categories - Mai Cafe Admin')

@section('content')
<div class="page-header">
    <h1>Categories</h1>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add Category
    </a>
</div>

<div class="card">
    <table class="data-table">
        <thead>
            <tr>
                <th>Image</th>
                <th>Name</th>
                <th>Type</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($categories as $category)
            <tr>
                <td>
                    @if($category->image)
                    <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                    @else
                    <div style="width: 50px; height: 50px; background: #f3f4f6; border-radius: 4px;"></div>
                    @endif
                </td>
                <td>{{ $category->name }}</td>
                <td>
                    <span class="badge" style="background: {{ $category->type === 'restaurant' ? '#fef3c7' : '#dbeafe' }}; color: {{ $category->type === 'restaurant' ? '#92400e' : '#1e40af' }};">
                        {{ ucfirst($category->type) }}
                    </span>
                </td>
                <td>
                    <span class="badge {{ $category->is_active ? 'badge-success' : 'badge-danger' }}">
                        {{ $category->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('admin.categories.edit', $category) }}" class="btn" style="padding: 6px 12px; background: #f3f4f6;">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?');">
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
                <td colspan="5" style="text-align: center; padding: 40px; color: #6b7280;">No categories found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    <div style="margin-top: 20px;">
        {{ $categories->links() }}
    </div>
</div>
@endsection


