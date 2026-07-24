<x-layout>

    <div class="main-content page-permission-show">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-1">Permission Details</h1>
                <p class="text-muted mb-0">
                    View permission information and assigned roles.
                </p>
            </div>

            <div>
                <a href="{{ route('admin.permissions.edit', $permission) }}"
                   class="btn btn-primary">
                    <i class="bi bi-pencil"></i> Edit
                </a>

                <a href="{{ route('admin.permissions.index') }}"
                   class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="card shadow-sm">

            <div class="card-header">
                <h5 class="mb-0">Permission Information</h5>
            </div>

            <div class="card-body">

                <table class="table table-bordered">

                    <tr>
                        <th width="220">ID</th>
                        <td>{{ $permission->id }}</td>
                    </tr>

                    <tr>
                        <th>Name</th>
                        <td>{{ $permission->name }}</td>
                    </tr>

                    <tr>
                        <th>Slug</th>
                        <td>
                            <span class="badge bg-primary">
                                {{ $permission->slug }}
                            </span>
                        </td>
                    </tr>

                    <tr>
                        <th>Description</th>
                        <td>
                            {{ $permission->description ?: 'No description available.' }}
                        </td>
                    </tr>

                    <tr>
                        <th>Assigned Roles</th>
                        <td>

                            @forelse($permission->roles as $role)

                                <span class="badge bg-success me-1">
                                    {{ $role->name }}
                                </span>

                            @empty

                                <span class="text-muted">
                                    No roles assigned.
                                </span>

                            @endforelse

                        </td>
                    </tr>

                    <tr>
                        <th>Created At</th>
                        <td>{{ $permission->created_at->format('d M Y h:i A') }}</td>
                    </tr>

                    <tr>
                        <th>Last Updated</th>
                        <td>{{ $permission->updated_at->format('d M Y h:i A') }}</td>
                    </tr>

                </table>

            </div>

        </div>

    </div>

</x-layout>