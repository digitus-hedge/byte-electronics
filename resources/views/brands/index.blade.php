@extends('layouts.app')

@section('content')
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active"><a href="{{ route('admin.brands.index') }}">Brands</a></li>
            </ol>
        </nav>
        <div class="card shadow" style="--bs-card-spacer-y:0rem !important;">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <span>List Of Brands</span>
                {{-- Search Input --}}
                <div class="d-flex align-items-center gap-2 ms-auto">
                    {{-- Search Bar (right-aligned, compact) --}}
                    <div class="input-group input-group-sm" style="width: 180px;">
                        <input type="text" id="brandSearch" class="form-control" placeholder="Search...">
                        <span class="input-group-text"><i class="fas fa-search fa-sm"></i></span>
                    </div>
        
                    {{-- Add New Button --}}
                    <a href="{{ route('admin.brands.create') }}" class="btn btn-sm btn-light text-primary shadow-sm">
                        <i class="fas fa-plus fa-sm"></i> Add New
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                   
                        <div class="table-responsive" id="brandTable">
                            @include('brands.partials.table', ['brands' => $brands])
                        </div>
                    
                </div>
            </div>
            <div class="card-footer text-muted">
                <div class="d-flex justify-content-between">
                    <div>
                        Total Brands: {{ $brands->total() }}
                    </div>
                    <div>
                        {{ $brands->links('layouts.pagination') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                document.querySelectorAll(".add-featured").forEach(button => {
                    button.addEventListener("click", function(e) {
                        e.preventDefault();
                        let brandId = this.getAttribute("data-id");
                        // let isFeatured = this.textContent === 'Remove from Featured';
                        let isFeatured = this.getAttribute("data-isFeatured") == 1;
                        let url = isFeatured
                            ? "{{ route('admin.brands.removeFeatured') }}"
                            : "{{ route('admin.brands.addFeatured') }}";
                        let successMessage = isFeatured
                            ? 'Brand removed from Featured successfully!'
                            : 'Brand marked as Featured successfully!';

                        fetch(url, {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                            },
                            body: JSON.stringify({ id: brandId })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                   
                                    icon: 'success',
                                    title: 'Success!',
                                    timer: 2000,
                                    text: successMessage,
                                    
                                });
                                location.reload();
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
        <script>
            $('#brandSearch').on('keyup', function() {
                let search=$(this).val();
                $.ajax({
                   
                    url: "{{ route('admin.brands.index') }}",
                    method: 'GET',
                    data: { keyword: search },
                    success: function(response) {
                        $('#brandTable').html(response);
                    },
                    error: function() {
                        alert('Failed to fetch data');
                    }
                });

            });
        </script>
    @endpush
@endsection
