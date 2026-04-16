@extends('front.layout')
@section('content')
  
    <div class="container ptb-90">
        <section class="error-area ptb-100 text-center">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="not-found">
                            <svg data-src="{{ asset('assets/img/404.svg') }}" data-unique-ids="disabled"
                                data-cache="disabled"></svg>
                        </div>
                        <div class="error-txt">

                            <h2 class="mb-4">{{ __('You are lost') }}...</h2>
                            <p class="paragraph-text mb-4">
                                {{ __('The page you are looking for might have been moved, renamed, or might never existed.') }}
                            </p>

                            <a href="{{ route('front.index') }}" class="btn btn-lg btn-primary">{{ __('Back to Home') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <script src="{{ asset('assets/tenant-front/js/vendors/svg-loader.min.js') }}"></script>

@endsection
