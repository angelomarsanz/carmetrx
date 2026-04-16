<!DOCTYPE html>
<html>

<head>
    {{-- required meta tags --}}
    <meta http-equiv="Content-Type" content="text/html" charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">

    {{-- csrf-token for ajax request --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- title --}}
    <title>{{ __('Agent') . ' | ' . $settings->website_title }}</title>

    {{-- fav icon --}}
    <link rel="shortcut icon" type="image/png" href="{{ asset($settings->favicon) }}">

    {{-- include styles --}}
    @includeIf('agent.partials.styles')

    {{-- additional style --}}
    @yield('style')

    @if (!empty($adminCurrentLang) && $adminCurrentLang->rtl == 1)
        <!--====== RTL Style css ======-->
        <link rel="stylesheet" href="{{ asset('assets/admin/css/admin-rtl.css') }}">
        <style>
            #editModal form input,
            #editModal form textarea,
            #editModal form select {
                direction: rtl;
            }

            #editModal form .note-editor.note-frame .note-editing-area .note-editable {
                direction: rtl;
                text-align: right;
            }
        </style>
    @else
        <style>
            .navbar-expand-lg .navbar-nav .dropdown-menu {
                left: auto;
                right: 0;
            }
        </style>
    @endif
</head>

<body data-background-color="{{ Session::get('agent_theme_version') == 'light' ? 'white2' : 'dark' }}">
    {{-- loader start --}}
    <div class="request-loader">
        <img src="{{ asset('assets/admin/img/loader.gif') }}" alt="loader">
    </div>
    {{-- loader end --}}

    <div class="wrapper">
        {{-- top navbar area start --}}
        @includeIf('agent.partials.top-navbar')
        {{-- top navbar area end --}}

        {{-- side navbar area start --}}
        @includeIf('agent.partials.side-navbar')
        {{-- side navbar area end --}}

        <div class="main-panel">
            <div class="content">
                <div class="page-inner">
                    @yield('content')
                </div>
            </div>

            {{-- footer area start --}}
            @includeIf('agent.partials.footer')
            {{-- footer area end --}}
        </div>
    </div>

    {{-- include scripts --}}
    @includeIf('agent.partials.scripts')

    {{-- additional script --}}
    @yield('variables')
    @yield('script')
</body>

</html>
