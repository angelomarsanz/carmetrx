@extends('tenant_frontend.layout')

@php $title = $keywords['Change Password'] ; @endphp

@section('pageHeading')
    {{ $title }}
@endsection

@section('content')
    @includeIf('tenant_frontend.partials.breadcrumb', [
        'breadcrumb' => $breadcrumb,
        'title' => $title,
        'subtitle' => $title,
    ])

    <section class="user-dashboard pt-100 pb-60">
        <div class="container">
            <div class="row">
                @includeIf('tenant_frontend.user.side-navbar')

                <div class="col-lg-9">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="user-profile-details">
                                <div class="account-info radius-md">
                                    <div class="title">
                                        <h4>{{ $keywords['Change Your Password'] }}</h4>
                                    </div>

                                    <div class="edit-info-area">
                                        <form action="{{ route('frontend.user.update_password', getParam()) }}"
                                            method="POST">
                                            @csrf
                                            <div class="row">
                                                <div class="col-12 mb-4">
                                                    <input type="password" class="form-control"
                                                        placeholder="{{ $keywords['Current Password'] }}"
                                                        name="current_password">
                                                    @error('current_password')
                                                        <p class="text-danger mt-1">{{ $message }}</p>
                                                    @enderror
                                                </div>

                                                <div class="col-12 mb-4">
                                                    <input type="password" class="form-control"
                                                        placeholder="{{ $keywords['New Password'] }}" name="new_password">
                                                    @error('new_password')
                                                        <p class="text-danger mt-1">{{ $message }}</p>
                                                    @enderror
                                                </div>

                                                <div class="col-12 mb-4">
                                                    <input type="password" class="form-control"
                                                        placeholder="{{ $keywords['Confirm New Password'] }}"
                                                        name="new_password_confirmation">
                                                    @error('new_password_confirmation')
                                                        <p class="text-danger mt-1">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                                @if ($basicInfo->google_recaptcha_status == 1)
                                                    <div class="col-md-12">
                                                        <div class="form-group mb-20">
                                                            {!! NoCaptcha::renderJs() !!}
                                                            {!! NoCaptcha::display() !!}
                                                            @error('g-recaptcha-response')
                                                                <div class="help-block with-errors text-danger">
                                                                    {{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                @endif
                                                <div class="col-12">
                                                    <div class="form-button">
                                                        <button
                                                            class="btn btn-lg btn-primary">{{ $keywords['Submit'] }}</button>
                                                    </div>
                                                </div>
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
    </section>
@endsection
