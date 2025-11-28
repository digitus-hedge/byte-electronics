
<!-- resources/views/products/index.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Product List</h1>
    <a href="" class="btn btn-success mb-3">Add New Product</a> 
     {{-- route('admin.products.create')  --}}
    <div class="row">
        {{-- @foreach($products as $product) --}}
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-header">
                        {{-- {{ $product->name }} --}}
                    </div>
                    <div class="card-body">
                        {{-- <p><strong>Description:</strong> {{ $product->description }}</p> --}}
                        {{-- <p><strong>Price:</strong> ${{ $product->price }}</p>
                        @if($product->images->isNotEmpty())
                            <div class="mb-3">
                                <img src="{{ asset('storage/' . $product->images->first()->path) }}" alt="Image" class="img-fluid">
                            </div>
                        @endif
                        <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-primary">Edit</a> --}}
                    </div>
                </div>
            </div>
        {{-- @endforeach --}}
    </div>
</div>
@endsection
