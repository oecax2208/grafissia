@include('layouts.header')

<body>
    <!-- Topbar Start -->
    @include('layouts.topbar')
    <!-- Topbar End -->


    <!-- Navbar Start -->
    @include('layouts.navbar')
    <!-- Navbar End -->

    @yield("content")

    <!-- Footer Start -->
    @include('layouts.footer')
    <!-- Footer End -->


    <!-- Back to Top -->
    <a href="#" class="btn btn-primary back-to-top"><i class="fa fa-angle-double-up"></i></a>


    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('ui-frontend/lib/easing/easing.min.js') }}"></script>
    <script src="{{ asset('ui-frontend/lib/owlcarousel/owl.carousel.min.js') }}"></script>

    <!-- Contact Javascript File -->
    <script src="{{ asset('ui-frontend/mail/jqBootstrapValidation.min.js') }}"></script>
    <script src="{{ asset('ui-frontend/mail/contact.js') }}"></script>

    <!-- Template Javascript -->
    <script src="{{ asset('ui-frontend/js/main.js') }}"></script>
</body>

</html>
