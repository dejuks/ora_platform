<x-layout>

  <div class="main-content page-my-ebook-library">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-1">My Digital Library</h1>
        <p class="text-muted mb-0">Every eBook you've purchased — download anytime, no limit.</p>
      </div>
      <a href="{{ route('ebook.public.index') }}" class="btn btn-outline-primary">
        <i class="bi bi-shop"></i> Browse the Bookstore
      </a>
    </div>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
      <div class="alert alert-warning">{{ session('error') }}</div>
    @endif

    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead>
              <tr>
                <th>Title</th>
                <th>Author</th>
                <th>Purchased</th>
                <th>Amount Paid</th>
                <th>Downloads</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($orders as $order)
                <tr>
                  <td>{{ $order->book->title ?? '—' }}</td>
                  <td>{{ $order->book?->author?->full_name ?? '—' }}</td>
                  <td>{{ optional($order->paid_at)->format('M d, Y') }}</td>
                  <td>{{ $order->currency }} {{ number_format($order->amount, 2) }}</td>
                  <td>{{ $order->download_count }}</td>
                  <td class="text-end">
                    @if($order->book)
                      <a href="{{ route('ebook.books.download', $order->book) }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-download"></i> Download
                      </a>
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center text-muted py-4">
                    You haven't purchased any eBooks yet.
                    <a href="{{ route('ebook.public.index') }}">Browse the bookstore</a>.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="mt-3">
          {{ $orders->links() }}
        </div>
      </div>
    </div>

  </div>

</x-layout>
