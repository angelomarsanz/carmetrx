@extends('front.layout')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/front/css/forgot-password.css') }}">
@endsection



@section('pagename')
    - {{ __('Reset Password') }}
@endsection


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
                        <form class="login-form" action="{{ route('user.reset.password.submit') }}" method="post"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="token" value="{{ $token }}">
                            <div class="form-group mb-3">
                                <span>{{ __('Email Address') }}*</span>
                                <input type="email" name="email" class="form-control" placeholder="{{ __('email') }}"
                                    value="{{ $email }}">
                                @error('email')
                                    <p class="text-danger mb-2 mt-2">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="form-group mb-3">
                                <span>{{ __('Password') }}*</span>
                                <input type="password" class="form-control" placeholder="{{ __('password') }}"
                                    name="password" value="{{ old('password') }}" required>
                                @error('password')
                                    <p class="text-danger mb-2 mt-2">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="form-group mb-3">
                                <span>{{ __('Confirm password') }}*</span>
                                <input id="password-confirm" type="password" class="form-control"
                                    placeholder="{{ __('confirm Password') }}" name="password_confirmation" required
                                    autocomplete="new-password">
                                @error('password')
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
                                    {{ __('Reset Password') }} </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
