{{-- fontawesome css --}}
<link rel="stylesheet" href="{{ asset('assets/front/fonts/fontawesome/css/all.min.css') }}">
{{-- fontawesome icon picker css --}}
<link rel="stylesheet" href="{{ asset('assets/admin/css/fontawesome-iconpicker.min.css') }}">
{{-- bootstrap css --}}
<link rel="stylesheet" href="{{ asset('assets/admin/css/bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/admin/css/dropzone.css') }}">
<link rel="stylesheet" href="{{ asset('assets/admin/css/bootstrap-tagsinput.css') }}">
<link rel="stylesheet" href="{{ asset('assets/admin/css/bootstrap-datepicker.css') }}">
<link rel="stylesheet" href="{{ asset('assets/tenant/css/jquery-ui.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/admin/css/jquery.timepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/admin/css/summernote-bs4.css') }}">
<link rel="stylesheet" href="{{ asset('assets/admin/css/atlantis.css') }}">
<link rel="stylesheet" href="{{ asset('assets/tenant/css/version-header.css') }}">
<link rel="stylesheet" href="{{ asset('assets/admin/css/select2.min.css') }}">

<link rel="stylesheet" href="{{ asset('assets/tenant/css/monokai-sublime.css') }}">

<link rel="stylesheet" href="{{ asset('assets/admin/css/custom.css') }}">
@if (request()->cookie('user-theme') == 'dark')
<link rel="stylesheet" href="{{ asset('assets/admin/css/dark.css') }}">
@endif

@yield('styles')
