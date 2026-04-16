@extends('tenant_frontend.layout')
@section('pageHeading')
    {{ $keywords['Reset Password'] ?? __('Reset Password') }}
@endsection

@section('content')
    @includeIf('tenant_frontend.partials.breadcrumb', [
        'breadcrumb' => $breadcrumb,
        'title' => $keywords['Reset Password'] ?? __('Reset Password'),
    ])
    
    <div class="authentication-area ptb-100">
        <div class="container">
            <div class="auth-form border radius-md">
                @if (Session::has('success'))
                    <div class="alert alert-success">{{ Session::get('success') }}</div>
                @endif
                @if (Session::has('error'))
                    <div class="alert alert-danger">{{ Session::get('error') }}</div>
                @endif
                <form action="{{ route('frontend.agent.update-forget-password', getParam()) }}" method="POST">
                    @csrf

                    <input type="hidden" name="token" value="{{ request()->input('token') }}">
                    <div class="title">
                        <h4 class="mb-20">{{ $keywords['Reset Password'] ?? __('Reset Password') }}</h4>
                    </div>
                    <div class="form-group mb-30">
                        <input type="password" class="form-control" name="new_password" placeholder="{{ $keywords['Password'] ??  __('Password') }}"
                            required>
                        @error('new_password')
                            <p class="text-danger mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-group mb-30">
                        <input type="password" name="new_password_confirmation"
                            value="{{ old('new_password_confirmation') }}" class="form-control"
                            placeholder="{{ $keywords['Confirm Password'] ??  __('Confirm Password') }}" required>
                        @error('new_password_confirmation')
                            <p class="text-danger mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-lg btn-primary radius-md w-100">
                        {{ $keywords['Submit'] ?? __('Submit') }} </button>
                </form>
            </div>
        </div>
    </div>
@endsection
