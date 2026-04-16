<!DOCTYPE html>
<html lang="en" @if ($rtl == 1) dir="rtl" @endif>

<head>
    <!--====== Required meta tags ======-->
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="@yield('meta-description')">
    <meta name="keywords" content="@yield('meta-keywords')">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    @yield('og-meta')
    <!--====== Title ======-->
    <title>{{ $bs->website_title }} @yield('pagename')</title>
    <link rel="icon" href="{{ asset('assets/front/img/' . $bs->favicon) }}">



    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('assets/front/css/bootstrap.min.css') }}">
    <!-- Fontawesome Icon CSS -->
    <link rel="stylesheet" href="{{ asset('assets/front/fonts/fontawesome/css/all.min.css') }}">
    <!-- Kreativ Icon -->
    <link rel="stylesheet" href="{{ asset('assets/front/fonts/icomoon/style.css') }}">
    <!-- Magnific Popup CSS -->
    <link rel="stylesheet" href="{{ asset('assets/front/css/magnific-popup.min.css') }}">
    <!-- Swiper Slider -->
    <link rel="stylesheet" href="{{ asset('assets/front/css/swiper-bundle.min.css') }}">
    <!-- AOS Animation CSS -->
    <link rel="stylesheet" href="{{ asset('assets/front/css/aos.min.css') }}">
    <!-- Nice Select -->
    <link rel="stylesheet" href="{{ asset('assets/front/css/nice-select.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/front/css/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/tenant-front/css/floating-whatsapp.css') }}">
    <!-- Main Style CSS -->
    <link rel="stylesheet" href="{{ asset('assets/tenant-front/css/summernote-content.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/front/css/style.css') }}">
    <!-- Responsive CSS -->
    <link rel="stylesheet" href="{{ asset('assets/front/css/responsive.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/front/css/cookie-alert.css') }}">
    @if ($rtl == 1)
        <link rel="stylesheet" href="{{ asset('assets/front/css/rtl.css') }}">
    @endif
    <!-- base color change -->

    @yield('styles')

    @if ($bs->is_whatsapp == 1 || $bs->is_tawkto == 1)
        <style>
            .go-top {
                right: auto;
                left: 30px;
            }
        </style>
    @endif

    <style>
        :root {
            --color-primary: #{{ $bs->base_color }};
            --color-primary-shade: #{{ $bs->base_color2 }};
            --bg-light: #{{ $bs->base_color2 }}14;
        }
    </style>

</head>

<body>


    @if ($bs->preloader_status == 1)
        <div id="preLoader">
            <div class="loader">

                <img src="{{ asset('assets/front/img/' . $bs->preloader) }}" alt="">
            </div>
        </div>
    @endif

    @if (!request()->routeIs('user.login') && !request()->routeIs('front.register.view'))
        @include('front.partials.header')
    @endif

    @yield('content')
    @if (!request()->routeIs('user.login') && !request()->routeIs('front.register.view'))
        {{-- footer section --}}
        @includeIf('front.partials.footer')
    @endif
    <div class="go-top"><i class="fal fa-angle-double-up"></i></div>

    @if ($be?->cookie_alert_status == 1)
        <div class="cookie">
            @include('front.cookie-alert.index')
        </div>
    @endif

    <!-- Magic Cursor -->
    <div class="cursor"></div>
    <!-- Magic Cursor -->

    {{-- WhatsApp Chat Button --}}
    <div id="WAButton"></div>

    <script>
        "use strict";
        var showMore = "{{ __('Show More') }} +";
        var showLess = "{{ __('Show Less') }} -";
        var demo_mode = "{{ env('DEMO_MODE') }}";
    </script>

    <!-- Jquery JS -->
    <script src="{{ asset('/assets/front/js/jquery.min.js') }}"></script>
    <!-- Bootstrap JS -->
    <script src="{{ asset('/assets/front/js/bootstrap.min.js') }}"></script>
    <!-- Nice Select JS -->
    <script src="{{ asset('/assets/front/js/jquery.nice-select.min.js') }}"></script>
    {{-- svg loader  --}}
    <script src="{{ asset('/assets/front/js/svg-loader.min.js') }}"></script>
    <!-- Magnific Popup JS -->
    <script src="{{ asset('/assets/front/js/jquery.magnific-popup.min.js') }}"></script>
    <!-- Toastr JS -->
    <script src=" {{ asset('/assets/tenant-front/js/floating-whatsapp.js') }}"></script>
    <script src="{{ asset('/assets/front/js/toastr.min.js') }}"></script>
    <!-- Swiper Slider JS -->
    <script src="{{ asset('/assets/front/js/swiper-bundle.min.js') }}"></script>
    <!-- Lazysizes -->
    <script src="{{ asset('/assets/front/js/lazysizes.min.js') }}"></script>
    <!-- AOS JS -->
    <script src="{{ asset('/assets/front/js/aos.min.js') }}"></script>
    <!-- Main script JS -->
    <script src="{{ asset('/assets/front/js/script.js') }}"></script>

    <script>
        "use strict";
        var rtl = {{ $rtl }};
    </script>

    @yield('scripts')

    @yield('vuescripts')


    @if (session()->has('success'))
        <script>
            "use strict";
            toastr['success']("{{ __(session('success')) }}");
        </script>
    @endif

    @if (session()->has('error'))
        <script>
            "use strict";
            toastr['error']("{{ __(session('error')) }}");
        </script>
    @endif

    @if (session()->has('warning'))
        <script>
            "use strict";
            toastr['warning']("{{ __(session('warning')) }}");
        </script>
    @endif
    <script>
        "use strict";

        function handleSelect(elm) {
            window.location.href = "{{ route('changeLanguage', '') }}" + "/" + elm.value;
        }
    </script>

    {{-- whatsapp init code --}}

    @if ($bs->is_whatsapp == 1)
        <script type="text/javascript">
            "use strict";
            var whatsapp_popup = {{ $bs->whatsapp_popup }};
            var whatsappImg = "{{ asset('assets/front/img/whatsapp.svg') }}";

            $(function() {
                $('#WAButton').floatingWhatsApp({
                    phone: "{{ $bs->whatsapp_number }}",
                    headerTitle: "{{ $bs->whatsapp_header_title }}",
                    popupMessage: `{!! !empty($bs->whatsapp_popup_message) ? nl2br($bs->whatsapp_popup_message) : '' !!}`,
                    showPopup: whatsapp_popup == 1 ? true : false,
                    buttonImage: '<img src="' + whatsappImg + '" />',
                    position: "right"

                });
            });
        </script>
    @endif

    @if ($bs->is_tawkto == 1)
        @php
            $directLink = str_replace('tawk.to', 'embed.tawk.to', $bs->tawkto_chat_link);
            $directLink = str_replace('chat/', '', $directLink);
        @endphp
        <!--Start of Tawk.to Script-->
        <script type="text/javascript">
            "use strict";
            var Tawk_API = Tawk_API || {},
                Tawk_LoadStart = new Date();
            (function() {
                var s1 = document.createElement("script"),
                    s0 = document.getElementsByTagName("script")[0];
                s1.async = true;
                s1.src = '{{ $directLink }}';
                s1.charset = 'UTF-8';
                s1.setAttribute('crossorigin', '*');
                s0.parentNode.insertBefore(s1, s0);
            })();
        </script>
        <!--End of Tawk.to Script-->
    @endif

</body>

</html>
