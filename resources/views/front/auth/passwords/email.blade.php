@extends('front.layout')

@section('pagename')
    - {{ __('Reset Password') }}
@endsection

@section('meta-description', !empty($seo) ? $seo->forget_password_meta_description : '')
@section('meta-keywords', !empty($seo) ? $seo->forget_password_meta_keywords : '')



@section('content')
    @includeIf('front.partials.breadcrumb', [
        'title' => __('Reset Password'),
        'link' => __('Reset Password'),
    ])

    <div class="authentication-area pt-120 pb-120">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="user-form">
                        <div class="title">
                        </div>
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                        <form class="login-form" action="{{ route('user.forgot.password.submit') }}" method="post"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="form-group ">
                                <span>{{ __('Email Address') }}*</span>
                                <input type="email" name="{{ __('email') }}" class="form-control mt-2"
                                    value="{{ Request::old('email') }}">
                                @error('email')
                                    <p class="text-danger mb-2 mt-2">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="form_group mb-3">
                                @if ($bs->is_recaptcha == 1)
                                    <div class="d-block mb-4">
                                        {!! NoCaptcha::renderJs() !!}
                                        {!! NoCaptcha::display() !!}
                                        @if ($errors->has('g-recaptcha-response'))
                                            @php
                                                $errmsg = $errors->first('g-recaptcha-response');
                                            @endphp
                                            <p class="text-danger mb-0 mt-2">{{ __("$errmsg") }}</p>
                                        @endif
                                    </div>
                                @endif
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-lg btn-primary w-100">
                                    {{ __('Send Password Reset Link') }} </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
