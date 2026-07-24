<x-layout>

  @php
    $user = auth()->user();
    $canCurate = $user->hasModulePermission('repository', 'curate-metadata');
    $canReview = $user->hasModulePermission('repository', 'review-repository-submissions');
    $canDecide = $user->hasModulePermission('repository', 'approve-repository-submissions');
    $canManageAccess = $user->hasModulePermission('repository', 'manage-repository-access');
  @endphp

  <div class="main-content page-repository-item-show">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-1">{{ $item->title }}</h1>
        <p class="text-muted mb-0">
          By {{ $item->authors }} ·
          <span class="badge bg-secondary">{{ $item->statusLabel() }}</span>
          <span class="badge {{ $item->access_level === 'open' ? 'bg-success' : 'bg-secondary' }}">
            {{ $item->accessLevelLabel() }}
          </span>
        </p>
      </div>
      <a href="{{ route('repository.items.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back
      </a>
    </div>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row g-4">

      <div class="col-lg-8">

        <div class="card mb-4">
          <div class="card-header"><strong>Bibliographic Metadata</strong></div>
          <div class="card-body">
            <p>{{ $item->abstract }}</p>

            <dl class="row small mb-0">
              <dt class="col-sm-3">Resource Type</dt>
              <dd class="col-sm-9">{{ $item->resourceTypeLabel() }}</dd>

              @if($item->keywords)
                <dt class="col-sm-3">Keywords</dt>
                <dd class="col-sm-9">{{ $item->keywords }}</dd>
              @endif

              @if($item->publisher)
                <dt class="col-sm-3">Publisher</dt>
                <dd class="col-sm-9">{{ $item->publisher }}</dd>
              @endif

              @if($item->contributors)
                <dt class="col-sm-3">Contributors</dt>
                <dd class="col-sm-9">{{ $item->contributors }}</dd>
              @endif

              @if($item->publication_date)
                <dt class="col-sm-3">Publication Date</dt>
                <dd class="col-sm-9">{{ $item->publication_date->format('M d, Y') }}</dd>
              @endif

              @if($item->source)
                <dt class="col-sm-3">Source</dt>
                <dd class="col-sm-9">{{ $item->source }}</dd>
              @endif

              <dt class="col-sm-3">Language</dt>
              <dd class="col-sm-9">{{ strtoupper($item->language) }}</dd>

              @if($item->external_identifier)
                <dt class="col-sm-3">Existing Identifier</dt>
                <dd class="col-sm-9">{{ $item->external_identifier }}</dd>
              @endif

              @if($item->related_identifiers)
                <dt class="col-sm-3">Related Identifiers</dt>
                <dd class="col-sm-9">{{ $item->related_identifiers }}</dd>
              @endif

              @if($item->coverage)
                <dt class="col-sm-3">Coverage</dt>
                <dd class="col-sm-9">{{ $item->coverage }}</dd>
              @endif

              @if($item->rights_statement)
                <dt class="col-sm-3">Rights</dt>
                <dd class="col-sm-9">{{ $item->rights_statement }}</dd>
              @endif

              @if($item->controlled_vocabulary)
                <dt class="col-sm-3">Controlled Vocabulary</dt>
                <dd class="col-sm-9">{{ $item->controlled_vocabulary }}</dd>
              @endif

              @if($item->bibliographic_references)
                <dt class="col-sm-3">References</dt>
                <dd class="col-sm-9" style="white-space: pre-line;">{{ $item->bibliographic_references }}</dd>
              @endif
            </dl>

            @if($item->file_path)
              <a href="{{ \Illuminate\Support\Facades\Storage::url($item->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary mt-3">
                <i class="bi bi-file-earmark-arrow-down"></i> Download File
              </a>
            @endif
          </div>
        </div>

        {{-- REPOSITORY CURATOR: Metadata Validation & Enrichment --}}
        @if($canCurate && in_array($item->status, ['submitted', 'metadata_review']))
          <div class="card mb-4">
            <div class="card-header"><strong>Metadata Validation & Enrichment</strong></div>
            <div class="card-body">
              <form action="{{ route('repository.items.curate', $item) }}" method="POST">
                @csrf
                <div class="mb-3">
                  <label class="form-label">Controlled Vocabulary / Subject Tags</label>
                  <input type="text" name="controlled_vocabulary" class="form-control" value="{{ $item->controlled_vocabulary }}">
                </div>
                <div class="mb-3">
                  <label class="form-label">Access Level *</label>
                  <select name="access_level" class="form-select" required>
                    @foreach(\App\Models\RepositoryItem::ACCESS_LEVELS as $value => $label)
                      <option value="{{ $value }}" {{ $item->access_level === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="mb-3">
                  <label class="form-label">Embargo Until</label>
                  <input type="date" name="embargo_until" class="form-control" value="{{ optional($item->embargo_until)->format('Y-m-d') }}">
                </div>
                <div class="form-check mb-3">
                  <input class="form-check-input" type="checkbox" name="copyright_verified" value="1" id="copyrightVerified"
                         {{ $item->copyright_verified ? 'checked' : '' }}>
                  <label class="form-check-label" for="copyrightVerified">
                    Copyright, embargo, and citation policy verified (Sherpa/RoMEO or institutional guidelines)
                  </label>
                </div>
                <div class="mb-3">
                  <label class="form-label">Curator Notes</label>
                  <textarea name="curator_notes" class="form-control" rows="2">{{ $item->curator_notes }}</textarea>
                </div>
                <button type="submit" name="decision" value="advance" class="btn btn-success">
                  Advance to Content Review
                </button>
                <button type="submit" name="decision" value="return" class="btn btn-outline-warning">
                  Return for Revision
                </button>
              </form>
            </div>
          </div>
        @endif

        {{-- CONTENT REVIEWER: Academic & Citation Integrity Review --}}
        @if($canReview && $item->status === 'content_review')
          <div class="card mb-4">
            <div class="card-header"><strong>Content & Citation Review</strong></div>
            <div class="card-body">
              <form action="{{ route('repository.items.review', $item) }}" method="POST">
                @csrf
                <div class="form-check mb-3">
                  <input class="form-check-input" type="checkbox" name="plagiarism_checked" value="1" id="plagiarismChecked" required>
                  <label class="form-check-label" for="plagiarismChecked">
                    Plagiarism and citation accuracy checked
                  </label>
                </div>
                <div class="mb-3">
                  <label class="form-label">Recommendation *</label>
                  <select name="reviewer_recommendation" class="form-select" required>
                    <option value="">Choose…</option>
                    @foreach(\App\Models\RepositoryItem::RECOMMENDATIONS as $value => $label)
                      <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="mb-3">
                  <label class="form-label">Notes</label>
                  <textarea name="reviewer_notes" class="form-control" rows="3"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Submit Recommendation</button>
              </form>
            </div>
          </div>
        @endif

        {{-- REPOSITORY ADMINISTRATOR: Final Approval --}}
        @if($canDecide && $item->status === 'recommended')
          <div class="card mb-4">
            <div class="card-header"><strong>Final Approval (Repository Administrator)</strong></div>
            <div class="card-body">
              @if($item->recommendationLabel())
                <p class="text-muted small">Content Reviewer recommendation: <strong>{{ $item->recommendationLabel() }}</strong></p>
              @endif
              <form action="{{ route('repository.items.decide', $item) }}" method="POST">
                @csrf
                <textarea name="notes" class="form-control mb-3" rows="3" placeholder="Decision notes"></textarea>
                <button type="submit" name="decision" value="approved" class="btn btn-success">Approve</button>
                <button type="submit" name="decision" value="revision_requested" class="btn btn-warning">Request Revision</button>
                <button type="submit" name="decision" value="rejected" class="btn btn-outline-danger">Reject</button>
              </form>
            </div>
          </div>
        @endif

        {{-- REPOSITORY ADMINISTRATOR: Publish --}}
        @if($canDecide && $item->status === 'approved')
          <div class="card mb-4">
            <div class="card-header"><strong>Publish</strong></div>
            <div class="card-body">
              <form action="{{ route('repository.items.publish', $item) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-success">
                  <i class="bi bi-globe"></i> Publish & Assign Persistent URL
                </button>
              </form>
            </div>
          </div>
        @endif

        @if($item->status === 'published')
          <div class="alert alert-success">
            <strong>Published.</strong> Persistent URL:
            <a href="{{ $item->persistent_url }}" target="_blank">{{ $item->persistent_url }}</a>
            ({{ optional($item->published_at)->format('M d, Y') }})
            <hr class="my-2">
            <div class="small"><strong>Citation:</strong> {{ $item->citation() }}</div>
          </div>
        @endif

        {{-- REPOSITORY ADMINISTRATOR: Manage Access on published items --}}
        @if($canManageAccess && $item->status === 'published')
          <div class="card mb-4">
            <div class="card-header"><strong>Manage Access</strong></div>
            <div class="card-body">
              <form action="{{ route('repository.items.access', $item) }}" method="POST" class="row g-3 align-items-end">
                @csrf
                <div class="col-md-5">
                  <label class="form-label">Access Level</label>
                  <select name="access_level" class="form-select">
                    @foreach(\App\Models\RepositoryItem::ACCESS_LEVELS as $value => $label)
                      <option value="{{ $value }}" {{ $item->access_level === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-5">
                  <label class="form-label">Embargo Until</label>
                  <input type="date" name="embargo_until" class="form-control" value="{{ optional($item->embargo_until)->format('Y-m-d') }}">
                </div>
                <div class="col-md-2">
                  <button type="submit" class="btn btn-primary w-100">Update</button>
                </div>
              </form>
            </div>
          </div>
        @endif

      </div>

      <div class="col-lg-4">
        <div class="card mb-4">
          <div class="card-header"><strong>Workflow</strong></div>
          <div class="card-body small">
            <div class="border-bottom py-2 d-flex justify-content-between">
              <span>Depositor</span>
              <span>{{ $item->depositor->full_name }}</span>
            </div>
            <div class="border-bottom py-2 d-flex justify-content-between">
              <span>Curator</span>
              <span>{{ $item->curator->full_name ?? '—' }}</span>
            </div>
            <div class="border-bottom py-2 d-flex justify-content-between">
              <span>Content Reviewer</span>
              <span>{{ $item->contentReviewer->full_name ?? '—' }}</span>
            </div>
            <div class="border-bottom py-2 d-flex justify-content-between">
              <span>Decided By</span>
              <span>{{ $item->decidedBy->full_name ?? '—' }}</span>
            </div>
            @if($item->reviewer_notes)
              <div class="pt-2">
                <div class="text-muted">Reviewer Notes</div>
                <div>{{ $item->reviewer_notes }}</div>
              </div>
            @endif
            @if($item->decision_notes)
              <div class="pt-2">
                <div class="text-muted">Decision Notes</div>
                <div>{{ $item->decision_notes }}</div>
              </div>
            @endif
          </div>
        </div>
      </div>

    </div>

  </div>

</x-layout>
