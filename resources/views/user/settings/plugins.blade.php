@extends('user.layout')
@php

    use App\Http\Helpers\UserPermissionHelper;
    $package = UserPermissionHelper::currentPackage($tenant->id);
    if (!empty($tenant)) {
        $permissions = UserPermissionHelper::packagePermission($tenant->id);
        $permissions = json_decode($permissions, true);
    }

@endphp
@section('content')
    <div class="page-header">
        <h4 class="page-title">{{ __('Plugins') }}</h4>
        <ul class="breadcrumbs">
            <li class="nav-home">
                <a href="{{ route('user-dashboard') }}">
                    <i class="flaticon-home"></i>
                </a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="#">{{ __('Settings') }}</a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="#">{{ __('Plugins') }}</a>
            </li>
        </ul>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-10">
                            <div class="card-title">
                                {{ __('Update Plugins Informations') }}</div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="row ">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card-title">{{ __('Google Map') }}</div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ route('user.update_googleapi') }}" method="POST" id="googleMapForm">
                        @csrf
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label>{{ __('Google Map Status') }}</label>
                                    <div class="selectgroup w-100">
                                        <label class="selectgroup-item">
                                            <input type="radio" name="google_map_api_status" value="1"
                                                class="selectgroup-input"
                                                {{ @$data->google_map_api_status == 1 ? 'checked' : '' }}>
                                            <span class="selectgroup-button">{{ __('Active') }}</span>
                                        </label>

                                        <label class="selectgroup-item">
                                            <input type="radio" name="google_map_api_status" value="0"
                                                class="selectgroup-input"
                                                {{ @$data->google_map_api_status == 0 ? 'checked' : '' }}>
                                            <span class="selectgroup-button">{{ __('Deactive') }}</span>
                                        </label>
                                    </div>

                                    @if ($errors->has('google_map_api_status'))
                                        <p class="mt-1 mb-0 text-danger">
                                            {{ $errors->first('google_map_api_status') }}</p>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <label>{{ __('Google Map Api Key') }}</label>
                                    <input type="text" class="form-control" name="google_map_api_key"
                                        value="{{ @$data->google_map_api_key }}">

                                    @if ($errors->has('google_map_api_key'))
                                        <p class="mt-1 mb-0 text-danger">
                                            {{ $errors->first('google_map_api_key') }}</p>
                                    @endif
                                    <h6 class="mt-2 mb-1 text-warning">
                                        {{ __('If the Google Maps is disabled:') }}</h6>
                                    <ul class="pl-20 mb-0">
                                        <li>
                                            <p class="mb-0 text-warning">
                                                {{ __('Google will not suggest locations under address inputs.') }}
                                            </p>
                                        </li>
                                    </ul>
                                </div>


                                <div class="form-group">
                                    <label>{{ __('Map Display Type') }}</label>
                                    <div class="selectgroup w-100">
                                        <label class="selectgroup-item">
                                            <input type="radio" name="google_map_type" value="exact_location"
                                                class="selectgroup-input" @checked(@$data->google_map_type == 'exact_location')>
                                            <span class="selectgroup-button">{{ __('Exact Location (Marker)') }}</span>
                                        </label>

                                        <label class="selectgroup-item">
                                            <input type="radio" name="google_map_type" value="approximate_area"
                                                class="selectgroup-input" @checked(@$data->google_map_type == 'approximate_area')>
                                            <span class="selectgroup-button"> {{ __('Approximate Area (Radius)') }}</span>
                                        </label>
                                    </div>

                                    @if ($errors->has('google_map_type'))
                                        <p class="mt-1 mb-0 text-danger">
                                            {{ $errors->first('google_map_type') }}</p>
                                    @endif
                                    <small class="form-text text-warning">
                                        {{ __('Specifies the visible area (in meters) around the location on Google Maps.') }}
                                        {{ __('Example: 40 = 40-meter red circle, ideal for showing an approximate area.') }}
                                    </small>

                                </div>
                                <div class="form-group">
                                    <label>{{ __('Radius') }}({{ __('meters') }})</label>
                                    <input type="text" class="form-control" name="google_map_radius"
                                        value="{{ @$data->google_map_radius }}">

                                    @if ($errors->has('google_map_radius'))
                                        <p class="mt-1 mb-0 text-danger">
                                            {{ $errors->first('google_map_radius') }}</p>
                                    @endif
                                    <small class="form-text text-warning">
                                        <ul class="pl-20 mb-0">
                                            <li>
                                                <p class="mb-0 text-warning">
                                                    {{ __('Defines how much area around the location point will be highlighted on Google Maps.') }}
                                                </p>
                                            </li>
                                            <li>
                                                <p class="mb-0 text-warning">
                                                    {{ __('Example: entering 40 shows a red circle covering a 40-meter radius.') }}
                                                </p>
                                            </li>
                                            <li>
                                                <p class="mb-0 text-warning">
                                                    {{ __('Useful for displaying an approximate area without revealing the exact address.') }}
                                                </p>
                                            </li>
                                        </ul>
                                    </small>


                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center">
                    <button type="submit" class="btn btn-success" form="googleMapForm">{{ __('Update') }}</button>
                </div>
            </div>
        </div>

        @if (!empty($permissions) && in_array('Google Recaptcha', $permissions))
            <div class="col-lg-4 d-flex">
                <div class="card flex-fill d-flex flex-column">
                    <form action="{{ route('user.update_recapcha') }}" method="post" class="d-flex flex-column h-100">
                        @csrf
                        <div class="card-header">
                            <div class="card-title">
                                {{ __('Google Recaptcha') }}
                            </div>
                        </div>
                        <div class="card-body flex-grow-1">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>{{ __('Google Recaptcha Status') }}</label>
                                        <div class="selectgroup w-100">
                                            <label class="selectgroup-item">
                                                <input type="radio" name="google_recaptcha_status" value="1"
                                                    class="selectgroup-input"
                                                    {{ $data->google_recaptcha_status == 1 ? 'checked' : '' }}>
                                                <span class="selectgroup-button">{{ __('Active') }}</span>
                                            </label>
                                            <label class="selectgroup-item">
                                                <input type="radio" name="google_recaptcha_status" value="0"
                                                    class="selectgroup-input"
                                                    {{ $data->google_recaptcha_status == 0 ? 'checked' : '' }}>
                                                <span class="selectgroup-button">{{ __('Deactive') }}</span>
                                            </label>
                                        </div>
                                        @if ($errors->has('google_recaptcha_status'))
                                            <p class="mb-0 text-danger">{{ $errors->first('google_recaptcha_status') }}
                                            </p>
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        <label>{{ __('Google Recaptcha Site key') }}</label>
                                        <input class="form-control" name="google_recaptcha_site_key"
                                            value="{{ $data->google_recaptcha_site_key }}">
                                        @if ($errors->has('google_recaptcha_site_key'))
                                            <p class="mb-0 text-danger">{{ $errors->first('google_recaptcha_site_key') }}
                                            </p>
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        <label>{{ __('Google Recaptcha Secret key') }}</label>
                                        <input class="form-control" name="google_recaptcha_secret_key"
                                            value="{{ $data->google_recaptcha_secret_key }}">
                                        @if ($errors->has('google_recaptcha_secret_key'))
                                            <p class="mb-0 text-danger">
                                                {{ $errors->first('google_recaptcha_secret_key') }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer mt-auto">
                            <div class="row">
                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-success">
                                        {{ __('Update') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endif
        @if (!empty($permissions) && in_array('Disqus', $permissions))
            <div class="col-lg-4 d-flex">
                <div class="card flex-fill d-flex flex-column">
                    <form action="{{ route('user.update_disqus') }}" method="post" class="d-flex flex-column h-100">
                        @csrf
                        <div class="card-header">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="card-title">{{ __('Disqus') }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>{{ __('Disqus Status') }} <span
                                                class="text-danger">{{ '*' }}</span> </label>
                                        <div class="selectgroup w-100">
                                            <label class="selectgroup-item">
                                                <input type="radio" name="disqus_status" value="1"
                                                    class="selectgroup-input"
                                                    {{ $data->disqus_status == 1 ? 'checked' : '' }}>
                                                <span class="selectgroup-button">{{ __('Active') }}</span>
                                            </label>

                                            <label class="selectgroup-item">
                                                <input type="radio" name="disqus_status" value="0"
                                                    class="selectgroup-input"
                                                    {{ $data->disqus_status == 0 ? 'checked' : '' }}>
                                                <span class="selectgroup-button">{{ __('Deactive') }}</span>
                                            </label>
                                        </div>

                                        @if ($errors->has('disqus_status'))
                                            <p class="mt-1 mb-0 text-danger">{{ $errors->first('disqus_status') }}</p>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <label>{{ __('Disqus Short Name') }} <span
                                                class="text-danger">{{ '*' }}</span> </label>
                                        <input type="text" class="form-control" name="disqus_short_name"
                                            value="{{ $data->disqus_short_name }}">

                                        @if ($errors->has('disqus_short_name'))
                                            <p class="mt-1 mb-0 text-danger">{{ $errors->first('disqus_short_name') }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer mt-auto">
                            <div class="row">
                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-success">
                                        {{ __('Update') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endif
        @if (!empty($permissions) && in_array('Whatsapp', $permissions))
            <div class="col-lg-4 d-flex">
                <div class="card flex-fill d-flex flex-column">
                    <form action="{{ route('user.update_whatsapp') }}" method="post" class="d-flex flex-column h-100">
                        @csrf
                        <div class="card-header">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="card-title">{{ __('WhatsApp') }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>{{ __('WhatsApp Status') }} <span
                                                class="text-danger">{{ '*' }}</span> </label>
                                        <div class="selectgroup w-100">
                                            <label class="selectgroup-item">
                                                <input type="radio" name="whatsapp_status" value="1"
                                                    class="selectgroup-input"
                                                    {{ $data->whatsapp_status == 1 ? 'checked' : '' }}>
                                                <span class="selectgroup-button">{{ __('Active') }}</span>
                                            </label>

                                            <label class="selectgroup-item">
                                                <input type="radio" name="whatsapp_status" value="0"
                                                    class="selectgroup-input"
                                                    {{ $data->whatsapp_status == 0 ? 'checked' : '' }}>
                                                <span class="selectgroup-button">{{ __('Deactive') }}</span>
                                            </label>
                                        </div>

                                        @if ($errors->has('whatsapp_status'))
                                            <p class="mt-1 mb-0 text-danger">{{ $errors->first('whatsapp_status') }}</p>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <label>{{ __('WhatsApp Number') }} <span
                                                class="text-danger">{{ '*' }}</span> </label>
                                        <input type="text" class="form-control" name="whatsapp_number"
                                            value="{{ $data->whatsapp_number }}">

                                        @if ($errors->has('whatsapp_number'))
                                            <p class="mt-1 mb-0 text-danger">{{ $errors->first('whatsapp_number') }}</p>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <label>{{ __('WhatsApp Header Title') }} <span
                                                class="text-danger">{{ '*' }}</span> </label>
                                        <input type="text" class="form-control" name="whatsapp_header_title"
                                            value="{{ $data->whatsapp_header_title }}">

                                        @if ($errors->has('whatsapp_header_title'))
                                            <p class="mt-1 mb-0 text-danger">
                                                {{ $errors->first('whatsapp_header_title') }}
                                            </p>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <label>{{ __('WhatsApp Popup Status') }} <span
                                                class="text-danger">{{ '*' }}</span> </label>
                                        <div class="selectgroup w-100">
                                            <label class="selectgroup-item">
                                                <input type="radio" name="whatsapp_popup_status" value="1"
                                                    class="selectgroup-input"
                                                    {{ $data->whatsapp_popup_status == 1 ? 'checked' : '' }}>
                                                <span class="selectgroup-button">{{ __('Active') }}</span>
                                            </label>

                                            <label class="selectgroup-item">
                                                <input type="radio" name="whatsapp_popup_status" value="0"
                                                    class="selectgroup-input"
                                                    {{ $data->whatsapp_popup_status == 0 ? 'checked' : '' }}>
                                                <span class="selectgroup-button">{{ __('Deactive') }}</span>
                                            </label>
                                        </div>

                                        @if ($errors->has('whatsapp_popup_status'))
                                            <p class="mt-1 mb-0 text-danger">
                                                {{ $errors->first('whatsapp_popup_status') }}
                                            </p>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <label>{{ __('WhatsApp Popup Message') }} <span
                                                class="text-danger">{{ '*' }}</span> </label>
                                        <textarea class="form-control" name="whatsapp_popup_message" rows="2">{{ $data->whatsapp_popup_message }}</textarea>

                                        @if ($errors->has('whatsapp_popup_message'))
                                            <p class="mt-1 mb-0 text-danger">
                                                {{ $errors->first('whatsapp_popup_message') }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer mt-auto">
                            <div class="row">
                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-success">
                                        {{ __('Update') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endif
        @if (!empty($permissions) && in_array('Google Login', $permissions))
            <div class="col-lg-4 d-flex">
                <div class="card flex-fill d-flex flex-column">
                    <form action="{{ route('user.update_google') }}" method="post" class="d-flex flex-column h-100">
                        @csrf
                        <div class="card-header">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="card-title">{{ __('Login via Google') }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>{{ __('Login Status') }} <span
                                                class="text-danger">{{ '*' }}</span></label>
                                        <div class="selectgroup w-100">
                                            <label class="selectgroup-item">
                                                <input type="radio" name="google_login_status" value="1"
                                                    class="selectgroup-input"
                                                    {{ !empty($data) && $data->google_login_status == 1 ? 'checked' : '' }}>
                                                <span class="selectgroup-button">{{ __('Active') }}</span>
                                            </label>

                                            <label class="selectgroup-item">
                                                <input type="radio" name="google_login_status" value="0"
                                                    class="selectgroup-input"
                                                    {{ !empty($data) && $data->google_login_status == 0 ? 'checked' : '' }}>
                                                <span class="selectgroup-button">{{ __('Deactive') }}</span>
                                            </label>
                                        </div>

                                        @if ($errors->has('google_login_status'))
                                            <p class="mt-1 mb-0 text-danger">{{ $errors->first('google_login_status') }}
                                            </p>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <label>{{ __('Client ID') }} <span
                                                class="text-danger">{{ '*' }}</span></label>
                                        <input type="text" class="form-control" name="google_client_id"
                                            value="{{ !empty($data) ? $data->google_client_id : '' }}">

                                        @if ($errors->has('google_client_id'))
                                            <p class="mt-1 mb-0 text-danger">{{ $errors->first('google_client_id') }}</p>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <label>{{ __('Client Secret') }} <span
                                                class="text-danger">{{ '*' }}</span></label>
                                        <input type="text" class="form-control" name="google_client_secret"
                                            value="{{ !empty($data) ? $data->google_client_secret : '' }}">

                                        @if ($errors->has('google_client_secret'))
                                            <p class="mt-1 mb-0 text-danger">{{ $errors->first('google_client_secret') }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer mt-auto">
                            <div class="row">
                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-success">
                                        {{ __('Update') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endif

    </div>
@endsection
