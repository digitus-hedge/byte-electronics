@extends('layouts.app')

@section('content')
    <!-- Breadcrumb Section -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
            <li class="breadcrumb-item active" aria-current="page">Add New User</li>
        </ol>
    </nav>

    <!-- Card Section -->
    <div class="container mt-4">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Add New User</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.users.store') }}" id="createUserForm">
                    @csrf
                    <div class="form-group mb-3">
                        <label for="name">Name</label>
                        <input type="text" name="name" class="form-control" >
                    </div>
                    <div class="form-group mb-3">
                        <label for="email">Email</label>
                        <input type="email" name="email" class="form-control" >
                    </div>
                    <div class="form-group mb-3 position-relative">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" class="form-control">
                        <span toggle="#password" class="fa fa-fw fa-eye toggle-password"
                              style="position:absolute; right:10px; top:38px; cursor:pointer;"></span>
                    </div>
                    
                    <div class="form-group mb-3 position-relative">
                        <label for="password_confirmation">Confirm Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control">
                        <span toggle="#password_confirmation" class="fa fa-fw fa-eye toggle-password"
                              style="position:absolute; right:10px; top:38px; cursor:pointer;"></span>
                    </div>
                    <div class="form-group mb-3">
                        <label for="role">Role</label>
                        <select name="role_id" class="form-control">
                            <option value="">Select Role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success">Create User</button>
                </form>
            </div>
        </div>
    </div>
    @push('scripts')

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function () {
            $('#createUserForm').on('submit', function (e) {
                e.preventDefault();
                let form = $(this);
                let formData = new FormData(this);

                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message || 'User created successfully!',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = '{{ route("admin.users.index") }}';
                        });
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            let html = '<ul class="text-left">';
                            $.each(errors, function (key, value) {
                                html += '<li><strong>' + key.charAt(0).toUpperCase() + key.slice(1) + ':</strong> ' + value[0] + '</li>';
                            });
                            html += '</ul>';

                            Swal.fire({
                                icon: 'warning',
                                title: 'Validation Error!',
                                html: html
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'An unexpected error occurred.'
                            });
                        }
                    }
                });
            });
        });
    </script>
    <script>
        document.querySelectorAll(".toggle-password").forEach(function(eyeIcon) {
            eyeIcon.addEventListener("click", function() {
                let input = document.querySelector(this.getAttribute("toggle"));
                if (input.type === "password") {
                    input.type = "text";
                    this.classList.remove("fa-eye");
                    this.classList.add("fa-eye-slash");
                } else {
                    input.type = "password";
                    this.classList.remove("fa-eye-slash");
                    this.classList.add("fa-eye");
                }
            });
        });
        </script>
    @endpush
@endsection
