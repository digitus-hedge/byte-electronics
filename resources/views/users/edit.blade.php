@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit User</li>
            </ol>
        </nav>
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h3>Edit User: {{ $user->name }}</h3>
            </div>
            <div class="card-body">
                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.users.update', $user) }}" id="editUserForm">
                    @csrf
                    @method('PUT')

                    <div class="form-group mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $user->name) }}"
                            >
                        @error('name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user->email) }}"
                            >
                        @error('email')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="role" class="form-label">Role</label>
                        @if ($roles->isEmpty())
                            <p>No roles available.</p>
                        @else
                            <select name="role" id="role" class="form-control" required >
                                <option value="" {{ !$userRoles ? 'selected' : '' }}>Select a Role</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->name }}"
                                        {{ $userRoles && in_array($role->name, $userRoles) ? 'selected' : '' }}>
                                        {{ ucfirst($role->name) }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                        @error('role')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">Update User</button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
    @endsection
    @push('scripts')
    <script>
    $(document).ready(function () {
        $('#editUserForm').on('submit', function (e) {
            e.preventDefault();
    
            const form = $(this);
            const url = form.attr('action');
            const method = form.attr('method');
            const formData = new FormData(this);
            const submitBtn = form.find('button[type="submit"]');
            const originalBtnText = submitBtn.html();
   
            // Clear old errors
            $('.text-danger').html('');
            $('.form-control').removeClass('is-invalid');
    
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
    
            $.ajax({
                url: url,
                type: method,
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json'
                },
                success: function (response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'User Updated!',
                        text: response.message || 'User has been successfully updated.',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = response.redirect || "{{ route('admin.users.index') }}";
                    });
                },
                error: function (xhr) {
                    submitBtn.prop('disabled', false).html(originalBtnText);
    
                    if (xhr.status === 422 && xhr.responseJSON?.errors) {
                        const errors = xhr.responseJSON.errors;
                        $.each(errors, function (field, messages) {
                            $('#error-' + field).html(messages[0]);
                            $('[name="' + field + '"]').addClass('is-invalid');
                        });
    
                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            html: 'Please fix the highlighted fields.',
                            confirmButtonText: 'OK'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'An unexpected error occurred.',
                            confirmButtonText: 'OK'
                        });
                    }
                }
            });
        });
    });
    </script>
    @endpush
    