<x-layout>

  <div class="main-content d-flex align-items-center justify-content-center" style="min-height:70vh;">
    <div class="text-center">
      <i class="bi bi-shield-lock display-1 text-muted"></i>
      <h1 class="h3 mt-3">No module access yet</h1>
      <p class="text-muted">Your account isn't assigned to any module. Ask a Super Admin to grant you access.</p>
      <form action="{{ route('logout') }}" method="POST" class="d-inline">
        @csrf
        <button class="btn btn-outline-secondary">Log out</button>
      </form>
    </div>
  </div>

</x-layout>
