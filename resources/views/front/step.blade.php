@extends('front.layout')

@section('pagename')
    - {{ $package->title }}
@endsection

@section('meta-description', !empty($package) ? $package->meta_keywords : '')
@section('meta-keywords', !empty($package) ? $package->meta_description : '')

@section('breadcrumb-title')
    {{ $package->title }}
@endsection
@section('breadcrumb-link')
    {{ $package->title }}
@endsection

@section('content')


    <div class="authentication-area">
        <div class="container">
            <div class="row min-vh-100 align-items-center">
                <div class="col-12">
                    <div class="wrapper">
                        <div class="row align-items-center">

                            <div class="col-lg-6 bg-primary-light">
                                <div class="content">
                                    <div class="logo mb-3">
                                        <a href="{{ route('front.index') }}"><img
                                                src="{{ asset('assets/front/img/' . $bs->logo) }}" alt="Logo"></a>
                                    </div>


                                    <div class="svg-image">
                                        <svg class="mw-100" data-src="{{ asset('assets/front/images/login.svg') }}"
                                            data-unique-ids="disabled" data-cache="disabled"></svg>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="main-form">
                                    <a href="{{ route('front.index') }}" class="icon-link" title="Go back to home"><i
                                            class="fal fa-home"></i></a>
                                    <div class="title">
                                        <h3 class="mb-4">{{ __('Signup to') . ' ' . $bs?->website_title }}</h3>
                                    </div>
                                    <form id="#authForm" class="singupForm" action="{{ route('front.checkout.view') }}"
                                        method="post" enctype="multipart/form-data">
                                        @csrf
                                        <div class="form-group mb-3">
                                            <label for="name" class="mb-1">{{ __('Username') }}</label>
                                            <input type="text" id="name" class="form-control" name="username"
                                                value="{{ old('username') }}" placeholder="{{ __('Username') }}" required>
                                            @if ($hasSubdomain)
                                                <p class="mb-0">
                                                    {{ __('Your subdomain based website URL will be') }}:
                                                    <strong class="text-primary"><span
                                                            id="username">{username}</span>.{{ env('WEBSITE_HOST') }}</strong>
                                                </p>
                                            @endif
                                            <p class="text-danger mb-0" id="usernameAvailable"></p>
                                            @error('username')
                                                <p class="text-danger mb-2 mt-2">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="email" class="mb-1"> {{ __('Email Address') }}</label>
                                            <input type="email" id="email" class="form-control" name="email"
                                                value="{{ old('email') }}" placeholder="{{ __('Enter your email') }}"
                                                required>
                                            @error('email')
                                                <p class="text-danger mb-2 mt-2">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="password" class="mb-1">{{ __('Password') }}</label>
                                            <input type="password" id="password" class="form-control" name="password"
                                                value="{{ old('password') }}" placeholder="{{ __('Password') }}"
                                                placeholder="Enter password" required>
                                            @error('password')
                                                <p class="text-danger mb-2 mt-2">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div class="form-group mb-30">
                                            <label for="password-confirm">{{ __('Confirm Password') }}</label>
                                            <input class="form-control" id="password-confirm" type="password"
                                                class="form_control" placeholder="{{ __('Confirm Password') }}"
                                                name="password_confirmation" required autocomplete="new-password">
                                            @error('password')
                                                <p class="text-danger mb-2 mt-2">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <input type="hidden" name="status" value="{{ $status }}">
                                            <input type="hidden" name="id" value="{{ $id }}">
                                        </div>
                                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">

                                            <div class="link go-signup">
                                                {{ __('Already a member') . '?' }} <a href="{{ route('user.login') }}">
                                                    {{ __('Login now') }} </a>
                                            </div>
                                            <div>
                                                <p class="text-danger mb-2" id="errorTermsCondition"></p>
                                            </div>
                                        </div>
                                        <div class="text-center">
                                            <button type="submit" class="btn btn-lg btn-primary w-100 sbtns">
                                                {{ __('Continue') }} </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection



@section('scripts')
    @if ($hasSubdomain)
        <script>
            "use strict";
            $(document).ready(function() {
                $("input[name='username']").on('input', function() {
                    let username = $(this).val();
                    if (username.length > 0) {
                        $("#username").text(username);
                    } else {
                        $("#username").text("{username}");
                    }
                });
            });
        </script>
    @endif
    <script>
        "use strict";
        $(document).ready(function() {
            $("input[name='username']").on('change', function() {
                let username = $(this).val();
                if (username.length > 0) {
                    $.get("{{ url('/') }}/check/" + username + '/username', function(data) {
                        if (data == true) {
                            $("#usernameAvailable").text('This username is already taken.');
                            $('button[type="submit"]').attr('disabled', 'disabled');
                        } else {
                            $("#usernameAvailable").text('');
                            $('button[type="submit"]').removeAttr('disabled');
                        }
                    });
                } else {
                    $("#usernameAvailable").text('');
                }
            });
        });
    </script>

@endsection
