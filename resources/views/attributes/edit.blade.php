@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item "><a href="{{ route('admin.attribute_master.index') }}">Attributes</a></li>
                    <li class="breadcrumb-item active"><a href="#">Edit Attribute</a></li>
                </ol>
            </div>
            <div class="card">
                <div class="card-header">
                    {{-- <h5 class="card-title">Update Attribute</h5> --}}

                </div>

                <form method="POST" id="updateForm" action="{{ route('admin.attribute_master.update', $attribute) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="name">Attribute Name</label>
                                    <input
                                        type="text"
                                        name="name"
                                        id="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        placeholder="Enter Attribute Name"
                                        value="{{ old('name', $attribute->name) }}"
                                        required>
                                    @error('name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea
                                        name="description"
                                        id="description"
                                        class="form-control @error('description') is-invalid @enderror"
                                        placeholder="Enter Description..."
                                        rows="4">{{ old('description', $attribute->description) }}</textarea>
                                    @error('description')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Status</label>
                                    <div class="custom-control custom-switch">
                                        <input
                                            name="status"
                                            type="checkbox"
                                            class="custom-control-input"
                                            id="customSwitch1"
                                            value="1"
                                            {{ old('status', $attribute->status) == 1 ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="customSwitch1"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card Footer with Submit and Cancel Buttons -->
                    <div class="card-footer text-end">
                        <a href="{{ route('admin.attribute_master.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary" id="submitBtn">Update</button>

                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts');
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function () {
        // Handle form submission via AJAX
        $('#updateForm').on('submit', function (e) {
            e.preventDefault(); // Prevent default form submission

            let formData = new FormData(this); // Create FormData object from the form

            $.ajax({
                url: $(this).attr('action'), // Form action (URL)
                type: 'POST', // Method type
                data: formData, // Form data
                processData: false, // Disable automatic data processing
                contentType: false, // Disable automatic content type setting
                success: function (response) {
                    // Success: show SweetAlert success message
                    Swal.fire({
                        title: 'Success!',
                        text: 'Attribute updated successfully.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.href = '{{ route('admin.attribute_master.index') }}'; // Redirect after success
                    });
                },
                error: function (xhr, status, error) {
                    // Error: show SweetAlert error message
                    Swal.fire({
                        title: 'Error!',
                        text: 'There was an error updating the attribute.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });
    });
</script>
@endpush

@endsection
