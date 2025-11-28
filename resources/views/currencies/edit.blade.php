@extends('layouts.app')

@section('content')

    <div class="container">
        <!-- Breadcrumbs & Back Button -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.currencies.index') }}">Currencies</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Currency</li>
                </ol>
            </nav>
            <a href="{{ route('admin.currencies.index') }}" class="btn btn-secondary">Back</a>
        </div>

        <!-- Card -->
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Edit Currency</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.currencies.update', $currency) }}" method="POST" id="editCurrencyForm">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="currency_code" class="form-label">Currency Code</label>
                        <input type="text" name="currency_code" id="currency_code" class="form-control"
                               value="{{ old('currency_code', $currency->currency_code) }}" >
                    </div>

                    <div class="mb-3">
                        <label for="currency_symbol" class="form-label">Currency Symbol
                        </label>
                        <input type="text" name="currency_symbol" id="currency_symbol" class="form-control"
                               value="{{ old('currency_symbol', $currency->currency_symbol) }}">
                    </div>

                    <div class="mb-3">
                        <label for="conversion_rate" class="form-label">Conversion Rate
                            @if($defaultCurrency)
                            <span class="text-muted"> (1 {{ $defaultCurrency->currency_code }} = ?) </span>
                        @endif
                        </label>
                        <input type="number" name="conversion_rate" id="conversion_rate" class="form-control"
                               value="{{ old('conversion_rate', $currency->conversion_rate) }}"  step="0.0001">
                    </div>

                    <!-- Status Switch -->
                    <div class="mb-3">
                        <label for="currency_status" class="form-label">Status</label>
                        <div class="d-flex align-items-center">
                        <div class="form-check form-switch">
                            <input type="hidden" name="currency_status" value="0">
                            <input class="form-check-input" type="checkbox" id="currency_status" name="currency_status" value="1"
                                   {{ old('status', $currency->status) ? 'checked' : '' }}>
                            {{-- <label class="form-check-label ms-2" for="status">Active</label> --}}
                        </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="is_default" class="form-label">Set as Default</label>
                        <div class="d-flex align-items-center">
                            <div class="form-check form-switch">
                                <input type="hidden" name="is_default" value="0">
                                <input class="form-check-input" type="checkbox" id="is_default" name="is_default" value="1"
                                    {{ old('is_default', $currency->is_default) ? 'checked' : '' }}>
                                {{-- <label class="form-check-label ms-2" for="is_default">Active</label> --}}
                            </div>
                        </div>
                    </div>
                   
                    {{-- <div class="form-check">
                    
                        <label class="form-check-label" for="status">Active</label>
                        
                        <div class="form-check form-switch">
                        <input type="hidden" name="status" value="0">
                        <input class="form-check-input" type="checkbox" role="switch" name="status" id="status" value="1" checked="">
                    </div>
                </div> --}}

                    <button type="submit" class="btn btn-success">Save</button>
                </form>

                <!-- Modal for updating conversion rates -->
                <div class="modal fade" id="conversionRateModal" tabindex="-1" aria-labelledby="conversionRateModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Update Conversion Rates</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>Update conversion rates for top 5 currencies based on the new default currency: {{$currency->currency_code}}</p>
                                <form id="conversionRateForm">
                                    <div id="currencyRates">
                                        <!-- Dynamically added currency rate fields -->
                                    </div>
                                    <button type="button" class="btn btn-primary" id="submitConversionRates">Update Rates</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
 <!-- SweetAlert + AJAX Script -->
 <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
 <script>
    document.getElementById('is_default').addEventListener('change', function(event) {
        let checkbox = this;
        let isChecked = checkbox.checked;
        let isCurrentlyDefault = {{ $currency->is_default ? 'true' : 'false' }};
        
        if (!isCurrentlyDefault && isChecked) {
            event.preventDefault();
            checkbox.checked = false;
    
            Swal.fire({
                title: 'Change Default Currency?',
                text: 'You are about to change the default currency.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Change Default',
                cancelButtonText: 'Cancel',
                showDenyButton: true,
                denyButtonText: 'Change Default & Update Conversion Rates'
            }).then((result) => {
                if (result.isConfirmed) {
                    checkbox.checked = true;
                } else if (result.isDenied) {
                    fetch("{{ url('/admin/getTopCurrencies', $currency->id) }}")
                        .then(response => response.json())
                        .then(data => {
                            let currencyRatesDiv = document.getElementById('currencyRates');
                            currencyRatesDiv.innerHTML = '';
                            data.forEach(currency => {
                                currencyRatesDiv.innerHTML += `
                                    <div class="mb-2">
                                        <label>${currency.currency_code}</label>
                                        <input type="number" class="form-control" name="conversion_rates[${currency.id}]" value="${currency.conversion_rate}" required step="0.0001">
                                    </div>`;
                            });
                            let modal = new bootstrap.Modal(document.getElementById('conversionRateModal'));
                            modal.show();
                        });
                }
            });
        }
    });
    
// Submit updated conversion rates
document.getElementById('submitConversionRates').addEventListener('click', function() {
    let form = document.getElementById('conversionRateForm');
    let formData = new FormData(form);
    
    fetch("{{ route('admin.updateConversionRates') }}", {
        method: "POST",
        body: formData,
        headers: {
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        }
    }).then(response => response.json())
      .then(data => {
          if (data.success) {
              Swal.fire('Updated!', 'Conversion rates updated successfully.', 'success');
              document.getElementById('is_default').checked = true;
              let modal = bootstrap.Modal.getInstance(document.getElementById('conversionRateModal'));
              modal.hide();
          }
      }).catch(error => {
          Swal.fire('Error!', 'Failed to update conversion rates.', 'error');
      });
});
</script>
 <script>
     document.getElementById('editCurrencyForm').addEventListener('submit', function(event) {
         event.preventDefault(); // Stop default form submission
         
         let form = this;
         let formData = new FormData(form);
        // Log form data for debugging
        formData.forEach((value, key) => {
            console.log(key + ': ' + value);
        });
         fetch(form.action, {
             method: 'POST',
             body: formData,
             headers: {
                 'X-Requested-With': 'XMLHttpRequest', // AJAX request
             }
         }).then(response => response.json())
           .then(data => {
               if (data.success) {
                   Swal.fire({
                       icon: 'success',
                       title: 'Updated!',
                       text: 'Currency updated successfully.',
                       confirmButtonText: 'OK'
                   }).then((result) => {
                    if (result.isConfirmed) {
                            window.location.href = "{{ route('admin.currencies.index') }}";
                        }
                   });
               } else {
                   Swal.fire({
                       icon: 'error',
                       title: 'Update Failed!',
                       text: data.message || 'Something went wrong!',
                   });
               }
           }).catch(error => {
               Swal.fire({
                   icon: 'error',
                   title: 'Error!',
                   text: 'Failed to update the currency.',
               });
           });
     });
 </script>
@endsection
