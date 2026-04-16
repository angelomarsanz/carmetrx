<link rel="stylesheet" href="{{ asset('assets/tenant-front/fonts/icomoon/style.css') }}">
<link rel="stylesheet" href="{{ asset('assets/tenant-front/fonts/fontawesome/css/all.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/tenant-front/css/vendors/bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/tenant-front/css/vendors/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/tenant-front/css/vendors/magnific-popup.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/tenant-front/css/vendors/swiper-bundle.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/tenant-front/css/vendors/nouislider.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/tenant-front/css/vendors/nice-select.css') }}">
<link rel="stylesheet" href="{{ asset('/assets/tenant-front/css/vendors/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('/assets/tenant-front/css/toastr.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/tenant-front/css/vendors/aos.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/tenant-front/css/vendors/leaflet.css') }}">
<link rel="stylesheet" href="{{ asset('assets/tenant-front/css/vendors/MarkerCluster.css') }}">
<link rel="stylesheet" href="{{ asset('/assets/tenant-front/css/floating-whatsapp.css') }}">
<link rel="stylesheet" href="{{ asset('assets/tenant-front/css/style.css') }}">
<link rel="stylesheet" href="{{ asset('assets/tenant-front/css/responsive.css') }}">
<link rel="stylesheet" href="{{ asset('/assets/tenant-front/css/summernote-content.css') }}">

{{-- rtl css are goes here --}}
@if ($currentLanguageInfo->rtl == 1)
    <link rel="stylesheet" href="{{ asset('assets/tenant-front/css/rtl.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/tenant-front/css/rtl-responsive.css') }}">
@endif

@yield('style')
