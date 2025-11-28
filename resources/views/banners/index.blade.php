@extends('layouts.app')
@push('styles')
    <style>
        .btnn {
            --bs-btn-padding-x: 0.0rem !important;
            --bs-btn-padding-y: 0.0rem !important;
        }

        .dropdown-menu {
            z-index: 1050;
        }
    </style>
@endpush
@section('content')
<div class="container">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item" active><a href="{{ route('admin.banners.index') }}">Banners</a></li>
        </ol>
    </nav>
    <div class="card shadow" style="--bs-card-spacer-y:0rem !important;">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <span>Banners</span>
              {{-- Search Input --}}
              <div class="d-flex align-items-center gap-2 ms-auto">
                    {{-- Search Bar (right-aligned, compact) --}}
                    <div class="input-group input-group-sm" style="width: 180px;">
                        <input type="text" id="bannerSearch" class="form-control" placeholder="Search...">
                        <span class="input-group-text"><i class="fas fa-search fa-sm"></i></span>
                    </div>
        
                    {{-- Add New Button --}}
                    <a href="{{ route('admin.banners.create') }}" class="btn btn-sm btn-light text-primary shadow-sm">
                        <i class="fas fa-plus fa-sm"></i> Add New
                    </a>
                </div>
            
        </div>
        <div class="card-body">
            <div class="table-responsive" id="bannerTable">
               @include('banners.partials.table', ['banners' => $banners])
            </div>
        </div>
        <div class="card-footer text-muted">
            <div class="d-flex justify-content-between">
                <div>
                    Total Users: {{ $banners->total() }}
                </div>
                <div>
                    {{ $banners->links('layouts.pagination') }}
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script>
    function deleteBanner(id) {
        console.log(id);
        
            Swal.fire({
                    title: 'Are you sure?',
                    text: 'You will not be able to recover this Banner!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'No, keep it'
            }).then((result) => {
                    if (result.isConfirmed) {
                            $.ajax({
                                    url: 'banners/' + id,
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
                                                'Failed to delete banner.',
                                                'error'
                                            );
                                        }
                                    },
                                    error: function(xhr, status, error) {
                                        Swal.fire(
                                            'Error!',
                                            'Failed to delete banner.',
                                            'error'
                                        );
                                     
                                    }
                            });
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                            Swal.fire(
                                    'Cancelled', 
                                    'Banner is safe :)',
                                    'error'
                            );
                    }
            });
    }
</script>
<script>
    $('#bannerSearch').on('keyup', function() {
        let search = $(this).val();
        $.ajax({
            url: "{{ route('admin.banners.index') }}",
            method: 'GET',
            data: { keyword: search },
            success: function(response) {
                $('#bannerTable').html(response);
            },
            error: function() {
                alert('Failed to fetch data');
            }
        });
    });
</script>
    
@endpush
@endsection
