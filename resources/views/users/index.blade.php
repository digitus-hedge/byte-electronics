@extends('layouts.app')

@section('content')
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item" active aria-current="page"><a href="{{ route('admin.users.index') }}">Users</a></li>
            </ol>
        </nav>
        <div class="card shadow" style="--bs-card-spacer-y:0rem !important;">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <span>Users</span>
                 {{-- Search Input --}}
                 <div class="d-flex align-items-center gap-2">
                    {{-- Search Input --}}
                    <div class="input-group input-group-sm" style="width: 180px;">
                        <input type="text" id="userSearch" class="form-control" placeholder="Search...">
                        <span class="input-group-text"><i class="fas fa-search fa-sm"></i></span>
                    </div>
            
                    {{-- Add New Button --}}
                    <a href="{{ route('admin.users.create')}}" class="btn btn-primary"><i class="fas fa-plus"></i> New User</a>
                </div>
    
                
            </div>
            <div class="card-body">
                <div class="table-responsive" id=userTable>
                 @include('users.partials.table', ['users' => $users])
                </div>
            </div>
            <div class="card-footer text-muted">
                <div class="d-flex justify-content-between">
                    <div>
                        Total Users: {{ $users->total() }}
                    </div>
                    <div>
                        {{ $users->links('layouts.pagination') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
           
            // Search functionality
            $('#userSearch').on('keyup',function(){
                let query = $(this).val();
                $.ajax({
                    url: "{{ route('admin.users.index') }}",
                    method: 'GET',
                    data: { keyword: query },
                    success: function(data) {
                        $('#userTable').html(data);
                    },
                    error: function(xhr, status, error) {
                        console.error("Search Error:", error);
                    }
                })
            })
        });
    </script>

@endpush
