@extends('agent.layout')

@section('content')
    <div class="page-header">
        <h4 class="page-title">{{ __('Edit Profile') }}</h4>
        <ul class="breadcrumbs">
            <li class="nav-home">
                <a href="{{ route('agent.dashboard', getParam()) }}">
                    <i class="flaticon-home"></i>
                </a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="#">{{ __('Edit Profile') }}</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card-title">{{ __('Edit Profile') }}</div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-8 mx-auto">
                            <div class="alert alert-danger pb-1 dis-none" id="propertyErrors">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                <ul></ul>
                            </div>
                            <form id="propertyForm" action="{{ route('agent.update_profile', getParam()) }}" method="post"
                                enctype="multipart/form-data">
                                @csrf
                                <h2>{{ __('Details') }}</h2>
                                <hr>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">{{ __('Photo') }}</label>
                                            <br>
                                            <div class="thumb-preview">
                                                @if ($agent->image != null)
                                                    <img src="{{ asset($agent->image) }}" alt="..."
                                                        class="uploaded-img">
                                                @endif

                                            </div>

                                            <div class="mt-3">
                                                <div role="button" class="btn btn-primary btn-sm upload-btn">
                                                    {{ __('Choose Photo') }}
                                                    <input type="file" class="img-input" name="photo">
                                                </div>
                                                <p id="editErr_photo" class="mt-1 mb-0 text-danger em"></p>
                                                {{-- <p class="mt-2 mb-0 text-warning">
                                                    {{ __('Image Size 80 * 80') }}</p> --}}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label>{{ __('Username') }} <span
                                                    class="text-danger">{{ '*' }}</span> </label>
                                            <input type="text" value="{{ $agent->username }}" class="form-control"
                                                name="username">
                                            <p id="editErr_username" class="mt-1 mb-0 text-danger em"></p>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label>{{ __('Email') }} <span
                                                    class="text-danger">{{ '*' }}</span></label>
                                            <input type="text" value="{{ $agent->email }}" class="form-control"
                                                name="email">
                                            <p id="editErr_email" class="mt-1 mb-0 text-danger em"></p>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label>{{ __('Phone') }} <span
                                                    class="text-danger">{{ '*' }}</span> </label>
                                            <input type="tel" value="{{ $agent->phone }}" class="form-control"
                                                name="phone">
                                            <p id="editErr_phone" class="mt-1 mb-0 text-danger em"></p>
                                        </div>
                                    </div>

                                    <div class="col-lg-12">
                                        <div class="row">
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox"
                                                            {{ $agent->show_email_addresss == 1 ? 'checked' : '' }}
                                                            name="show_email_addresss" class="custom-control-input"
                                                            id="show_email_addresss">
                                                        <label class="custom-control-label"
                                                            for="show_email_addresss">{{ __('Show Email Address') }}</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox"
                                                            {{ $agent->show_phone_number == 1 ? 'checked' : '' }}
                                                            name="show_phone_number" class="custom-control-input"
                                                            id="show_phone_number">
                                                        <label class="custom-control-label"
                                                            for="show_phone_number">{{ __('Show Phone Number') }}</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox"
                                                            {{ $agent->show_contact_form == 1 ? 'checked' : '' }}
                                                            name="show_contact_form" class="custom-control-input"
                                                            id="show_contact_form">
                                                        <label class="custom-control-label"
                                                            for="show_contact_form">{{ __('Show Contact Form') }}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-12">
                                        <div id="accordion" class="mt-5">
                                            @foreach ($tenantLangs as $language)
                                                <div class="version">
                                                    <div class="version-header" id="heading{{ $language->id }}">
                                                        <h5 class="mb-0">
                                                            <button type="button"
                                                                class="btn btn-link {{ $language->direction == 1 ? 'rtl text-right' : '' }}"
                                                                data-toggle="collapse"
                                                                data-target="#collapse{{ $language->id }}"
                                                                aria-expanded="{{ $language->is_default == 1 ? 'true' : 'false' }}"
                                                                aria-controls="collapse{{ $language->id }}">
                                                                {{ $language->name . __(' Language') }}
                                                                {{ $language->is_default == 1 ? '(Default)' : '' }}
                                                            </button>
                                                        </h5>
                                                    </div>

                                                    @php
                                                        $agent_info = $agent
                                                            ->agentInfo(function ($q) use ($language) {
                                                                $q->where('language_id', $language->id);
                                                            })
                                                            ->first();

                                                    @endphp

                                                    <div id="collapse{{ $language->id }}"
                                                        class="collapse {{ $language->is_default == 1 ? 'show' : '' }}"
                                                        aria-labelledby="heading{{ $language->id }}"
                                                        data-parent="#accordion">
                                                        <div class="version-body">
                                                            <div class="row">
                                                                <div class="col-lg-4">
                                                                    <div class="form-group">
                                                                        <label>{{ __('First Name') }} <span
                                                                                class="text-danger">{{ '*' }}</span>
                                                                        </label>
                                                                        <input type="text"
                                                                            value="{{ !empty($agent_info) ? $agent_info->first_name : '' }}"
                                                                            class="form-control"
                                                                            name="{{ $language->code }}_first_name"
                                                                            placeholder="{{ __('Enter first name') }}">
                                                                        <p id="editErr_{{ $language->code }}_first_name"
                                                                            class="mt-1 mb-0 text-danger em"></p>
                                                                    </div>
                                                                </div>

                                                                <div class="col-lg-4">
                                                                    <div class="form-group">
                                                                        <label>{{ __('Last Name') }} <span
                                                                                class="text-danger">{{ '*' }}</span>
                                                                        </label>
                                                                        <input type="text"
                                                                            value="{{ !empty($agent_info) ? $agent_info->last_name : '' }}"
                                                                            class="form-control"
                                                                            name="{{ $language->code }}_last_name"
                                                                            placeholder="{{ __('Enter last name') }}">
                                                                        <p id="editErr_{{ $language->code }}_last_name"
                                                                            class="mt-1 mb-0 text-danger em"></p>
                                                                    </div>
                                                                </div>

                                                                <div class="col-lg-4">
                                                                    <div class="form-group">
                                                                        <label>{{ __('Country') }}</label>
                                                                        <input type="text"
                                                                            value="{{ !empty($agent_info) ? $agent_info->country : '' }}"
                                                                            class="form-control"
                                                                            name="{{ $language->code }}_country"
                                                                            placeholder="{{ __('Enter country name') }}">
                                                                        <p id="editErr_{{ $language->code }}_country"
                                                                            class="mt-1 mb-0 text-danger em"></p>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-4">
                                                                    <div class="form-group">
                                                                        <label>{{ __('City') }}</label>
                                                                        <input type="text"
                                                                            value="{{ !empty($agent_info) ? $agent_info->city : '' }}"
                                                                            class="form-control"
                                                                            name="{{ $language->code }}_city"
                                                                            placeholder="{{ __('Enter city name') }}">
                                                                        <p id="editErr_{{ $language->code }}_city"
                                                                            class="mt-1 mb-0 text-danger em"></p>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-4">
                                                                    <div class="form-group">
                                                                        <label>{{ __('State') }}</label>
                                                                        <input type="text"
                                                                            value="{{ !empty($agent_info) ? $agent_info->state : '' }}"
                                                                            class="form-control"
                                                                            name="{{ $language->code }}_state"
                                                                            placeholder="{{ __('Enter state name') }}">
                                                                        <p id="editErr_{{ $language->code }}_state"
                                                                            class="mt-1 mb-0 text-danger em"></p>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-4">
                                                                    <div class="form-group">
                                                                        <label>{{ __('Zip Code') }}</label>
                                                                        <input type="text"
                                                                            value="{{ !empty($agent_info) ? $agent_info->zip_code : '' }}"
                                                                            class="form-control"
                                                                            name="{{ $language->code }}_zip_code"
                                                                            placeholder="{{ __('Enter zip code') }}">
                                                                        <p id="editErr_{{ $language->code }}_zip_code"
                                                                            class="mt-1 mb-0 text-danger em">
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-12">
                                                                    <div class="form-group">
                                                                        <label>{{ __('Address') }}</label>
                                                                        <textarea name="{{ $language->code }}_address" class="form-control" placeholder="{{ __('Enter Address') }}">{{ !empty($agent_info) ? $agent_info->address : '' }}</textarea>
                                                                        <p id="editErr_{{ $language->code }}_email"
                                                                            class="mt-1 mb-0 text-danger em"></p>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-12">
                                                                    <div class="form-group">
                                                                        <label>{{ __('Details') }}</label>
                                                                        <textarea name="{{ $language->code }}_details" class="form-control summernote" rows="5"
                                                                            placeholder="{{ __('Enter details about you') }}">{{ !empty($agent_info) ? $agent_info->details : '' }}</textarea>
                                                                        <p id="editErr_{{ $language->code }}_details"
                                                                            class="mt-1 mb-0 text-danger em"></p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col">
                                                                    @php $currLang = $language; @endphp

                                                                    @foreach ($tenantLangs as $language)
                                                                        @continue($language->id == $currLang->id)

                                                                        <div class="form-check py-0">
                                                                            <label class="form-check-label">
                                                                                <input class="form-check-input"
                                                                                    type="checkbox"
                                                                                    onchange="cloneInput('collapse{{ $currLang->id }}', 'collapse{{ $language->id }}', event)">
                                                                                <span
                                                                                    class="form-check-sign">{{ __('Clone for') }}
                                                                                    <strong
                                                                                        class="text-capitalize text-secondary">{{ $language->name }}</strong>
                                                                                    {{ __('language') }}
                                                                                </span>
                                                                            </label>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="row">
                        <div class="col-12 text-center">
                            <button type="submit" id="propertySubmit" class="btn btn-success">
                                {{ __('Update') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection
