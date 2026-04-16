<script>
    "use strict";
    var mainurl = "{{ url('/') }}";
    var imgupload = "{{ route('user.summernote.upload') }}";
    var storeUrl = "";
    var removeUrl = "";
    var rmvdbUrl = "";
    var userStatusRoute = "{{ route('user-status') }}";
    var demo_mode = "{{ env('DEMO_MODE') }}";
    var position = "{{ $adminCurrentLang->rtl == 1 ? 'left' : 'right' }}";
    var are_you_sure = "{{ __('Are you sure?') }}";
    var wont_revert_text = {!! json_encode(__("You won't be able to revert this!")) !!}
    var yes_delete_it = "{{ __('Yes, delete it!') }}";
    var dltSucesMsg = "{{ __('Deleted successfully!') }}";
    var wentWrgMsg = "{{ __('Something went worng!') }}";
    var cancel = "{{ __('Cancel') }}";
    var success = "{{ __('Success!') }}";
    var warning = "{{ __('Warning!') }}";
    var ContactSiteOwner = "{{ __('Contact with site owner.') }}"
    var downgradeMsg = "{{ __('Listing limit reached or exceeded!') }}" + ' ' + ContactSiteOwner;
    var downgrade = "{{ __('Downgrade!') }}";
</script>
<!--   Core JS Files   -->
<script src="{{ asset('assets/admin/js/core/jquery-3.7.1.min.js') }}"></script>
<script src="{{ asset('assets/admin/js/core/popper.min.js') }}"></script>
<script src="{{ asset('assets/admin/js/core/bootstrap.min.js') }}"></script>

<!-- jQuery UI -->
<script src="{{ asset('assets/admin/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js') }}"></script>
<script src="{{ asset('assets/admin/js/plugin/jquery-ui-touch-punch/jquery.ui.touch-punch.min.js') }}"></script>

<!-- jQuery Timepicker -->
<script src="{{ asset('assets/front/js/jquery.timepicker.min.js') }}"></script>

<!-- jQuery Scrollbar -->
<script src="{{ asset('assets/admin/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>

<!-- Bootstrap Notify -->
<script src="{{ asset('assets/admin/js/plugin/bootstrap-notify/bootstrap-notify.min.js') }}"></script>

<!-- Sweet Alert -->
<script src="{{ asset('assets/admin/js/plugin/sweetalert/sweetalert.min.js') }}"></script>

<!-- Bootstrap Tag Input -->
<script src="{{ asset('assets/admin/js/plugin/bootstrap-tagsinput/bootstrap-tagsinput.min.js') }}"></script>

<!-- Bootstrap Datepicker -->
<script src="{{ asset('assets/admin/js/plugin/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>

<!-- Dropzone JS -->
<script src="{{ asset('assets/admin/js/plugin/dropzone/jquery.dropzone.min.js') }}"></script>

<script src="{{ asset('assets/admin/js/plugin/tinymce/js/tinymce/tinymce.min.js') }}"></script>
<!-- JS color JS -->
<script src="{{ asset('assets/admin/js/plugin/jscolor/jscolor.js') }}"></script>

<!-- Datatable -->
<script src="{{ asset('assets/admin/js/plugin/datatables.min.js') }}"></script>

<!-- Select2 JS -->
<script src="{{ asset('assets/admin/js/plugin/select2.min.js') }}"></script>

<!-- Atlantis JS -->
<script src="{{ asset('assets/admin/js/atlantis.min.js') }}"></script>

<!-- Fontawesome Icon Picker JS -->
<script src="{{ asset('assets/admin/js/plugin/fontawesome-iconpicker/fontawesome-iconpicker.min.js') }}"></script>

{{-- highlight js --}}
<script type="text/javascript" src="{{ asset('assets/tenant/js/highlight.pack.js') }}"></script>

<!-- Fonts and icons -->
<script src="{{ asset('assets/admin/js/plugin/webfont/webfont.min.js') }}"></script>

<!-- Custom JS -->
<script src="{{ asset('assets/admin/js/custom.js') }}"></script>

@yield('variables')
<!-- misc JS -->
<script src="{{ asset('assets/admin/js/misc.js') }}"></script>
<script src="{{ asset('assets/tenant/js/admin-partial.js') }}"></script>
<!-- main JS -->
<script src="{{ asset('assets/tenant/js/main.js') }}"></script>



@yield('vuescripts')

@if (session()->has('success'))
    <script>
        "use strict";
        let content = {};

        content.message = '{{ session('success') }}';
        content.title = success;
        content.icon = 'fa fa-bell';

        $.notify(content, {
            type: 'success',
            placement: {
                from: 'top',
                align: position
            },
            showProgressbar: true,
            time: 1000,
            delay: 4000,
        });
    </script>
@endif


@if (session()->has('warning'))
    <script>
        "use strict";
        let content = {};

        content.message = '{{ session('warning') }}';
        content.title = warning;
        content.icon = 'fa fa-bell';

        $.notify(content, {
            type: 'warning',
            placement: {
                from: 'top',
                align: position
            },
            showProgressbar: true,
            time: 1000,
            delay: 4000,
        });
    </script>
@endif

@if (session()->has('downgrade'))
    <script>
        "use strict";
        let content = {};

        content.message = '{{ session('downgrade') }}';
        content.title = warning;
        content.icon = 'fa fa-times';

        $.notify(content, {
            type: 'danger',
            placement: {
                from: 'top',
                align: position
            },
            showProgressbar: true,
            time: 1000,
            delay: 4000,
        });
        $("#allLimits").modal('show');
    </script>
@endif
