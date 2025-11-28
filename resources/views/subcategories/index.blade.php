@extends('layouts.app')

@section('content')
<div class="content-wrapper">
   
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                  <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                  <li class="breadcrumb-item" active><a href="{{ route('admin.subcategories.index') }}"> Sub Categories</a></li>
               
              </ol>
          </div>
            <!-- Default box -->
            <div class="card">
              <div class="card-header bg-primary text-white">
                  <div class="d-flex justify-content-between align-items-center w-80 h-80 flex-wrap gap-2">
                      <span>Sub Categories</span>
                      
                          {{-- Search Input --}}
                          <div class="d-flex align-items-center gap-2 ms-auto">
                              <form action="{{ route('admin.subcategories.index') }}" method="get" class="d-flex flex-wrap gap-2 align-items-center mb-0">
                                <div class="row align-items-center ">
                                    {{-- Category Dropdown --}}
                                    <div class="col-md-4 mb-2 mb-md-0">
                                        <select name="category_id" class="form-select" style="padding: 5px !important;">
                                            <option value="">Select Category</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ request()->category_id == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                            
                                    {{-- Search Input --}}
                                    <div class="col-md-5 mb-2 mb-md-0">
                                        <div class="input-group h-100">
                                            <input name="keyword" class="form-control " type="search" placeholder="Search Sub Categories" style="padding: 5px !important;"
                                                  aria-label="Search Sub Categories" value="{{ request()->keyword ?? '' }}" >
                                            <button class="btn btn-outline-secondary" type="submit" style="padding: 5px !important;">
                                                <i class="fas fa-search fa-fw"></i>
                                            </button>
                                        </div>
                                    </div>
                            
                                    {{-- Add New Button --}}
                                    <div class="col-md-3 text-md-end">
                                        <a href="{{ route('admin.subcategories.create') }}"
                                        class="btn btn-sm btn-light text-primary shadow-sm " style="padding: 5px !important;">
                                            <i class="fas fa-plus me-1 fa-sm"></i> Add New
                                        </a>
                                    </div>
                                </div>
                            </form>
                          
                          </div>
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
                                <th>Product Default Image</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th style="width: 40px">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($subcategories as $key => $subcategory)
                                <tr>
                                    <td>{{$key + $subcategories->firstItem()}}</td>
                                    <td><img style="height: 150px;" src="{{ asset(''.$subcategory->image_sub_cat) }}" alt="Image"
                                                class="img-fluid"></td>

                                                @if($subcategory->product_default_sub_cat != "")
                                                <td><img style="height: 150px;" src="{{ asset(''.$subcategory->product_default_sub_cat) }}" alt="Image"
                                                class="img-fluid"></td>
                                                @else
                                                <td><img style="height: 150px;" src="{{ asset('admin-assets/img/image-placeholder.png')}}" alt="Image"
                                                class="img-fluid"></td>
                                                @endif

                                    <td>{{ $subcategory->name }}</td>
                                    <td>{{ $subcategory->category->name ?? '' }}</td>
                                    <td>{{
                                            $subcategory->status == 1
                                              ? 'Active'
                                              : 'Disabled'
                                        }}
                                    </td>
                                    <td>
                                    <div class="btn-group">
                                      <button type="button" class="btn btn-light">Action</button>
                                      <button type="button" class="btn btn-light dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="true">
                                        <i class="mdi mdi-chevron-down"></i>
                                      </button>
                                      <div class="dropdown-menu" role="menu" x-placement="top-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(70px, -165px, 0px);">
                                        <a class="dropdown-item" href="{{ route('admin.subcategories.edit',$subcategory) }}">Edit</a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="javascript:void(0);" onclick="deleteCategory({{ $subcategory->id }})">Delete</a>
                                      </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">No Data Found!</td>
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
              <div class="card-footer clearfix">
              {{ $subcategories->appends(request()->query())->links('layouts.pagination') }}
              </div>
            </div>
             <!-- /.card-footer -->
            <!-- /.card -->
          </div>
        </div>
      </div>
    </section>
    <!-- /.content -->
  </div>
  @push('scripts')
  <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
  <script>
    function deleteCategory(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'You will not be able to recover this Sub Category!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'No, keep it'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'subcategories/' + id,
                    type: 'DELETE',
                    data: {
                        _token: $("input[name='_token']").val()
                    },
                    success: function(response) {
                      if (response.success) {
                        Swal.fire(
                          'Deleted!',
                          response.message,
                          'success'
                        );
                        window.location.reload();
                      } else {
                        Swal.fire(
                          'Error!',
                          'Failed to delete sub category.',
                          'error'
                        );
                      }
                    },
                    error: function(xhr, status, error) {
                      Swal.fire(
                        'Error!',
                        'Failed to delete sub category.',
                        'error'
                      );
                     
                    }
                });
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                Swal.fire(
                    'Cancelled',
                    'Sub Category is safe :)',
                    'error'
                );
            }
        });
    }
</script>
            

  @endpush
@endsection
