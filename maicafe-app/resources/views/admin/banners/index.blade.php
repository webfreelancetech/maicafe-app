@extends('layouts.admin')

@section('title', 'Banners - Mai Cafe Admin')

@section('content')
<div class="page-header">
    <h1>Banners</h1>
    <a href="{{ route('admin.banners.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add Banner
    </a>
</div>

<div class="card">
    <table class="data-table">
        <thead>
            <tr>
                <th>Image</th>
                <th>Title</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($banners as $banner)
            <tr>
                <td>
                    @if($banner->image)
                    <img src="{{ asset('storage/' . $banner->image) }}" alt="{{ $banner->title }}" style="width: 100px; height: 60px; object-fit: cover; border-radius: 4px;">
                    @else
                    <div style="width: 100px; height: 60px; background: #f3f4f6; border-radius: 4px;"></div>
                    @endif
                </td>
                <td>{{ $banner->title }}</td>
                <td>
                    <span class="badge {{ $banner->is_active ? 'badge-success' : 'badge-danger' }}">
                        {{ $banner->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('admin.banners.edit', $banner) }}" class="btn" style="padding: 6px 12px; background: #f3f4f6;">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('admin.banners.destroy', $banner) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?');">
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
                <td colspan="4" style="text-align: center; padding: 40px; color: #6b7280;">No banners found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection


