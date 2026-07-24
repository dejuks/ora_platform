<x-layout>

  <div class="main-content d-flex flex-column align-items-center justify-content-center text-center" style="min-height:60vh;">
    <i class="bi bi-gear-wide-connected display-1 text-muted"></i>
    <h1 class="h3 mt-3">{{ $moduleLabel }} — Module Admin</h1>
    <p class="text-muted">You administer this module. Its admin tools are the next thing to build.</p>

    <a href="{{ route('journal.settings.edit') }}" class="btn btn-outline-secondary mt-2">
      <i class="bi bi-sliders"></i> Payment Settings
    </a>
  </div>

</x-layout>
