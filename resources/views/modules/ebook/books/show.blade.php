<x-layout>

  @php
    $user = auth()->user();
    $canScreen = $user->hasModulePermission('ebook', 'screen-manuscripts');
    $canAssignReviewers = $user->hasModulePermission('ebook', 'assign-peer-reviewers');
    $canDecide = $user->hasModulePermission('ebook', 'make-editorial-decision');
    $canClearFinance = $user->hasModulePermission('ebook', 'manage-payments');
    $canProduce = $user->hasModulePermission('ebook', 'convert-and-publish-ebook');
    $canManageAccess = $user->hasModulePermission('ebook', 'manage-ebook-access');
    $myReview = $book->reviews->firstWhere('reviewer_id', $user->id);
  @endphp

  <div class="main-content page-book-show">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-1">{{ $book->title }}</h1>
        <p class="text-muted mb-0">
          By {{ $book->author->full_name }} ·
          <span class="badge bg-secondary">{{ $book->statusLabel() }}</span>
        </p>
      </div>
      <a href="{{ route('ebook.books.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back
      </a>
    </div>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
      <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <div class="card mb-4">
      <div class="card-body">
        @include('modules.ebook.books._workflow-steps')
      </div>
    </div>

    <div class="row g-4">

      <div class="col-lg-8">

        <div class="card mb-4">
          <div class="card-header"><strong>Abstract</strong></div>
          <div class="card-body">
            <p>{{ $book->abstract }}</p>
            @if($book->category)
              <p class="text-muted mb-0"><strong>Category:</strong> {{ $book->category->name }}</p>
            @endif
            @if($book->keywords)
              <p class="text-muted mb-0"><strong>Keywords:</strong> {{ $book->keywords }}</p>
            @endif
            @if($book->manuscript_file)
              <a href="{{ \Illuminate\Support\Facades\Storage::url($book->manuscript_file) }}" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                <i class="bi bi-file-earmark-pdf"></i> Download Submitted Manuscript
              </a>
            @endif
          </div>
        </div>

        {{-- AUTHOR: Revise & Resubmit — the workflow "pauses" at desk_rejected,
             minor_revision, major_revision, and rejected until the author
             does this. Every editorial outcome except "accepted" lands here. --}}
        @if($book->author_id === $user->id && $book->isEditable() && $book->status !== 'submitted')
          <div class="card mb-4 border-warning">
            <div class="card-header bg-warning-subtle"><strong>Action Needed: Revise &amp; Resubmit</strong></div>
            <div class="card-body">
              @if($book->status === 'desk_rejected')
                <p>Your manuscript was desk-rejected at editorial screening. You may revise it and resubmit — it will go back through screening from the start.</p>
              @elseif($book->status === 'minor_revision')
                <p>The Book Editor has requested a <strong>minor revision</strong>. Update your manuscript and resubmit — it will go straight back to your current reviewers for a fresh round.</p>
              @elseif($book->status === 'major_revision')
                <p>The Book Editor has requested a <strong>major revision</strong>. Update your manuscript and resubmit — it will go straight back to your current reviewers for a fresh round.</p>
              @elseif($book->status === 'rejected')
                <p>This manuscript was rejected. You may still revise and resubmit it as a new attempt, which will re-enter editorial screening.</p>
              @endif
              @if($book->editor_decision_notes)
                <p class="text-muted"><strong>Editorial notes:</strong> {{ $book->editor_decision_notes }}</p>
              @endif
              <a href="{{ route('ebook.books.edit', $book) }}" class="btn btn-warning">
                <i class="bi bi-pencil-square"></i> Revise &amp; Resubmit
              </a>
            </div>
          </div>
        @endif

        {{-- BOOK EDITOR: Screening --}}
        @if($canScreen && $book->status === 'submitted')
          <div class="card mb-4">
            <div class="card-header"><strong>Editorial Screening</strong></div>
            <div class="card-body">
              <form action="{{ route('ebook.books.screen', $book) }}" method="POST">
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

        {{-- BOOK EDITOR: Assign Peer Reviewers --}}
        @if($canAssignReviewers && in_array($book->status, ['screening', 'under_review']))
          <div class="card mb-4">
            <div class="card-header"><strong>Assign Peer Reviewers</strong></div>
            <div class="card-body">
              <form action="{{ route('ebook.books.assign-reviewers', $book) }}" method="POST">
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
                    <p class="text-muted">No users hold the Peer Reviewer role in eBook Publishing yet.</p>
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

        {{-- PEER REVIEWER: Submit Review --}}
        @if($myReview && $myReview->status !== 'submitted')
          <div class="card mb-4">
            <div class="card-header"><strong>Your Review</strong></div>
            <div class="card-body">
              <form action="{{ route('ebook.books.reviews.submit', [$book, $myReview]) }}" method="POST">
                @csrf
                <div class="mb-3">
                  <label class="form-label">Recommendation *</label>
                  <select name="recommendation" class="form-select" required>
                    <option value="">Choose…</option>
                    @foreach(\App\Models\BookReview::RECOMMENDATIONS as $value => $label)
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

        {{-- BOOK EDITOR: Editorial Decision — every outcome except
             "Accept" requires the author to revise and resubmit. --}}
        @if($canDecide && in_array($book->status, ['under_review', 'minor_revision', 'major_revision']))
          <div class="card mb-4">
            <div class="card-header"><strong>Editorial Decision</strong></div>
            <div class="card-body">
              <form action="{{ route('ebook.books.decide', $book) }}" method="POST">
                @csrf
                <textarea name="notes" class="form-control mb-3" rows="3" placeholder="Decision notes to author"></textarea>
                <button type="submit" name="decision" value="accepted" class="btn btn-success">Accept</button>
                <button type="submit" name="decision" value="minor_revision" class="btn btn-warning">Minor Revision</button>
                <button type="submit" name="decision" value="major_revision" class="btn btn-warning">Major Revision</button>
                <button type="submit" name="decision" value="rejected" class="btn btn-outline-danger">Reject</button>
              </form>
            </div>
          </div>
        @endif

        {{-- AUTHOR: Pay the Book Processing Charge / Request Waiver --}}
        @if($book->author_id === $user->id && $book->status === 'financial_clearance' && ! $book->isFeeSettled())
          <div class="card mb-4 border-warning">
            <div class="card-header bg-warning-subtle"><strong>Book Processing Charge Due</strong></div>
            <div class="card-body">
              @if($book->payment_status === 'pending')
                <p class="mb-3">
                  <i class="bi bi-hourglass-split"></i>
                  We're confirming your payment with Chapa. This page will update
                  automatically once it clears — no need to pay again.
                </p>
                <a href="{{ route('ebook.books.pay', $book) }}" class="btn btn-warning">Check Payment Status</a>
              @elseif($book->waiver_requested)
                <p class="mb-0">
                  <i class="bi bi-hourglass-split"></i>
                  Your fee waiver request is pending review by the Finance & Operations Officer.
                </p>
              @else
                <p class="mb-3">
                  Your book has been accepted. A processing fee of
                  <strong>{{ \App\Models\EbookSetting::current()->currency }} {{ number_format($book->processing_fee, 2) }}</strong>
                  must be paid — or a waiver granted — before it can move to digital production.
                </p>
                <a href="{{ route('ebook.books.pay', $book) }}" class="btn btn-warning me-2">
                  <i class="bi bi-credit-card"></i> Pay Now
                </a>
                <button type="button" class="btn btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#waiverForm">
                  Request a Fee Waiver
                </button>
                <form id="waiverForm" class="collapse mt-3" action="{{ route('ebook.books.waiver', $book) }}" method="POST">
                  @csrf
                  <textarea name="waiver_reason" class="form-control mb-2" rows="2" required
                            placeholder="Briefly explain why you're requesting a fee waiver"></textarea>
                  <button type="submit" class="btn btn-sm btn-secondary">Submit Waiver Request</button>
                </form>
              @endif
            </div>
          </div>
        @endif

        {{-- FINANCE & OPERATIONS OFFICER: Clearance --}}
        @if($canClearFinance && $book->status === 'financial_clearance')
          <div class="card mb-4">
            <div class="card-header"><strong>Financial Clearance</strong></div>
            <div class="card-body">
              <p class="mb-2">
                Processing Fee: <strong>{{ \App\Models\EbookSetting::current()->currency }} {{ number_format($book->processing_fee, 2) }}</strong>
                — Status: <span class="badge bg-secondary">{{ ucfirst($book->payment_status) }}</span>
              </p>

              @if($book->waiver_requested && $book->payment_status !== 'waived')
                <div class="alert alert-info">
                  <strong>Waiver requested:</strong> {{ $book->waiver_reason }}
                </div>
                <form action="{{ route('ebook.books.clear', $book) }}" method="POST" class="d-inline">
                  @csrf
                  <input type="hidden" name="action" value="approve_waiver">
                  <button type="submit" class="btn btn-success btn-sm">Approve Waiver</button>
                </form>
                <form action="{{ route('ebook.books.clear', $book) }}" method="POST" class="d-inline">
                  @csrf
                  <input type="hidden" name="action" value="decline_waiver">
                  <button type="submit" class="btn btn-outline-danger btn-sm">Decline Waiver</button>
                </form>
              @endif

              @if($book->isFeeSettled())
                <form action="{{ route('ebook.books.clear', $book) }}" method="POST" class="mt-2">
                  @csrf
                  <input type="hidden" name="action" value="grant_clearance">
                  <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-circle"></i> Grant Clearance &amp; Send to Production
                  </button>
                </form>
              @else
                <p class="text-muted mb-0 mt-2">Waiting on payment or a waiver decision.</p>
              @endif
            </div>
          </div>
        @endif

        {{-- DIGITAL CONTENT MANAGER: Upload Proof (convert + assign ISBN/DOI) --}}
        @if($canProduce && $book->status === 'in_production')
          <div class="card mb-4">
            <div class="card-header"><strong>Digital Production — Upload Proof</strong></div>
            <div class="card-body">
              @if($book->proof_change_notes)
                <div class="alert alert-warning">
                  <strong>Author requested changes:</strong>
                  <p class="mb-0">{{ $book->proof_change_notes }}</p>
                </div>
              @endif
              <form action="{{ route('ebook.books.proof.upload', $book) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label">ISBN</label>
                    <input type="text" name="isbn" class="form-control" value="{{ $book->isbn }}" placeholder="978-...">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Cover Image</label>
                    <input type="file" name="cover_image" class="form-control" accept="image/*">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Final eBook (PDF) *</label>
                    <input type="file" name="ebook_pdf" class="form-control" accept="application/pdf" required>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Final eBook (EPUB)</label>
                    <input type="file" name="ebook_epub" class="form-control" accept=".epub">
                  </div>
                </div>
                <button type="submit" class="btn btn-primary mt-3">
                  <i class="bi bi-send-check"></i> Send Proof to Author
                </button>
              </form>
            </div>
          </div>
        @endif

        {{-- AUTHOR: Approve final proof before publication --}}
        @if($book->author_id === $user->id && $book->status === 'proof_review')
          <div class="card mb-4 border-primary">
            <div class="card-header bg-primary-subtle"><strong>Action Needed: Review Your Proof</strong></div>
            <div class="card-body">
              <p>The Digital Content Manager has prepared the final proof for <strong>"{{ $book->title }}"</strong>. Review it before it's published.</p>
              @if($book->ebook_pdf)
                <a href="{{ Illuminate\Support\Facades\Storage::url($book->ebook_pdf) }}" target="_blank" class="btn btn-outline-primary mb-3">
                  <i class="bi bi-file-earmark-pdf"></i> View Proof (PDF)
                </a>
              @endif

              <form action="{{ route('ebook.books.proof.approve', $book) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success">
                  <i class="bi bi-check-lg"></i> Approve Proof
                </button>
              </form>

              <button type="button" class="btn btn-outline-warning" data-bs-toggle="collapse" data-bs-target="#proofChangesForm">
                Request Changes
              </button>

              <form id="proofChangesForm" class="collapse mt-3" action="{{ route('ebook.books.proof.request-changes', $book) }}" method="POST">
                @csrf
                <label class="form-label">What needs to change?</label>
                <textarea name="proof_change_notes" class="form-control mb-2" rows="3" required></textarea>
                <button type="submit" class="btn btn-warning">Send Change Request</button>
              </form>
            </div>
          </div>
        @endif

        {{-- DIGITAL CONTENT MANAGER: Final Publish (author-approved proof only) --}}
        @if($canProduce && $book->status === 'ready_to_publish')
          <div class="card mb-4">
            <div class="card-header"><strong>Ready to Publish</strong></div>
            <div class="card-body">
              <p class="text-success"><i class="bi bi-check-circle"></i> The author approved the proof on {{ optional($book->proof_approved_at)->format('M j, Y') }}.</p>
              <form action="{{ route('ebook.books.publish', $book) }}" method="POST">
                @csrf
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label">Access Type *</label>
                    <select name="access_type" class="form-select" required id="accessType">
                      @foreach(\App\Models\Book::ACCESS_TYPES as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-md-6" id="embargoField" style="display:none">
                    <label class="form-label">Embargo Until</label>
                    <input type="date" name="embargo_until" class="form-control">
                  </div>
                </div>
                <button type="submit" class="btn btn-success mt-3">
                  <i class="bi bi-globe"></i> Publish to ORA Digital Library
                </button>
              </form>
            </div>
          </div>
          <script>
            document.getElementById('accessType').addEventListener('change', function (e) {
              document.getElementById('embargoField').style.display =
                e.target.value === 'embargoed' ? 'block' : 'none';
            });
          </script>
        @endif

        {{-- DIGITAL CONTENT MANAGER: Manage Access (post-publish) --}}
        @if($canManageAccess && $book->status === 'published')
          <div class="card mb-4">
            <div class="card-header"><strong>Access Rights</strong></div>
            <div class="card-body">
              <form action="{{ route('ebook.books.access', $book) }}" method="POST">
                @csrf
                <div class="row g-3 align-items-end">
                  <div class="col-md-5">
                    <label class="form-label">Access Type</label>
                    <select name="access_type" class="form-select" id="accessType2">
                      @foreach(\App\Models\Book::ACCESS_TYPES as $value => $label)
                        <option value="{{ $value }}" {{ $book->access_type === $value ? 'selected' : '' }}>{{ $label }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-md-4" id="embargoField2" style="{{ $book->access_type === 'embargoed' ? '' : 'display:none' }}">
                    <label class="form-label">Embargo Until</label>
                    <input type="date" name="embargo_until" class="form-control"
                           value="{{ optional($book->embargo_until)->format('Y-m-d') }}">
                  </div>
                  <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">Update</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
          <script>
            document.getElementById('accessType2').addEventListener('change', function (e) {
              document.getElementById('embargoField2').style.display =
                e.target.value === 'embargoed' ? 'block' : 'none';
            });
          </script>
        @endif

        @if($book->status === 'published')
          <div class="alert alert-success">
            <strong>Published.</strong>
            ISBN: {{ $book->isbn ?: '—' }} · DOI: {{ $book->doi }}
            ({{ optional($book->published_at)->format('M d, Y') }})
            <br>
            <a href="{{ route('ebook.public.show', $book) }}" target="_blank">
              View on the ORA Digital Library <i class="bi bi-box-arrow-up-right"></i>
            </a>
          </div>
        @endif

      </div>

      <div class="col-lg-4">
        <div class="card">
          <div class="card-header"><strong>Peer Reviews</strong></div>
          <div class="card-body">
            @forelse($book->reviews as $review)
              <div class="border-bottom py-2">
                <div class="d-flex justify-content-between">
                  <span>{{ $review->reviewer->full_name }}</span>
                  <span class="badge {{ $review->status === 'submitted' ? 'bg-success' : 'bg-secondary' }}">
                    {{ ucfirst($review->status) }}
                  </span>
                </div>
                @if($review->recommendation)
                  <div class="text-muted small">
                    {{ \App\Models\BookReview::RECOMMENDATIONS[$review->recommendation] ?? $review->recommendation }}
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
