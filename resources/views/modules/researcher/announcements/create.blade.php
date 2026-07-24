<x-layout>

  <div class="main-content page-researcher-announcement-create">

    <h1 class="h3 mb-4">New Announcement</h1>

    <div class="card">
      <div class="card-body">
        <form method="POST" action="{{ route('researcher.announcements.store') }}">
          @csrf

          <div class="mb-3">
            <label class="form-label">Title</label>
            <input type="text" name="title" value="{{ old('title') }}" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Type</label>
            <select name="type" class="form-select" required>
              @foreach(\App\Models\ResearcherAnnouncement::TYPES as $value => $label)
                <option value="{{ $value }}" {{ old('type') === $value ? 'selected' : '' }}>{{ $label }}</option>
              @endforeach
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Details</label>
            <textarea name="body" rows="5" class="form-control" required>{{ old('body') }}</textarea>
          </div>

          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label">Location / Venue</label>
              <input type="text" name="location" value="{{ old('location') }}" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">Link (registration, CFP page, etc.)</label>
              <input type="url" name="link_url" value="{{ old('link_url') }}" class="form-control" placeholder="https://">
            </div>
            <div class="col-md-6">
              <label class="form-label">Event Date</label>
              <input type="datetime-local" name="event_date" value="{{ old('event_date') }}" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">Submission Deadline</label>
              <input type="datetime-local" name="submission_deadline" value="{{ old('submission_deadline') }}" class="form-control">
            </div>
          </div>

          <div class="form-check mb-3">
            <input type="checkbox" name="publish_now" value="1" class="form-check-input" id="publishNow">
            <label class="form-check-label" for="publishNow">Publish immediately</label>
          </div>

          <button class="btn btn-primary" type="submit">Save Announcement</button>

        </form>
      </div>
    </div>

  </div>

</x-layout>
