@extends('tenant_frontend.layout')
@section('pageHeading')
    {{ __('404') }}
@endsection
@section('content')

    <section class="error-area ptb-100 text-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="not-found">
                        <svg data-src="{{ asset('assets/img/404.svg') }}" data-unique-ids="disabled"
                            data-cache="disabled"></svg>
                    </div>
                    <div class="error-txt">
                        <h2>{{ __('404 not found') }}</h2>
                        <p class="mx-auto">
                            {{ __('The page you are looking for might have been moved, renamed, or might never existed.') }}
                        </p>
                        <a href="{{ route('frontend.user.index', getParam()) }}"
                            class="btn btn-lg btn-primary">{{ __('Back to Home') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="{{ asset('assets/tenant-front/js/vendors/svg-loader.min.js') }}"></script>
@endsection
