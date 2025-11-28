@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.brands.index') }}">Brands</a></li>
                        <li class="breadcrumb-item active"><a href="#">Edit Brand</a></li>
                    </ol>
                </div>
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Brands</h1>
                    </div>
                   
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    {{-- <div class="col-12">
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item "><a href="{{ route('admin.brands.index') }}">Brands</a></li>
                                <li class="breadcrumb-item active"><a href="#">Edit Brands</a></li>
                            </ol>
                        </div> --}}
                        <!-- Default box -->
                        <form id="brandForm" action="{{ route('admin.brands.update', $brand) }}" method="post" enctype="multipart/form-data"  >
                            @csrf
                            @method('PUT')
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Edit Brand</h3>
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
                                                    placeholder="Enter Brand Name"
                                                    value="{{ old('name') ?? ($brand->name ?? '') }}" >
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
                                                    placeholder="Enter Brand Slug"
                                                    value="{{ old('slug') ?? ($brand->slug ?? '') }}" >
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
                                                    value="{{ old('image') }}" accept="image/*">
                                                <p id="fileInput_label">Dimensions : 110 * 80 px</p>

                                                @error('image')
                                                    <span class="error invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        {{-- @php
                                        dd(!$brand->file_name );
                                    @endphp --}}
                                        <div class="col-sm-6">
                                            <div class="position-relative">
                                                <img id="filePreview" style="height: 150px;"
                                                    src="{{ !$brand->file_name ? asset('admin-assets/img/image-placeholder.png') : asset('uploads/brand/' . $brand->file_name) }}"
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
                                                <input id="secondary_logo" name="secondary_logo" type="file"
                                                    class="form-control @error('secondary_logo') is-invalid @enderror"
                                                    value="{{ old('secondary_logo') }}" accept="image/*">
                                                <p id="secondary_logo_label">Dimensions: 150 * 110 px</p>

                                                @error('secondary_logo')
                                                    <span class="error invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        {{-- Preview --}}
                                        <div class="col-sm-6">
                                            <div class="position-relative">
                                                <img id="filePreview_secondary_logo" style="height: 150px;"
                                                    src="{{ !$brand->secondary_logo ? asset('admin-assets/img/image-placeholder.png') : asset('uploads/brand/secondary_logo/' . $brand->secondary_logo) }}"
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
                                                <p id="fileInputBanner_label">Dimensions: 1350 * 271 px</p>
                                                @error('banner')
                                                    <span class="error invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-sm-6 mt-1">
                                            <div class="position-relative">
                                                <img id="filePreviewBanner" style="height: 150px; display: none;"
                                                    src="{{ asset('uploads/brand/banner' . $brand->banner) }}"
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
                                            <div class="form-group">
                                                <label>Description</label>
                                                <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                                    placeholder="Enter Description..." id="editor1">{!! $brand->description !!}</textarea>
                                                @error('description')
                                                    <span class="error invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label>Meta Tag</label>
                                                <textarea name="meta_tag" class="form-control @error('meta_tag') is-invalid @enderror"
                                                    placeholder="Enter Meta Tag...">{{ old('meta_tag') ?? ($brand->meta_tag ?? '') }}</textarea>
                                                @error('meta_tag')
                                                    <span class="error invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label>Meta Description</label>
                                                <textarea name="meta_description" class="form-control @error('meta_description') is-invalid @enderror"
                                                    placeholder="Enter Meta Description...">{{ old('meta_description') ?? ($brand->meta_description ?? '') }}</textarea>
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
                                                        id="customSwitch1" {{ $brand->status == 1 ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="customSwitch1"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.card-body -->
                                <div class="card-footer">
                                    <button type="reset" class="btn btn-default">Cancel</button>
                                    <button type="submit" class="btn btn-info float-right">Save Changes</button>
                                </div>
                                <!-- /.card-footer -->
                                <!-- /.card -->
                        </form>
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
    
        $('#name').on('input', function() {
        var title = $(this).val();
        var slug = getSlug(title);
        $('#slug').val(slug);
    });
    </script>   
        <script>
            fileInput.onchange = evt => {
                const [file] = fileInput.files
                if (file) {
                    filePreview.src = URL.createObjectURL(file)
                }
            }

            $(document).ready(function() {
                $('#banner_type').on('change', function(e) {
                    var banner_type = e.target.value;
                    if (banner_type == 1) {
                        $('#dimensions').text('dimensions : 960 * 360 px');
                    } else if (banner_type == 2) {
                        $('#dimensions').text('dimensions : 960 * 240 px');
                    } else {
                        $('#dimensions').text('');
                    }
                });
            });
            // Preview for the image input
            // Preview for the banner image input
            document.getElementById('fileInputBanner').onchange = function(evt) {
                const filePreviewBanner = document.getElementById('filePreviewBanner');
                const [file] = evt.target.files;

                if (file) {
                    // Show the uploaded image
                    filePreviewBanner.src = URL.createObjectURL(file);
                    filePreviewBanner.style.display = 'block'; // Show the preview
                } else {
                    // If no file is selected, keep the existing banner or placeholder image
                    filePreviewBanner.style.display = 'none'; // Hide the preview if no file is selected
                }
            };

            // Display existing image on page load if the brand has one
            document.addEventListener("DOMContentLoaded", function() {
                const filePreviewBanner = document.getElementById('filePreviewBanner');
                const existingImagePath =
                    '{{ $brand->banner ? asset('uploads/brand/banner/' . $brand->banner) : asset('admin-assets/img/image-placeholder.png') }}';

                if (existingImagePath) {
                    filePreviewBanner.src = existingImagePath; // Set the existing image path
                    filePreviewBanner.style.display = 'block'; // Ensure it is visible
                }
            });
        </script>
        <script>
            document.getElementById('secondary_logo').onchange = function(evt) {
                const fileInput = evt.target;
                const filePreview = document.getElementById('filePreview_secondary_logo');
                const [file] = fileInput.files;

                if (file) {
                    filePreview.src = URL.createObjectURL(file);
                }
            };
        </script>
        <script>
            // CKEDITOR.replace('editor1');
        </script>
       
        
        <script>
            // Function to validate image dimensions
            function validateImageDimensions(file, expectedWidth, expectedHeight, fieldName) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onload = function(e) {
                const img = new Image();
                img.src = e.target.result;
                img.onload = function() {
                    if (this.width === expectedWidth && this.height === expectedHeight) {
                    resolve(true);
                    } else {
                    reject(`${fieldName} dimensions should be ${expectedWidth}x${expectedHeight} pixels. Current dimensions: ${this.width}x${this.height}`);
                    }
                };
                };
            });
            }

            // Helper function to update label with tick
            function updateLabel(labelId, dimensions) {
            $(`#${labelId}`).html(`Dimensions: ${dimensions} px <i class="fas fa-check text-success"></i>`);
            $(`#${labelId}`).css('color', 'black');
            }

            // Validate primary logo
            $('#fileInput').on('change', async function() {
            try {
                const file = this.files[0];
                if (file) {
                await validateImageDimensions(file, 110, 80, 'Primary logo');
                updateLabel('fileInput_label', '110 * 80');
                }
            } catch (error) {
                $(this).siblings('p').text(error).css('color', 'red');
                this.value = '';
                $('#filePreview').attr('src', "{{ asset('admin-assets/img/image-placeholder.png') }}");
            }
            });

            // Validate secondary logo
            $('#secondary_logo').on('change', async function() {
            try {
                const file = this.files[0];
                if (file) {
                await validateImageDimensions(file, 150, 110, 'Secondary logo');
                updateLabel('secondary_logo_label', '150 * 110');
                }
            } catch (error) {
                $(this).siblings('p').text(error).css('color', 'red');
                this.value = '';
                $('#filePreview_secondary_logo').attr('src', "{{ asset('admin-assets/img/image-placeholder.png') }}");
            }
            });

            // Validate banner
            $('#fileInputBanner').on('change', async function() {
            try {
                const file = this.files[0];
                if (file) {
                await validateImageDimensions(file, 1350, 271, 'Banner');
                updateLabel('fileInputBanner_label', '1350 * 271');
                }
            } catch (error) {
                $(this).siblings('p').text(error).css('color', 'red');
                this.value = '';
                $('#filePreviewBanner').attr('src', "{{ asset('admin-assets/img/image-placeholder.png') }}");
                $('#filePreviewBanner').hide();
            }
            });
        </script>
        <script>
            $(document).ready(function () {
                $('#brandForm').on('submit', function (e) {
                    e.preventDefault();
            
                    let formData = new FormData(this);
                    let submitBtn = $('#submitBtn');
                    let originalBtnText = submitBtn.html();
            
                    submitBtn.prop('disabled', true).html('Saving...');
            
                    $.ajax({
                        url: $(this).attr('action'),
                        method: $(this).attr('method'),
                        data: formData,
                        contentType: false,
                        processData: false,
                        headers: {
                            'X-CSRF-TOKEN': $('input[name="_token"]').val()
                        },
                        success: function (response) {
                            if (response.status === 'success') {
                                Swal.fire({
                                    title: 'Success!',
                                    text: response.message,
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href = "{{ route('admin.brands.index') }}";
                                    }
                                });
                            }
                        },
                        error: function (xhr) {
                            submitBtn.prop('disabled', false).html(originalBtnText);
            
                            if (xhr.status === 422 && xhr.responseJSON.errors) {
                                let errorMessages = '';
            
                                $.each(xhr.responseJSON.errors, function (field, messages) {
                                    let fieldName = field.replace(/_/g, ' ');
                                    errorMessages += `<p><strong>${fieldName}:</strong> ${messages[0]}</p>`;
                                });
            
                                Swal.fire({
                                    title: 'Validation Error',
                                    html: errorMessages,
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'An unexpected error occurred.',
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                                console.log("Ajax Error:", xhr.responseText);
                            }
                        }
                    });
                });
            });
            </script>
            
    @endpush
@endsection
