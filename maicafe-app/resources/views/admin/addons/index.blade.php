@extends('layouts.admin')

@section('title', 'Add-ons & Extras - Mai Cafe Admin')

@section('content')
<div class="page-header">
    <h1>Add-ons & Extras</h1>
    <a href="{{ route('admin.addons.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Create Addon Group
    </a>
</div>

<div class="card">
    <table class="data-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Type</th>
                <th>Options</th>
                <th>Required</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($addonGroups as $group)
            <tr>
                <td>
                    <strong>{{ $group->name }}</strong>
                    @if($group->description)
                    <br><small style="color: #6b7280;">{{ $group->description }}</small>
                    @endif
                </td>
                <td>
                    <span class="badge" style="background: {{ $group->selection_type == 'single' ? '#fef3c7' : '#dbeafe' }}; color: {{ $group->selection_type == 'single' ? '#92400e' : '#1e40af' }};">
                        {{ $group->selection_type == 'single' ? 'Single Select' : 'Multiple Select' }}
                    </span>
                </td>
                <td>
                    <span class="badge" style="background: #e0e7ff; color: #3730a3;">
                        {{ $group->addons_count }} options
                    </span>
                </td>
                <td>
                    @if($group->is_required)
                    <span class="badge badge-warning">Required</span>
                    @else
                    <span style="color: #6b7280;">Optional</span>
                    @endif
                </td>
                <td>
                    <span class="badge {{ $group->is_active ? 'badge-success' : 'badge-danger' }}">
                        {{ $group->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('admin.addons.edit', $group) }}" class="btn" style="padding: 6px 12px; background: #f3f4f6;">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('admin.addons.destroy', $group) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure? This will remove this addon group from all products.');">
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
                <td colspan="6" style="text-align: center; padding: 40px; color: #6b7280;">
                    No addon groups found. Create your first addon group to get started.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    <div style="margin-top: 20px;">
        {{ $addonGroups->links() }}
    </div>
</div>
@endsection
