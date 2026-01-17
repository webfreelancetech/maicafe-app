@extends('layouts.admin')

@section('title', 'Stores - Mai Cafe Admin')

@section('content')
<div class="page-header">
    <h1>Stores</h1>
</div>

<div class="card">
    <table class="data-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Address</th>
                <th>Phone</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($stores as $store)
            <tr>
                <td>{{ $store->name }}</td>
                <td>{{ $store->address }}</td>
                <td>{{ $store->phone ?? 'N/A' }}</td>
                <td>
                    <span class="badge {{ $store->is_active ? 'badge-success' : 'badge-danger' }}">
                        {{ $store->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align: center; padding: 40px; color: #6b7280;">No stores found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection


