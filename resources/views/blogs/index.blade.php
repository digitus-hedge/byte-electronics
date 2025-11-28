@extends('layouts.app')

@section('content')
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active"><a href="{{ route('admin.blogs.index') }}">Blogs</a></li>
            </ol>
        </nav>
        <div class="card shadow" style="--bs-card-spacer-y:0rem !important;">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <span>List Of Blogs</span>
                {{-- Search Input --}}
                <div class="d-flex align-items-center gap-2 ms-auto">
                    {{-- Search Bar (right-aligned, compact) --}}
                    <div class="input-group input-group-sm" style="width: 180px;">
                        <input type="text" id="blogSearch" class="form-control" placeholder="Search...">
                        <span class="input-group-text"><i class="fas fa-search fa-sm"></i></span>
                    </div>
        
                    {{-- Add New Button --}}
                    <a href="{{ route('admin.blogs.create') }}" class="btn btn-sm btn-light text-primary shadow-sm">
                        <i class="fas fa-plus fa-sm"></i> Add New
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                   
                        <div class="table-responsive" id="blogTable">
                            @include('blogs.partials.table', ['blogs' => $blogs])
                        </div>
                          

                    
                </div>
            </div>
            <div class="card-footer text-muted">
                <div class="d-flex justify-content-between">
                    <div>
                        Total Brands: {{ $blogs->total() }}
                    </div>
                    <div>
                        {{ $blogs->links('layouts.pagination') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
   
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const deleteButtons = document.querySelectorAll('.delete-btn');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function () {
                const blogId = this.getAttribute('data-id');
                const form = document.getElementById(`delete-form-${blogId}`);
                if (confirm('Do you really want to delete the blog?')) {
                    form.submit();
                }
            });
        });
    });
</script>
<script>
    $('#blogSearch').on('keyup',function(){
        let search=$(this).val();
        $.ajax({
            url: "{{ route('admin.blogs.index') }}",
            type: "GET",
            data: { keyword: search },
            success: function(data) {
                $('#blogTable').html(data);
            },
            error: function(xhr, status, error) {
                console.error("Error fetching data:", error);
            }
        });
    }) 
</script>
@endpush

@endsection
