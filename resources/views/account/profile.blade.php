<x-layout>

    <div class="main-content page-account-profile">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">My Profile</h1>
        </div>

        @if(session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row g-4">

            <!-- Avatar -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body text-center">
                        @php
                            $avatar = $user->profile_photo
                                ? \Illuminate\Support\Facades\Storage::url($user->profile_photo)
                                : asset('assets/img/profile-img.webp');
                        @endphp
                        <img src="{{ $avatar }}" alt="{{ $user->full_name }}"
                             class="rounded-circle mb-3" style="width:120px;height:120px;object-fit:cover;">
                        <h5 class="mb-0">{{ $user->full_name }}</h5>
                        <p class="text-muted small mb-3">{{ '@'.$user->username }}</p>

                        <form method="POST" action="{{ route('account.profile.photo') }}" enctype="multipart/form-data">
                            @csrf
                            <input type="file" name="profile_photo" class="form-control form-control-sm mb-2" accept="image/*" required>
                            <button type="submit" class="btn btn-sm btn-outline-primary w-100">
                                <i class="bi bi-upload"></i> Update Photo
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Details -->
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <strong>Profile Details</strong>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('account.profile.update') }}">
                            @csrf
                            @method('PUT')

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">First Name</label>
                                    <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Middle Name</label>
                                    <input type="text" name="middle_name" value="{{ old('middle_name', $user->middle_name) }}" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}" class="form-control" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Username</label>
                                    <input type="text" name="username" value="{{ old('username', $user->username) }}" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Phone</label>
                                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Gender</label>
                                    <select name="gender" class="form-select">
                                        <option value="">—</option>
                                        <option value="Male" @selected(old('gender', $user->gender) === 'Male')>Male</option>
                                        <option value="Female" @selected(old('gender', $user->gender) === 'Female')>Female</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Date of Birth</label>
                                    <input type="date" name="date_of_birth" value="{{ old('date_of_birth', optional($user->date_of_birth)->format('Y-m-d')) }}" class="form-control">
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check2"></i> Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <strong>Change Password</strong>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('account.profile.password') }}">
                            @csrf
                            @method('PUT')

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Current Password</label>
                                    <input type="password" name="current_password" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">New Password</label>
                                    <input type="password" name="password" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Confirm New Password</label>
                                    <input type="password" name="password_confirmation" class="form-control" required>
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-outline-danger">
                                    <i class="bi bi-shield-lock"></i> Change Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- My Modules -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <strong>My Modules</strong>
                        <a href="{{ route('my-modules') }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-grid-3x3-gap"></i> Manage Modules
                        </a>
                    </div>
                    <div class="card-body">
                        @if($joinedModules->isEmpty())
                            <p class="text-muted mb-0">
                                You haven't joined any modules yet.
                                <a href="{{ route('my-modules') }}">Browse what's available</a>.
                            </p>
                        @else
                            <div class="row g-3">
                                @foreach($joinedModules as $module)
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center justify-content-between border rounded p-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="bi {{ $module->icon }}"></i>
                                                <span>{{ $module->name }}</span>
                                            </div>
                                            @if($module->route)
                                                <a href="{{ route($module->route) }}" class="btn btn-sm btn-outline-secondary">Open</a>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @if($availableModulesCount > 0)
                                <p class="text-muted small mt-3 mb-0">
                                    {{ $availableModulesCount }} more module{{ $availableModulesCount === 1 ? '' : 's' }} available to join —
                                    <a href="{{ route('my-modules') }}">add another one</a>.
                                </p>
                            @endif
                        @endif
                    </div>
                </div>
            </div>

        </div>

    </div>

</x-layout>
