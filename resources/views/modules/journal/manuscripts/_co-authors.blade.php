{{--
    Dynamic, unlimited co-author repeater.

    Usage: @include('modules.journal.manuscripts._co-authors', ['existing' => $coAuthors ?? collect()])

    Renders one row per entry in old('co_authors') if validation failed
    and bounced back, otherwise one row per $existing (co-authors
    already saved on the manuscript being edited), otherwise zero rows
    — this section is entirely optional, so an empty form starts blank
    rather than with an empty row to delete.

    Every field except Full Name is optional per row; a row left with
    no name is dropped server-side (see ManuscriptController::syncCoAuthors()).
--}}

@php
  $coAuthorRows = old('co_authors', ($existing ?? collect())->map(fn ($c) => [
      'full_name' => $c->full_name,
      'email' => $c->email,
      'affiliation' => $c->affiliation,
      'orcid' => $c->orcid,
  ])->all());
@endphp

<div class="card mb-4">
  <div class="card-header d-flex justify-content-between align-items-center">
    <strong>Co-Authors <span class="text-muted fw-normal">(optional)</span></strong>
    <button type="button" id="add-co-author" class="btn btn-sm btn-outline-primary">
      <i class="bi bi-plus-lg"></i> Add Co-Author
    </button>
  </div>
  <div class="card-body">
    <p class="text-muted small">
      List anyone besides yourself who should be credited as an author — there's no
      limit. Only the full name is required per co-author; email, affiliation, and
      ORCID are optional and can be filled in later.
    </p>

    <div id="co-authors-list">
      @foreach($coAuthorRows as $i => $row)
        <div class="co-author-row border rounded p-3 mb-3 position-relative">
          <button type="button" class="btn btn-sm btn-outline-danger remove-co-author position-absolute top-0 end-0 m-2">
            <i class="bi bi-x-lg"></i>
          </button>
          <div class="row g-2">
            <div class="col-md-6">
              <label class="form-label">Full Name</label>
              <input type="text" name="co_authors[{{ $i }}][full_name]" class="form-control"
                     value="{{ $row['full_name'] ?? '' }}">
            </div>
            <div class="col-md-6">
              <label class="form-label">Email <span class="text-muted">(optional)</span></label>
              <input type="email" name="co_authors[{{ $i }}][email]" class="form-control"
                     value="{{ $row['email'] ?? '' }}">
            </div>
            <div class="col-md-6">
              <label class="form-label">Affiliation <span class="text-muted">(optional)</span></label>
              <input type="text" name="co_authors[{{ $i }}][affiliation]" class="form-control"
                     value="{{ $row['affiliation'] ?? '' }}">
            </div>
            <div class="col-md-6">
              <label class="form-label">ORCID <span class="text-muted">(optional)</span></label>
              <input type="text" name="co_authors[{{ $i }}][orcid]" class="form-control"
                     placeholder="0000-0000-0000-0000" value="{{ $row['orcid'] ?? '' }}">
            </div>
          </div>
        </div>
      @endforeach
    </div>

    <p id="no-co-authors-note" class="text-muted small mb-0 {{ count($coAuthorRows) ? 'd-none' : '' }}">
      No co-authors added yet.
    </p>
  </div>
</div>

{{-- Blank template for JS to clone — never submitted itself since it
     lives outside any <form>-recognized input (no name attributes
     until cloned and re-indexed). --}}
<template id="co-author-row-template">
  <div class="co-author-row border rounded p-3 mb-3 position-relative">
    <button type="button" class="btn btn-sm btn-outline-danger remove-co-author position-absolute top-0 end-0 m-2">
      <i class="bi bi-x-lg"></i>
    </button>
    <div class="row g-2">
      <div class="col-md-6">
        <label class="form-label">Full Name</label>
        <input type="text" data-field="full_name" class="form-control">
      </div>
      <div class="col-md-6">
        <label class="form-label">Email <span class="text-muted">(optional)</span></label>
        <input type="email" data-field="email" class="form-control">
      </div>
      <div class="col-md-6">
        <label class="form-label">Affiliation <span class="text-muted">(optional)</span></label>
        <input type="text" data-field="affiliation" class="form-control">
      </div>
      <div class="col-md-6">
        <label class="form-label">ORCID <span class="text-muted">(optional)</span></label>
        <input type="text" data-field="orcid" class="form-control" placeholder="0000-0000-0000-0000">
      </div>
    </div>
  </div>
</template>

@push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      var list = document.getElementById('co-authors-list');
      var addBtn = document.getElementById('add-co-author');
      var template = document.getElementById('co-author-row-template');
      var emptyNote = document.getElementById('no-co-authors-note');

      if (! list || ! addBtn || ! template) {
        return;
      }

      var nextIndex = list.querySelectorAll('.co-author-row').length;

      function toggleEmptyNote() {
        if (! emptyNote) return;
        emptyNote.classList.toggle('d-none', list.querySelectorAll('.co-author-row').length > 0);
      }

      function addRow() {
        var row = template.content.firstElementChild.cloneNode(true);

        row.querySelectorAll('[data-field]').forEach(function (input) {
          input.name = 'co_authors[' + nextIndex + '][' + input.dataset.field + ']';
        });

        list.appendChild(row);
        nextIndex++;
        toggleEmptyNote();
        row.querySelector('input').focus();
      }

      addBtn.addEventListener('click', addRow);

      list.addEventListener('click', function (event) {
        var removeBtn = event.target.closest('.remove-co-author');
        if (! removeBtn) return;

        removeBtn.closest('.co-author-row').remove();
        toggleEmptyNote();
      });

      toggleEmptyNote();
    });
  </script>
@endpush
