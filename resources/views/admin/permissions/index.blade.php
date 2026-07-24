<x-layout>

    <div class="main-content page-permissions">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-1">Permissions</h1>
                <p class="text-muted mb-0">
                    Manage system permissions and access control.
                </p>
            </div>

            <a href="{{ route('admin.permissions.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Add Permission
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div class="card">
            <div class="card-body">

                <div class="table-responsive">

                    <table class="table table-hover align-middle">

                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Permission Name</th>
                                <th>Slug</th>
                                <th>Description</th>
                                <th>Created</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>

                        <tbody>

                        @forelse($permissions as $permission)

                            <tr>

                                <td>{{ $permission->id }}</td>

                                <td>
                                    <strong>{{ $permission->name }}</strong>
                                </td>

                                <td>
                                    <span class="badge bg-light text-dark border">
                                        {{ $permission->slug }}
                                    </span>
                                </td>

                                <td>
                                    {{ $permission->description ?? '-' }}
                                </td>

                                <td>
                                    {{ $permission->created_at->format('d M Y') }}
                                </td>

                                <td class="text-end">

                                    <a href="{{ route('admin.permissions.show', $permission) }}"
                                       class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    <a href="{{ route('admin.permissions.edit', $permission) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    <form action="{{ route('admin.permissions.destroy', $permission) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('Delete this permission?')">

                                        @csrf
                                        @method('DELETE')

                                        <button class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>

                                    </form>

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    No permissions found.
                                </td>
                            </tr>

                        @endforelse

                        </tbody>

                    </table>

                </div>

                <div class="mt-3">
                    {{ $permissions->links() }}
                </div>

            </div>
        </div>

    </div>

</x-layout>