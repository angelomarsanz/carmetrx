@extends('tenant_frontend.layout')
@section('pageHeading')
    {{ !empty($pageHeading) ? $pageHeading->agent_login_page_title : $keywords['Agent Login'] ?? __('Agent Login') }}
@endsection
@section('metaKeywords')
    @if (!empty($seoInfo))
        {{ $seoInfo->meta_keyword_agent_login }}
    @endif
@endsection

@section('metaDescription')
    @if (!empty($seoInfo))
        {{ $seoInfo->meta_description_agent_login }}
    @endif
@endsection

@section('content')
    @includeIf('tenant_frontend.partials.breadcrumb', [
        'breadcrumb' => $breadcrumb,
        'title' => !empty($pageHeading)
            ? $pageHeading->agent_login_page_title
            : $keywords['Agent Login'] ?? __('Agent Login'),
        'subtitle' => $keywords['Agent Login'] ?? __('Agent Login'),
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
                <form action="{{ route('agent.login_submit', getParam()) }}" method="POST">
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
                    @if ($bs->google_recaptcha_status == 1)
                        <div class="form-group mb-30">
                            {!! NoCaptcha::renderJs() !!}
                            {!! NoCaptcha::display() !!}

                            @error('g-recaptcha-response')
                                <p class="mt-1 text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif
                    <div class="row align-items-center mb-20">
                        <div class="col-4 col-xs-12">
                            <div class="link">
                                <a href="{{ route('frontend.agent.forget.password', getParam()) }}">
                                    {{ $keywords['Forgot password?'] ?? __('Forgot password?') }} </a>
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
