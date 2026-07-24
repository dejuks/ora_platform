<x-layout>

  <div class="main-content page-researcher-profile-show">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="h3 mb-0">{{ $user->full_name }}</h1>
      <a href="{{ route('researcher.members.index') }}" class="btn btn-outline-secondary btn-sm">Back to Directory</a>
    </div>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('info'))
      <div class="alert alert-info">{{ session('info') }}</div>
    @endif

    <div class="row g-4">

      <div class="col-lg-8">
        <div class="card mb-4">
          <div class="card-body">
            <h5 class="mb-1">{{ $profile->headline ?? $profile->position_title }}</h5>
            <div class="text-muted mb-3">
              @if($profile->institution) {{ $profile->institution }} @endif
              @if($profile->department) &middot; {{ $profile->department }} @endif
            </div>

            @if($profile->bio)
              <p>{{ $profile->bio }}</p>
            @endif

            @if($profile->research_interests)
              <div class="mb-2"><strong>Research Interests:</strong> {{ $profile->research_interests }}</div>
            @endif

            @if($profile->credentials)
              <div class="mb-2"><strong>Credentials:</strong><br>{{ $profile->credentials }}</div>
            @endif

            @if($profile->publications)
              <div class="mb-2"><strong>Publications:</strong><br>{!! nl2br(e($profile->publications)) !!}</div>
            @endif

            <div class="d-flex gap-3 mt-3 small text-muted">
              @if($profile->city || $profile->country)
                <span><i class="bi bi-geo-alt"></i> {{ trim(($profile->city ?? '').', '.($profile->country ?? ''), ', ') }}</span>
              @endif
              @if($profile->orcid_id)<span><i class="bi bi-file-earmark-person"></i> ORCID: {{ $profile->orcid_id }}</span>@endif
              @if($profile->website_url)<a href="{{ $profile->website_url }}" target="_blank"><i class="bi bi-globe"></i> Website</a>@endif
              @if($profile->linkedin_url)<a href="{{ $profile->linkedin_url }}" target="_blank"><i class="bi bi-linkedin"></i> LinkedIn</a>@endif
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        @if($user->id !== auth()->id())
          <div class="card">
            <div class="card-body d-grid gap-2">
              @if($connectionStatus === 'accepted')
                <button class="btn btn-outline-success" disabled><i class="bi bi-check-circle"></i> Connected</button>
              @elseif($connectionStatus === 'pending')
                <button class="btn btn-outline-secondary" disabled><i class="bi bi-clock"></i> Request Pending</button>
              @else
                <form method="POST" action="{{ route('researcher.connections.store', $user) }}">
                  @csrf
                  <button class="btn btn-primary w-100" type="submit"><i class="bi bi-person-plus"></i> Connect</button>
                </form>
              @endif

              <a href="{{ route('researcher.messages.show', $user) }}" class="btn btn-outline-primary">
                <i class="bi bi-chat-dots"></i> Message
              </a>
            </div>
          </div>
        @endif
      </div>

    </div>

  </div>

</x-layout>
