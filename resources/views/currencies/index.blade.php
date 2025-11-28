@extends('layouts.app')

@section('content')
    <div class="container">
        <!-- Breadcrumbs -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Currencies</li>
            </ol>
        </nav>

        <!-- Currency Card -->
        <div class="card shadow" style="--bs-card-spacer-y: 0rem !important;">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <span>List Of Currencies</span>

                <div class="d-flex align-items-center gap-2 ms-auto">
                    <!-- Search Bar -->
                   

                    <!-- Add New Button -->
                    <a href="{{ route('admin.currencies.create') }}" class="btn btn-sm btn-light text-primary shadow-sm">
                        <i class="fas fa-plus fa-sm"></i> Add New
                    </a>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered mb-0">
                        <thead >
                            <tr>
                                <th>Currency Code</th>
                                <th>Currency Symbol</th>
                                <th>Conversion Rate</th>
                                <th>Status</th>
                                <th>Default</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($currencies as $currency)
                                <tr>
                                    <td>{{ $currency->currency_code }}</td>
                                    <td>{{ $currency->currency_symbol }}</td>
                                    <td>{{ $currency->conversion_rate }}</td>
                                    <td>
                                        <span class="badge {{ $currency->status ? 'bg-success' : 'bg-danger' }}">
                                            {{ $currency->status ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $currency->is_default ? 'bg-primary' : 'bg-secondary' }}">
                                            {{ $currency->is_default ? 'Yes' : 'No' }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.currencies.edit', $currency) }}" class="btn btn-warning btn-sm">
                                            ✏️ Edit
                                        </a>
                                        <button class="btn btn-danger btn-sm delete-btn" data-id="{{ $currency->id }}">
                                            🗑️ Delete
                                        </button>
                                        <form id="delete-form-{{ $currency->id }}" action="{{ route('admin.currencies.destroy', $currency) }}" method="POST" class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No currencies found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Footer with Pagination -->
            <div class="card-footer text-muted">
                <div class="d-flex justify-content-between">
                    <div>
                        Total Currencies: {{ $currencies->total() }}
                    </div>
                    <div>
                        {{ $currencies->links('layouts.pagination') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SweetAlert + Delete Script -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Delete Confirmation
            document.querySelectorAll(".delete-btn").forEach(button => {
                button.addEventListener("click", function () {
                    const currencyId = this.getAttribute("data-id");
                    Swal.fire({
                        title: "Are you sure?",
                        text: "You won’t be able to revert this!",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#d33",
                        cancelButtonColor: "#3085d6",
                        confirmButtonText: "Yes, delete it!"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById(`delete-form-${currencyId}`).submit();
                        }
                    });
                });
            });

            // Flash Messages
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: '{{ session('success') }}',
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: '{{ session('error') }}',
                });
            @endif
        });
    </script>
@endsection
