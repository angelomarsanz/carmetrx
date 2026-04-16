<script>
    'use strict';
    const baseURL = "{{ route('frontend.user.index', getParam()) }}";
    const tenantBaseURL = "{{ route('frontend.user.index', getParam()) }}";
    const all_model = "{{ __('All') }}";
    const read_more = "{{ __('Read More') }}";
    const read_less = "{{ __('Read Less') }}";
    const show_more = "{{ __('Show More') . '+' }}";
    const show_less = "{{ __('Show Less') . '-' }}";
    var demo_mode = "{{ env('DEMO_MODE') }}";
    const langDir = "{{ $currentLanguageInfo->direction }}";
    var vapid_public_key = "{!! env('VAPID_PUBLIC_KEY') !!}";
</script>
<!-- Jquery JS -->
<script src="{{ asset('/assets/tenant-front/js/vendors/jquery.min.js') }}"></script>
<script src="{{ asset('/assets/tenant-front/js/vendors/bootstrap.min.js') }}"></script>
<script src="{{ asset('/assets/tenant-front/js/vendors/datatables.min.js') }}"></script>
<script src="{{ asset('/assets/tenant-front/js/vendors/jquery.nice-select.min.js') }}"></script>
<script src="{{ asset('/assets/tenant-front/js/vendors/select2.min.js') }}"></script>
<script src="{{ asset('/assets/tenant-front/js/vendors/jquery.magnific-popup.min.js') }}"></script>
<script src="{{ asset('/assets/tenant-front/js/vendors/jquery.counterup.min.js') }}"></script>
<script src="{{ asset('/assets/tenant-front/js/vendors/jquery.waypoints.js') }}"></script>
<script src="{{ asset('/assets/tenant-front/js/vendors/swiper-bundle.min.js') }}"></script>
<script src="{{ asset('/assets/tenant-front/js/vendors/lazysizes.min.js') }}"></script>
<script src="{{ asset('/assets/tenant-front/js/vendors/nouislider.min.js') }}"></script>
<script src="{{ asset('/assets/tenant-front/js/vendors/aos.min.js') }}"></script>
<script src="{{ asset('/assets/tenant-front/js/toastr.min.js') }}"></script>
<script src="{{ asset('/assets/tenant-front/js/vendors/masonry.pkgd.js') }}"></script>
<script src="{{ asset('/assets/tenant-front/js/vendors/imagesloaded.pkgd.js') }}"></script>
<script src="{{ asset('/assets/tenant-front/js/floating-whatsapp.js') }}"></script>
<script src="{{ asset('/assets/tenant-front/js/script.js') }}"></script>

<script src="{{ asset('assets/tenant-front/js/jquery-syotimer.min.js') }}"></script>
<script src="{{ asset('assets/tenant-front/js/main.js') }}"></script>

@includeIf('tenant_frontend.partials.scripts.plugins')
@yield('script')
@includeIf('tenant_frontend.partials.toastr')
