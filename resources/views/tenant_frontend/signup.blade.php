@extends('tenant_frontend.layout')

@section('pageHeading')
    @if (!empty($pageHeading))
        {{ $pageHeading->signup_page_title }}
    @endif
@endsection

@section('metaKeywords')
    @if (!empty($seoInfo))
        {{ !empty($pageHeading) ? $pageHeading->signup_page_title : $keywords['User Singup'] ?? __('User Singup') }}
    @endif
@endsection

@section('metaDescription')
    @if (!empty($seoInfo))
        {{ $seoInfo->meta_description_signup }}
    @endif
@endsection

@section('content')
    @includeIf('tenant_frontend.partials.breadcrumb', [
        'breadcrumb' => $breadcrumb,
        'title' => !empty($pageHeading) ? $pageHeading->signup_page_title : $keywords['Singup'] ?? __('Signup'),
        'subtitle' => $keywords['Singup'] ?? __('Signup'),
    ])

    <div class="authentication-area ptb-100">
        <div class="container">
            <div class="auth-form border radius-md">
                @if (Session::has('success'))
                    <div class="alert alert-success">{{ __(Session::get('success')) }}</div>
                @endif
                @if (Session::has('error'))
                    <div class="alert alert-danger">{{ __(Session::get('error')) }}</div>
                @endif
                <form action="{{ route('frontend.user.signup_submit', getParam()) }}" method="POST">
                    @csrf
                    <div class="form-group mb-30">

                        <input type="text" placeholder="{{ $keywords['Username'] . '*' }}" class="form-control"
                            name="username" value="{{ old('username') }}">
                        @error('username')
                            <p class="text-danger mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group mb-30">

                        <input type="email" placeholder="{{ $keywords['Email Address'] . '*' }}" class="form-control"
                            name="email_address" value="{{ old('email_address') }}">
                        @error('email_address')
                            <p class="text-danger mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group mb-30">

                        <input type="password" class="form-control" name="password"
                            placeholder="{{ $keywords['Password'] . '*' }}" value="{{ old('password') }}">
                        @error('password')
                            <p class="text-danger mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group mb-30">

                        <input type="password" class="form-control" placeholder="{{ $keywords['Confirm Password'] . '*' }}"
                            name="password_confirmation" value="{{ old('password_confirmation') }}">
                        @error('password_confirmation')
                            <p class="text-danger mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    @if ($basicInfo->google_recaptcha_status == 1)
                        <div class="form_group my-4">
                            {!! NoCaptcha::renderJs() !!}
                            {!! NoCaptcha::display() !!}

                            @error('g-recaptcha-response')
                                <p class="text-danger mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    <div class="form-group">
                        <button type="submit"
                            class="btn btn-lg btn-primary radius-md w-100">{{ $keywords['signup'] }}</button>
                    </div>
                </form>


            </div>
        </div>
    </div>
@endsection
