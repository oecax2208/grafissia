<?php echo $__env->make('layouts.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<body>
    <!-- Topbar Start -->
    <?php echo $__env->make('layouts.topbar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <!-- Topbar End -->


    <!-- Navbar Start -->
    <?php echo $__env->make('layouts.navbar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <!-- Navbar End -->

    <?php echo $__env->yieldContent("content"); ?>

    <!-- Footer Start -->
    <?php echo $__env->make('layouts.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <!-- Footer End -->


    <!-- Back to Top -->
    <a href="#" class="btn btn-primary back-to-top"><i class="fa fa-angle-double-up"></i></a>


    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo e(asset('ui-frontend/lib/easing/easing.min.js')); ?>"></script>
    <script src="<?php echo e(asset('ui-frontend/lib/owlcarousel/owl.carousel.min.js')); ?>"></script>

    <!-- Contact Javascript File -->
    <script src="<?php echo e(asset('ui-frontend/mail/jqBootstrapValidation.min.js')); ?>"></script>
    <script src="<?php echo e(asset('ui-frontend/mail/contact.js')); ?>"></script>

    <!-- Template Javascript -->
    <script src="<?php echo e(asset('ui-frontend/js/main.js')); ?>"></script>
</body>

</html>
<?php /**PATH D:\laragon\www\grafissia\resources\views/layouts/user.blade.php ENDPATH**/ ?>