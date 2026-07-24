<?php if (isset($component)) { $__componentOriginal23a33f287873b564aaf305a1526eada4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal23a33f287873b564aaf305a1526eada4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layout','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>

  <div class="main-content page-researcher-profile-edit">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="h3 mb-0">My Profile</h1>
      <a href="<?php echo e(route('researcher.members.show', auth()->user())); ?>" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-eye"></i> Preview Public Profile
      </a>
    </div>

    <?php if(session('success')): ?>
      <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="card">
      <div class="card-body">
        <form method="POST" action="<?php echo e(route('researcher.profile.update')); ?>">
          <?php echo csrf_field(); ?>
          <?php echo method_field('PUT'); ?>

          <div class="row g-3">

            <div class="col-md-6">
              <label class="form-label">Headline</label>
              <input type="text" name="headline" value="<?php echo e(old('headline', $profile->headline)); ?>" class="form-control" placeholder="e.g. Associate Professor of Oromo Linguistics">
            </div>

            <div class="col-md-6">
              <label class="form-label">Position / Title</label>
              <input type="text" name="position_title" value="<?php echo e(old('position_title', $profile->position_title)); ?>" class="form-control">
            </div>

            <div class="col-md-4">
              <label class="form-label">Institution / Affiliation</label>
              <input type="text" name="institution" value="<?php echo e(old('institution', $profile->institution)); ?>" class="form-control">
            </div>

            <div class="col-md-4">
              <label class="form-label">Department</label>
              <input type="text" name="department" value="<?php echo e(old('department', $profile->department)); ?>" class="form-control">
            </div>

            <div class="col-md-4">
              <label class="form-label">Highest Academic Degree</label>
              <input type="text" name="academic_degree" value="<?php echo e(old('academic_degree', $profile->academic_degree)); ?>" class="form-control" placeholder="e.g. PhD in History">
            </div>

            <div class="col-md-6">
              <label class="form-label">Field of Study</label>
              <input type="text" name="field_of_study" value="<?php echo e(old('field_of_study', $profile->field_of_study)); ?>" class="form-control">
            </div>

            <div class="col-md-6">
              <label class="form-label">Research Interests</label>
              <input type="text" name="research_interests" value="<?php echo e(old('research_interests', $profile->research_interests)); ?>" class="form-control" placeholder="Comma-separated keywords">
            </div>

            <div class="col-12">
              <label class="form-label">Bio</label>
              <textarea name="bio" rows="4" class="form-control"><?php echo e(old('bio', $profile->bio)); ?></textarea>
            </div>

            <div class="col-12">
              <label class="form-label">Credentials</label>
              <textarea name="credentials" rows="3" class="form-control" placeholder="Degrees, certifications, honors..."><?php echo e(old('credentials', $profile->credentials)); ?></textarea>
            </div>

            <div class="col-12">
              <label class="form-label">Publications</label>
              <textarea name="publications" rows="4" class="form-control" placeholder="List your publications, or paste links to your works..."><?php echo e(old('publications', $profile->publications)); ?></textarea>
            </div>

            <div class="col-md-4">
              <label class="form-label">City</label>
              <input type="text" name="city" value="<?php echo e(old('city', $profile->city)); ?>" class="form-control">
            </div>

            <div class="col-md-4">
              <label class="form-label">Country</label>
              <input type="text" name="country" value="<?php echo e(old('country', $profile->country)); ?>" class="form-control">
            </div>

            <div class="col-md-4">
              <label class="form-label">ORCID iD</label>
              <input type="text" name="orcid_id" value="<?php echo e(old('orcid_id', $profile->orcid_id)); ?>" class="form-control" placeholder="0000-0000-0000-0000">
            </div>

            <div class="col-md-6">
              <label class="form-label">Website</label>
              <input type="url" name="website_url" value="<?php echo e(old('website_url', $profile->website_url)); ?>" class="form-control" placeholder="https://">
            </div>

            <div class="col-md-6">
              <label class="form-label">LinkedIn</label>
              <input type="url" name="linkedin_url" value="<?php echo e(old('linkedin_url', $profile->linkedin_url)); ?>" class="form-control" placeholder="https://">
            </div>

            <div class="col-12">
              <div class="form-check">
                <input type="checkbox" name="is_public" value="1" class="form-check-input" id="isPublic" <?php echo e(old('is_public', $profile->is_public) ? 'checked' : ''); ?>>
                <label class="form-check-label" for="isPublic">Show my profile in the member directory</label>
              </div>
            </div>

          </div>

          <button class="btn btn-primary mt-4" type="submit">Save Profile</button>

        </form>
      </div>
    </div>

  </div>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal23a33f287873b564aaf305a1526eada4)): ?>
<?php $attributes = $__attributesOriginal23a33f287873b564aaf305a1526eada4; ?>
<?php unset($__attributesOriginal23a33f287873b564aaf305a1526eada4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal23a33f287873b564aaf305a1526eada4)): ?>
<?php $component = $__componentOriginal23a33f287873b564aaf305a1526eada4; ?>
<?php unset($__componentOriginal23a33f287873b564aaf305a1526eada4); ?>
<?php endif; ?>
<?php /**PATH C:\Users\Dejene\Desktop\project\ora\resources\views/modules/researcher/profile/edit.blade.php ENDPATH**/ ?>