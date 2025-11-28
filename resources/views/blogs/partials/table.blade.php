<table class="table table-bordered table-striped">
    <thead class="thead-dark">
        <tr>
            <th scope="col">Title</th>
            <th scope="col">Category</th>
            <th scope="col">Status</th>
            <th scope="col">Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($blogs as $blog)
        <tr>
            <td style="max-width: 250px; word-wrap: break-word; white-space: normal;">{{ $blog->title }}</td>
            <td>{{ $blog->categories }}</td>
            <td class="text-capitalize">{{ $blog->status }}</td>
            
                <td>
                    <div class="d-flex flex-wrap gap-1">
                        <a href="{{ route('admin.blogs.edit', $blog) }}" class="btn btn-warning btn-sm">
                            ✏️ Edit
                        </a>
                        <button class="btn btn-danger btn-sm delete-btn" data-id="{{ $blog->id }}">
                            🗑️ Delete
                        </button>

                        <form id="delete-form-{{ $blog->id }}" action="{{ route('admin.blogs.destroy', $blog->id) }}" method="POST" class="d-none"
                            onsubmit="return confirm('Do you really want to delete the brand?');">
                            @csrf
                            @method('DELETE')
                        </form>


                    </div>
                   
                

                </td>

                
            
        </tr>
        @endforeach

    </tbody>
</table>