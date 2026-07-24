<x-layout>

  <div class="main-content page-researcher-connections">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="h3 mb-0">My Connections</h1>
      <a href="{{ route('researcher.members.index') }}" class="btn btn-primary btn-sm"><i class="bi bi-person-plus"></i> Find Researchers</a>
    </div>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($incoming->isNotEmpty())
      <div class="card mb-4">
        <div class="card-header"><strong>Pending Requests ({{ $incoming->count() }})</strong></div>
        <ul class="list-group list-group-flush">
          @foreach($incoming as $connection)
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <div>
                <a href="{{ route('researcher.members.show', $connection->requester) }}">{{ $connection->requester->full_name }}</a>
                <div class="small text-muted">{{ optional($connection->requester->researcherProfile)->headline }}</div>
              </div>
              <div class="d-flex gap-2">
                <form method="POST" action="{{ route('researcher.connections.accept', $connection) }}">
                  @csrf
                  <button class="btn btn-sm btn-success">Accept</button>
                </form>
                <form method="POST" action="{{ route('researcher.connections.decline', $connection) }}">
                  @csrf
                  <button class="btn btn-sm btn-outline-danger">Decline</button>
                </form>
              </div>
            </li>
          @endforeach
        </ul>
      </div>
    @endif

    @if($outgoing->isNotEmpty())
      <div class="card mb-4">
        <div class="card-header"><strong>Sent Requests ({{ $outgoing->count() }})</strong></div>
        <ul class="list-group list-group-flush">
          @foreach($outgoing as $connection)
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <a href="{{ route('researcher.members.show', $connection->addressee) }}">{{ $connection->addressee->full_name }}</a>
              <form method="POST" action="{{ route('researcher.connections.destroy', $connection) }}">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-secondary">Cancel</button>
              </form>
            </li>
          @endforeach
        </ul>
      </div>
    @endif

    <div class="card">
      <div class="card-header"><strong>Connected ({{ $accepted->count() }})</strong></div>
      <ul class="list-group list-group-flush">
        @forelse($accepted as $connection)
          @php $peer = $connection->otherUser(auth()->id()); @endphp
          <li class="list-group-item d-flex justify-content-between align-items-center">
            <div>
              <a href="{{ route('researcher.members.show', $peer) }}">{{ $peer->full_name }}</a>
              <div class="small text-muted">{{ optional($peer->researcherProfile)->headline }}</div>
            </div>
            <div class="d-flex gap-2">
              <a href="{{ route('researcher.messages.show', $peer) }}" class="btn btn-sm btn-outline-primary">Message</a>
              <form method="POST" action="{{ route('researcher.connections.destroy', $connection) }}">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger">Remove</button>
              </form>
            </div>
          </li>
        @empty
          <li class="list-group-item text-muted small">You have no connections yet.</li>
        @endforelse
      </ul>
    </div>

  </div>

</x-layout>
