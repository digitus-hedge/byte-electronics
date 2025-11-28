@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                {{-- <div class="col-sm-6">
                    <h1>Categories</h1>
                </div> --}}
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">Categories</a></li>
                        <li class="breadcrumb-item active"><a href="#">Edit Category</a></li>
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
                    <form action="{{ route('admin.categories.update',$category) }}" method="post"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Edit Category</h3>
                                <div class="card-tools">
                                    <a href="{{ url()->previous() }}" class="btn btn-secondary" title="Back">
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
                                                placeholder="Enter Category Name"

                                                value="{{ old('name') ?? $category->name ?? '' }}"  autofocus>
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
                                                placeholder="Enter Category Slug"
                                                value="{{ old('slug') ?? $category->slug ?? '' }}" autofocus>
                                            @error('slug')
                                            <span class="error invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>Description</label>
                                            <textarea name="description"
                                                class="form-control @error('description') is-invalid @enderror"
                                                placeholder="Enter Description..." id="editor1">{{ old('description') ?? $category->description ?? '' }}</textarea>
                                            @error('description')
                                            <span class="error invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>Meta Tag</label>
                                            <textarea name="meta_tag" class="form-control @error('meta_tag') is-invalid @enderror"
                                                      placeholder="Enter Meta Tag...">{{ old('meta_tag') ?? $category->meta_tag ?? '' }}</textarea>
                                            @error('meta_tag')
                                            <span class="error invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>Meta Description</label>
                                            <textarea name="meta_description" class="form-control @error('meta_description') is-invalid @enderror"
                                                      placeholder="Enter Meta Description...">{{ old('meta_description') ?? $category->meta_description ?? ''}}</textarea>
                                            @error('meta_description')
                                            <span class="error invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Image</label>
                                            <input id="fileInput" name="image" type="file"
                                                class="form-control @error('image') is-invalid @enderror" value="{{ old('image') }}" accept="image/*">
                                            <p>Dimensions : 110 * 80 px</p>
                                            @error('image')
                                            <span class="error invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="position-relative">
                                            <img id="filePreview" style="height: 150px;"
                                                src="{{ !$category->file_name ? asset('admin-assets/img/image-placeholder.png') : asset('uploads/category/'.$category->file_name) }}"
                                                alt="{{ $category->file_name }}" class="img-fluid">
                                            <div class="ribbon-wrapper ribbon-lg">
                                                <div class="ribbon bg-success text-lg">
                                                    Preview
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>Status</label>
                                            <div class="custom-control custom-switch">
                                                <input name="status" type="checkbox" class="custom-control-input"
                                                    id="customSwitch1" value="1"
                                                    {{ $category->status == 1 ? 'checked' : '' }}>
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
fileInput.onchange = evt => {
    const [file] = fileInput.files
    if (file) {
        filePreview.src = URL.createObjectURL(file)
    }
}
</script>
<script>
    CKEDITOR.replace( 'editor1' );
</script>

    <script>
         $('#name').on('input', function() {
        var title = $(this).val();
        var slug = getSlug(title);
        $('#slug').val(slug);
    });
    </script>
 <script>
    $(document).ready(function () {
        $('form').on('submit', function (e) {
            e.preventDefault();

            let form = $(this);
            let formData = new FormData(this);

            $.ajax({
                url: form.attr('action'),
                type: form.attr('method'), // PATCH/PUT
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Category has been updated successfully.',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = response.redirect_url;
                    });
                },
                error: function (xhr) {
                    if (xhr.status === 422 && xhr.responseJSON.errors) {
                        let messages = '';
                        $.each(xhr.responseJSON.errors, function (field, errors) {
                            messages += `• ${errors[0]}<br>`;
                        });

                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            html: messages,
                            confirmButtonText: 'OK'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Something went wrong. Please try again.',
                            confirmButtonText: 'OK'
                        });
                    }
                }
            });
        });
    });
</script>

    <script>
        $('#fileInput').change(function() {
            var file = this.files[0];
            var img = new Image();
            img.onload = function() {
                if (this.width != 110 || this.height != 80) {
                    $('#fileInput').addClass('is-invalid');
                    $('#fileInput').after('<span class="error invalid-feedback">Please upload an image that is exactly 500x500 pixels.</span>');
                    $('#fileInput').val('');
                    $('#filePreview').attr('src', '{{ asset("admin-assets/img/image-placeholder.png") }}');
                } else {
                    $('#fileInput').removeClass('is-invalid');
                    $('#fileInput').next('.error.invalid-feedback').remove();
                }
            };
            if (file) {
                img.src = URL.createObjectURL(file);
            }
        });
    </script>
@endpush
@endsection
