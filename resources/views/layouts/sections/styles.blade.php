<!-- BEGIN: Theme CSS-->
<!-- Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">


<link rel="stylesheet" href="{{ asset('assets/vendor/fonts/boxicons.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/fontawesome/all.min.css') }}" />

<!-- Core CSS -->
<link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/css/theme-default.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />
<!-- Vendors CSS -->
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">

<!-- Vendor Styles -->
@yield('vendor-style')

@livewireStyles


<!-- Page Styles -->
@yield('page-style')
