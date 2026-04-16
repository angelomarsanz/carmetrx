<html>

<head>
    <title>{{ $userBs->website_title }} - {{ __('Maintainance Mode') }}</title>
    <!-- favicon -->
    <link rel="shortcut icon" href="{{ asset($userBs->favicon) }}" type="image/x-icon">
    <!-- bootstrap css -->
    <link rel="stylesheet" href="{{ asset('assets/front/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/tenant-front/css/style.css') }}">
</head>

<body>
    <section class="container pt-60">
        <div class="content">
            <div class="row">
                <div class="col-lg-6 offset-lg-3">
                    <div class="maintain-img-wrapper">
                        <img src="{{ asset('assets/tenant/image/maintenance/' . $userBs->maintenance_img) }}"
                            alt="">
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-lg-6 offset-lg-3">
                    <h3 class="maintain-txt text-center">
                        {!! nl2br($userBs->maintenance_msg) !!}
                    </h3>
                </div>
            </div>
        </div>
    </section>
</body>

</html>
