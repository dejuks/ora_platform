<x-layout>

  <div class="main-content page-researcher-profile-edit">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="h3 mb-0">My Profile</h1>
      <a href="{{ route('researcher.members.show', auth()->user()) }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-eye"></i> Preview Public Profile
      </a>
    </div>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
      <div class="card-body">
        <form method="POST" action="{{ route('researcher.profile.update') }}">
          @csrf
          @method('PUT')

          <div class="row g-3">

            <div class="col-md-6">
              <label class="form-label">Headline</label>
              <input type="text" name="headline" value="{{ old('headline', $profile->headline) }}" class="form-control" placeholder="e.g. Associate Professor of Oromo Linguistics">
            </div>

            <div class="col-md-6">
              <label class="form-label">Position / Title</label>
              <input type="text" name="position_title" value="{{ old('position_title', $profile->position_title) }}" class="form-control">
            </div>

            <div class="col-md-4">
              <label class="form-label">Institution / Affiliation</label>
              <input type="text" name="institution" value="{{ old('institution', $profile->institution) }}" class="form-control">
            </div>

            <div class="col-md-4">
              <label class="form-label">Department</label>
              <input type="text" name="department" value="{{ old('department', $profile->department) }}" class="form-control">
            </div>

            <div class="col-md-4">
              <label class="form-label">Highest Academic Degree</label>
              <input type="text" name="academic_degree" value="{{ old('academic_degree', $profile->academic_degree) }}" class="form-control" placeholder="e.g. PhD in History">
            </div>

            <div class="col-md-6">
              <label class="form-label">Field of Study</label>
              <input type="text" name="field_of_study" value="{{ old('field_of_study', $profile->field_of_study) }}" class="form-control">
            </div>

            <div class="col-md-6">
              <label class="form-label">Research Interests</label>
              <input type="text" name="research_interests" value="{{ old('research_interests', $profile->research_interests) }}" class="form-control" placeholder="Comma-separated keywords">
            </div>

            <div class="col-12">
              <label class="form-label">Bio</label>
              <textarea name="bio" rows="4" class="form-control">{{ old('bio', $profile->bio) }}</textarea>
            </div>

            <div class="col-12">
              <label class="form-label">Credentials</label>
              <textarea name="credentials" rows="3" class="form-control" placeholder="Degrees, certifications, honors...">{{ old('credentials', $profile->credentials) }}</textarea>
            </div>

            <div class="col-12">
              <label class="form-label">Publications</label>
              <textarea name="publications" rows="4" class="form-control" placeholder="List your publications, or paste links to your works...">{{ old('publications', $profile->publications) }}</textarea>
            </div>

            <div class="col-md-4">
              <label class="form-label">City</label>
              <input type="text" name="city" value="{{ old('city', $profile->city) }}" class="form-control">
            </div>

            <div class="col-md-4">
              <label class="form-label">Country</label>
              <input type="text" name="country" value="{{ old('country', $profile->country) }}" class="form-control">
            </div>

            <div class="col-md-4">
              <label class="form-label">ORCID iD</label>
              <input type="text" name="orcid_id" value="{{ old('orcid_id', $profile->orcid_id) }}" class="form-control" placeholder="0000-0000-0000-0000">
            </div>

            <div class="col-md-6">
              <label class="form-label">Website</label>
              <input type="url" name="website_url" value="{{ old('website_url', $profile->website_url) }}" class="form-control" placeholder="https://">
            </div>

            <div class="col-md-6">
              <label class="form-label">LinkedIn</label>
              <input type="url" name="linkedin_url" value="{{ old('linkedin_url', $profile->linkedin_url) }}" class="form-control" placeholder="https://">
            </div>

            <div class="col-12">
              <div class="form-check">
                <input type="checkbox" name="is_public" value="1" class="form-check-input" id="isPublic" {{ old('is_public', $profile->is_public) ? 'checked' : '' }}>
                <label class="form-check-label" for="isPublic">Show my profile in the member directory</label>
              </div>
            </div>

          </div>

          <button class="btn btn-primary mt-4" type="submit">Save Profile</button>

        </form>
      </div>
    </div>

  </div>

</x-layout>
