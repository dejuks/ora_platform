<x-layout>

  @php
    $user = auth()->user();
    $canScreen = $user->hasModulePermission('journal', 'screen-submissions');
    $canAssignReviewers = $user->hasModulePermission('journal', 'assign-reviewers');
    $canRecommend = $user->hasModulePermission('journal', 'recommend-decision');
    $canDecide = $user->hasModulePermission('journal', 'make-final-decision');
    $canPublish = $user->hasModulePermission('journal', 'manage-workflow');
    $myReview = $manuscript->reviews->firstWhere('reviewer_id', $user->id);
  @endphp

  <div class="main-content page-manuscript-show">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-1">{{ $manuscript->title }}</h1>
        <p class="text-muted mb-0">
          By {{ $manuscript->author->full_name }} ·
          <span class="badge bg-secondary">{{ $manuscript->statusLabel() }}</span>
        </p>
      </div>
      <a href="{{ route('journal.manuscripts.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back
      </a>
    </div>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row g-4">

      <div class="col-lg-8">

        <div class="card mb-4">
          <div class="card-header"><strong>Abstract</strong></div>
          <div class="card-body">
            <div>{!! $manuscript->abstract !!}</div>
            @if($manuscript->keywords)
              <p class="text-muted mb-0"><strong>Keywords:</strong> {{ $manuscript->keywords }}</p>
            @endif
            @if($manuscript->manuscript_file)
              <a href="{{ \Illuminate\Support\Facades\Storage::url($manuscript->manuscript_file) }}" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                <i class="bi bi-file-earmark-pdf"></i> Download Manuscript File
              </a>
            @endif
          </div>
        </div>

        {{-- AUTHOR: Revise & Resubmit — the workflow "pauses" at desk_rejected,
             revision_requested, and rejected until the author does this. --}}
        @if($manuscript->author_id === $user->id && $manuscript->isEditable() && $manuscript->status !== 'submitted')
          <div class="card mb-4 border-warning">
            <div class="card-header bg-warning-subtle"><strong>Action Needed: Revise &amp; Resubmit</strong></div>
            <div class="card-body">
              @if($manuscript->status === 'desk_rejected')
                <p>Your manuscript was desk-rejected at editorial screening. You may revise it and resubmit — it will go back through screening from the start.</p>
              @elseif($manuscript->status === 'revision_requested')
                <p>The Editor-in-Chief has requested revisions. Update your manuscript and resubmit — it will go straight back to your current reviewers for a fresh round.</p>
              @elseif($manuscript->status === 'rejected')
                <p>This manuscript was rejected. You may still revise and resubmit it as a new attempt, which will re-enter editorial screening.</p>
              @endif
              @if($manuscript->editor_decision_notes)
                <p class="text-muted"><strong>Editorial notes:</strong> {{ $manuscript->editor_decision_notes }}</p>
              @endif
              <a href="{{ route('journal.manuscripts.edit', $manuscript) }}" class="btn btn-warning">
                <i class="bi bi-pencil-square"></i> Revise &amp; Resubmit
              </a>
            </div>
          </div>
        @endif

        {{-- ASSOCIATE EDITOR: Screening --}}
        @if($canScreen && in_array($manuscript->status, ['submitted']))
          <div class="card mb-4">
            <div class="card-header"><strong>Editorial Screening</strong></div>
            <div class="card-body">
              <form action="{{ route('journal.manuscripts.screen', $manuscript) }}" method="POST">
                @csrf
                <div class="mb-3">
                  <label class="form-label">Notes</label>
                  <textarea name="notes" class="form-control" rows="2"></textarea>
                </div>
                <button type="submit" name="decision" value="advance" class="btn btn-success">
                  Advance to Peer Review
                </button>
                <button type="submit" name="decision" value="desk_reject" class="btn btn-outline-danger">
                  Desk Reject
                </button>
              </form>
            </div>
          </div>
        @endif

        {{-- ASSOCIATE EDITOR: Assign Reviewers --}}
        @if($canAssignReviewers && in_array($manuscript->status, ['screening', 'under_review']))
          <div class="card mb-4">
            <div class="card-header"><strong>Assign Reviewers</strong></div>
            <div class="card-body">
              <form action="{{ route('journal.manuscripts.assign-reviewers', $manuscript) }}" method="POST">
                @csrf
                <div class="mb-3">
                  <label class="form-label">Reviewers</label>
                  @forelse($reviewers as $reviewer)
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="reviewers[]" value="{{ $reviewer->id }}"
                             id="reviewer{{ $reviewer->id }}">
                      <label class="form-check-label" for="reviewer{{ $reviewer->id }}">
                        {{ $reviewer->full_name }}
                        <span class="text-muted small">({{ $reviewer->email }})</span>
                      </label>
                    </div>
                  @empty
                    <p class="text-muted">No users hold the Reviewer role in Journal Management yet.</p>
                  @endforelse
                </div>
                <div class="mb-3">
                  <label class="form-label">Due Date</label>
                  <input type="date" name="due_date" class="form-control" style="max-width:200px">
                </div>
                <button type="submit" class="btn btn-primary">Assign</button>
              </form>
            </div>
          </div>
        @endif

        {{-- REVIEWER: Submit Review --}}
        @if($myReview && $myReview->status !== 'submitted')
          <div class="card mb-4">
            <div class="card-header"><strong>Your Review</strong></div>
            <div class="card-body">
              <form action="{{ route('journal.manuscripts.reviews.submit', [$manuscript, $myReview]) }}" method="POST">
                @csrf
                <div class="mb-3">
                  <label class="form-label">Recommendation *</label>
                  <select name="recommendation" class="form-select" required>
                    <option value="">Choose…</option>
                    @foreach(\App\Models\ManuscriptReview::RECOMMENDATIONS as $value => $label)
                      <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="mb-3">
                  <label class="form-label">Comments to Author</label>
                  <textarea name="comments_to_author" class="form-control" rows="4"></textarea>
                </div>
                <div class="mb-3">
                  <label class="form-label">Confidential Comments to Editor</label>
                  <textarea name="comments_to_editor" class="form-control" rows="3"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Submit Review</button>
              </form>
            </div>
          </div>
        @endif

        {{-- ASSOCIATE EDITOR: Recommend --}}
        @if($canRecommend && $manuscript->status === 'under_review')
          <div class="card mb-4">
            <div class="card-header"><strong>Recommend Decision to Editor-in-Chief</strong></div>
            <div class="card-body">
              <form action="{{ route('journal.manuscripts.recommend', $manuscript) }}" method="POST">
                @csrf
                <textarea name="recommendation_notes" class="form-control mb-3" rows="3" required
                          placeholder="Summarize reviewer feedback and your recommendation"></textarea>
                <button type="submit" class="btn btn-primary">Send Recommendation</button>
              </form>
            </div>
          </div>
        @endif

        {{-- EDITOR-IN-CHIEF: Final Decision --}}
        @if($canDecide && in_array($manuscript->status, ['under_review', 'revision_requested']))
          <div class="card mb-4">
            <div class="card-header"><strong>Final Decision (Editor-in-Chief)</strong></div>
            <div class="card-body">
              <form action="{{ route('journal.manuscripts.decide', $manuscript) }}" method="POST">
                @csrf
                <textarea name="notes" class="form-control mb-3" rows="3" placeholder="Decision notes to author"></textarea>
                <button type="submit" name="decision" value="accepted" class="btn btn-success">Accept</button>
                <button type="submit" name="decision" value="revision_requested" class="btn btn-warning">Request Revision</button>
                <button type="submit" name="decision" value="rejected" class="btn btn-outline-danger">Reject</button>
              </form>
            </div>
          </div>
        @endif

        {{-- AUTHOR: Pay the Article Processing Charge --}}
        @if($manuscript->author_id === $user->id && $manuscript->status === 'accepted' && ! $manuscript->isFeeSettled())
          <div class="card mb-4 border-warning">
            <div class="card-header bg-warning-subtle"><strong>Publication Fee Due</strong></div>
            <div class="card-body">
              @if($manuscript->payment_status === 'pending')
                <p class="mb-3">
                  <i class="bi bi-hourglass-split"></i>
                  We're confirming your payment with Chapa. This page will update
                  automatically once it clears — no need to pay again.
                </p>
              @else
                <p class="mb-3">
                  Your manuscript has been accepted. A publication fee of
                  <strong>{{ \App\Models\JournalSetting::current()->currency }} {{ number_format($manuscript->publication_fee, 2) }}</strong>
                  must be paid before it can be published.
                </p>
              @endif
              <a href="{{ route('journal.manuscripts.pay', $manuscript) }}" class="btn btn-warning">
                <i class="bi bi-credit-card"></i>
                {{ $manuscript->payment_status === 'pending' ? 'Check Payment Status' : 'Pay Now' }}
              </a>
            </div>
          </div>
        @endif

        {{-- JOURNAL MANAGER / EIC: Publish --}}
        @if($canPublish && $manuscript->status === 'accepted')
          <div class="card mb-4">
            <div class="card-header"><strong>Publish</strong></div>
            <div class="card-body">
              @if($manuscript->isFeeSettled())
                <form action="{{ route('journal.manuscripts.publish', $manuscript) }}" method="POST">
                  @csrf
                  <button type="submit" class="btn btn-success">
                    <i class="bi bi-globe"></i> Publish & Assign DOI
                  </button>
                </form>
              @else
                <p class="text-muted mb-0">
                  <i class="bi bi-hourglass-split"></i>
                  Waiting on the author's publication fee
                  ({{ \App\Models\JournalSetting::current()->currency }} {{ number_format($manuscript->publication_fee, 2) }})
                  before this can be published.
                </p>
              @endif
            </div>
          </div>
        @endif

        @if($manuscript->status === 'published')
          <div class="alert alert-success">
            <strong>Published.</strong> DOI: {{ $manuscript->doi }}
            ({{ optional($manuscript->published_at)->format('M d, Y') }})
            <br>
            <a href="{{ route('journal.public.show', $manuscript) }}" target="_blank">
              View on the public article page <i class="bi bi-box-arrow-up-right"></i>
            </a>
          </div>
        @endif

      </div>

      <div class="col-lg-4">
        <div class="card">
          <div class="card-header"><strong>Reviews</strong></div>
          <div class="card-body">
            @forelse($manuscript->reviews as $review)
              <div class="border-bottom py-2">
                <div class="d-flex justify-content-between">
                  <span>{{ $review->reviewer->full_name }}</span>
                  <span class="badge {{ $review->status === 'submitted' ? 'bg-success' : 'bg-secondary' }}">
                    {{ ucfirst($review->status) }}
                  </span>
                </div>
                @if($review->recommendation)
                  <div class="text-muted small">
                    {{ \App\Models\ManuscriptReview::RECOMMENDATIONS[$review->recommendation] ?? $review->recommendation }}
                  </div>
                @endif
              </div>
            @empty
              <p class="text-muted mb-0">No reviewers assigned yet.</p>
            @endforelse
          </div>
        </div>
      </div>

    </div>

  </div>

</x-layout>
