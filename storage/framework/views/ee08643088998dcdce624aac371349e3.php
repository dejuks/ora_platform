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

    <div class="main-content page-manuscript-payment">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-1">Publication Fee</h1>
                <p class="text-muted mb-0"><?php echo e($manuscript->title); ?></p>
            </div>
            <a href="<?php echo e(route('journal.manuscripts.show', $manuscript)); ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>

        <?php if($errors->any()): ?>
            <div class="alert alert-danger"><?php echo e($errors->first()); ?></div>
        <?php endif; ?>

        <?php if(session('error')): ?>
            <div class="alert alert-warning"><?php echo e(session('error')); ?></div>
        <?php endif; ?>

        <?php if($manuscript->payment_status === 'pending'): ?>
            <div class="alert alert-info">
                <i class="bi bi-hourglass-split"></i>
                We're waiting for Chapa to confirm your last payment attempt. If you already paid,
                this will update automatically within a minute — feel free to refresh.
            </div>
        <?php endif; ?>

        <div class="row g-4 justify-content-center">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header"><strong>Article Processing Charge</strong></div>
                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-center mb-3 p-3 bg-light rounded">
                            <span class="text-muted">Amount Due</span>
                            <span class="h4 mb-0">
                <?php echo e(\App\Models\JournalSetting::current()->currency); ?> <?php echo e(number_format($manuscript->publication_fee, 2)); ?>

              </span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4 px-3 py-2 border rounded">
                            <span class="text-muted"><i class="bi bi-envelope"></i> Billing Email</span>
                            <span class="fw-semibold"><?php echo e($manuscript->author->email); ?></span>
                        </div>

                        <form action="<?php echo e(route('journal.manuscripts.pay.process', $manuscript)); ?>" method="POST">
                            <?php echo csrf_field(); ?>

                            <div class="mb-3">
                                <label class="form-label">Payment Method</label>
                                <select name="method" class="form-select" required id="paymentMethod">
                                    <option value="card">Credit / Debit Card</option>
                                    <option value="mobile_money">Mobile Money (Telebirr, CBE Birr, etc.)</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                </select>
                                <div class="form-text">
                                    This just tells Chapa which tab to open first — you can still switch
                                    methods on the next page.
                                </div>
                            </div>

                            <div class="mb-3" id="cardholderField">
                                <label class="form-label">Cardholder / Account Name</label>
                                <input type="text" name="cardholder_name" class="form-control"
                                       placeholder="Name on card or account"
                                       value="<?php echo e(old('cardholder_name', $manuscript->author->full_name ?? '')); ?>">
                            </div>

                            <p class="text-muted small">
                                <i class="bi bi-shield-lock"></i>
                                You'll be redirected to Chapa's secure checkout to complete payment.
                                Receipt and confirmation will be sent to
                                <strong><?php echo e($manuscript->author->email); ?></strong>. We never see or store
                                your card or account details — Chapa confirms the payment back to us
                                automatically.
                            </p>

                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-lock-fill"></i>
                                Pay <?php echo e(\App\Models\JournalSetting::current()->currency); ?> <?php echo e(number_format($manuscript->publication_fee, 2)); ?> with Chapa
                            </button>
                        </form>

                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        document.getElementById('paymentMethod').addEventListener('change', function (e) {
            document.getElementById('cardholderField').style.display =
                e.target.value === 'card' ? 'block' : 'none';
        });
    </script>

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
<?php /**PATH C:\Users\Dejene\Desktop\project\ora\resources\views/modules/journal/payment.blade.php ENDPATH**/ ?>