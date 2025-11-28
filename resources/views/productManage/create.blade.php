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
                        <li class="breadcrumb-item active"><a href="#">Add New product</a></li>
                    </ol>
                </div>
                <div id="addproduct-accordion" class="custom-accordion">
                    <div class="card">
                        <a href="#addproduct-billinginfo-collapse" class="text-reset" data-bs-toggle="collapse"  aria-expanded="true" aria-controls="addproduct-billinginfo-collapse">
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
                                        <h5 class="font-size-16 mb-1">Product Details</h5>
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

                        <div id="addproduct-billinginfo-collapse" class="collapse show" data-bs-parent="#addproduct-accordion" style="">
                            <div class="p-4 border-top">
                                <form id="productForm" action="{{ route('admin.productManage.store') }}" method="POST" >
                                    @csrf
                                    <div class="row">
                                    <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="name">Product Name</label>
                                        <input id="name" name="name" placeholder="Enter Product Name" type="text" class="form-control">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="manufacturers_no">Manufacture Number</label>
                                        <input id="manufacturers_no" name="manufacturers_no" placeholder="Enter manufacture number" type="text" class="form-control">
                                    </div>
                                    </div>

                                        <div class="col-lg-4">

                                            <div class="mb-3">
                                                <label class="form-label" for="slug">Slug</label>
                                                <input id="slug" name="slug" placeholder="Enter slug" type="text" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label class="form-label">Category</label>
                                                {{-- <div class="col-md-10"> --}}
                                                    <select id="category_id" name="category_id" class="form-control @error('category_id') is-invalid @enderror">
                                                        <option value="">Select Category</option>
                                                        @foreach($categories as $category)
                                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                        @endforeach
                                                     </select>
                                                     @error('category_id')
                                                      <span class="error invalid-feedback">{{ $message }}</span>
                                                     @enderror
                                                {{-- </div> --}}
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label class="form-label">Sub-Category</label>
                                                {{-- <div class="col-md-10"> --}}
                                                    <select id="sub_category_id" name="sub_category_id" class="form-control select2 @error('sub_category_id') is-invalid @enderror">
                                                        <option value="">Select Sub Category</option>
                                                     </select>
                                                     @error('sub_category_id')
                                                      <span class="error invalid-feedback">{{ $message }}</span>
                                                     @enderror
                                                {{-- </div> --}}
                                            </div>
                                        </div>
                                        
                                    </div>
                                    <div class="row">
                                        
                                        
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label class="col-md-2 col-form-label">Brand</label>
                                                {{-- <div class="col-md-10"> --}}
                                                    <select id="brand_id" name="brand_id" class="form-control @error('brand_id') is-invalid @enderror">
                                                        <option value="">Select Brand</option>
                                                        @foreach($brands as $brand)
                                                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                                        @endforeach
                                                     </select>
                                                     @error('brand_id')
                                                      <span class="error invalid-feedback">{{ $message }}</span>
                                                     @enderror
                                                {{-- </div> --}}
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label for="tag" class="col-form-label">Tags</label>
                                               
                                                <input type="text" id="tag" name="tag[]" class="form-control" placeholder="Add tags">
                                            </div>
                                        </div>
                                    </div>
                                  
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h4 class="card-title mb-0">Pricing Details</h4>
                                                <div style="width: 120px">
                                                    {{-- <select id="currency" name="currency" class="form-select form-select-sm">
                                                        <option value="AED-AED" selected>AED-AED</option>
                                                        <option value="USD-$">USD-$</option>
                                                    </select> --}}
                                                    <select id="currency" name="currency_id" class="form-select form-select-sm">
                                                    @foreach($currencies as $currency)
                                                    <option value="{{ $currency->id }}" 
                                                        {{ $currency->is_default ? 'selected' : '' }}>
                                                        {{ $currency->currency_code }} - {{ $currency->currency_symbol }}
                                                    </option>
                                                @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div id="pricing-rows">
                                                <!-- Initial Row -->
                                                
                                                <div class="row pricing-row align-items-end">
                                                    
                                                    <div class="col-lg-1">
                                                        <div class="mb-3">
                                                            <label class="form-label">#</label>
                                                            <span class="serial-number">1</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <div class="mb-3">
                                                            <label class="form-label" for="unit_name_1">Unit Name</label>
                                                            <input id="unit_name_1" name="unit_name[]" placeholder="Enter Unit Name" type="text" class="form-control" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <div class="mb-3">
                                                            <label class="form-label" for="quantity_1">Quantity</label>
                                                            <input id="quantity_1" name="quantity[]" placeholder="Enter Quantity" type="number" step="1" max="1" min="1" class="form-control" required oninput="validateNumberInput(this);">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <div class="mb-3">
                                                            <label class="form-label" for="unit_price_1">Unit Price</label>
                                                            <input id="unit_price_1" name="unit_price[]" placeholder="Enter Unit Price" type="text" step="0.01" min="0" class="form-control" required oninput="validateDecimalInput(this);">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <div class="mb-3">
                                                            <label class="form-label" for="bulk_price_1">Bulk Price</label>
                                                            <input id="bulk_price_1" name="bulk_price[]" placeholder="Enter Bulk Price" type="text" step="0.01" min="0" class="form-control" required oninput="validateDecimalInput(this);">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-3 d-flex align-items-center">
                                                        <button type="button" class="btn btn-success btn-add" onclick="addRow()" style="margin:7%;">+</button> 
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h4 class="card-title">Product Attributes</h4>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-lg-4">
                                                            <div class="mb-3">
                                                                <label class="form-label">Attribute Heading</label>
                                                                <div class="d-flex">
                                                                    <select id="attribute_heading" name="attribute_heading" class="form-control">
                                                                        <option value="">Select Heading</option>
                                                                        @foreach($attributeHeadings as $heading)
                                                                            <option value="{{ $heading->id }}">{{ $heading->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                    <button type="button" class="btn btn-sm btn-primary ms-2" data-bs-toggle="modal" data-bs-target="#addHeadingModal">
                                                                        Add
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-4">
                                                            <div class="mb-3">
                                                                <label class="form-label">Attribute Name</label>
                                                                <div class="d-flex">
                                                                    <select id="attribute_name" name="attribute_name" class="form-control">
                                                                        <option value="">Select Attribute</option>
                                                                    </select>
                                                                    <button type="button" class="btn btn-sm btn-primary ms-2" data-bs-toggle="modal" data-bs-target="#addAttributeModal">
                                                                        Add
                                                                    </button>
                                                                </div>
                                                                <!-- Hidden Value Type Label -->
                                                                <small id="value_type_label" class="text-muted mt-1 d-none"></small>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="col-lg-4">
                                                            <div class="mb-3">
                                                                <label class="form-label">Attribute Value</label>
                                                                <div id="attribute_value_container">
                                                                    <input type="text" id="attribute_value" name="attribute_value" class="form-control" placeholder="Enter Value">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <button type="button" class="btn btn-primary" id="add_attribute">
                                                                <i class="bx bx-plus me-1"></i> Add Attribute
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-3">
                                                        <div class="col-12">
                                                            <div class="table-responsive">
                                                                <table class="table table-bordered" id="attributes_table">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>#</th>
                                                                            <th>Heading</th>
                                                                            <th>Attribute</th>
                                                                            <th>Value</th>
                                                                            <th>Action</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Modal start --}}
                                    <!-- Add New Heading Modal -->
                        <div class="modal fade" id="addHeadingModal" tabindex="-1" aria-labelledby="addHeadingModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Add New Heading</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="text" id="new_heading" class="form-control" placeholder="Enter Heading">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="button" class="btn btn-primary" id="saveHeading">Save</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Add New Attribute Modal -->
                        <div class="modal fade" id="addAttributeModal" tabindex="-1" aria-labelledby="addAttributeModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Add New Attribute</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <select id="attribute_heading_select" class="form-control">
                                            <option value="">Select Heading</option>
                                        </select>
                                        
                                        <input type="text" id="new_attribute" class="form-control mt-2" placeholder="Enter Attribute">
                                    
                                        <!-- Value Type Dropdown -->
                                        <select id="attribute_value_type" class="form-control mt-2">
                                            <option value="">Select Value Type</option>
                                            <option value="Text">Text</option>
                                            <option value="Image">Image</option>
                                            <option value="Document">Document</option>
                                        </select>
                                    </div>
                                    
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="button" class="btn btn-primary" id="saveAttribute">Save</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                                    {{-- modal end --}}

                                    <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label>Image</label>
                                            <input id="fileInput" name="image" type="file"
                                                class="form-control @error('image') is-invalid @enderror"
                                                value="{{ old('image') }}" accept="image/*">
                                            <p>Dimensions : 792 x 729 px</p>
                                            @error('image')
                                            <span class="error invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="position-relative" style="border-style: solid;border-color: #efefef;">
                                            <img id="filePreview" style="height: 150px;"
                                                src="{{ asset('admin-assets/img/image-placeholder.png') }}" alt="Image"
                                                class="img-fluid">
                                            <div class="ribbon-wrapper ribbon-lg">
                                                <div class="ribbon bg-success text-lg">
                                                    Preview
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    </div>
                                    <div class="card">
                                        <div class="card-header justify-content-between d-flex align-items-center">
                                            <h4 class="card-title">Meta-Title</h4>
                                        </div><!-- end card header -->
                                    <div class="card-body">
                                        <input id="meta_title" name="meta_title" placeholder="Enter meta-title" type="text" class="form-control">
                                        </div>
                                    </div>
                                    <div class="card">
                                        <div class="card-header justify-content-between d-flex align-items-center">
                                            <h4 class="card-title">Meta-Description</h4>
                                        </div><!-- end card header -->
                                    <div class="card-body">
                                        <div id="meta-description-container" style="height: 100px;">
                                        </div>
                                    </div>
                                    </div>


                                    <div class="card">
                                        <div class="card-header justify-content-between d-flex align-items-center">
                                            <h4 class="card-title">Product Description</h4>
                                        </div><!-- end card header -->
                                        <div class="card-body">
                                         <div id="description-container" style="height: 150px;">
                                        </div>
                                    </div>
                                    </div>
                                    <div class="card">
                                        <div class="card-header justify-content-between d-flex align-items-center">
                                            <h4 class="card-title">Product More-Description</h4>
                                        </div><!-- end card header -->
                                    <div class="card-body">
                                        <div id="more-description-container" style="height: 100px;">
                                        </div>
                                    </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label>Status</label>
                                                <div class="custom-control custom-switch">
                                                    <input name="status" type="hidden" value="0">
                                                    <input name="status" type="checkbox" class="custom-control-input"
                                                        id="status" value="1" checked>
                                                    <label class="custom-control-label" for="status"></label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label>Repairable ?</label>
                                                <div class="custom-control custom-switch">
                                                    <input name="is_repairable" type="hidden" value="0">
                                                    <input name="is_repairable" type="checkbox" class="custom-control-input"
                                                        id="is_repairable" value="1" checked>
                                                    <label class="custom-control-label" for="is_repairable"></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label>Featured ?</label>
                                                <div class="custom-control custom-switch">
                                                    <input name="featured" type="hidden" value="0">
                                                    <input name="featured" type="checkbox" class="custom-control-input"
                                                        id="featured" value="1">
                                                    <label class="custom-control-label" for="featured"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-4">
                                        <div class="col text-end">
                                            <a href="#" class="btn btn-danger" type="reset" id="reset"> <i class="bx bx-x me-1"></i> Cancel </a>
                                            <a href="#" class="btn btn-success" type="submit" id="submit"> <i class=" bx bx-file me-1"></i> Save </a>
                                        </div> <!-- end col -->
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tagify/4.17.5/tagify.min.js"></script>
<script src="{{asset('assets/libs/quill/quill.min.js')}}"></script>
<script src="{{asset('assets/libs/choices.js/public/assets/scripts/choices.min.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/speakingurl/14.0.1/speakingurl.min.js"></script>
<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    toggleAddButton();
});
   
  let rowCount = 1; // Counter for dynamic IDs

// Function to validate number input (for quantity)
function validateNumberInput(input) {
    input.value = input.value.replace(/[^0-9]/g, ''); // Allow only digits
}

// Function to validate decimal input (for prices)
function validateDecimalInput(input) {
    input.value = input.value.replace(/[^0-9.]/g, ''); // Allow digits and one decimal point
    const decimalCount = (input.value.match(/\./g) || []).length;
    if (decimalCount > 1) {
        input.value = input.value.slice(0, -1); // Remove extra decimal points
    }
}

// Function to add a new row
function addRow() {
    rowCount++;
    const newRow = `
        <div class="row pricing-row align-items-end mt-3">
            <div class="col-lg-1">
                <div class="mb-3">
                    <label class="form-label">#</label>
                    <span class="serial-number">${rowCount}</span>
                </div>
            </div>
            <div class="col-lg-2">
                <div class="mb-3">
                    <label class="form-label" for="unit_name_${rowCount}">Unit Name</label>
                    <input id="unit_name_${rowCount}" name="unit_name[]" placeholder="Enter Unit Name" type="text" class="form-control" required>
                </div>
            </div>
            <div class="col-lg-2">
                <div class="mb-3">
                    <label class="form-label" for="quantity_${rowCount}">Quantity</label>
                    <input id="quantity_${rowCount}" name="quantity[]" placeholder="Enter Quantity" type="number" step="1" min="1" class="form-control" required oninput="validateNumberInput(this);">
                </div>
            </div>
            <div class="col-lg-2">
                <div class="mb-3">
                    <label class="form-label" for="unit_price_${rowCount}">Unit Price</label>
                    <input id="unit_price_${rowCount}" name="unit_price[]" placeholder="Enter Unit Price" type="text" step="0.01" min="0" class="form-control" required oninput="validateDecimalInput(this);">
                </div>
            </div>
            <div class="col-lg-2">
                <div class="mb-3">
                    <label class="form-label" for="bulk_price_${rowCount}">Bulk Price</label>
                    <input id="bulk_price_${rowCount}" name="bulk_price[]" placeholder="Enter Bulk Price" type="text" step="0.01" min="0" class="form-control" required oninput="validateDecimalInput(this);">
                </div>
            </div>
            <div class="col-lg-3 d-flex align-items-center">
                <button type="button" class="btn btn-danger btn-remove" onclick="removeRow(this)" style="margin:7%;">-</button>
                <button type="button" class="btn btn-success btn-add" onclick="addRow()" style="margin:7%;">+</button>
            </div>
        </div>
    `;
    document.getElementById('pricing-rows').insertAdjacentHTML('beforeend', newRow);
    updateSerialNumbers();
    toggleAddButton(); // Ensure the new row's add button is disabled if empty
}

// Function to remove a row
function removeRow(button) {
    const row = button.closest('.pricing-row');
    row.remove();
    updateSerialNumbers();
    toggleAddButton(); // Update the state of the remaining rows' add buttons
}

// Function to update serial numbers
function updateSerialNumbers() {
    const rows = document.querySelectorAll('.pricing-row');
    rows.forEach((row, index) => {
        row.querySelector('.serial-number').textContent = index + 1;
    });
}

// Function to toggle the add button based on row content
// Function to toggle the add button based on row content
function toggleAddButton() {
    const rows = document.querySelectorAll('.pricing-row');
    rows.forEach((row) => {
        const inputs = row.querySelectorAll('input');
        const addButton = row.querySelector('.btn-add');

        if (addButton) {
            // Check if all inputs in the row are filled
            const isFilled = Array.from(inputs).every(input => input.value.trim() !== '');
            addButton.disabled = !isFilled; // Disable the button if any field is empty
        }
    });
}

// Event listeners for input fields to toggle add button
document.addEventListener('input', function (event) {
    if (event.target.matches('.pricing-row input')) {
        toggleAddButton();
    }
});

// Function to remove empty last row on submit
function removeEmptyLastRow() {
    const rows = document.querySelectorAll('.pricing-row');
    const lastRow = rows[rows.length - 1];
    const inputs = lastRow.querySelectorAll('input');
    const isLastRowEmpty = Array.from(inputs).every(input => input.value.trim() === '');
    if (isLastRowEmpty) {
        lastRow.remove();
    }
}

// Form submission handler
document.getElementById('productForm').addEventListener('submit', function (event) {
    removeEmptyLastRow();
    // Proceed with form submission
});
</script>
<script>
    $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
$(document).ready(function () {
    // Load attributes when heading changes
    $('#attribute_heading').change(function () {
        let headingId = $(this).val();
        $('#attribute_name').html('<option value="">Select Attribute</option>');
        if (headingId) {
            $.ajax({
                url: '/admin/get-attributes/' + headingId,
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    $.each(response, function (index, attribute) {
                        console.log(attribute);
                        $('#attribute_name').append(`<option value="${attribute.id}" data-value-type="${attribute.value_type}">${attribute.name}</option>`);
                    });
                },
                error: function () {
                    Swal.fire("Error!", "Failed to load attributes.", "error");
                }
            });
        }
    });
});
    
    $('#attribute_name').change(function () {
        console.log("change");
        let selectedOption = $(this).find(':selected');
        let valueType = selectedOption.data('value-type');

       
        console.log("data type",selectedOption.attr('data-value-type'));
        if (valueType) {
            $('#value_type_label').text(`Value Type:${valueType.charAt(0).toUpperCase() + valueType.slice(1)}`);
            $('#value_type_label').removeClass('d-none');
        } else {
            $('#value_type_label').addClass('d-none').text('');
        }

          // Update input field based on value type
          let inputField = '';
          console.log()
        if (valueType === 'Text') {
            inputField = `<input type="text" data-value_type="text" id="attribute_value" name="attribute_value" class="form-control" placeholder="Enter Value">`;
        } else if (valueType === 'Image') {
            inputField = `<input type="file" data-value_type="image" id="attribute_value" name="attribute_value" class="form-control" accept="image/*">`;
        } else if (valueType === 'Document') {
            inputField = `<input type="file" data-value_type="document" id="attribute_value" name="attribute_value" class="form-control" accept=".pdf,.doc,.docx,.xls,.xlsx">`;
        } else {
            inputField = `<input type="text" data-value_type="other" id="attribute_value" name="attribute_value" class="form-control" placeholder="Enter Value">`;
        }

        // Replace input field
        $('#attribute_value_container').html(inputField);
    });

    
    // $('#attribute_name').change(function () {

    //     let selectedAttributeId = $(this).val();

    //     if (selectedAttributeId) {

    //         $.ajax({
    //             url: '/admin/get-attribute/' + selectedAttributeId,
    //             type: 'GET',
    //             headers: {
    //                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //             },
    //             success: function (response) {
    //                 $('#value_type_label').text(`Value Type: ${response.value_type}`).removeClass('d-none');
    //             },
    //             error: function () {
    //                 Swal.fire("Error!", "Failed to load attribute.", "error");
    //             }
    //         });
    //     } else {
    //         $('#value_type_label').addClass('d-none');
    //     }
    // });

    // Save new heading
    $('#saveHeading').click(function () {
    let headingName = $('#new_heading').val();

    if (headingName) {
        $.ajax({
            url: '/admin/add-heading',
            type: 'POST',
            data: {
                name: headingName,
                _token: $('meta[name="csrf-token"]').attr('content') // Ensure token is in the request
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Ensure token is in headers
            },
            success: function (data) {
                $('#attribute_heading').append(`<option value="${data.id}">${data.name}</option>`);
                $('#addHeadingModal').modal('hide');
                $('#new_heading').val('');
                Swal.fire("Success!", "New heading added successfully.", "success");
            },
            error: function (xhr) {
                let errorMsg = xhr.responseJSON?.message || "Failed to add heading.";
                Swal.fire("Error!", errorMsg, "error");
            }
        });
    } else {
        Swal.fire("Warning!", "Please enter a heading name.", "warning");
    }
});

$('#attribute_heading').change(function () {
    let selectedHeadingId = $(this).val();
    let selectedHeadingText = $("#attribute_heading option:selected").text();

    if (selectedHeadingId) {
        // Check if the option already exists in #attribute_heading_select
        if ($('#attribute_heading_select option[value="' + selectedHeadingId + '"]').length === 0) {
            // Append the new option if it doesn't exist
            $('#attribute_heading_select').append(`<option value="${selectedHeadingId}">${selectedHeadingText}</option>`);
        }

        // Set the selected option
        $('#attribute_heading_select').val(selectedHeadingId);
    }
});


    // Save new attribute
    $('#saveAttribute').click(function () {
    let headingId = $('#attribute_heading_select').val(); // Get selected heading
    let attributeName = $('#new_attribute').val();
    let attributeValueType = $('#attribute_value_type').val();

    if (!headingId) {
        Swal.fire("Warning!", "Please select a heading first.", "warning");
        return;
    }

    if (attributeName) {
        $.ajax({
            url: '/admin/add-attribute',
            type: 'POST',
            data: {
                heading_id: headingId,
                name: attributeName,
                value_type: attributeValueType,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (data) {
                // Append new attribute to the attributes dropdown
                $('#attribute_name').append(`<option value="${data.id}">${data.name}</option>`);

                $('#addAttributeModal').modal('hide');
                $('#new_attribute').val('');
                Swal.fire("Success!", "New attribute added successfully.", "success");
                // Load all attributes for this heading
                $.ajax({
                    url: '/admin/get-attributes/' + headingId, 
                    type: 'GET',
                    success: function(response) {
                        // Clear existing options
                        $('#attribute_name').empty().append('<option value="">Select Attribute</option>');
                        
                        // Add new options and select the newly created one
                        $.each(response, function(index, attribute) {
                            $('#attribute_name').append(`<option value="${attribute.id}" data-value-type="${attribute.value_type}">${attribute.name}</option>`);
                            
                            // Select the newly created attribute
                            if(attribute.id === data.id) {
                                $('#attribute_name').val(data.id);
                                // Trigger change to update value type label
                                $('#attribute_name').trigger('change');
                            }
                        });
                    },
                    error: function() {
                        Swal.fire("Error!", "Failed to reload attributes.", "error"); 
                    }
                });
            },
            error: function (xhr) {
                let errorMsg = xhr.responseJSON?.message || "Failed to add attribute.";
                Swal.fire("Error!", errorMsg, "error");
            }
        });
    } else {
        Swal.fire("Warning!", "Please enter an attribute name.", "warning");
    }
});


    // Add attribute row
    $(document).ready(function () {
    let attributeIndex = 1; // Serial number counter

    $('#add_attribute').click(function () {
        let heading = $('#attribute_heading option:selected').text();
        let headingId = $('#attribute_heading option:selected').val();
        let attribute = $('#attribute_name option:selected').text();
        let attributeId = $('#attribute_name option:selected').val();
        let valueType = $('#value_type_label').text().replace('Value Type:', '').trim().toLowerCase();
        let value = $('#attribute_value').val();
        let fileInput = $('#attribute_value')[0];
        let displayValue = '';

        if (!heading || !attribute || !valueType) {
            Swal.fire("Warning!", "Please fill all fields before adding.", "warning");
            return;
        }
        console.log("change:",valueType);
        // Handle display of value based on type
            let hiddenFileInput = ''; // Hidden file input field

            // Handle value display & hidden input for files
            
    if (valueType === 'image' || valueType === 'document') {
        if (fileInput.files.length > 0) {
            let file = fileInput.files[0];
            let reader = new FileReader();
            reader.onload = function (e) {
                displayValue = (valueType === 'image') 
                    ? `<img src="${e.target.result}" alt="Preview" width="50" height="50">` 
                    : file.name; // Show document name for file
                 appendRow();
            };

            fileClone = $(fileInput).clone();
            fileClone.attr("name", `attributes[${attributeIndex-1}][file]`);
            fileClone.addClass("hidden-file-input").hide();
            reader.readAsDataURL(file);

            // ✅ Clone file input and update its name
           
        } else {
            Swal.fire("Warning!", "File is required for image/document.", "warning");
            return;
        }
    } else {
        displayValue = value;
        appendRow();
    }


        

        function appendRow() {
                    let newRow = $(`
                        <tr>
                            <td>${attributeIndex}</td>
                            <td data-heading_id="${headingId}">${heading}</td>
                            <td data-attribute_id="${attributeId}">${attribute}</td>
                            <td data-value_type="${valueType}">
                                ${displayValue}
                               <!-- <input type="file" name="attributes[${attributeIndex-1}][file]" class="hidden-file-input" style="display:none;" /> -->
                            </td>
                            <td>
                                <button class="btn btn-danger btn-sm remove-row">Remove</button>
                            </td>
                        </tr>
                    `);

            $('#attributes_table tbody').append(newRow);
            attributeIndex++;
            // ✅ Copy the file to the hidden input
           if((valueType === 'image' || valueType === 'document')){
            if (fileClone) {
            newRow.find('td:eq(3)').append(fileClone);
                }
            else
            {
                console.log("No file selected");
            }
        }

            $('#attribute_value').val('');
            Swal.fire("Success!", "Attribute added successfully.", "success");
        } 
        

    });
// <td>${valueType.charAt(0).toUpperCase() + valueType.slice(1)}</td>
    // Remove attribute row and update serial numbers
    $(document).on('click', '.remove-row', function () {
        $(this).closest('tr').remove();
        Swal.fire("Deleted!", "Attribute removed successfully.", "success");
        updateSerialNumbers();
    });

    function updateSerialNumbers() {
        attributeIndex = 1;
        $('#attributes_table tbody tr').each(function () {
            $(this).find('td:first').text(attributeIndex);
            let fileInput = $(this).find('.hidden-file-input');
            if (fileInput.length) {
                fileInput.attr("name", `attributes[${attributeIndex - 1}][file]`);
            }

            attributeIndex++;
        });
    }

});

</script>
<script>
    fileInput.onchange = evt => {
        const [file] = fileInput.files
        if (file) {
            filePreview.src = URL.createObjectURL(file)
        }
    }
</script>

<script>
    $('#name').on('input', function() {
   var title = $(this).val();
   var slug = getSlug(title);
   $('#slug').val(slug);
});
</script>

 <script>
    // Initialize the Quill editors
    var descriptionQuill = new Quill('#description-container', {
        theme: 'snow',
        placeholder: 'Compose your content here...',
        modules: {
            toolbar: [
                [ { 'header': '2' }, { 'font': [] }],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                ['bold', 'italic', 'underline'],
                [{ 'align': [] }],
                ['link']
            ]
        }
    });
</script>
<script>
    // Initialize the Quill editor
    var moreQuill = new Quill('#more-description-container', {
        theme: 'snow',
        placeholder: 'Compose your content here...',
        modules: {
            toolbar: [
                [ { 'header': '2' }, { 'font': [] }],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                ['bold', 'italic', 'underline'],
                [{ 'align': [] }],
                ['link']
            ]
        }
    });
</script>
<script>
    // Initialize the Quill editor
    var metaQuill = new Quill('#meta-description-container', {
        theme: 'snow',
        placeholder: 'Compose your content here...',
        modules: {
            toolbar: [
                [ { 'header': '2' }, { 'font': [] }],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                ['bold', 'italic', 'underline'],
                [{ 'align': [] }],
                ['link']
            ]
        }
    });
</script>
<script>
    // $(document).ready(function() {
    //     $('#tag-select').select2({
    //         multiple:true,
    //         tags: true,
    //         placeholder: "Select or add tags",
    //         allowClear: true
    //     });
    // });
</script>
<script>

    // Initialize Tagify on the input element
    var input = document.getElementById('tag');
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
    $('#category_id').on('change', function() {
        const categoryId = $(this).val();
        const subCategoryDropdown = $('#sub_category_id');

        // Clear existing options
        subCategoryDropdown.empty();
        subCategoryDropdown.append('<option value="">Select Sub Category</option>');

        if (categoryId) {
            $.ajax({
                url: `/admin/get-subcategories/${categoryId}`, // Update with your actual route
                type: 'GET',
                dataType: 'json',
                
                success: function(data) {
                    if (data.length > 0) {
                        data.forEach(function(subCategory) {
                            subCategoryDropdown.append(
                                `<option value="${subCategory.id}">${subCategory.name}</option>`
                            );
                        });
                    } else {
                        subCategoryDropdown.append('<option value="">No Sub Categories Available</option>');
                    }
                },
                error: function() {
                    alert('Error fetching subcategories. Please try again.');
                }
            });
        }
    });
});
</script>
{{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
<script>
    $(document).ready(function () {
        $('#submit').on('click', function (event) {
            event.preventDefault(); // Prevent default form submission
    
            const form = $('#productForm');
            const formData = new FormData(form[0]); // Get form data
    
            // Append Quill editor data
            formData.append('description', descriptionQuill.root.innerHTML);
            formData.append('more_description', moreQuill.root.innerHTML);
            formData.append('meta_description', metaQuill.root.innerHTML);
    
            // Fetch attribute data
            $('#attributes_table tbody tr').each(function (index, row) {
                let $row = $(row);
                let valueType = $row.find('td:eq(3)').data('value_type');
                let valueInput = $row.find('td:eq(3) input').val() || $row.find('td:eq(3)').text().trim();
                let fileInput = $row.find('.hidden-file-input')[0]; // Select hidden file input
    
                if ((valueType === 'image' || valueType === 'document') && fileInput && fileInput.files.length > 0) {
                    let file = fileInput.files[0];
                    formData.append(`attributes[${index}][file]`, file); // Append file
                    valueInput = file.name; // Update value
                }
    
                // Append each attribute field separately
                formData.append(`attributes[${index}][heading]`, $row.find('td:eq(1)').text().trim());
                formData.append(`attributes[${index}][attribute]`, $row.find('td:eq(2)').text().trim());
                formData.append(`attributes[${index}][heading_id]`, $row.find('td:eq(1)').data('heading_id'));
                formData.append(`attributes[${index}][attribute_id]`, $row.find('td:eq(2)').data('attribute_id'));
                formData.append(`attributes[${index}][value_type]`, valueType);
                formData.append(`attributes[${index}][value]`, valueInput);
            });
    
            // Fetch pricing details
            $('.pricing-row').each(function (index, row) {
                let $row = $(row);
                formData.append(`pricing[${index}][unit_name]`, $row.find('input[name="unit_name[]"]').val());
                formData.append(`pricing[${index}][quantity]`, $row.find('input[name="quantity[]"]').val());
                formData.append(`pricing[${index}][unit_price]`, $row.find('input[name="unit_price[]"]').val());
                formData.append(`pricing[${index}][bulk_price]`, $row.find('input[name="bulk_price[]"]').val());
            });
    
            // Remove unused form data
            formData.delete('attribute_heading');
            formData.delete('attribute_name');
            formData.delete('attribute_value');
            formData.delete('unit_name');
            formData.delete('quantity');
            formData.delete('unit_price');
            formData.delete('bulk_price');
    
            // Log form data for debugging
            for (var pair of formData.entries()) {
                console.log(pair[0] + ', ' + pair[1]);
            }
    
            // Submit form data via AJAX
            $.ajax({
                url: "{{ route('admin.productManage.store') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    Swal.fire({
                        title: 'Success!',
                        text: response.message || 'Product created successfully!',
                        icon: 'success',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(function() {
                        window.location.href = "{{ route('admin.productManage.index') }}";
                    });
                },
                error: function (xhr) {
                    console.error('Error:', xhr.responseJSON);
                    Swal.fire({
                        title: 'Error!',
                        text: xhr.responseJSON?.message || 'Something went wrong!',
                        icon: 'error',
                        confirmButtonText: 'Ok'
                    });
                }
            });
        });
    });
    </script>

@endpush
