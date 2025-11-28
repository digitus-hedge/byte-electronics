@extends('layouts.app')

@section('content')
    <div class="container">
        <!-- Breadcrumbs & Back Button -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.currencies.index') }}">Currencies</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Add Currency</li>
                </ol>
            </nav>
            <a href="{{ route('admin.currencies.index') }}" class="btn btn-secondary">Back</a>
        </div>

        <!-- Card -->
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Add Currency</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.currencies.store') }}" method="POST" id="addCurrency">
                    @csrf

                    <!-- Country Selector -->
                    <div class="form-group mb-3">
                        <label for="country_id">Select Country</label>
                        <select name="country_id" id="country_id" class="form-control" >
                            <option value="">-- Select Country --</option>
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}">{{ $country->name }} ({{ $country->short_name }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="currency_code">Currency Code</label>
                        <input type="text" name="currency_code" id="currency_code" class="form-control" >
                    </div>

                    <div class="form-group mb-3">
                        <label for="currency_symbol">Currency Symbol</label>
                        <input type="text" name="currency_symbol" id="currency_symbol" class="form-control">
                    </div>

                    <div class="form-group mb-3">
                        <label for="conversion_rate">Conversion Rate
                            @if($defaultCurrency)
                                <span class="text-muted"> (1 {{ $defaultCurrency->currency_code }} = ?) </span>
                            @endif
                        </label>
                        <input type="number" name="conversion_rate" id="conversion_rate" class="form-control"  step="0.0001">
                    </div>

                    <div class="mb-3">
                        <label for="currency_status" class="form-label">Status</label>
                        <div class="d-flex align-items-center">
                            <div class="form-check form-switch">
                                <input type="hidden" name="currency_status" value="0">
                                <input class="form-check-input" type="checkbox" id="currency_status" name="currency_status" value="1">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="is_default" class="form-label">Set as Default</label>
                        <div class="d-flex align-items-center">
                            <div class="form-check form-switch">
                                <input type="hidden" name="is_default" value="0">
                                <input class="form-check-input" type="checkbox" id="is_default" name="is_default" value="1">
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success">Save</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const form = document.getElementById('addCurrency');
        
            form.addEventListener('submit', function(event) {
                event.preventDefault();
        
                const formData = new FormData(form);
        
                fetch(form.action, {
                    method: "POST",
                    body: formData,
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value
                    }
                })
                .then(async (response) => {
                    if (response.status === 422) {
                        const data = await response.json();
                        const errors = data.errors;
                        let errorMessage = "";
        
                        // Collect error messages
                        for (let field in errors) {
                            errorMessage += `<div> ${errors[field][0]}</div>`;
                        }
        
                        // Show validation errors
                        Swal.fire({
                            icon: "error",
                            title: "Validation Error",
                            html: errorMessage,
                            confirmButtonText: "OK"
                        });
                    } else {
                        return response.json();
                    }
                })
                .then(data => {
                    if (data && data.success) {
                        Swal.fire({
                            icon: "success",
                            title: "Success",
                            text: data.message || "Currency added successfully!",
                        }).then(() => {
                            window.location.href = data.redirect_url || "{{ route('admin.currencies.index') }}";
                        });
                    }
                })
                .catch(() => {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "Something went wrong. Please try again.",
                    });
                });
            });
        });
        </script>
        
@endsection
