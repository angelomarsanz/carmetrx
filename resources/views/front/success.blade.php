@extends('front.layout')


@section('pagename')
    - {{ __('Success') }}
@endsection


@section('content')
 @includeIf('front.partials.breadcrumb', [
        'title' => __('Success'),
        'link' => __('Success')
    ])
    <div class="container ptb-120">
        <div class="row align-items-center gx-xl-5">
            <div class="col-md-6 mx-auto">
                <div class="payment-img mb-30 ">
                    <svg class="img-fluid" data-src="{{ asset('assets/front/img/success.svg') }}" data-unique-ids="disabled"
                        data-cache="disabled"></svg>
                </div>
            </div>
            <div class="col-md-6 mx-auto" id="mt">
                <div class="payment mb-30">
                    <div class="content">
                        @auth
                            <h2 class="mb-4">{{ __('payment_success') }}</h2>
                            <p class="paragraph-text mb-4">
                                {{ __('extend_success_msg') }}
                            </p>
                            <a href="{{ route('user-dashboard') }}" class="btn primary-btn">{{ __('Go to Dashboard') }}</a>
                        @endauth
                        @guest
                            <h2 class="mb-4">{{ __('payment_success') }}</h2>
                            <p class="paragraph-text mb-4">
                                {{ __('payment_success_msg') }}
                            </p>
                            <a href="{{ route('front.index') }}" class="btn primary-btn">{{ __('Go to Home') }}</a>
                        @endguest

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
