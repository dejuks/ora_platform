<x-layout>

    <div class="main-content page-permissions">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-1">Create Permission</h1>
                <p class="text-muted mb-0">
                    Add a new permission to the system.
                </p>
            </div>

            <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card">
            <div class="card-body">

                <form action="{{ route('admin.permissions.store') }}" method="POST">

                    @csrf

                    @include('admin.permissions._form')

                </form>

            </div>
        </div>

    </div>

</x-layout>