<div class="mb-3">
    <label class="form-label">Permission Name</label>

    <input
        type="text"
        name="name"
        class="form-control"
        value="{{ old('name', $permission->name ?? '') }}"
        required>
</div>

<div class="mb-3">
    <label class="form-label">Slug</label>

    <input
        type="text"
        name="slug"
        class="form-control"
        value="{{ old('slug', $permission->slug ?? '') }}">
</div>

<div class="mb-3">
    <label class="form-label">Description</label>

    <textarea
        name="description"
        class="form-control"
        rows="4">{{ old('description', $permission->description ?? '') }}</textarea>
</div>

<div class="d-flex gap-2">
    <button type="submit" class="btn btn-primary">
        <i class="bi bi-check-lg"></i> Save
    </button>

    <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary">
        Cancel
    </a>
</div>