@extends('layouts.admin')

@section('title', 'Customers - Mai Cafe Admin')

@section('content')
<div class="page-header">
    <h1>Customers</h1>
</div>

<div class="card">
    <table class="data-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Joined</th>
            </tr>
        </thead>
        <tbody>
            @forelse($customers as $customer)
            <tr>
                <td>{{ $customer->name }}</td>
                <td>{{ $customer->email }}</td>
                <td>{{ $customer->phone ?? 'N/A' }}</td>
                <td>{{ $customer->created_at->format('M d, Y') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align: center; padding: 40px; color: #6b7280;">No customers found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection


