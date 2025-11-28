@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.banners.index') }}">Banners</a></li>
                        <li class="breadcrumb-item active"><a href="{{ route('admin.banners.edit', $banner) }}">Edit Banner</a></li>
                    </ol>
                </div>
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h4 class="card-title">Edit Banner</h4>
                    </div>
                    <div class="card-body pb-2">
                        @include('banners._form', [
                            'action' => route('admin.banners.update', $banner->id),
                            'method' => 'PUT',
                            'banner' => $banner,
                            'submitText' => 'Update Banner',
                        ])
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // Image sizes aligned with backend
            const imageSizes = {
                main_banner: {
                    image1: { width: 630, height: 530, label: 'Desktop (630 × 530 px)' },
                    image2: { width: 360, height: 300, label: 'Mobile (360 × 300 px)' },
                    image3: { width: 540, height: 450, label: 'Tablet (540 × 450 px)' }
                },
                secondary_banner_1: {
                    image1: { width: 320, height: 270, label: 'Desktop (320 × 270 px)' },
                    image2: { width: 180, height: 150, label: 'Mobile (180 × 150 px)' },
                    image3: { width: 260, height: 210, label: 'Tablet (260 × 210 px)' }
                },
                secondary_banner_2: {
                    image1: { width: 320, height: 270, label: 'Desktop (320 × 270 px)' },
                    image2: { width: 180, height: 150, label: 'Mobile (180 × 150 px)' },
                    image3: { width: 260, height: 210, label: 'Tablet (260 × 210 px)' }
                },
                secondary_banner_3: {
                    image1: { width: 320, height: 270, label: 'Desktop (320 × 270 px)' },
                    image2: { width: 180, height: 150, label: 'Mobile (180 × 150 px)' },
                    image3: { width: 260, height: 210, label: 'Tablet (260 × 210 px)' }
                },
                secondary_banner_4: {
                    image1: { width: 320, height: 270, label: 'Desktop (320 × 270 px)' },
                    image2: { width: 180, height: 150, label: 'Mobile (180 × 150 px)' },
                    image3: { width: 260, height: 210, label: 'Tablet (260 × 210 px)' }
                },
                blog_banner: {
                    image1: { width: 1600, height: 320, label: 'Desktop (1600 × 320 px)' },
                    image2: { width: 360, height: 300, label: 'Mobile (360 × 300 px)' },
                    image3: { width: 540, height: 450, label: 'Tablet (540 × 450 px)' }
                },
                category_banner: {
                    image1: { width: 600, height: 500, label: 'Desktop (600 × 500 px)' },
                    image2: { width: 450, height: 350, label: 'Mobile (450 × 350 px)' },
                    image3: { width: 600, height: 500, label: 'Tablet (600 × 500 px)' }
                },
                promotion_banner: {
                    image1: { width: 550, height: 450, label: 'Desktop (550 × 450 px)' },
                    image2: { width: 400, height: 320, label: 'Mobile (400 × 320 px)' },
                    image3: { width: 550, height: 450, label: 'Tablet (550 × 450 px)' }
                }
            };

            // Page-specific type options
            const typeOptions = {
                home: [
                    { value: 'main_banner', text: 'Main Banner' },
                    { value: 'secondary_banner_1', text: 'Secondary Banner 1' },
                    { value: 'secondary_banner_2', text: 'Secondary Banner 2' },
                    { value: 'secondary_banner_3', text: 'Secondary Banner 3' },
                    { value: 'secondary_banner_4', text: 'Secondary Banner 4' }
                ],
                Category: [
                    { value: 'category_banner', text: 'Category Banner' },
                    { value: 'promotion_banner', text: 'Promotion Banner' }
                ],
                blog: [
                    { value: 'blog_banner', text: 'Blog Banner' }
                ]
            };

            // Track image states
            let imageStates = {
                image1: { valid: false, existing: false, url: '' },
                image2: { valid: false, existing: false, url: '' },
                image3: { valid: false, existing: false, url: '' }
            };

            function validateImage(inputId, previewId, errorId, requiredWidth, requiredHeight) {
                const input = document.getElementById(inputId);
                const preview = document.getElementById(previewId);
                const error = document.getElementById(errorId);

                // Remove existing event listeners
                const newInput = input.cloneNode(true);
                input.parentNode.replaceChild(newInput, input);

                newInput.addEventListener('change', function(event) {
                    const file = event.target.files[0];
                    if (!file) {
                        // Revert to existing image if available
                        if (imageStates[inputId].existing) {
                            preview.src = imageStates[inputId].url;
                            preview.style.display = 'block';
                            error.style.display = 'none';
                            imageStates[inputId].valid = true;
                        } else {
                            preview.style.display = 'none';
                            error.style.display = 'none';
                            imageStates[inputId].valid = false;
                        }
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const img = new Image();
                        img.onload = function() {
                            if (img.width === requiredWidth && img.height === requiredHeight) {
                                preview.src = e.target.result;
                                preview.style.display = 'block';
                                error.style.display = 'none';
                                imageStates[inputId].valid = true;
                                imageStates[inputId].existing = false; // New image uploaded
                            } else {
                                preview.src = imageStates[inputId].existing ? imageStates[inputId].url : '';
                                preview.style.display = imageStates[inputId].existing ? 'block' : 'none';
                                error.style.display = 'block';
                                error.textContent = `Invalid dimensions. Required: ${requiredWidth} × ${requiredHeight} px.`;
                                newInput.value = ''; // Reset file input
                                imageStates[inputId].valid = imageStates[inputId].existing;
                            }
                        };
                        img.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                });
            }

            // Validate existing images against required dimensions
            function validateExistingImage(imageId, previewId, errorId, requiredWidth, requiredHeight) {
                const preview = document.getElementById(previewId);
                const error = document.getElementById(errorId);

                if (imageStates[imageId].existing) {
                    const img = new Image();
                    img.onload = function() {
                        if (img.width === requiredWidth && img.height === requiredHeight) {
                            preview.src = imageStates[imageId].url;
                            preview.style.display = 'block';
                            error.style.display = 'none';
                            imageStates[imageId].valid = true;
                        } else {
                            preview.style.display = 'none';
                            error.style.display = 'block';
                            error.textContent = `Existing image has invalid dimensions. Required: ${requiredWidth} × ${requiredHeight} px.`;
                            imageStates[imageId].valid = false;
                        }
                    };
                    img.src = imageStates[imageId].url;
                } else {
                    preview.style.display = 'none';
                    error.style.display = 'none';
                    imageStates[imageId].valid = false;
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                const pageNameSelect = document.getElementById('pagename');
                const typeSelect = document.getElementById('type');
                const image1Label = document.querySelector('label[for="image1"]');
                const image2Label = document.querySelector('label[for="image2"]');
                const image3Label = document.querySelector('label[for="image3"]');

                // Initialize image states for editing
                @if (!empty($banner))
                    imageStates.image1 = {
                        valid: {{ $banner->url1 ? 'true' : 'false' }},
                        existing: {{ $banner->url1 ? 'true' : 'false' }},
                        url: '{{ $banner->url1 ? asset('storage/' . $banner->url1) : '' }}'
                    };
                    imageStates.image2 = {
                        valid: {{ $banner->url2 ? 'true' : 'false' }},
                        existing: {{ $banner->url2 ? 'true' : 'false' }},
                        url: '{{ $banner->url2 ? asset('storage/' . $banner->url2) : '' }}'
                    };
                    imageStates.image3 = {
                        valid: {{ $banner->url3 ? 'true' : 'false' }},
                        existing: {{ $banner->url3 ? 'true' : 'false' }},
                        url: '{{ $banner->url3 ? asset('storage/' . $banner->url3) : '' }}'
                    };
                @endif

                function updateTypeOptions(selectedPage) {
                    typeSelect.innerHTML = ''; // Clear existing options
                    if (typeOptions[selectedPage]) {
                        typeOptions[selectedPage].forEach(option => {
                            const newOption = new Option(option.text, option.value);
                            typeSelect.add(newOption);
                        });
                    }
                    // Preserve selected type for editing
                    @if (!empty($banner))
                        typeSelect.value = '{{ old('type', $banner->type) }}';
                    @endif
                    updateImageLabels(typeSelect.value);
                }

                function updateImageLabels(selectedType) {
                    if (imageSizes[selectedType]) {
                        image1Label.innerHTML = imageSizes[selectedType].image1.label;
                        image2Label.innerHTML = imageSizes[selectedType].image2.label;
                        image3Label.innerHTML = imageSizes[selectedType].image3.label;

                        // Validate existing images and apply validation for new uploads
                        validateExistingImage('image1', 'imagePreview', 'errorImage1', imageSizes[selectedType].image1.width, imageSizes[selectedType].image1.height);
                        validateExistingImage('image2', 'imagePreview2', 'errorImage2', imageSizes[selectedType].image2.width, imageSizes[selectedType].image2.height);
                        validateExistingImage('image3', 'imagePreview3', 'errorImage3', imageSizes[selectedType].image3.width, imageSizes[selectedType].image3.height);

                        validateImage('image1', 'imagePreview', 'errorImage1', imageSizes[selectedType].image1.width, imageSizes[selectedType].image1.height);
                        validateImage('image2', 'imagePreview2', 'errorImage2', imageSizes[selectedType].image2.width, imageSizes[selectedType].image2.height);
                        validateImage('image3', 'imagePreview3', 'errorImage3', imageSizes[selectedType].image3.width, imageSizes[selectedType].image3.height);
                    }
                }

                pageNameSelect.addEventListener('change', function() {
                    updateTypeOptions(this.value);
                });

                typeSelect.addEventListener('change', function() {
                    updateImageLabels(this.value);
                });

                // Form submission with validation
                document.addEventListener('submit', function(e) {
                    if (e.target.matches('form')) {
                        e.preventDefault();

                        // Check for at least one valid image
                        if (!imageStates.image1.valid && !imageStates.image2.valid && !imageStates.image3.valid) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Please upload or retain at least one valid image with correct dimensions.'
                            });
                            return;
                        }

                        const formData = new FormData(e.target);
                        fetch(e.target.action, {
                            method: e.target.method,
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: data.message || 'Banner updated successfully!',
                                    showConfirmButton: false,
                                    timer: 1500
                                }).then(() => {
                                    window.location.href = '{{ route("admin.banners.index") }}';
                                });
                            } else {
                                let errorMessage = data.message || 'Something went wrong';
                                if (data.errors) {
                                    errorMessage = Object.values(data.errors).flat().join(' ');
                                }
                                throw new Error(errorMessage);
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: error.message || 'An error occurred while processing the banner.'
                            });
                        });
                    }
                });

                // Initialize
                updateTypeOptions(pageNameSelect.value);
            });
        </script>
    @endpush
@endsection