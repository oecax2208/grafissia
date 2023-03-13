
<?php $__env->startSection('content'); ?>
<div class="content">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    Dashboard
                </div>

                <div class="card-body">
                    <?php if(session('status')): ?>
                        <div class="alert alert-success" role="alert">
                            <?php echo e(session('status')); ?>

                        </div>
                    <?php endif; ?>

                    <div class="row">
                        
                        <div class="<?php echo e($settings1['column_class']); ?>" style="overflow-x: auto;">
                            <h3><?php echo e($settings1['chart_title']); ?></h3>
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <?php $__currentLoopData = $settings1['fields']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <th>
                                                <?php echo e(trans(sprintf('cruds.%s.fields.%s', $settings1['translation_key'] ?? 'pleaseUpdateWidget', $key))); ?>

                                            </th>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $settings1['data']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr>
                                            <?php $__currentLoopData = $settings1['fields']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <td>
                                                    <?php if($value === ''): ?>
                                                        <?php echo e($entry->{$key}); ?>

                                                    <?php elseif(is_iterable($entry->{$key})): ?>
                                                        <?php $__currentLoopData = $entry->{$key}; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subEentry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <span class="label label-info"><?php echo e($subEentry->{$value}); ?></span>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    <?php else: ?>
                                                        <?php echo e(data_get($entry, $key . '.' . $value)); ?>

                                                    <?php endif; ?>
                                                </td>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="<?php echo e(count($settings1['fields'])); ?>"><?php echo e(__('No entries found')); ?></td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="<?php echo e($chart2->options['column_class']); ?>">
                            <h3><?php echo $chart2->options['chart_title']; ?></h3>
                            <?php echo $chart2->renderHtml(); ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
##parent-placeholder-16728d18790deb58b3b8c1df74f06e536b532695##
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script><?php echo $chart2->renderJs(); ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\laragon\www\grafissia\resources\views/home.blade.php ENDPATH**/ ?>