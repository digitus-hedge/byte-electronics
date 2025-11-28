@extends('layouts.app')

@section('content')
    <div class="container">
        <!-- Breadcrumbs -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Products</li>
            </ol>
        </nav>

        <!-- Product Card -->
        <div class="card shadow" style="--bs-card-spacer-y: 0rem !important;">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <span>List Of Products</span>
                
                <div class="d-flex align-items-center gap-2 ms-auto">
                 

                  

                  {{-- serach bar --}}
                  <div class="">
                    <form action="{{ route('admin.productManage.index') }}" method="get">
                        @csrf
                        <div class="input-group input-group-sm" style="max-width: 300px;">
                            <input name="keyword" class="form-control" type="search" placeholder="Enter product name and press enter to search" aria-label="Search Product" value="{{ request()->keyword ?? '' }}">
                            {{-- <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search fa-fw"></i>
                            </button> --}}
                            <button class="btn btn-primary" type="submit" 
                             style="border-color: #f8f8f8;">
                                 <i class="fas fa-search fa-fw"></i>
                            </button>
                        </div>
                    </form>
                  </div>
                    <!-- Add New Button -->
                    <a href="{{ route('admin.productManage.create') }}" class="btn btn-sm btn-light text-primary shadow-sm">
                        <i class="fas fa-plus fa-sm"></i> Add New
                    </a>
                   
                   
                </div>
            </div>

            <div class="card-body">
                <!-- Search Bar -->
                

                <!-- Product Table -->
                <div class="table-responsive">
                    <table class="table table-striped table-bordered mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Mfr ID</th>
                                <th>Brand</th>
                                <th>Category</th>
                                <th>Sub-Category</th>
                                <th>Price</th>
                                <th>Featured</th>
                                <th>Best Sellers</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $key => $product)
                                <tr>
                                    <td>{{ $key + $products->firstItem() }}</td>
                                    <td><img style="height: 70px;" src="{{ asset('uploads/products/'.$product->file_name) }}" alt="Image" class="img-fluid"></td>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->manufacturers_no }}</td>
                                    <td>{{ $product->brands->name }}</td>
                                    <td>{{ $product->category->name }}</td>
                                    <td>
                                      <div style="max-height: 120px;width: 250px; overflow-y: auto; white-space: normal; word-wrap: break-word;">
                                        @if($product->category->subcategories->isNotEmpty())
                                            @foreach($product->category->subcategories as $subcategory)
                                                {{ $subcategory->name }}@if(!$loop->last) > @endif
                                            @endforeach
                                        @else
                                            N/A
                                        @endif
                                      </div>
                                    </td>
                                    <td>{{ $product->price }}</td>
                                    <td>
                                        {!! $product->featured ? '<i class="fas fa-star text-warning"></i>' : '<i class="far fa-star"></i>' !!}
                                    </td>
                                    <td>
                                        {!! $product->best_sellers ? '<i class="fas fa-star text-warning"></i>' : '<i class="far fa-star"></i>' !!}
                                    </td>
                                    <td>{{ $product->status ? 'Active' : 'Disabled' }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-light btn-sm">Action</button>
                                            <button type="button" class="btn btn-light btn-sm dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="mdi mdi-chevron-down"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="{{ route('admin.productManage.edit', $product) }}">Edit</a></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a href="#" class="dropdown-item add-featured" data-id="{{ $product->id }}">{{ $product->featured ? 'Remove from Featured' : 'Add as Featured' }}</a></li>
                                                <li><a href="#" class="dropdown-item add-best-sellers" data-id="{{ $product->id }}">{{ $product->best_sellers ? 'Remove from Best Sellers' : 'Add as Best Seller' }}</a></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a href="#" class="dropdown-item delete-product" data-id="{{ $product->id }}">Delete</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="text-center">No Data Found!</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Footer with Pagination -->
            <div class="card-footer text-muted">
                <div class="d-flex justify-content-between">
                    <div>
                        Total Products: {{ $products->total() }}
                    </div>
                    <div>
                        {{ $products->links('layouts.pagination') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll(".add-best-sellers").forEach(button => {
      button.addEventListener("click", function(e) {
        e.preventDefault();
        let productId = this.getAttribute("data-id");
        let isFeatured = this.textContent === 'Remove from Best Sellers';
        let url = isFeatured 
          ? "{{ route('admin.productManage.removeBestSeller') }}"
          : "{{ route('admin.productManage.addBestSeller') }}";
        let successMessage = isFeatured 
          ? 'Product removed from Best Sellers successfully!'
          : 'Product marked as Best Seller successfully!';
        
        fetch(url, {
          method: "PUT",
          headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
          },
          body: JSON.stringify({ id: productId })
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            Swal.fire({
              icon: 'success',
              title: 'Success!',
              text: successMessage
            });
            location.reload();
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Failed!',
              text: 'Operation failed.'
            });
          }
        })
        .catch(error => {
          Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Something went wrong!'
          });
          console.error("Error:", error);
        });
      });
    });
  });


  document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll(".add-featured").forEach(button => {
      button.addEventListener("click", function(e) {
        e.preventDefault();
        let productId = this.getAttribute("data-id");
        let isFeatured = this.textContent === 'Remove from Featured';
        let url = isFeatured 
          ? "{{ route('admin.productManage.removeFeatured') }}"
          : "{{ route('admin.productManage.addFeatured') }}";
        let successMessage = isFeatured 
          ? 'Product removed from featured successfully!'
          : 'Product marked as featured successfully!';
        
        fetch(url, {
          method: "PUT",
          headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
          },
          body: JSON.stringify({ id: productId })
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            Swal.fire({
              icon: 'success',
              title: 'Success!',
              text: successMessage
            });
            location.reload();
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Failed!',
              text: 'Operation failed.'
            });
          }
        })
        .catch(error => {
          Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Something went wrong!'
          });
          console.error("Error:", error);
        });
      });
    });
  });

    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll(".delete-product").forEach(button => {
            button.addEventListener("click", function(e) {
                e.preventDefault();
                let productId = this.getAttribute("data-id");
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`{{ route('admin.productManage.destroy', '') }}/${productId}`, {
                            method: "DELETE",
                            headers: {
                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                            }
                        }).then(response => response.json())
                        .then(data => {
                            Swal.fire(data.success ? 'Deleted!' : 'Error!', data.message, data.success ? 'success' : 'error').then(() => {
                                if (data.success) location.reload();
                            });
                        }).catch(() => {
                            Swal.fire('Error!', 'Failed to delete product.', 'error');
                        });
                    }
                });
            });
        });
      });
</script>
@endsection
