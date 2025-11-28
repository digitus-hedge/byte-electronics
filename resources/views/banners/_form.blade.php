<form method="POST" action="{{ isset($banner) ? route('admin.banners.update', $banner->id) : route('admin.banners.store') }}" enctype="multipart/form-data">
    @csrf
    @if (isset($banner))
        @method('PUT')
    @endif
    <div class="row">
        <!-- Banner Name, Page Name, Type -->
        <div class="col-md-6">
            <label class="form-label" for="bannername">Banner Name:</label>
            <div class="col-md-12">
                <input class="form-control" type="text" name="bannername" id="bannername" value="{{ old('bannername', $banner->bannername ?? '') }}">
            </div>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="pagename">Page Name:</label>
            <div class="col-md-12">
                <select class="form-control" name="pagename" id="pagename">
                    <option value="home" {{ old('pagename', $banner->pagename ?? '') == 'home' ? 'selected' : '' }}>Home</option>
                    <option value="Category" {{ old('pagename', $banner->pagename ?? '') == 'Category' ? 'selected' : '' }}>Category</option>
                    <option value="blog" {{ old('pagename', $banner->pagename ?? '') == 'blog' ? 'selected' : '' }}>Blog</option>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="type">Type:</label>
            <div class="col-md-12">
                <select class="form-control" name="type" id="type" >
                    <option value="main_banner" {{ old('type', $banner->type ?? '') == 'main_banner' ? 'selected' : '' }}>Main Banner</option>
                    <option value="secondary_banner_1" {{ old('type', $banner->type ?? '') == 'secondary_banner_1' ? 'selected' : '' }}>Secondary Banner 1</option>
                    <option value="secondary_banner_2" {{ old('type', $banner->type ?? '') == 'secondary_banner_2' ? 'selected' : '' }}>Secondary Banner 2</option>
                    <option value="secondary_banner_3" {{ old('type', $banner->type ?? '') == 'secondary_banner_3' ? 'selected' : '' }}>Secondary Banner 3</option>
                    <option value="secondary_banner_4" {{ old('type', $banner->type ?? '') == 'secondary_banner_4' ? 'selected' : '' }}>Secondary Banner 4</option>
                    <option value="promotion_banner" {{ old('type', $banner->type ?? '') == 'promotion_banner' ? 'selected' : '' }}>Promotion Banner</option>
                    <option value="category_banner" {{ old('type', $banner->type ?? '') == 'category_banner' ? 'selected' : '' }}>Category Banner</option>
                    <option value="blog_banner" {{ old('type', $banner->type ?? '') == 'blog_banner' ? 'selected' : '' }}>Blog Banner</option>
                </select>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <!-- Priority, Status -->
        <div class="col-md-6">
            <label class="form-label" for="priority">Priority:</label>
            <div class="col-md-12">
                <input class="form-control" type="number" name="priority" id="priority" value="{{ old('priority', $banner->priority ?? '') }}">
            </div>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="status">Status:</label>
            <div class="col-md-12">
                <div class="form-check form-switch">
                    <input type="hidden" name="status" value="0">
                    <input class="form-check-input" type="checkbox" role="switch" name="status" id="status" value="1" {{ old('status', isset($banner) ? $banner->status : 1) == 1 ? 'checked' : '' }}>
                    <label class="form-check-label" for="status">Active</label>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <!-- Redirect URL -->
        <div class="col-md-6">
            <label class="form-label" for="redirect_url">Redirect URL:</label>
            <div class="col-md-12">
                <input class="form-control" type="text" name="redirect_url" id="redirect_url" value="{{ old('redirect_url', $banner->redirect_url ?? '') }}">
            </div>
        </div>
    </div>

    <!-- Image Inputs -->
    <div class="row mt-3">
        <div class="col-md-6">
            <label class="form-label" for="image1">Desktop:</label>
            <input class="form-control" type="file" name="image1" id="image1" accept="image/*">
            <small id="errorImage1" style="color: red; display: none;"></small>
            @if (isset($banner) && $banner->url1)
                <p class="mt-2">Current Image: <a href="{{ asset('storage/' . $banner->url1) }}" target="_blank">{{ basename($banner->url1) }}</a></p>
                <input type="hidden" name="existing_image1" value="{{ $banner->url1 }}">
            @endif
        </div>
        <div class="col-md-6" id="previewContainer">
            <img id="imagePreview" src="{{ isset($banner) && $banner->url1 ? asset('storage/' . $banner->url1) : '' }}" alt="Image Preview" style="max-width: 50%; height: auto; {{ isset($banner) && $banner->url1 ? '' : 'display: none;' }}">
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-6">
            <label class="form-label" for="image2">Mobile:</label>
            <input class="form-control" type="file" name="image2" id="image2" accept="image/*">
            <small id="errorImage2" style="color: red; display: none;"></small>
            @if (isset($banner) && $banner->url2)
                <p class="mt-2">Current Image: <a href="{{ asset('storage/' . $banner->url2) }}" target="_blank">{{ basename($banner->url2) }}</a></p>
                <input type="hidden" name="existing_image2" value="{{ $banner->url2 }}">
            @endif
        </div>
        <div class="col-md-6" id="previewContainer2">
            <img id="imagePreview2" src="{{ isset($banner) && $banner->url2 ? asset('storage/' . $banner->url2) : '' }}" alt="Image Preview" style="max-width: 50%; height: auto; {{ isset($banner) && $banner->url2 ? '' : 'display: none;' }}">
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-6">
            <label class="form-label" for="image3">Tablet:</label>
            <input class="form-control" type="file" name="image3" id="image3" accept="image/*">
            <small id="errorImage3" style="color: red; display: none;"></small>
            @if (isset($banner) && $banner->url3)
                <p class="mt-2">Current Image: <a href="{{ asset('storage/' . $banner->url3) }}" target="_blank">{{ basename($banner->url3) }}</a></p>
                <input type="hidden" name="existing_image3" value="{{ $banner->url3 }}">
            @endif
        </div>
        <div class="col-md-6" id="previewContainer3">
            <img id="imagePreview3" src="{{ isset($banner) && $banner->url3 ? asset('storage/' . $banner->url3) : '' }}" alt="Image Preview" style="max-width: 50%; height: auto; {{ isset($banner) && $banner->url3 ? '' : 'display: none;' }}">
        </div>
    </div>

    <!-- Submit Button -->
    <div class="col-md-12 mt-3">
        <button class="btn btn-primary w-md" style="float: right;" type="submit">{{ $submitText ?? 'Save' }}</button>
    </div>
</form>