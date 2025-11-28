@extends('layouts.app')

@section('content')
    <div class="container">
        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Orders</li>
            </ol>
        </nav>

        <div class="card shadow" style="--bs-card-spacer-y:0rem !important;">
            {{-- Header --}}
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <span>List of Orders</span>

                {{-- Optional search (implement logic if needed) --}}
                <div class="d-flex align-items-center gap-2 ms-auto">
                    <div class="input-group input-group-sm" style="width: 200px;">
                        <input type="text" id="orderSearch" class="form-control" placeholder="Search...">
                        <span class="input-group-text"><i class="fas fa-search fa-sm"></i></span>
                    </div>
                </div>
            </div>

            {{-- Table --}}
            <div class="card-body">
                <div class="table-responsive" id="orderTable">
                    {{-- Include the orders table partial --}}
                    @include('orders.partials.table', ['orders' => $orders])
                </div>
            </div>

            {{-- Footer --}}
            <div class="card-footer text-muted">
                <div class="d-flex justify-content-between">
                    <div>
                        Total Orders: {{ $orders->total() }}
                    </div>
                    <div>
                        {{ $orders->links('layouts.pagination') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

@push('scripts')
<script>
      $('#orderSearch').on('keyup', function() {
                let search=$(this).val();
                $.ajax({
                   
                    url: "{{ route('admin.orders.index') }}",
                    method: 'GET',
                    data: { keyword: search },
                    success: function(response) {
                        $('#orderTable').html(response);
                    },
                    error: function() {
                        alert('Failed to fetch data');
                    }
                });

            });
</script>
    
@endpush
@endsection

