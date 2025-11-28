@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    {{-- <h1>Create Product Attribute</h1> --}}

                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item "><a href="{{ route('admin.attribute_master.index') }}">Attributes</a></li>
                            <li class="breadcrumb-item active"><a href="#">Add Attribute</a></li>
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
                    <form action="{{ route('admin.attribute_master.store') }}" method="POST" id="attributeForm">
                        @csrf
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Add New Attribute</h3>
                                <div class="card-tools">
                                    <a href="{{ route('admin.attribute_master.index') }}" class="btn btn-secondary" title="Back">
                                        <i class="fas fa-arrow-left"></i> Back
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Name Field -->
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label for="name">Attribute Name</label>
                                            <input type="text" name="name" id="name"
                                                class="form-control @error('name') is-invalid @enderror"
                                                placeholder="Enter Attribute Name" value="{{ old('name') }}" required>
                                            @error('name')
                                            <span class="error invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Description Field -->
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label for="description">Description</label>
                                            <textarea name="description" id="description"
                                                class="form-control @error('description') is-invalid @enderror"
                                                placeholder="Enter Description">{{ old('description') }}</textarea>
                                            @error('description')
                                            <span class="error invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Status Field -->
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label for="Attributestatus">Status</label>
                                            <select name="Attributestatus" id="Attributestatus"
                                                class="form-control @error('status') is-invalid @enderror" required>
                                                <option value="1" selected>Active</option>
                                                <option value="0">Inactive</option>
                                            </select>
                                            @error('status')
                                            <span class="error invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.card-body -->
                            <div class="card-footer">
                                <button type="reset" class="btn btn-default">Cancel</button>
                                <button type="submit" class="btn btn-info float-right">Save</button>
                            </div>
                            <!-- /.card-footer -->
                        </div>
                        <!-- /.card -->
                    </form>
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->
</div>
@endsection
