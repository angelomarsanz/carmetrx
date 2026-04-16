@extends('tenant_frontend.layout')
@section('pageHeading')
    @if (!empty($pageHeading))
        {{ $pageHeading->login_page_title }}
    @endif
@endsection

@section('metaKeywords')
    @if (!empty($seoInfo))
        {{ $seoInfo->meta_keyword_login }}
    @endif
@endsection

@section('metaDescription')
    @if (!empty($seoInfo))
        {{ $seoInfo->meta_description_login }}
    @endif
@endsection

@section('content')
    @includeIf('tenant_frontend.partials.breadcrumb', [
        'breadcrumb' => $breadcrumb,
        'title' => !empty($pageHeading) ? $pageHeading->login_page_title : $keywords['Login'] ?? __('Login'),
        'subtitle' => $keywords['Login'] ?? __('Login'),
    ])

    <div class="authentication-area ptb-100">
        <div class="container">
            <div class="auth-form border radius-md">
                <div class="form-group overflow-hidden mb-3">
                    <div class="row justify-content-center">

                        @if ($basicInfo->google_login_status == 1)
                            <a class="text-center w-100 pt-2 py-2 bg-google"
                                href="{{ route('frontend.user.login.google', getParam()) }}">
                                <i class="fab fa-google"></i>
                                {{ __('Login with Google') }}</a>
                        @endif
                    </div>
                </div>
                @if (Session::has('success'))
                    <div class="alert alert-success">{{ __(Session::get('success')) }}</div>
                @endif
                @if (Session::has('error'))
                    <div class="alert alert-danger">{{ __(Session::get('error')) }}</div>
                @endif
                <form action="{{ route('frontend.user.login_submit', getParam()) }}" method="POST">
                    @csrf
                    <div class="title">
                        <h4 class="mb-20">{{ $keywords['Login'] ?? __('Login') }}</h4>
                    </div>
                    <div class="form-group mb-30">
                        <input type="text" class="form-control" name="username" value=""
                            placeholder="{{ $keywords['Username'] ?? __('Username') }}" required>
                        @error('username')
                            <p class="text-danger mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-group mb-30">
                        <input type="password" class="form-control" name="password" value=""
                            placeholder="{{ $keywords['Password'] ?? __('Password') }}" required>
                        @error('password')
                            <p class="text-danger mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                    @if ($basicInfo->google_recaptcha_status == 1)
                        <div class="form-group mb-30">
                            {!! NoCaptcha::renderJs() !!}
                            {!! NoCaptcha::display() !!}

                            @error('g-recaptcha-response')
                                <p class="mt-1 text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif
                    <div class="row align-items-center justify-content-between mb-20">
                        <div class="col-4 col-xs-12">
                            <div class="link">
                                <a href="{{ route('frontend.user.forget_password', getParam()) }}">
                                    {{ $keywords['Forgot password?'] ?? __('Forgot password?') }} </a>
                            </div>
                        </div>
                        <div class="col-8 col-xs-12">
                            <div class="link go-signup">
                                {{ $keywords["Don't have an account?"] ?? __("Don't have an account?") }} <a
                                    href="{{ route('frontend.user.signup', getParam()) }}">{{ $keywords['Click Here'] ?? __('Click Here') }}</a>
                                {{ $keywords['to Signup'] ?? __('to Signup') }}
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-lg btn-primary radius-md w-100">
                        {{ $keywords['Login'] ?? __('Login') }} </button>
                </form>
            </div>
        </div>
    </div>
@endsection
