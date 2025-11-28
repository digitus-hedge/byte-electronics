<table class="table table-bordered table-hover table-striped mb-0">
    <thead class="table-light">
        <tr>
            <th>#</th>
            <th>Customer Name</th>
            <th>Email</th>
            <th>Total Items</th>
            <th>Total Amount</th>
            <th>Currency</th>
            <th>Status</th>
            <th>Created At</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($orders as $order)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $order->customer_name }}</td>
                <td>{{ $order->email }}</td>
                <td>{{ $order->total_distinct_items }}</td>
                <td>{{ $order->total_amount }}</td>
                <td>{{ $order->currency }}</td>
                <td>{{ $order->status->name }}</td>
                <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
                <td>
                    <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-primary">View</a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="9" class="text-center text-muted">No orders found.</td>
            </tr>
        @endforelse
    </tbody>
</table>