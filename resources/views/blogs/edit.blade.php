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
                    <form action="{{ isset($blog) ? route('admin.blogs.update', $blog) : route('admin.blogs.store') }}" method="POST" enctype="multipart/form-data" id="blogForm">
                        @csrf
                        @isset($blog) @method('PUT') @endisset
                    
                        <div class="row">
                            <div class="col-md-6">
                                <!-- Title -->
                                <div class="form-group">
                                    <label for="title" class="font-weight-bold">Title</label>
                                    <input type="text" name="title" id="title" class="form-control" value="{{ $blog->title ?? '' }}" >
                                </div>
                                @error('title')
                                    <small>{{ $message}}</small>
                                @enderror
                    
                                <!-- Slug -->
                                <div class="form-group">
                                    <label for="slug" class="font-weight-bold">Slug</label>
                                    <input type="text" name="slug" id="slug" class="form-control" value="{{ $blog->slug ?? '' }}" readonly>
                                </div>
                    
                                <!-- Author -->
                                <div class="form-group">
                                    <label for="author" class="font-weight-bold">Author</label>
                                    <input type="text" name="author" id="author" class="form-control" value="{{ $blog->author ?? '' }}">
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
                                        <input type="hidden" name="existing_featured_image" value="{{ $blog->featured_image }}">
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
                                    <input type="text" id="tags" name="tags[]" class="form-control" placeholder="Add tags" value="{{ $blog->tags ?? '' }}">
                                </div>
                    
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
                       <!-- Upload Type Selection --><!-- Upload Type Selection -->
<div class="form-group">
    <label class="font-weight-bold">Upload Type</label>
    <select id="upload_type" class="form-control">
        <option value="1" {{ isset($blog->image_1) && !$blog->image_2 ? 'selected' : '' }}>Upload 1 Image</option>
        <option value="4" {{ isset($blog->image_2) ? 'selected' : '' }}>Upload 4 Images</option>
    </select>
</div>

<!-- Image 1 -->
<div class="form-group">
    <label id="image1Label" for="image_1" class="font-weight-bold">
        Image 1 ({{ isset($blog->image_2) ? '718x420' : '1596x420' }})
    </label>
    <input type="file" name="image_1" id="image_1" class="form-control image-input" accept="image/*">
    
    @if(isset($blog->image_1))
        <img src="{{ asset('storage/' . $blog->image_1) }}" class="preview-image" width="200">
        <input type="hidden" name="existing_image_1" value="{{ $blog->image_1 }}">
    @endif
</div>

<!-- Additional images (shown if "Upload 4 Images" is selected) -->
<div id="multi-image-fields" style="{{ isset($blog->image_2) ? 'display:block;' : 'display:none;' }}">
    <div class="form-group">
        <label for="image_2" class="font-weight-bold">Image 2 (580x150)</label>
        <input type="file" name="image_2" id="image_2" class="form-control image-input" accept="image/*">
        @if(isset($blog->image_2))
            <img src="{{ asset('storage/' . $blog->image_2) }}" class="preview-image" width="200">
            <input type="hidden" name="existing_image_2" value="{{ $blog->image_2 }}">
        @endif
    </div>

    <div class="form-group">
        <label for="image_3" class="font-weight-bold">Image 3 (580x254)</label>
        <input type="file" name="image_3" id="image_3" class="form-control image-input" accept="image/*">
        @if(isset($blog->image_3))
            <img src="{{ asset('storage/' . $blog->image_3) }}" class="preview-image" width="200">
            <input type="hidden" name="existing_image_3" value="{{ $blog->image_3 }}">
        @endif
    </div>

    <div class="form-group">
        <label for="image_4" class="font-weight-bold">Image 4 (420x257)</label>
        <input type="file" name="image_4" id="image_4" class="form-control image-input" accept="image/*">
        @if(isset($blog->image_4))
            <img src="{{ asset('storage/' . $blog->image_4) }}" class="preview-image" width="200">
            <input type="hidden" name="existing_image_4" value="{{ $blog->image_4 }}">
        @endif
    </div>
</div>

<!-- Error message container -->
<span id="extraImagesError" class="text-danger"></span>


                    
                        <!-- Content Editor Card -->
                        <div class="card mt-3">
                            <div class="card-header bg-secondary text-white">
                                <h4 class="mb-0">Write Your Blog</h4>
                            </div>
                            <div class="card-body">
                                <div id="editor" style="height: 350px;">{!! $blog->content ?? '' !!}</div>
                                <input type="hidden" name="content" id="content">
                                 <!-- Enable Extra Images -->
                                 <!-- Extra Images Upload Section -->
                                 <!-- Image Upload Type Dropdown -->
<!-- Upload Type Selection -->

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
        document.addEventListener("DOMContentLoaded", function() {
            const uploadType = document.getElementById("upload_type");
        
            if (!uploadType) {
                console.error("Element #upload_type not found!");
                return;
            }
        
            const image1 = document.getElementById("image_1");
            const image2 = document.getElementById("image_2");
            const image3 = document.getElementById("image_3");
            const image4 = document.getElementById("image_4");
            const multiImageUpload = document.getElementById("multi-image-fields");
            const errorSpan = document.getElementById("extraImagesError");
        
            let expectedDimensions = {
                image_1: { width: 1596, height: 420 }, // Default for single image upload
                image_2: { width: 580, height: 150 },
                image_3: { width: 580, height: 254 },
                image_4: { width: 420, height: 257 }
            };
        
            function setUploadType() {
                if (uploadType.value === "1") {
                    multiImageUpload.style.display = "none";
                    expectedDimensions.image_1 = { width: 1596, height: 420 };
                    document.querySelector("label[for='image_1']").textContent = "Image 1 (1596x420)";
                } else {
                    multiImageUpload.style.display = "block";
                    expectedDimensions.image_1 = { width: 718, height: 420 };
                    document.querySelector("label[for='image_1']").textContent = "Image 1 (718x420)";
                }
            }
        
            async function validateImage(file, expectedWidth, expectedHeight) {
                return new Promise((resolve, reject) => {
                    const img = new Image();
                    img.src = URL.createObjectURL(file);
                    img.onload = () => {
                        URL.revokeObjectURL(img.src);
                        if (img.width === expectedWidth && img.height === expectedHeight) {
                            resolve();
                        } else {
                            reject(`Image must be ${expectedWidth}x${expectedHeight} pixels`);
                        }
                    };
                    img.onerror = () => reject('Error loading image');
                });
            }
        
            function handleFileValidation(event) {
                const input = event.target;
                const file = input.files[0];
                const { width, height } = expectedDimensions[input.id];
        
                if (file) {
                    validateImage(file, width, height)
                        .then(() => {
                            errorSpan.textContent = "";
                        })
                        .catch((error) => {
                            errorSpan.textContent = error;
                            input.value = ""; // Clear invalid input
                        });
                }
            }
        
            uploadType.addEventListener("change", setUploadType);
            document.querySelectorAll(".image-input").forEach(input => {
                input.addEventListener("change", handleFileValidation);
            });
        
            setUploadType();
        });
        </script>
        

<script>

    // Initialize Tagify on the input element
    var input = document.getElementById('tags');
    var tagify = new Tagify(input, {
       // whitelist: ["large", "small", "small2", "small3", "small4", "small5"], // Optional predefined tags
        dropdown: {
            maxItems: 5, // Show 5 suggestions at once
            enabled: 0   // Always show suggestions
        }
    });
</script>

    <script>
        $(document).ready(function() {
            $('#blogForm').on('submit', function(e) {
                e.preventDefault();
                
                let formData = new FormData(this);
                formData.append('content',quill.root.innerHTML);
                formData.append('content_1',quill.root.innerHTML);
              
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
                                text: response.error || 'Something went wrong!',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                    if (xhr.status === 422 && xhr.responseJSON.errors) {
                        let errorMessages = '';

                        $.each(xhr.responseJSON.errors, function (field, messages) {
                            let message = messages[0];

                            // Handle tags.0, tags.1, etc.
                            if (field.startsWith('tags.')) {
                                message = message.replace(/tags\.\d+/, 'tag');
                            }

                            // Optional: you can do similar for other complex keys here

                            errorMessages += `<p>${message}</p>`;
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
                            text: 'An error occurred while saving the blog.',
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

    

    // Set hidden input value on form submit
    document.querySelector('form').onsubmit = function() {
        document.querySelector('#content').value = quill.root.innerHTML;
    };

    // Auto-generate slug from title
    $('#title').on('input', function() {
        var title = $(this).val();
        var slug = getSlug(title);
        $('#slug').val(slug);
    });
</script>
@endsection
