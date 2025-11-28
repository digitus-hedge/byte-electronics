@extends('layouts.app')
@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    {{-- <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Categories</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
              <li class="breadcrumb-item active"><a href="#">Categories</a></li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section> --}}

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                  <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                  <li class="breadcrumb-item" active><a href="{{ route('admin.categories.index') }}">Categories</a></li>
                  {{-- <li class="breadcrumb-item" active><a href="{{ route('categories.index') }}">Categories</a></li> --}}
              </ol>
          </div>
            <!-- Default box -->
            <div class="card">
              <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <span>Categories</span>
                  
                      {{-- Search Input --}}
                      <div class="d-flex align-items-center gap-2 ms-auto">

                        {{-- Search Bar --}}
                        <form action="{{ route('admin.categories.index') }}" method="GET" class="d-flex align-items-center">
                            <div class="input-group input-group-sm">
                                <input 
                                    name="keyword" 
                                    type="search" 
                                    class="form-control form-control-sm" 
                                    placeholder="Search category..." 
                                    aria-label="Search Categories"
                                    value="{{ request()->keyword ?? '' }}"
                                    style="max-width: 160px;"
                                >
                                <button class="btn btn-sm btn-light text-primary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>
                    
                        {{-- Add New Button --}}
                        <a href="{{ route('admin.categories.create') }}" class="btn btn-sm btn-light text-primary shadow-sm d-flex align-items-center">
                            <i class="fas fa-plus me-1 fa-sm"></i> Add New
                        </a>
                    
                    </div>
              </div>
            
              <div class="card-body">
                <div class="row">
                    
                    <div class="col-md-12">
                        <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                <th style="width: 10px">#</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Featured</th>
                                <th style="width: 40px">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categories as $key => $category)
                                <tr>
                                    <td>{{ $key + $categories->firstItem() }}</td>
                                    <td style="background: var(--bs-header-dark-bg);"><img style="height: 70px;" src="{{ asset('uploads/category/'.$category->file_name) }}" alt="Image"
                                                class="img-fluid">
                                    <td>{{ $category->name }}</td>
                                    <td>{{
                                            $category->status == 1
                                              ? 'Active'
                                              : 'Disabled'
                                        }}
                                    </td>
                                    <td>
                                        @if($category->featured == 1)
                                            <i class="fas fa-star text-warning"></i>
                                        @else
                                            <i class="far fa-star"></i>
                                        @endif
                                    </td>
                                    <td>
                                     <div class="btn-group">
                                        <button type="button" class="btn btn-light">Action</button>
                                        <button type="button" class="btn btn-light dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="true">
                                          <i class="mdi mdi-chevron-down"></i>
                                        </button>
                                        <ul class="dropdown-menu" role="menu" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(70px, -165px, 0px);">
                                          <li><a class="dropdown-item" href="{{ route('admin.categories.edit', $category->id) }}">Edit</a></li>
                                          <li><a class="dropdown-item add-featured" href="#" data-id="{{ $category->id }}" data-isFeatured="{{ $category->featured }}">
                                              {{ $category->featured == 1 ? 'Remove from Featured' : 'Add to Featured' }}
                                          </a></li>
                                          <li><div class="dropdown-divider"></div></li>
                                          <li>
                                            <form action="{{ route('admin.categories.destroy', $category->id) }}" method="post" class="delete-form">
                                              @csrf
                                              @method('DELETE')
                                              <button type="submit" class="dropdown-item delete-btn">Delete</button>
                                            </form>
                                          </li>
                                        </ul>
                                      </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">No Data Found!</td>
                                </tr>
                                @endforelse
                            </tbody>
                            </table>
                        </div>
                    </div>
                </div>
              </div>
              <!-- /.card-body -->
              <!-- card-footer -->
             <div class="card-footer text-muted">
                <div class="d-flex justify-content-between">
                    <div>
                        Total Categories: {{ $categories->total() }}
                    </div>
                    <div>
                        {{ $categories->links('layouts.pagination') }}
                    </div>
                </div>
            </div>
            </div>
            <!-- /.card -->

            {{-- Dont delete,just for checking category,sub-category heirarchy --}}
            <div class="card">
              <div class="card-header">
                  <h4>Categories and Subcategories</h4>
              </div>
              <div class="card-body">
                  @forelse($categories as $category)
                      <div class="card mb-3">
                          <div class="card-body">
                              <h5 class="card-title">{{ $category->name }}</h5>
                              <div>
                                  @if($category->subcategories->isNotEmpty())
                                      @include('categories.subcategories', ['subcategories' => $category->subcategories])
                                  @else
                                      <p class="text-muted">No subcategories</p>
                                  @endif
                              </div>
                          </div>
                      </div>
                  @empty
                      <p class="text-muted">No categories available</p>
                  @endforelse
              </div>
          </div>
      </div>
          </div>
        </div>
      </div>
    </section>
    <!-- /.content -->
  </div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
console.log('SweetAlert script loaded'); // Debug: Check if script is running

// Check if SweetAlert2 is available
if (typeof Swal === 'undefined') {
    console.error('SweetAlert2 is not loaded');
} else {
    console.log('SweetAlert2 is available');
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM fully loaded'); // Debug: Check if DOM is ready

    // Handle delete confirmation
    const deleteForms = document.querySelectorAll('.delete-form');
    console.log('Found delete forms:', deleteForms.length); // Debug: Check number of forms

    deleteForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Delete form submitted'); // Debug: Confirm form submission

            Swal.fire({
                title: 'Are you sure?',
                text: 'This will also soft-delete related subcategories and products!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    console.log('Delete confirmed'); // Debug: Confirm user action
                    form.submit();
                } else {
                    console.log('Delete canceled'); // Debug: Confirm cancellation
                }
            }).catch(error => {
                console.error('SweetAlert error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while showing the confirmation dialog.'
                });
            });
        });
    });

    // Handle featured toggle
   

    // Show success message if present
    @if(session('success'))
        console.log('Success message present:', '{{ session('success') }}'); // Debug: Check session message
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: '{{ session('success') }}',
            timer: 3000,
            showConfirmButton: false
        }).catch(error => {
            console.error('SweetAlert success error:', error);
        });
    @endif
});
</script>
<script>
document.addEventListener("DOMContentLoaded", function() {
  
  document.querySelectorAll(".add-featured").forEach(button => {
     
    button.addEventListener("click", function(e) {
      e.preventDefault();
      
      let categoryId = this.getAttribute("data-id");
      console.log(this.textContent); // Debug: Check button click
      // let isFeatured = this.textContent === 'Remove from Featured';
      let isFeatured = this.getAttribute("data-isFeatured") == 1;
      console.log('Category ID:', categoryId); // Debug: Check category ID
      console.log('Is Featured:', isFeatured); // Debug: Check if featured
      let url = isFeatured
        ? "{{ route('admin.categories.removeFeatured') }}"
        : "{{ route('admin.categories.addFeatured') }}";
      let successMessage = isFeatured
        ? 'Category removed from Featured successfully!'
        : 'Category marked as Featured successfully!';

      fetch(url, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({ id: categoryId })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: successMessage,
            timer: 2000,
            showConfirmButton: false
          }).then(() => {
            location.reload();
          });
        } else {
          Swal.fire({
            timer: 2000,
            icon: 'error',
            title: 'Failed!',
            text: 'Operation failed.'
          });
        }
      })
      .catch(error => {
        Swal.fire({
          timer: 2000,
          icon: 'error',
          title: 'Error!',
          text: 'Something went wrong!'
        });
        console.error("Error:", error);
      });
    });
  });
});

</script>
@endpush
@endsection