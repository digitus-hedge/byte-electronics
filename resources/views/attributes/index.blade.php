<!-- resources/views/product_attributes/index.blade.php -->

@extends('layouts.app')

@section('content')
<div class="container">

    <section class="content-header">
        <div class="container-fluid">
            {{-- <div class="row mb-2"> --}}
                {{-- <div class="col-sm-6">
                    <h1>Categories</h1>
                </div> --}}
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active"><a href="{{ route('admin.attributes.index') }}">Attributes</a></li>
                        {{-- <li class="breadcrumb-item active"><a href="#">Add New Category</a></li> --}}
                    </ol>
                </div>
            {{-- </div> --}}
        </div><!-- /.container-fluid -->
    </section>

    <!-- Search and Filter Form -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Filter Attributes</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.attributes.index') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <input
                            type="text"
                            name="name"
                            value="{{ request('name') }}"
                            class="form-control"
                            placeholder="Search by name">
                    </div>
                    <div class="col-md-4">
                        <select name="trashed" class="form-control">
                            <option value="with" {{ request('trashed') === 'with' ? 'selected' : '' }}>ALL</option>
                            <option value="only" {{ request('trashed') === 'only' ? 'selected' : '' }}>Deleted</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                </div>
            </form>

        </div>
    </div>

    <div class="card">
        <div class="card-body d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Manage Attributes</h5>
            <a href="{{ route('admin.attributes.create') }}" class="btn btn-primary">Add New</a>
        </div>
    </div>



    <!-- Product Attributes List -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Attribute Name</th>
                <th>Description</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($Attributes as $attribute)
                <tr>
                    <!-- Serial number with pagination -->
                    <td>{{ $loop->iteration + ($Attributes->currentPage() - 1) * $Attributes->perPage() }}</td>
                    <td>{{ $attribute->name }}</td>
                    <td>{{ $attribute->description }}</td>
                    <td>{{ $attribute->status ? 'Active' : 'Inactive' }}</td>
                    <td>
                        @if ($attribute->trashed())
                            <form action="{{ route('admin.attributes.restore', $attribute->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm">Restore</button>
                            </form>
                            <form action="{{ route('admin.attributes.forceDelete', $attribute->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Permanently Delete</button>
                            </form>
                        @else
                            <a href="{{ route('admin.attributes.edit', $attribute->id) }}" class="btn btn-warning btn-sm">Edit</a>
                            <form action="{{ route('admin.attributes.destroy', $attribute->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">No Data Found!</td>
                </tr>
            @endforelse
        </tbody>
    </table>



    <!-- Pagination Links -->
    <div class="pagination">
        {{ $Attributes->links() }}
    </div>
</div>
@endsection
