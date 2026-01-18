@extends('layouts.admin')

@section('title', 'Banners - Mai Cafe Admin')

@section('content')
<div class="page-header">
    <h1>Banners</h1>
    <a href="{{ route('admin.banners.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add Banner
    </a>
</div>

<!-- Drag & Drop Instructions -->
<div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 12px 16px; margin-bottom: 20px; display: flex; align-items: center; gap: 12px;">
    <i class="fas fa-info-circle" style="color: #3b82f6; font-size: 18px;"></i>
    <span style="color: #1e40af; font-size: 14px;">
        <strong>Tip:</strong> Drag and drop rows to reorder banners. Changes are saved automatically.
    </span>
    <div id="saveStatus" style="margin-left: auto; display: none; align-items: center; gap: 8px;">
        <span id="savingIndicator" style="color: #f59e0b; display: none;">
            <i class="fas fa-spinner fa-spin"></i> Saving...
        </span>
        <span id="savedIndicator" style="color: #10b981; display: none;">
            <i class="fas fa-check-circle"></i> Saved!
        </span>
    </div>
</div>

<div class="card">
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 50px;"></th>
                <th style="width: 60px;">Order</th>
                <th>Image</th>
                <th>Title</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="bannerSortable">
            @forelse($banners as $banner)
            <tr data-id="{{ $banner->id }}" class="sortable-row">
                <td class="drag-handle" style="cursor: grab; text-align: center;">
                    <i class="fas fa-grip-vertical" style="color: #9ca3af; font-size: 16px;"></i>
                </td>
                <td>
                    <span class="sort-order-badge" style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; background: #f3f4f6; border-radius: 6px; font-weight: 600; color: #374151;">
                        {{ $banner->sort_order }}
                    </span>
                </td>
                <td>
                    @if($banner->image)
                    <img src="{{ asset('storage/' . $banner->image) }}" alt="{{ $banner->title }}" style="width: 100px; height: 60px; object-fit: cover; border-radius: 4px;">
                    @else
                    <div style="width: 100px; height: 60px; background: #f3f4f6; border-radius: 4px;"></div>
                    @endif
                </td>
                <td>
                    <strong>{{ $banner->title }}</strong>
                    @if($banner->subtitle)
                    <br><small style="color: #6b7280;">{{ $banner->subtitle }}</small>
                    @endif
                </td>
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
                <td colspan="6" style="text-align: center; padding: 40px; color: #6b7280;">No banners found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    <div style="margin-top: 20px;">
        {{ $banners->links() }}
    </div>
</div>

<style>
.sortable-row {
    transition: background-color 0.2s;
}
.sortable-row:hover .drag-handle {
    color: #3b82f6 !important;
}
.sortable-row.sortable-ghost {
    opacity: 0.4;
    background: #eff6ff;
}
.sortable-row.sortable-chosen {
    background: #f0fdf4;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.drag-handle:active {
    cursor: grabbing;
}
</style>

<!-- SortableJS Library -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tbody = document.getElementById('bannerSortable');
    const saveStatus = document.getElementById('saveStatus');
    const savingIndicator = document.getElementById('savingIndicator');
    const savedIndicator = document.getElementById('savedIndicator');
    
    if (!tbody || tbody.children.length === 0) return;
    
    // Initialize SortableJS
    const sortable = new Sortable(tbody, {
        handle: '.drag-handle',
        animation: 150,
        ghostClass: 'sortable-ghost',
        chosenClass: 'sortable-chosen',
        onEnd: function(evt) {
            updateOrder();
        }
    });
    
    function updateOrder() {
        // Show saving indicator
        saveStatus.style.display = 'flex';
        savingIndicator.style.display = 'inline';
        savedIndicator.style.display = 'none';
        
        // Collect new order
        const rows = tbody.querySelectorAll('.sortable-row');
        const banners = [];
        
        rows.forEach((row, index) => {
            const id = row.dataset.id;
            banners.push({
                id: parseInt(id),
                sort_order: index
            });
            
            // Update the displayed sort order badge
            const badge = row.querySelector('.sort-order-badge');
            if (badge) {
                badge.textContent = index;
            }
        });
        
        // Send AJAX request
        fetch('{{ route('admin.banners.updateOrder') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ banners: banners })
        })
        .then(response => response.json())
        .then(data => {
            // Show saved indicator
            savingIndicator.style.display = 'none';
            savedIndicator.style.display = 'inline';
            
            // Hide after 2 seconds
            setTimeout(() => {
                saveStatus.style.display = 'none';
            }, 2000);
        })
        .catch(error => {
            console.error('Error:', error);
            savingIndicator.style.display = 'none';
            alert('Failed to update order. Please try again.');
        });
    }
});
</script>
@endsection


