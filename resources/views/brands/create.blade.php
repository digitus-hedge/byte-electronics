@extends('layouts.app')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Brands</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.brands.index') }}">Brands</a></li>

                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <!-- Default box --> 
                        <form id="brandForm" action="{{ route('admin.brands.store') }}" onsubmit="return validateform();" method="post" enctype="multipart/form-data" >
                            @csrf
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Add New Brand</h3>
                                    <div class="card-tools">
                                        <a href="javascript: history.go(-1)" class="btn btn-secondary" title="Back">
                                            <i class="fas fa-arrow-left"></i> Back
                                        </a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label>Name</label>
                                                <input name="name" type="text" id="name"
                                                    class="form-control @error('name') is-invalid @enderror"
                                                    placeholder="Enter Brand Name" value="{{ old('name') }}" 
                                                     >
                                                @error('name')
                                                    <span class="error invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label>Slug</label>
                                                <input name="slug" type="text" id="slug"
                                                    class="form-control @error('slug') is-invalid @enderror"
                                                    placeholder="Enter Brand Slug" value="{{ old('slug') }}" 
                                                    >
                                                @error('slug')
                                                    <span class="error invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Image</label>
                                                <input id="fileInput" name="image" type="file"
                                                    class="form-control @error('image') is-invalid @enderror"
                                                    value="{{ old('image') }}" accept="image/*" >
                                                <p>Dimensions: 110 * 80 px</p>
                                                @error('image')
                                                    <span class="error invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="position-relative">
                                                <img id="filePreview" style="height: 150px; display: none;"
                                                    src="{{ asset('admin-assets/img/image-placeholder.png') }}"
                                                    alt="Image" class="img-fluid">
                                                <div class="ribbon-wrapper ribbon-lg">
                                                    <div class="ribbon bg-success text-lg">
                                                        Preview
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- secondary Icon --}}
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Secondary Logo</label>
                                                <input id="secondaryLogoInput" name="secondary_logo" type="file"
                                                    class="form-control @error('secondary_logo') is-invalid @enderror"
                                                    value="{{ old('secondary_logo') }}" accept="image/*">
                                                <p>Dimensions: 150 * 110 px</p>
                                                @error('secondary_logo')
                                                    <span class="error invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="position-relative">
                                                <img id="secondaryLogoPreview" style="height: 150px; display: none;"
                                                    src="{{ asset('admin-assets/img/image-placeholder.png') }}"
                                                    alt="Image" class="img-fluid">
                                                <div class="ribbon-wrapper ribbon-lg">
                                                    <div class="ribbon bg-success text-lg">
                                                        Preview
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Banner --}}
                                        <div class="col-sm-6 mt-1">
                                            <div class="form-group">
                                                <label>Banner</label>
                                                <input id="fileInputBanner" name="banner" type="file"
                                                    class="form-control @error('banner') is-invalid @enderror"
                                                    value="{{ old('banner') }}" accept="image/*">
                                                <p>Dimensions: 1350 * 271 px</p>
                                                @error('banner')
                                                    <span class="error invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-sm-6 mt-1">
                                            <div class="position-relative">
                                                <img id="filePreviewBanner" style="height: 150px; display: none;"
                                                    src="{{ asset('admin-assets/img/image-placeholder.png') }}"
                                                    alt="Image" class="img-fluid">
                                                <div class="ribbon-wrapper ribbon-lg">
                                                    <div class="ribbon bg-success text-lg">
                                                        Preview
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- banner end --}}
                                        <div class="col-sm-12">
                                            <div class="form-group edit">
                                                <label>Description</label>
                                                <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                                    placeholder="Enter Description..." id="editor1">{{ old('description') }}</textarea>
                                                @error('description')
                                                    <span class="error invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label>Meta Tag</label>
                                                <textarea name="meta_tag" class="form-control @error('meta_tag') is-invalid @enderror"
                                                    placeholder="Enter Meta Tag...">{{ old('meta_tag') }}</textarea>
                                                @error('meta_tag')
                                                    <span class="error invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label>Meta Description</label>
                                                <textarea name="meta_description" class="form-control @error('meta_description') is-invalid @enderror"
                                                    placeholder="Enter Meta Description...">{{ old('meta_description') }}</textarea>
                                                @error('meta_description')
                                                    <span class="error invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label>Status</label>
                                                <div class="custom-control custom-switch">
                                                    <input name="status" type="checkbox" class="custom-control-input"
                                                        id="customSwitch1" value="1" checked>
                                                    <label class="custom-control-label" for="customSwitch1"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.card-body -->
                                <div class="card-footer">
                                    <button type="reset" class="btn btn-default">Cancel</button>
                                    <button type="submit" class="btn btn-info float-right">Save</button>
                                </div>
                            </form>
                                <!-- /.card-footer -->
                                <!-- /.card -->
                    
                    </div>
                </div>
            </div>
        </section>
        <!-- /.content -->
    </div>

    @push('scripts')
    
<script src="https://cdnjs.cloudflare.com/ajax/libs/speakingurl/14.0.1/speakingurl.min.js"></script>
<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>


    <script>
        // function validateform()
        // {
        //     // alert('Form submitted successfully!');
        //     let brandfrom = $('#brandForm');
        //     brandfrom.submit();
        // }
         $('#name').on('input', function() {
        var title = $(this).val();
        var slug = getSlug(title);
        $('#slug').val(slug);
    });
    </script>
        <script>
             
            // For regular image input
            const fileInput = document.getElementById('fileInput');
            const filePreview = document.getElementById('filePreview');

            fileInput.onchange = evt => {
                const [file] = fileInput.files;
                if (file) {
                    filePreview.src = URL.createObjectURL(file);
                    filePreview.style.display = 'block'; // Show the preview
                } else {
                    filePreview.src = "{{ asset('admin-assets/img/image-placeholder.png') }}"; // Reset to placeholder
                    filePreview.style.display = 'none'; // Hide the preview if no file is selected
                }
            };

            // For banner input
            const fileInputBanner = document.getElementById('fileInputBanner');
            const filePreviewBanner = document.getElementById('filePreviewBanner');

            fileInputBanner.onchange = evt => {
                const [file] = fileInputBanner.files;
                if (file) {
                    filePreviewBanner.src = URL.createObjectURL(file);
                    filePreviewBanner.style.display = 'block'; // Show the preview
                } else {
                    filePreviewBanner.src =
                        "{{ asset('admin-assets/img/image-placeholder.png') }}"; // Reset to placeholder
                    filePreviewBanner.style.display = 'none'; // Hide the preview if no file is selected
                }
            };

            // Handling banner type change
            $(document).ready(function() {
                $('#banner_type').on('change', function(e) {
                    var banner_type = e.target.value;
                    var dimensionsText = '';
                    if (banner_type == 1) {
                        dimensionsText = 'Dimensions: 960 * 360 px';
                    } else if (banner_type == 2) {
                        dimensionsText = 'Dimensions: 960 * 240 px';
                    } else {
                        dimensionsText = '';
                    }
                    $('#dimensions').text(dimensionsText); // Make sure you have an element with id="dimensions"
                });
            });
            // For secondary logo input
            const secondaryLogoInput = document.getElementById('secondaryLogoInput');
            const secondaryLogoPreview = document.getElementById('secondaryLogoPreview');

            secondaryLogoInput.onchange = evt => {
                const [file] = secondaryLogoInput.files;
                if (file) {
                    secondaryLogoPreview.src = URL.createObjectURL(file);
                    secondaryLogoPreview.style.display = 'block'; // Show the preview
                } else {
                    secondaryLogoPreview.src =
                    "{{ asset('admin-assets/img/image-placeholder.png') }}"; // Reset to placeholder
                    secondaryLogoPreview.style.display = 'none'; // Hide the preview if no file is selected
                }
            };
        </script>
        {{-- <script src="{{ asset('admin-assets/plugins/jquery/jquery.min.js') }}"></script> --}}
        {{-- <script src="{{ asset('admin-assets/js/richtext/froala_editor.min.js') }}"></script> --}}
        <script>
            // CKEDITOR.replace('editor1');
        </script>
        <script>
            // Image preview
    function validateImage(input, width, height, previewId, errorMessageId) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const img = new Image();
            img.src = URL.createObjectURL(file);

            img.onload = function () {
                if (this.width !== width || this.height !== height) {
                    // alert("Invalid image size! Required: " + width + "x" + height + " pixels.");
                    input.value = "";
                    document.getElementById(previewId).style.display = "none";
                } else {
                    document.getElementById(previewId).src = img.src;
                    document.getElementById(previewId).style.display = "block";
                }
            };
        }
    }

    document.getElementById("fileInput").addEventListener("change", function () {
        validateImage(this, 110, 80, "filePreview", "imageError");
    });

    document.getElementById("secondaryLogoInput").addEventListener("change", function () {
        validateImage(this, 150, 110, "secondaryLogoPreview", "secondaryLogoError");
    });

    document.getElementById("fileInputBanner").addEventListener("change", function () {
        validateImage(this, 1350, 271, "filePreviewBanner", "bannerError");
    });
</script>
<script>
    $(document).ready(function () {
        $('#brandForm').on('submit', function (e) {
            e.preventDefault();

            const form = this;
            let formData = new FormData(form);

            $.ajax({
                url: $(form).attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('input[name="_token"]').val()
                },
                success: function (response) {
                    Swal.fire({
                        title: 'Success!',
                        text: response.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.href = "{{ route('admin.brands.index') }}";
                    });
                },
                error: function (xhr) {
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        let messages = '';

                        Object.keys(errors).forEach(key => {
                            messages += `<p><strong>${key.replace('_', ' ')}:</strong> ${errors[key][0]}</p>`;
                        });

                        Swal.fire({
                            title: 'Validation Error!',
                            html: messages,
                            icon: 'warning',
                            confirmButtonText: 'Fix Errors'
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Something went wrong while saving the brand.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                        console.error(xhr.responseText);
                    }
                }
            });
        });
    });
</script>

       
    @endpush
@endsection
