<x-layout>

  <div class="main-content page-library-pricing-plans">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-1">Digital Library — Pricing Plans</h1>
        <p class="text-muted mb-0">Fee rules a Digital Librarian can attach to a paid ebook, journal article, paper, or other resource.</p>
      </div>
      <a href="{{ route('library.pricing-plans.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> New Pricing Plan
      </a>
    </div>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="d-flex gap-2 mb-3 flex-wrap">
      <a href="{{ route('library.pricing-plans.index') }}"
         class="btn btn-sm btn-outline-secondary {{ !request('resource_type') ? 'active' : '' }}">All types</a>
      @foreach($resourceTypes as $key => $label)
        <a href="{{ route('library.pricing-plans.index', ['resource_type' => $key]) }}"
           class="btn btn-sm btn-outline-secondary {{ request('resource_type') === $key ? 'active' : '' }}">{{ $label }}</a>
      @endforeach
    </div>

    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead>
              <tr>
                <th>Name</th>
                <th>Applies To</th>
                <th>Amount</th>
                <th>Resources</th>
                <th>Purchases</th>
                <th>Status</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($plans as $plan)
                <tr>
                  <td>
                    <div>{{ $plan->name }}</div>
                    @if($plan->description)
                      <div class="text-muted small">{{ \Illuminate\Support\Str::limit($plan->description, 80) }}</div>
                    @endif
                    <div class="text-muted small"><code>{{ $plan->slug }}</code></div>
                  </td>
                  <td>{{ $plan->resourceTypeLabel() }}</td>
                  <td>{{ $plan->currency }} {{ number_format($plan->amount, 2) }}</td>
                  <td>{{ $plan->resources_count }}</td>
                  <td>{{ $plan->purchases_count }}</td>
                  <td>
                    @if($plan->is_active)
                      <span class="badge bg-success">Active</span>
                    @else
                      <span class="badge bg-secondary">Inactive</span>
                    @endif
                  </td>
                  <td class="text-end">
                    <a href="{{ route('library.pricing-plans.edit', $plan) }}" class="btn btn-sm btn-outline-primary">
                      <i class="bi bi-pencil"></i> Edit
                    </a>
                    <form action="{{ route('library.pricing-plans.destroy', $plan) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('Delete this pricing plan? Resources using it become free until reassigned.');">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-trash"></i> Delete
                      </button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="7" class="text-center text-muted py-4">No pricing plans yet.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="mt-3">
          {{ $plans->links() }}
        </div>
      </div>
    </div>

  </div>

</x-layout>
