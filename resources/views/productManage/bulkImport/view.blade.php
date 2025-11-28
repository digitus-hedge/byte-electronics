@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="container-fluid">


            <div class="row">
                <div class="col-lg-12">
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.productManage.index') }}">Products</a></li>
                            <li class="breadcrumb-item active"><a href="#">Product Import </a></li>
                        </ol>
                    </div>
                    <div id="addproduct-accordion" class="custom-accordion">
                        <div class="card">
                            <a href="#addproduct-billinginfo-collapse" class="text-reset" data-bs-toggle="collapse"
                                aria-expanded="true" aria-controls="addproduct-billinginfo-collapse">
                                <div class="p-4">

                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="avatar">
                                                <div class="avatar-title rounded-circle bg-primary-subtle text-primary">
                                                    01
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 overflow-hidden">
                                            <h5 class="font-size-16 mb-1">Product import </h5>
                                            <p class="text-muted text-truncate mb-0">Fill all information below</p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            {{-- <i class="mdi mdi-chevron-up accor-down-icon font-size-24"></i> --}}
                                            {{-- <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary"  title="Back">
                                            <i class="fas fa-arrow-left"></i> Back --}}
                            </a>
                        </div>

                    </div>

                </div>
                </a>

                <div id="addproduct-billinginfo-collapse" class="collapse show" data-bs-parent="#addproduct-accordion">
                    <div class="p-4 border-top">
                        <form id="productImportForm" action="{{url('admin/products/import')}}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="card p-4">
                                <div class="row mb-3">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form-label">Product Import</label>
                                            <input id="fileInput" name="file" type="file"
                                                class="form-control @error('file') is-invalid @enderror"
                                                value="{{ old('file') }}" accept="">
                                            @error('file')
                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                
                                <div class="row">
                                    <div class="col text-end">
                                        <button type="reset" class="btn btn-danger me-2">
                                            <i class="bx bx-x me-1"></i> Cancel
                                        </button>
                                        <button type="submit" class="btn btn-success" id="uploadButton">
                                            <i class="bx bx-file me-1"></i> Save
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div id="addproduct-billinginfo-collapse" class="collapse show" data-bs-parent="#addproduct-accordion">
                    <div class="p-4 border-top">
                        <form id="productPriceImportForm" action="{{url('admin/products/price_import')}}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="card p-4">
                                <div class="row mb-3">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form-label">Product Price Import</label>
                                            <input id="fileInput" name="price_file" type="file"
                                                class="form-control @error('price_file') is-invalid @enderror"
                                                value="{{ old('file') }}" accept="">
                                            @error('price_file')
                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                
                                <div class="row">
                                    <div class="col text-end">
                                        <button type="reset" class="btn btn-danger me-2">
                                            <i class="bx bx-x me-1"></i> Cancel
                                        </button>
                                        <button type="submit" class="btn btn-success" id="uploadButton">
                                            <i class="bx bx-file me-1"></i> Save
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div id="addproduct-billinginfo-collapse" class="collapse show" data-bs-parent="#addproduct-accordion">
                    <div class="p-4 border-top">
                        <form id="productPriceImportForm" action="{{url('admin/products/specification_import')}}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="card p-4">
                                <div class="row mb-3">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form-label">Product Specification Import</label>
                                            <input id="fileInput" name="specification_file" type="file"
                                                class="form-control @error('specification_file') is-invalid @enderror"
                                                value="{{ old('specification_file') }}" accept="">
                                            @error('specification_file')
                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                
                                <div class="row">
                                    <div class="col text-end">
                                        <button type="reset" class="btn btn-danger me-2">
                                            <i class="bx bx-x me-1"></i> Cancel
                                        </button>
                                        <button type="submit" class="btn btn-success" id="uploadButton">
                                            <i class="bx bx-file me-1"></i> Save
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div id="addproduct-billinginfo-collapse" class="collapse show" data-bs-parent="#addproduct-accordion">
                    <div class="p-4 border-top">
                        <form id="productPriceImportForm" action="{{url('admin/products/more_info_import')}}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="card p-4">
                                <div class="row mb-3">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form-label">Product More Information</label>
                                            <input id="fileInput" name="more_info_file" type="file"
                                                class="form-control @error('more_info_file') is-invalid @enderror"
                                                value="{{ old('more_info_file') }}" accept="">
                                            @error('more_info_file')
                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                
                                <div class="row">
                                    <div class="col text-end">
                                        <button type="reset" class="btn btn-danger me-2">
                                            <i class="bx bx-x me-1"></i> Cancel
                                        </button>
                                        <button type="submit" class="btn btn-success" id="uploadButton">
                                            <i class="bx bx-file me-1"></i> Save
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>




        </div>
    </div>
    </div>


    </div>
    </div>
@endsection
@push('scripts')
<script>
    // document.getElementById('uploadButton').addEventListener('click', function (event) {
    // event.preventDefault(); // Prevent the default form submission
    //     const formData= new FormData();
    //     const file = document.getElementById('fileInput').files[0];
    //     formData.append('file', file);
        
    // fetch("{{ route('admin.productManage.import') }}", {
    //     method: 'POST',
    //     headers: {
    //         'X-CSRF-TOKEN': '{{ csrf_token() }}'
    //     },
    //     body: formData
    // })
    // .then(response => response.json())
    // .then(data => {
    //     console.log(data);
    //     alert('Upload successful!');
    // })
    // .catch(error => {
    //     console.error(error);
    //     alert('Upload failed!');
    // });
    // });
</script>
@endpush>


