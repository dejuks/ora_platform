<x-layout>

  <div class="main-content page-researcher-group-show">

    <div class="d-flex justify-content-between align-items-start mb-4">
      <div>
        <h1 class="h3 mb-1">{{ $group->name }}</h1>
        <div class="text-muted">
          {{ $group->field_of_study }}
          &middot; <span class="badge {{ $group->privacy === 'public' ? 'bg-success' : 'bg-secondary' }}">{{ \App\Models\ResearchGroup::PRIVACY_LEVELS[$group->privacy] ?? $group->privacy }}</span>
          &middot; Moderated by {{ optional($group->moderator)->full_name ?? optional($group->creator)->full_name }}
        </div>
      </div>

      <div class="d-flex gap-2">
        @if($group->isModerator(auth()->user()))
          <a href="{{ route('researcher.groups.edit', $group) }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-gear"></i> Manage</a>
        @endif

        @if(!$membership)
          <form method="POST" action="{{ route('researcher.groups.join', $group) }}">
            @csrf
            <button class="btn btn-primary btn-sm">
              {{ $group->privacy === 'public' ? 'Join Group' : 'Request to Join' }}
            </button>
          </form>
        @elseif($membership->status === 'approved')
          <form method="POST" action="{{ route('researcher.groups.leave', $group) }}">
            @csrf
            <button class="btn btn-outline-danger btn-sm">Leave Group</button>
          </form>
        @else
          <button class="btn btn-outline-secondary btn-sm" disabled>Request Pending</button>
        @endif
      </div>
    </div>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('info'))
      <div class="alert alert-info">{{ session('info') }}</div>
    @endif

    <p>{{ $group->description }}</p>

    @if($pendingMembers->isNotEmpty())
      <div class="card mb-4">
        <div class="card-header"><strong>Pending Membership Requests</strong></div>
        <ul class="list-group list-group-flush">
          @foreach($pendingMembers as $pm)
            <li class="list-group-item d-flex justify-content-between align-items-center">
              {{ $pm->user->full_name }}
              <div class="d-flex gap-2">
                <form method="POST" action="{{ route('researcher.groups.members.approve', [$group, $pm->user]) }}">
                  @csrf
                  <button class="btn btn-sm btn-success">Approve</button>
                </form>
                <form method="POST" action="{{ route('researcher.groups.members.remove', [$group, $pm->user]) }}">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger">Reject</button>
                </form>
              </div>
            </li>
          @endforeach
        </ul>
      </div>
    @endif

    <h4 class="h5 mb-3">Discussions</h4>

    @if($membership && $membership->status === 'approved')
      <div class="card mb-4">
        <div class="card-body">
          <form method="POST" action="{{ route('researcher.groups.posts.store', $group) }}">
            @csrf
            <div class="mb-2">
              <input type="text" name="title" class="form-control" placeholder="Discussion title" required>
            </div>
            <div class="mb-2">
              <textarea name="body" rows="3" class="form-control" placeholder="Start a discussion..." required></textarea>
            </div>
            <button class="btn btn-primary btn-sm" type="submit">Post</button>
          </form>
        </div>
      </div>
    @endif

    @foreach($posts as $post)
      <div class="card mb-3">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <h5 class="card-title">
              @if($post->is_pinned)<i class="bi bi-pin-angle-fill text-warning"></i>@endif
              {{ $post->title }}
            </h5>

            @if($group->isModerator(auth()->user()))
              <div class="d-flex gap-1">
                <form method="POST" action="{{ route('researcher.groups.posts.pin', [$group, $post]) }}">
                  @csrf
                  <button class="btn btn-sm btn-outline-secondary">{{ $post->is_pinned ? 'Unpin' : 'Pin' }}</button>
                </form>
                <form method="POST" action="{{ route('researcher.groups.posts.lock', [$group, $post]) }}">
                  @csrf
                  <button class="btn btn-sm btn-outline-secondary">{{ $post->is_locked ? 'Unlock' : 'Lock' }}</button>
                </form>
                <form method="POST" action="{{ route('researcher.groups.posts.destroy', [$group, $post]) }}" onsubmit="return confirm('Remove this post?');">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger">Remove</button>
                </form>
              </div>
            @endif
          </div>

          <div class="small text-muted mb-2">by {{ $post->author->full_name }} &middot; {{ $post->created_at->diffForHumans() }}</div>
          <p>{{ $post->body }}</p>

          @if($post->is_locked)
            <div class="text-muted small"><i class="bi bi-lock"></i> This discussion is locked.</div>
          @endif

          <div class="ms-3 border-start ps-3 mt-3">
            @foreach($post->comments as $comment)
              <div class="mb-2">
                <strong>{{ $comment->author->full_name }}</strong>
                <span class="text-muted small">{{ $comment->created_at->diffForHumans() }}</span>
                <div>{{ $comment->body }}</div>
              </div>
            @endforeach

            @if($membership && $membership->status === 'approved' && !$post->is_locked)
              <form method="POST" action="{{ route('researcher.groups.posts.comments.store', [$group, $post]) }}" class="d-flex gap-2 mt-2">
                @csrf
                <input type="text" name="body" class="form-control form-control-sm" placeholder="Write a reply..." required>
                <button class="btn btn-sm btn-outline-primary">Reply</button>
              </form>
            @endif
          </div>
        </div>
      </div>
    @endforeach

    {{ $posts->links() }}

  </div>

</x-layout>
