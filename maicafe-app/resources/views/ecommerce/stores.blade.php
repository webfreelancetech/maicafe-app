@extends('layouts.ecommerce')

@section('title', 'Stores - Mai Cafe')

@section('content')
<div class="container" style="padding: 40px 20px;">
    <h1 style="margin-bottom: 32px; font-size: 32px;">Our Stores</h1>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 24px;">
        @forelse($stores as $store)
        <div style="background: #fff; border-radius: 12px; padding: 24px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h3 style="margin-bottom: 12px;">{{ $store->name }}</h3>
            <p style="color: #6b7280; margin-bottom: 8px;">{{ $store->address }}</p>
            @if($store->phone)
            <p style="color: #6b7280; margin-bottom: 8px;">Phone: {{ $store->phone }}</p>
            @endif
        </div>
        @empty
        <p style="text-align: center; grid-column: 1 / -1; color: #6b7280;">No stores available</p>
        @endforelse
    </div>
</div>
@endsection


