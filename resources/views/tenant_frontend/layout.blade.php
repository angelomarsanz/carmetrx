<!DOCTYPE html>

<html lang="{{ $currentLanguageInfo->code }}" @if ($currentLanguageInfo->rtl == 1) dir="rtl" @endif>

<head>
    {{-- required meta tags --}}
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    {{-- csrf-token for ajax request --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- title --}}
    <title>{{ convertUtf8($basicInfo->website_title) }} - @yield('pageHeading') </title>

    <meta name="keywords" content="@yield('metaKeywords')">
    <meta name="description" content="@yield('metaDescription')">
    @yield('og:tag')
    {{-- fav icon --}}
    <link rel="shortcut icon" type="image/png" href="{{ asset($basicInfo->favicon) }}">


    @php
        $primaryColor = $basicInfo->primary_color;
        $secoundaryColor = $basicInfo->secondary_color;
        // check, whether color has '#' or not, will return 0 or 1
        if (!function_exists('checkColorCode')) {
            function checkColorCode($color)
            {
                return preg_match('/^#[a-f0-9]{6}/i', $color);
            }
        }

        // if, primary color value does not contain '#', then add '#' before color value
        if (isset($primaryColor) && checkColorCode($primaryColor) == 0 && checkColorCode($secoundaryColor) == 0) {
            $primaryColor = '#' . $primaryColor;
            $secoundaryColor = '#' . $secoundaryColor;
        }

        // change decimal point into hex value for opacity
        if (!function_exists('rgb')) {
            function rgb($color = null)
            {
                if (!$color || !preg_match('/^#([a-fA-F0-9]{6})$/', $color)) {
                    return '';
                }

                [$r, $g, $b] = sscanf($color, '#%02x%02x%02x');
                return "$r, $g, $b";
            }
        }

    @endphp
    <style>
        :root {
            --color-primary: {{ $primaryColor }};
            --color-primary-rgb: {{ rgb(htmlspecialchars($primaryColor)) }};
            --color-secondary: {{ $secoundaryColor }};
            --color-secondary-rgb: {{ rgb(htmlspecialchars($secoundaryColor)) }};
        }
    </style>

    {{-- include styles --}}
    @if ($basicInfo->theme_version == 1)
        @includeIf('tenant_frontend.partials.styles.styles-v1')
    @elseif($basicInfo->theme_version == 2)
        @includeIf('tenant_frontend.partials.styles.styles-v2')
    @elseif($basicInfo->theme_version == 3)
        @includeIf('tenant_frontend.partials.styles.styles-v3')
    @endif



</head>

<body>
    {{-- preloader start --}}
    @if ($basicInfo->preloader_status == 1)
        <div id="preLoader">
            <div class="loader">
                <svg viewBox="0 0 80 80">
                    <rect x="8" y="8" width="64" height="64"></rect>
                </svg>
                <div class="icon"><img src="{{ !empty($basicInfo->preloader) ? asset($basicInfo->preloader) : '' }}">
                </div>
            </div>
        </div>
    @endif
    <div class="request-loader">
        <img src="{{ asset('assets/tenant-front/images/loaders.gif') }}">
    </div>

    {{-- preloader end --}}
    @if ($basicInfo->theme_version == 1)
        @includeIf('tenant_frontend.partials.header.header-v1')
    @elseif ($basicInfo->theme_version == 2)
        @includeIf('tenant_frontend.partials.header.header-v2')
    @elseif ($basicInfo->theme_version == 3)
        @includeIf('tenant_frontend.partials.header.header-v3')
    @endif
    {{-- header end --}}


    @yield('content')

    @if (!empty($permissions) && in_array('Whatsapp', $permissions) && $basicInfo->whatsapp_status == 1)
        <div id="WAButton"></div>
    @endif

    {{-- announcement popup --}}
    @includeIf('tenant_frontend.partials.popups')

    {{-- cookie alert --}}
    @if (!is_null($cookieAlertInfo) && $cookieAlertInfo->cookie_alert_status == 1)
        @include('tenant_frontend.cookie-alert.index')
    @endif

    {{-- include footer --}}
    @if ($basicInfo->theme_version == 1)
        @includeIf('tenant_frontend.partials.footer.footer-v1')
    @elseif ($basicInfo->theme_version == 2)
        @includeIf('tenant_frontend.partials.footer.footer-v2')
    @elseif ($basicInfo->theme_version == 3)
        @includeIf('tenant_frontend.partials.footer.footer-v3')
    @endif
    <div class="go-top"><i class="fal fa-angle-double-up"></i></div>
    {{-- end main-wrapper --}}

    {{-- include scripts --}}
    @if ($basicInfo->theme_version == 1)
        @includeIf('tenant_frontend.partials.scripts.scripts-v1')
    @elseif ($basicInfo->theme_version == 2)
        @includeIf('tenant_frontend.partials.scripts.scripts-v2')
    @elseif ($basicInfo->theme_version == 3)
        @includeIf('tenant_frontend.partials.scripts.scripts-v3')
    @endif

</body>

</html>
