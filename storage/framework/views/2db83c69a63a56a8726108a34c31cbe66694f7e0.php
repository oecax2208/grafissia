
<?php $__env->startSection('title', 'Products'); ?>
<?php $__env->startSection('content'); ?>
    <div class="container products">
        <div class="row">
             <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-xs-18 col-sm-6 col-md-3">
                    <div class="thumbnail">
                    <?php if($product->photo): ?>
                        <a href="<?php echo e($product->photo->getUrl()); ?>" target="_blank" style="display: inline-block">
                    
                            <img src="<?php echo e($product->photo->getUrl('thumb')); ?>" width="500" height="300">
                        </a>
                     <?php endif; ?>
                        
                        <div class="caption">
                            <h4> <?php echo e($product->name ?? ''); ?></h4>
                            <p><?php echo e(strtolower($product->description)); ?></p>
                            <p><strong>Price: </strong><?php echo e($product->price ?? ''); ?></p>
                            <p class="btn-holder"><a href="<?php echo e(url('add-to-cart/'.$product->id)); ?>" class="btn btn-warning btn-block text-center" role="button">Add to cart</a> </p>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div><!-- End row -->
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.user', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\grafissia\resources\views/user/products.blade.php ENDPATH**/ ?>