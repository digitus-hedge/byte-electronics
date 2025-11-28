@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <!-- Breadcrumb -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.blogs.index') }}">Blogs</a></li>
                    <li class="breadcrumb-item active">{{ isset($blog) ? 'Edit Blog' : 'Create Blog' }}</li>
                </ol>
            </div>

            <!-- Card -->
            <div class="card mt-3">
                <div class="card-header justify-content-between d-flex align-items-center">
                    <h4 class="card-title">{{ isset($blog) ? 'Edit' : 'Create' }} Blog</h4>
                </div>

                <div class="card-body">
                    <form action="{{ isset($blog) ? route('admin.blogs.update', $blog) : route('admin.blogs.store') }}" method="POST" enctype="multipart/form-data" id="blogForm" >
                        @csrf
                        @isset($blog) @method('PUT') @endisset
                    
                        <div class="row">
                            <div class="col-md-6">
                                <!-- Title -->
                                <div class="form-group">
                                    <label for="title" class="font-weight-bold">Title</label>
                                    <input type="text" name="title" id="title" class="form-control" value="{{old( 'title',$blog->title ?? '') }}">
                                    @error('title')
                                        <small class="text-danger">{{ $message }}</small>        
                                    @enderror
                                </div>
                    
                                <!-- Slug -->
                                <div class="form-group">
                                    <label for="slug" class="font-weight-bold">Slug</label>
                                    <input type="text" name="slug" id="slug" class="form-control" value="{{ $blog->slug ?? '' }}" >
                                </div>
                    
                                <!-- Author -->
                                <div class="form-group">
                                    <label for="author" class="font-weight-bold">Author</label>
                                    <input type="text" name="author" id="author" class="form-control" value="{{old('author', $blog->author ?? '') }}">
                                </div>
                    
                                <!-- Publish Date -->
                                <div class="form-group">
                                    <label for="publish_date" class="font-weight-bold">Publish Date</label>
                                    <input type="date" name="publish_date" id="publish_date" class="form-control" value="{{ $blog->publish_date ?? '' }}">
                                </div>
                    
                                <!-- Summary -->
                                <div class="form-group">
                                    <label for="summary" class="font-weight-bold">Summary</label>
                                    <textarea name="summary" id="summary" class="form-control">{{ $blog->summary ?? '' }}</textarea>
                                </div>
                    
                                <!-- SEO Title -->
                                <div class="form-group">
                                    <label for="seo_title" class="font-weight-bold">SEO Title</label>
                                    <input type="text" name="seo_title" id="seo_title" class="form-control" value="{{ $blog->seo_title ?? '' }}">
                                </div>
                    
                                <!-- Meta Description -->
                                <div class="form-group">
                                    <label for="meta_description" class="font-weight-bold">Meta Description</label>
                                    <textarea name="meta_description" id="meta_description" class="form-control">{{ $blog->meta_description ?? '' }}</textarea>
                                </div>
                            </div>
                    
                            <div class="col-md-6">
                                <!-- Featured Image -->
                                <div class="form-group">
                                    <label for="featured_image" class="font-weight-bold">Featured Image</label>
                                    <input type="file" name="featured_image" id="featured_image" class="form-control" accept="image/*">
                                    @isset($blog->featured_image)
                                        <img src="{{ asset('storage/' . $blog->featured_image) }}" class="img-fluid mt-2 rounded shadow" style="max-width: 100px;">
                                    @endisset
                                </div>
                    
                                <!-- Categories -->
                                <div class="form-group">
                                    <label for="categories" class="font-weight-bold">Categories</label>
                                    <input type="text" name="categories" id="categories" class="form-control" value="{{ $blog->categories ?? '' }}">
                                </div>
                    
                                <!-- Tags -->
                                <div class="form-group">
                                    <label for="tags" class="font-weight-bold">Tags</label>
                                    <input type="text" id="tags" name="tags" class="form-control" placeholder="Add tags" value="{{ old('tags', $blog->tags ?? '') }}">
                                   

                                </div>
                                @error('tags')
                                <small class="text-danger">{{ $message }}</small>        
                            @enderror
                    
                                <!-- Social Sharing -->
                                <div class="form-group">
                                    <label for="social_sharing" class="font-weight-bold">Enable Social Sharing</label>
                                    <select name="social_sharing" id="social_sharing" class="form-control">
                                        <option value="1" {{ isset($blog) && $blog->social_sharing == 1 ? 'selected' : '' }}>Yes</option>
                                        <option value="0" {{ isset($blog) && $blog->social_sharing == 0 ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>
                    
                                <!-- Status Dropdown -->
                                <div class="form-group d-flex flex-column">
                                    <label for="blogstatus" class="font-weight-bold">Status</label>
                                    <select name="blogstatus" id="blogstatus" class="form-control w-100">
                                        <option value="draft" {{ isset($blog) && $blog->status == 'draft' ? 'selected' : '' }} >Draft</option>
                                        <option value="published" {{ isset($blog) && $blog->status == 'published' ? 'selected' : '' }}>Published</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    
                        <!-- Content Editor Card -->
                        <!-- Content Editor Card -->
                    <div class="card mt-3">
                        <div class="card-header bg-secondary text-white">
                            <h4 class="mb-0">Write Your Blog</h4>
                        </div>
                        <div class="card-body">
                            <!-- Quill Editor Container -->
                            <div id="editor" style="height: 350px;">{!! old('content', $blog->content ?? '') !!}</div>

                            <!-- Hidden input to hold Quill content -->
                            <input type="hidden" name="content" id="content">

                            <!-- Validation Error Message -->
                            @error('content')
                                <small class="text-danger d-block mt-2">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

    <div class="card-header bg-secondary text-white">
        <h4 class="mb-0">Upload Extra Images</h4>
    </div>
    <div class="card-body">
        <div class="form-group">
            <label class="font-weight-bold">Select Image Upload Type</label>
            <select id="upload_type" class="form-control">
                <option value="1">Upload 1 Image</option>
                <option value="4">Upload 4 Images</option>
            </select>
        </div>

        <!-- Image Upload Inputs -->
       
            <div class="form-group image-group">
                <label for="image_1" class="font-weight-bold" id="image1-label">Image 1 (1596x420)</label>
                <input type="file" name="image_1" id="image_1" class="form-control  image-input" accept="image/*">
            </div>
            <div id="multi-image-upload">
            <div class="form-group image-group image-4">
                <label for="image_2" class="font-weight-bold">Image 2 (580x150)</label>
                <input type="file" name="image_2" id="image_2" class="form-control  image-input" accept="image/*">
            </div>

            <div class="form-group image-group image-4">
                <label for="image_3" class="font-weight-bold">Image 3 (580x254)</label>
                <input type="file" name="image_3" id="image_3" class="form-control  image-input" accept="image/*">
            </div>

            <div class="form-group image-group image-4">
                <label for="image_4" class="font-weight-bold">Image 4 (420x257)</label>
                <input type="file" name="image_4" id="image_4" class="form-control  image-input" accept="image/*">
            </div>
        </div>

        <span id="extraImagesError" class="text-danger"></span>
    </div>
</div>
                                <div id="editor_1" style="height: 350px;">{!! $blog->content_1 ?? '' !!}</div>
                                <input type="hidden" name="content_1" id="content_1">
                            </div>
                        </div>
                    
                        <!-- Submit Button -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <button type="submit" class="btn btn-success btn-lg w-100">Save Blog</button>
                            </div>
                        </div>
                    </form>
                    
                </div>
            </div>

            
        </div>
    </div>
</div>

<!-- Quill & jQuery Scripts -->
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/tagify/4.17.5/tagify.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/speakingurl/14.0.1/speakingurl.min.js"></script>
<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>

    <!-- JavaScript Validation -->
    

<script>
    // Initialize Quill Editor
    var quill = new Quill('#editor', {
        theme: 'snow',
        placeholder: 'Write something...',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                [{ 'indent': '-1'}, { 'indent': '+1' }],
                [{ 'align': [] }],
                [{ 'color': [] }, { 'background': [] }],
                ['link', 'image', 'video'],
                ['clean']
            ]
        }
    });

    var quill2 = new Quill('#editor_1', {
        theme: 'snow',
        placeholder: 'Write something...',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                [{ 'indent': '-1'}, { 'indent': '+1' }],
                [{ 'align': [] }],
                [{ 'color': [] }, { 'background': [] }],
                ['link', 'image', 'video'],
                ['clean']
            ]
        }
    });
 
    // Auto-generate slug from title
    $('#title').on('input', function() {
        var title = $(this).val();
        var slug = getSlug(title);
        $('#slug').val(slug);
    });
    

</script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const tagInput = document.querySelector('#tags');
        if (tagInput) {
            const tagify = new Tagify(tagInput);
            tagInput.tagify = tagify; // Store Tagify instance on the input for later use
        }
    });
</script>

<script>
    $(document).ready(function() {
    // Initialize Tagify
    const tagInput = document.querySelector('#tags');
    let tagify = null;
    
    if (tagInput) {
        tagify = new Tagify(tagInput, {
            originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join(',')
        });
    }

    $('#blogForm').on('submit', function(e) {
        e.preventDefault();
        
        let formData = new FormData(this);
        
        // Get Quill content
        formData.set('content', quill.root.innerHTML);
        formData.set('content_1', quill2.root.innerHTML);
        
        // Handle tags properly
        if (tagify) {
            // Remove the original tags input
            formData.delete('tags');
            
            // Add each tag as a separate entry
            const tags = tagify.value.map(tag => tag.value);
            tags.forEach((tag, index) => {
                formData.append(`tags[${index}]`, tag);
            });
        }

        $.ajax({
            url: $(this).attr('action'),
            type: $(this).attr('method'),
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('input[name="_token"]').val()
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Blog saved successfully!',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "{{ route('admin.blogs.index') }}";
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: response.error,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function(xhr, status, error) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    let errorMessages = '';

                    Object.keys(errors).forEach(function(field) {
                        let label = field.replace(/\..*/, '');
                        label = label.charAt(0).toUpperCase() + label.slice(1).replace('_', ' ');
                        errorMessages += `<p><strong>${label}:</strong> ${errors[field][0]}</p>`;
                    });

                    Swal.fire({
                        title: 'Validation Error!',
                        html: errorMessages,
                        icon: 'warning',
                        confirmButtonText: 'Fix Errors'
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: 'An unexpected error occurred while saving the blog.',
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



    


   
    
@endsection
