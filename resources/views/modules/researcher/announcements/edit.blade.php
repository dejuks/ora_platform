<x-layout>

  <div class="main-content page-researcher-announcement-edit">

    <h1 class="h3 mb-4">Edit Announcement</h1>

    <div class="card">
      <div class="card-body">
        <form method="POST" action="{{ route('researcher.announcements.update', $announcement) }}">
          @csrf
          @method('PUT')

          <div class="mb-3">
            <label class="form-label">Title</label>
            <input type="text" name="title" value="{{ old('title', $announcement->title) }}" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Type</label>
            <select name="type" class="form-select" required>
              @foreach(\App\Models\ResearcherAnnouncement::TYPES as $value => $label)
                <option value="{{ $value }}" {{ old('type', $announcement->type) === $value ? 'selected' : '' }}>{{ $label }}</option>
              @endforeach
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Details</label>
            <textarea name="body" rows="5" class="form-control" required>{{ old('body', $announcement->body) }}</textarea>
          </div>

          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label">Location / Venue</label>
              <input type="text" name="location" value="{{ old('location', $announcement->location) }}" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">Link</label>
              <input type="url" name="link_url" value="{{ old('link_url', $announcement->link_url) }}" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">Event Date</label>
              <input type="datetime-local" name="event_date" value="{{ old('event_date', optional($announcement->event_date)->format('Y-m-d\TH:i')) }}" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">Submission Deadline</label>
              <input type="datetime-local" name="submission_deadline" value="{{ old('submission_deadline', optional($announcement->submission_deadline)->format('Y-m-d\TH:i')) }}" class="form-control">
            </div>
          </div>

          <button class="btn btn-primary" type="submit">Save Changes</button>

        </form>

        @if($announcement->status !== 'published')
          <form method="POST" action="{{ route('researcher.announcements.publish', $announcement) }}" class="mt-3">
            @csrf
            <button class="btn btn-success">Publish Now</button>
          </form>
        @endif
      </div>
    </div>

  </div>

</x-layout>
