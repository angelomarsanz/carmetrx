@extends('tenant_frontend.layout')
@section('pageHeading')
    {{ !empty($pageHeading) ? $pageHeading->agent_forget_password_page_title : __('Forget Password') }}
@endsection
@section('metaKeywords')
    @if (!empty($seoInfo))
        {{ $seoInfo->meta_keywords_agent_forget_password }}
    @endif
@endsection

@section('metaDescription')
    @if (!empty($seoInfo))
        {{ $seoInfo->meta_descriptions_agent_forget_password }}
    @endif
@endsection

@section('content')
    @includeIf('tenant_frontend.partials.breadcrumb', [
        'breadcrumb' => $breadcrumb,
        'title' => !empty($pageHeading)
            ? $pageHeading->agent_forget_password_page_title
            : $keywords['Forget Password'] ?? __('Forget Password'),
        'subtitle' => $keywords['Forget Password'] ?? __('Forget Password'),
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
                <form action="{{ route('frontend.agent.forget.mail', getParam()) }}" method="POST">
                    @csrf
                    <div class="title">
                        <h4 class="mb-20">{{ $keywords['Forget Password'] ?? __('Forget Password') }}</h4>
                    </div>
                    <div class="form-group mb-30">
                        <input type="email" class="form-control" name="email"
                            placeholder="{{ $keywords['Email Address'] ?? __('Email Address') }}" required>
                        @error('email')
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
                    <button type="submit" class="btn btn-lg btn-primary radius-md w-100">
                        {{ $keywords['Submit'] ?? __('Submit') }} </button>
                </form>
            </div>
        </div>
    </div>
@endsection
