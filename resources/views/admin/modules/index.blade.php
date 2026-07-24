<x-layout>

  <div class="main-content page-modules">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-1">Modules</h1>
        <p class="text-muted mb-0">The modules that make up ORA. Super Admin manages all of them.</p>
      </div>
      <a href="{{ route('admin.modules.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Add Module
      </a>
    </div>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
      <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead>
              <tr>
                <th></th>
                <th>Name</th>
                <th>Code</th>
                <th>Users</th>
                <th>Status</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($modules as $module)
                <tr>
                  <td><i class="bi {{ $module->icon ?: 'bi-circle' }} fs-5"></i></td>
                  <td>{{ $module->name }}</td>
                  <td><code>{{ $module->code }}</code></td>
                  <td>{{ $module->users()->count() }}</td>
                  <td>
                    <span class="badge {{ $module->is_active ? 'bg-success' : 'bg-secondary' }}">
                      {{ $module->is_active ? 'Active' : 'Inactive' }}
                    </span>
                  </td>
                  <td class="text-end">
                    <a href="{{ route('admin.modules.show', $module) }}" class="btn btn-sm btn-outline-secondary">
                      <i class="bi bi-eye"></i>
                    </a>
                    <a href="{{ route('admin.modules.edit', $module) }}" class="btn btn-sm btn-outline-primary">
                      <i class="bi bi-pencil"></i>
                    </a>
                    <form action="{{ route('admin.modules.toggle-status', $module) }}" method="POST" class="d-inline">
                      @csrf
                      @method('PATCH')
                      <button class="btn btn-sm btn-outline-warning" type="submit">
                        <i class="bi bi-toggle2-on"></i>
                      </button>
                    </form>
                    <form action="{{ route('admin.modules.destroy', $module) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('Delete this module? This cannot be undone.');">
                      @csrf
                      @method('DELETE')
                      <button class="btn btn-sm btn-outline-danger" type="submit">
                        <i class="bi bi-trash"></i>
                      </button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center text-muted py-4">No modules yet.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="mt-3">
          {{ $modules->links() }}
        </div>
      </div>
    </div>

    <div class="alert alert-info mt-4">
      <i class="bi bi-info-circle"></i>
      Creating a module here adds it to the list, menus, and access control. Its actual pages/features still need
      to be built in code and wired into <code>routes/web.php</code> the same way Journal, Ebook, Library,
      Researcher Network, and Oromo Wikipedia are.
    </div>

  </div>

</x-layout>
