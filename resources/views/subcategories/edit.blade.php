@extends('layouts.app')
@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                {{-- <div class="col-sm-6">
                    <h1>Sub Categories</h1>
                </div> --}}
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.subcategories.index') }}">Sub Categories</a></li>
                        <li class="breadcrumb-item active"><a href="#">Edit Sub Category</a></li>
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
                    <form id="editSubCategoryForm" action="{{ route('admin.subcategories.update',$subcategory) }}" method="post" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Edit Sub Category</h3>
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
                                                placeholder="Enter Sub Category Name"
                                                value="{{ old('name') ?? $subcategory->name ?? '' }}" required
                                                autofocus>
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
                                                placeholder="Enter Sub Category Slug"
                                                value="{{ old('slug') ?? $subcategory->slug ?? '' }}" required
                                                autofocus>
                                            @error('slug')
                                            <span class="error invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>Category</label>
                                            <select name="category_id" id="categoryDropdown"
                                                class="form-control @error('category_id') is-invalid @enderror">
                                                <option value="">Select Category</option>
                                                @foreach($categories as $category)
                                                <option value="{{ $category->id }}"
                                                    {{ $category->id == $subcategory->category_id ? 'selected' : '' }}>
                                                    {{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('category_id')
                                            <span class="error invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>Parent Subcategory</label>
                                            <select name="parent_id" id="parentSubcategoryDropdown" class="form-control @error('parent_id') is-invalid @enderror">
                                                <option value="">Select Parent Subcategory (Optional)</option>
                                                @foreach($subcategories as $subcategory1)
                                                 <option value="{{ $subcategory1->id }}"
                                                {{$subcategory1->id == $subcategory->parent_id ? 'selected' : ''}}>
                                                   {{ $subcategory1->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('parent_id')
                                            <span class="error invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>Description</label>
                                            <textarea name="description"
                                                class="form-control @error('description') is-invalid @enderror"
                                                placeholder="Enter Description...">{{ old('description') ?? $subcategory->description ?? '' }}</textarea>
                                            @error('description')
                                            <span class="error invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>Meta Tag</label>
                                            <textarea name="meta_tag" class="form-control @error('meta_tag') is-invalid @enderror"
                                                      placeholder="Enter Meta Tag...">{{ old('meta_tag') ?? $subcategory->meta_tag ?? '' }}</textarea>
                                            @error('meta_tag')
                                            <span class="error invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>Meta Description</label>
                                            <textarea name="meta_description" class="form-control @error('meta_description') is-invalid @enderror"
                                                      placeholder="Enter Meta Description...">{{ old('meta_description') ?? $subcategory->meta_description ?? ''}}</textarea>
                                            @error('meta_description')
                                            <span class="error invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Image</label>
                                            <input id="fileInput" name="image" type="file" accept="image/*" 
                                                class="form-control @error('image') is-invalid @enderror" value="{{ old('image') }}"
                                                data-default-file="{{ asset('public/' . basename($subcategory->image_sub_cat)) }}">
                                            <p>Required Dimensions: 110x80 px</p>
                                            <span id="fileInputError" class="text-danger"></span> <!-- Error message -->
                                            @error('image')
                                            <span class="error invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="position-relative">
                                            <img id="filePreview" style="height: 150px;"
                                                src="{{ !$subcategory->image_sub_cat ? asset('admin-assets/img/image-placeholder.png') : asset($subcategory->image_sub_cat) }}"
                                                alt="Image" class="img-fluid">
                                            <div class="ribbon-wrapper ribbon-lg">
                                                <div class="ribbon bg-success text-lg">
                                                    Preview
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Product Default Image</label>
                                            <input id="fileInput2" name="default_image" type="file"
                                                class="form-control @error('default_image') is-invalid @enderror" value="{{ old('default_image') }}" accept="image/*">
                                            <p>Required Dimensions: 800x800 px</p>
                                            <span id="fileInput2Error" class="text-danger"></span> <!-- Error message -->
                                            @error('default_image')
                                            <span class="error invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="position-relative">
                                            <img id="filePreview2" style="height: 150px;"
                                                src="{{ !$subcategory->product_default_sub_cat ? asset('admin-assets/img/image-placeholder.png') : asset($subcategory->product_default_sub_cat) }}"
                                                alt="Image" class="img-fluid">
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
                                                    {{ $subcategory->status == 1 ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="customSwitch1"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.card-body -->
                            <div class="card-footer">
                                <button type="reset" class="btn btn-default">Cancel</button>
                                <button id="submitFormBtn" type="submit" class="btn btn-info float-right">Save Changes</button>
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
    $(document).ready(function() {
        $('#categoryDropdown').on('change', function() {
            const categoryId = $(this).val();



            if (categoryId) {
                $.ajax({
                    url: `/admin/get-subcategories/${categoryId}`,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        console.log('here' , data);
                        $('#parentSubcategoryDropdown').empty().append('<option value="">Select Parent Subcategory (Optional)</option>');

                        data.forEach(function(subcategory) {
                            $('#parentSubcategoryDropdown').append(
                                `<option value="${subcategory.id}">${subcategory.name}</option>`
                            );
                        });
                    },
                    error: function() {
                        alert('Error fetching subcategories.');
                    }
                });
            } else {
                $('#parentSubcategoryDropdown').empty().append('<option value="">Select Parent Subcategory (Optional)</option>');
            }
        });
    });


</script>
<script>
    document.getElementById('submitFormBtn').addEventListener('click', function (e) {
        e.preventDefault();

        const form = document.getElementById('editSubCategoryForm');
        const formData = new FormData(form);

    

        fetch(form.action, {
            method: 'POST',
            body: formData,
            credentials: 'same-origin'
        })
        .then(async response => {
            if (response.status === 422) {
                const result = await response.json();

                let errorMessages = '';
                const errors = result.errors || {};

                // Clear previous highlights
                form.querySelectorAll('input, select, textarea').forEach(input => {
                    input.classList.remove('is-invalid');
                });

                for (const field in errors) {
                    errorMessages += `• ${errors[field][0]}<br>`;
                    const input = form.querySelector(`[name="${field}"]`);
                    if (input) {
                        input.classList.add('is-invalid');
                    }
                }

                Swal.fire({
                    title: 'Validation Error',
                    html: errorMessages,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            } else {
                return response.json();
            }
        })
        .then(data => {
            if (!data) return;

            if (data.success) {
                Swal.fire(
                    'Success!',
                    'Sub Category has been updated.',
                    'success'
                ).then(() => {
                    window.location.href = data.redirect_url || '{{ route("admin.subcategories.index") }}';
                });
            } else {
                Swal.fire(
                    'Error!',
                    data.message || 'Something went wrong.',
                    'error'
                );
            }
        })
        .catch(error => {
            Swal.fire(
                'Error!',
                'Something went wrong. Please try again later.',
                'error'
            );
        });
    });
</script>




<script>
      fileInput.onchange = evt => {
        const [file] = fileInput.files
        if (file) {
            filePreview.src = URL.createObjectURL(file)
        }
    }

    fileInput2.onchange = evt => {
        const [file] = fileInput2.files
        if (file) {
            filePreview2.src = URL.createObjectURL(file)
        }
    }
</script>
<script>
    // Image size validation
    $('#fileInput, #fileInput2').on('change', function() {
        const file = this.files[0];
        const maxSize = 2 * 1024 * 1024; // 2MB
        const input = $(this);
        const formGroup = input.closest('.form-group');
        
        if (file) {
            if (file.size > maxSize) {
                input.addClass('is-invalid');
                formGroup.find('.invalid-feedback').remove();
                formGroup.append('<span class="error invalid-feedback">Image size must be less than 2MB</span>');
                this.value = '';
                return;
            }

            const img = new Image();
            img.src = URL.createObjectURL(file);
            
            img.onload = function() {
                let isValid = false;
                let message = '';
                
                if (input.attr('id') === 'fileInput') {
                    isValid = (this.width === 110 && this.height === 80);
                    message = 'Image dimensions must be 110x80 pixels';
                    if (!isValid) {
                        $('#filePreview').hide();
                    }
                    else{
                        $('#filePreview').show();
                    }
                } else {
                    isValid = (this.width === 800 && this.height === 800);
                    message = 'Image dimensions must be 800x800 pixels';
                    if (!isValid) {
                        $('#filePreview2').hide();
                    }
                    else{
                        $('#filePreview2').show();
                    }
                }

                if (!isValid) {
                    input.addClass('is-invalid');
                    formGroup.find('.valid-feedback').remove();
                    formGroup.find('.invalid-feedback').remove();
                    formGroup.append(`<span class="error invalid-feedback">${message}</span>`);
                    // input[0].value = '';
                } else {
                    input.removeClass('is-invalid').addClass('is-valid');
                    formGroup.find('.invalid-feedback').remove();
                    formGroup.find('.valid-feedback').remove();
                    formGroup.append(`<span class="valid-feedback">Dimensions:${this.width} * ${this.height}px</span>`);
                    // input[0].value = '';
                }
            };
        }
    });
</script>
@endpush
@endsection
