@extends('user.layout')

@section('content')
    <div class="page-header">
        <h4 class="page-title">{{ __('Contact Page') }}</h4>
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
                <a href="#">{{ __('Pages') }}</a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="#">{{ __('Contact Page') }}</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <form action="{{ route('user.update.contact_form') }}" method="post">
                    @csrf
                    <div class="card-header">
                        <div class="row">
                            <div class="col-lg-10">
                                <div class="card-title">{{ __('Update Contact Page Information') }}
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6 offset-lg-3">


                                <div class="form-group">
                                    <label>{{ __('Email Address') }} <span class="text-danger">{{ '*' }}</span>
                                    </label>
                                    <input type="text" class="form-control" name="email_address"
                                        value="{{ $data->email_address ?? old('email_address') }}"
                                        placeholder="{{ __('Enter email address') }}" data-role="tagsinput">
                                    @if ($errors->has('email_address'))
                                        <p class="mt-2 mb-0 text-danger">{{ $errors->first('email_address') }}</p>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <label>{{ __('Contact Number') }} <span class="text-danger">{{ '*' }}</span>
                                    </label>

                                    <input type="text" class="form-control" name="contact_number"
                                        value="{{ $data->contact_number ?? old('contact_number') }}"
                                        placeholder="{{ __('Enter contact number') }}" data-role="tagsinput">
                                    @if ($errors->has('contact_number'))
                                        <p class="mt-2 mb-0 text-danger">{{ $errors->first('contact_number') }}</p>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <label>{{ __('Address') }} <span class="text-danger">{{ '*' }}</span>
                                    </label>
                                    <input type="text" class="form-control" name="address"
                                        value="{{ $data->address ?? old('address') }}"
                                        placeholder="{{ __('Enter Address') }}">
                                    @if ($errors->has('address'))
                                        <p class="mt-2 mb-0 text-danger">{{ $errors->first('address') }}</p>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <label>{{ __('Latitude') }} <span class="text-danger">{{ '*' }}</span>
                                    </label>
                                    <input type="text" class="form-control" name="latitude"
                                        value="{{ $data->latitude ?? old('latitude') }}"
                                        placeholder="{{ __('Enter latitude') }}">
                                    @if ($errors->has('latitude'))
                                        <p class="mt-2 mb-0 text-danger">{{ $errors->first('latitude') }}</p>
                                    @endif
                                    <p class="mt-2 mb-0 text-warning">
                                        {{ __('The value of the latitude will be helpful to show your location in the map') }}
                                    </p>
                                </div>

                                <div class="form-group">
                                    <label>{{ __('Longitude') }} <span class="text-danger">{{ '*' }}</span>
                                    </label>
                                    <input type="text" class="form-control" name="longitude"
                                        value="{{ $data->longitude ?? old('longitude') }}"
                                        placeholder="{{ __('Enter Longitude') }}">
                                    @if ($errors->has('longitude'))
                                        <p class="mt-2 mb-0 text-danger">{{ $errors->first('longitude') }}</p>
                                    @endif
                                    <p class="mt-2 mb-0 text-warning">
                                        {{ __('The value of the longitude will be helpful to show your location in the map') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
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
    </div>
@endsection
