<table class="table table-bordered table-hover">
    <thead class="thead-dark">
        <tr>
            <tr>
                <th style="width: 10px">#</th>
                <th>Image</th>
                <th>Banner Name</th>
                <th>Type</th>
                <th>page</th>
                <th>Priority</th>
                <th style="width: 40px">Action</th>
            </tr>
        </tr>
    </thead>
    <tbody>
        @forelse($banners as $key => $banner)
        <tr>
            <td>{{ $key + $banners->firstItem() }}</td>
            <td><img style="height: 70px;"
                    src="{{ asset('storage/' . $banner->url1) }}" alt="Image"
                    class="img-fluid">
            <td>{{ $banner->bannername }}</td>
            <td>
                @switch($banner->type)
                    @case('main_banner')
                        Main Banner
                        @break
                    @case('secondary_banner_1')
                        Secondary Banner 1
                        @break
                    @case('secondary_banner_2')
                        Secondary Banner 2
                        @break
                    @case('secondary_banner_3')
                        Secondary Banner 3
                        @break
                    @case('secondary_banner_4')
                        Secondary Banner 4
                        @break
                    @case('blog_banner')
                        Blog Banner 
                        @break
                    @default
                @endswitch
            </td>
            {{-- <td>{{ $banner->banner_type == 0 ? 'Primary' : 'Secondary' }}</td> --}}
            {{-- <td>{{ $banner->banner_type == 1 ? 'Main Banner' : ($banner->banner_type == 2 ? 'Sub Banner' : 'Single Slider Banner') }}</td> --}}
            <td>{{ $banner->pagename }}</td>
            <td>{{ $banner->priority }}</td>
            <td>
                <div class="btn-group">
                    <button type="button" class="btn btn-light">Action</button>
                    <button type="button"
                        class="btn btn-light dropdown-toggle dropdown-toggle-split"
                        data-bs-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="false" data-bs-display="static">
                        <i class="mdi mdi-chevron-down"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item"
                            href="{{ route('admin.banners.edit', $banner) }}">Edit</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="javascript:void(0);" onclick="deleteBanner({{ $banner->id }})">Delete</a>
                      
                    </div>
                </div>

            </td>
        </tr>
    @empty
        <tr>
            <td colspan="5" class="text-center">No Data Found!</td>
        </tr>
    @endforelse
    </tbody>
</table>