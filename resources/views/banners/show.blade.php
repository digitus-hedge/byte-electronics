@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header justify-content-between d-flex align-items-center">
                        <h4 class="card-title"> Banner Details</h4>
                    </div>

                    <div class="card-body pb-2">
                        <h2>{{ $banner->bannername }}</h2>
                        <p><strong>Type:</strong> {{ $banner->type }}</p>
                        <p><strong>Priority:</strong> {{ $banner->priority }}</p>
                        <p><strong>Status:</strong> {{ $banner->status ? 'Active' : 'Inactive' }}</p>
                        <p><strong>Page Name:</strong> {{ $banner->pagename }}</p>
                        @if ($banner->redirect_url)
                            <p><strong>Redirect URL:</strong> <a href="{{ $banner->redirect_url }}"
                                    target="_blank">{{ $banner->redirect_url }}</a></p>
                        @endif
                        <div class="banner-images">
                            @if ($banner->url1)
                                <div>
                                    <strong>Image 1:</strong>
                                    <img src="{{ Storage::url($banner->url1) }}" alt="Banner Image 1"
                                        style="max-width: 100%; height: auto;">
                                </div>
                            @endif

                            @if ($banner->url2)
                                <div>
                                    <strong>Image 2:</strong>
                                    <img src="{{ Storage::url($banner->url2) }}" alt="Banner Image 2"
                                        style="max-width: 100%; height: auto;">
                                </div>
                            @endif

                            @if ($banner->url3)
                                <div>
                                    <strong>Image 3:</strong>
                                    <img src="{{ Storage::url($banner->url3) }}" alt="Banner Image 3"
                                        style="max-width: 100%; height: auto;">
                                </div>
                            @endif
                        </div>
                    </div>
                    <a href="{{ route('admin.banners.index') }}" class="btn btn-primary">Back to List</a>
                </div>
            </div>
        </div>
@endsection
