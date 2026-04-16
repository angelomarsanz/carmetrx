@extends('user.layout')

{{-- this style will be applied when the direction of language is right-to-left --}}
@includeIf('user.partials.rtl-style')

@section('content')
    <div class="page-header">
        <h4 class="page-title">{{ __('Edit Profile') }}</h4>
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
                <a href="#">{{ __('Edit Profile') }}</a>
            </li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title d-inline-block">{{ __('Update Profile') }}</div>
                </div>
                <div class="card-body pt-5 pb-5">
                    <div class="row">
                        <div class="col-lg-6 offset-lg-3">
                            <form id="ajaxForm" class="" action="{{ route('user-profile-update') }}" method="post"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <div class="col-12 mb-2">
                                                <label for="image"><strong>{{ __('Image') . '*' }}</strong></label>
                                            </div>
                                            <div class="col-md-12 showImage mb-3">
                                                <img src="{{ asset($user->photo) }}" alt="..." class="img-thumbnail">
                                            </div>
                                            <input type="file" name="photo" id="image" class="form-control">
                                            <p id="errphoto" class="mt-2 mb-0 text-danger em"></p>
                                            <p class="text-warning mb-0">
                                                {{ __('Upload 100 * 100 image for best quality') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="row">
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox"
                                                            {{ $user->show_email_addresss == 1 ? 'checked' : '' }}
                                                            name="show_email_addresss" class="custom-control-input"
                                                            id="show_email_addresss">
                                                        <label class="custom-control-label"
                                                            for="show_email_addresss">{{ __('Show Email Address') }}</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox"
                                                            {{ $user->show_phone_number == 1 ? 'checked' : '' }}
                                                            name="show_phone_number" class="custom-control-input"
                                                            id="show_phone_number">
                                                        <label class="custom-control-label"
                                                            for="show_phone_number">{{ __('Show Phone Number') }}</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox"
                                                            {{ $user->show_contact_form == 1 ? 'checked' : '' }}
                                                            name="show_contact_form" class="custom-control-input"
                                                            id="show_contact_form">
                                                        <label class="custom-control-label"
                                                            for="show_contact_form">{{ __('Show Contact Form') }}</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox"
                                                            {{ $user->show_profile == 1 ? 'checked' : '' }}
                                                            name="show_profile" class="custom-control-input"
                                                            id="show_profile">
                                                        <label class="custom-control-label"
                                                            for="show_profile">{{ __('Show Profile') }}</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox"
                                                            {{ $user->show_profile_on_admin_website == 1 ? 'checked' : '' }}
                                                            name="show_profile_on_admin_website"
                                                            class="custom-control-input" id="show_profile_on_admin_website">
                                                        <label class="custom-control-label"
                                                            for="show_profile_on_admin_website">{{ __('Show Profile on the Admin Website') }}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="">{{ __('First Name') }} *</label>
                                            <input type="text" class="form-control" name="first_name"
                                                value="{{ $user->first_name }}">
                                            <p id="errfirst_name" class="mb-0 text-danger em"></p>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="">{{ __('Last Name') }} *</label>
                                            <input type="text" class="form-control" name="last_name"
                                                value="{{ $user->last_name }}">
                                            <p id="errlast_name" class="mb-0 text-danger em"></p>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="">{{ __('Company Name') }} *</label>
                                            <input type="text" class="form-control" name="company_name"
                                                value="{{ $user->company_name }}">
                                            <p id="errcompany_name" class="mb-0 text-danger em"></p>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="">{{ __('Username') }} *</label>
                                            <input type="text" class="form-control" name="username"
                                                value="{{ $user->username }}">
                                            <p id="errusername" class="mb-0 text-danger em"></p>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="">{{ __('Phone') }} *</label>
                                            <input type="text" class="form-control" name="phone"
                                                value="{{ $user->phone }}">
                                            <p id="errphone" class="mb-0 text-danger em"></p>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="">{{ __('Address') }}</label>
                                            <input type="text" class="form-control" name="address"
                                                value="{{ $user->address }}">

                                            <p id="erraddress" class="mb-0 text-danger em"></p>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="">{{ __('City') }}</label>
                                            <input type="text" class="form-control" name="city" rows="5"
                                                value="{{ $user->city }}">
                                            <p id="errcity" class="mb-0 text-danger em"></p>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="">{{ __('State') }}</label>
                                            <input type="text" class="form-control" name="state" rows="5"
                                                value="{{ $user->state }}">
                                            <p id="errstate" class="mb-0 text-danger em"></p>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="">{{ __('Country') }} *</label>
                                            <input type="text" class="form-control" name="country" rows="5"
                                                value="{{ $user->country }}">
                                            <p id="errcountry" class="mb-0 text-danger em"></p>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="form">
                        <div class="form-group from-show-notify row">
                            <div class="col-12 text-center">
                                <button type="submit" id="submitBtn"
                                    class="btn btn-success">{{ __('Update') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
