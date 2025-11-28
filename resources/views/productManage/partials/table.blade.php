<div class="table-responsive">
    <table class="table table-striped table-bordered mb-0">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
               <th>Product</th>
               <th>Quantity</th>
               <th>Email</th>
               <th>Contact Number</th>
                <th>Status</th>
            
            </tr>
        </thead>
        <tbody>
            @forelse($rfqs as $key => $rfq)
                <tr>
                    <td>{{ $key + $rfqs->firstItem() }}</td>
                    <td>{{ $rfq->user->name?? 'Null' }}</td>
                    <td>{{ $rfq->product->name }}</td>
                    <td>{{ $rfq->quantity }}</td>
                    <td>{{ $rfq->email?? 'Null' }}</td>
                    <td>{{ $rfq->phone ?? 'Null'}}</td>
                    <td>{{ $rfq->status==1 ? 'Active' : 'Inactive' }}</td>
                    {{-- <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-light btn-sm">Action</button>
                            <button type="button" class="btn btn-light btn-sm dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="mdi mdi-chevron-down"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('admin.productManage.edit', $product) }}">Edit</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a href="#" class="dropdown-item add-featured" data-id="{{ $product->id }}">{{ $product->featured ? 'Remove from Featured' : 'Add as Featured' }}</a></li>
                                <li><a href="#" class="dropdown-item add-best-sellers" data-id="{{ $product->id }}">{{ $product->best_sellers ? 'Remove from Best Sellers' : 'Add as Best Seller' }}</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a href="#" class="dropdown-item delete-product" data-id="{{ $product->id }}">Delete</a></li>
                            </ul>
                        </div>
                    </td> --}}
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">No Data Found!</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>