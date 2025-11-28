@extends('layouts.app')

@section('content')
    <div class="container">
        <!-- Breadcrumbs -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Products  Rfq</li>
            </ol>
        </nav>

        <!-- Product Card -->
        <div class="card shadow" style="--bs-card-spacer-y: 0rem !important;">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <span>List Of Products Rfq</span>
                
                <div class="d-flex align-items-center gap-2 ms-auto">
                 

                  

                  {{-- serach bar --}}
                  <div class="d-flex align-items-center gap-2 ms-auto">
                    {{-- Search Bar (right-aligned, compact) --}}
                    <div class="input-group input-group-sm" style="width: 180px;">
                        <input type="text" id="rfqSearch" class="form-control" placeholder="Search...">
                        <span class="input-group-text"><i class="fas fa-search fa-sm"></i></span>
                    </div>
        
                  
                </div>
                    <!-- Add New Button -->
                   
                   
                   
                </div>
            </div>

            <div class="card-body">
                <!-- Search Bar -->
                

                <!-- Product Table -->
                <div class="table-responsive" id="rfqTable">
                  @include('productManage.partials.table', ['rfqs' => $rfqs])
                </div>
            </div>

            <!-- Footer with Pagination -->
            <div class="card-footer text-muted">
                <div class="d-flex justify-content-between">
                    <div>
                        {{ $rfqs->links('layouts.pagination') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
<script>
  $('#rfqSearch').on('keyup',function(){
       let search=$(this).val();
      
       $.ajax({
           url: "{{ route('admin.productManage.requestQuote') }}",
           type: "GET",
           data: { keyword: search },
           success: function(data) {
               $('#rfqTable').html(data);
           },
           error: function(xhr, status, error) {
               console.error("Error fetching data:", error);
           }
       });
   }) 
</script>
  
@endpush

