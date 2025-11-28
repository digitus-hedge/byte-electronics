<table class="table table-bordered table-hover" >
<thead class="thead-dark">
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Image</th>
        <th>Status</th>
        <th>Featured</th>
        <th>Actions</th>
    </tr>
</thead>
<tbody>
    @forelse($brands as $key => $brand)
        <tr>
            <td>{{ $key + $brands->firstItem() }}</td>
            <td>{{ $brand->name }}</td>
            <td>
                <img style="height: 70px;" src="{{ asset('uploads/brand/' . $brand->file_name) }}"
                     alt="Image" class="img-fluid">
            </td>
            <td>{{ $brand->status == 1 ? 'Active' : 'Disabled' }}</td>
            <td>
                @if($brand->featured == 1)
                    <i class="fas fa-star text-warning"></i>
                @else
                    <i class="far fa-star"></i>
                @endif
            </td>
            <td>
                <div class="btn-group">
                    <button type="button" class="btn btn-light">Action</button>
                    <button type="button"
                            class="btn btn-light dropdown-toggle dropdown-toggle-split"
                            data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="mdi mdi-chevron-down"></i>
                    </button>
                    <div class="dropdown-menu" role="menu">
                        <a class="dropdown-item"
                           href="{{ route('admin.brands.edit', $brand) }}">Edit</a>
                        <a class="dropdown-item add-featured"
                           href="#" data-id="{{ $brand->id }}" data-isFeatured="{{ $brand->featured }}">
                            {{ $brand->featured == 1 ? 'Remove from Featured' : 'Add to Featured' }}
                        </a>
                        <div class="dropdown-divider"></div>
                        <form action="{{ route('admin.brands.destroy', $brand) }}" method="post"
                              onsubmit="return confirm('Do you really want to delete the brand?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="dropdown-item">Delete</button>
                        </form>
                    </div>
                </div>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="6" class="text-center">No Data Found!</td>
        </tr>
    @endforelse
</tbody>
</table>