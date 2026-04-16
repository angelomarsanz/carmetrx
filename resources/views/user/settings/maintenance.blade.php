@extends('user.layout')

@section('content')
    <div class="page-header">
        <h4 class="page-title">{{ __('Maintenance Mode') }}</h4>
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
                <a href="#">{{ __('Maintenance Mode') }}</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-10">
                            <div class="card-title">
                                {{ __('Update Maintenance Mode') }}</div>
                        </div>

                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6 offset-lg-3">
                            <form id="ajaxForm" action="{{ route('user.update_maintenance_mode') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <div class="col-12 mb-2">
                                                <label for="image"><strong>{{ __('Maintenance Mode Image') }}</strong>
                                                    <span class="text-danger">{{ '*' }}</span> </label>
                                            </div>
                                            <div class="col-md-12 showImage mb-3">
                                                <img src="{{ !empty($data->maintenance_img) ? asset(Constant::WEBSITE_MAINTENANCE_IMAGE . '/' . $data->maintenance_img) : asset('assets/img/noimage.jpg') }}"
                                                    alt="..." class="img-thumbnail">
                                            </div>
                                            <input type="file" name="maintenance_img" id="image"
                                                class="form-control">
                                            <p id="errmaintenance_img" class="mb-0 text-danger em"></p>
                                            <p class="text-warning mb-0">
                                                {{ __('Upload 770 * 720 image for best quality') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>{{ __('Maintenance Status') }} <span
                                            class="text-danger">{{ '*' }}</span> </label>
                                    <div class="selectgroup w-100">
                                        <label class="selectgroup-item">
                                            <input type="radio" name="maintenance_status" value="1"
                                                class="selectgroup-input"
                                                {{ $data->maintenance_status == 1 ? 'checked' : '' }}>
                                            <span class="selectgroup-button">{{ __('Active') }}</span>
                                        </label>
                                        <label class="selectgroup-item">
                                            <input type="radio" name="maintenance_status" value="0"
                                                class="selectgroup-input"
                                                {{ $data->maintenance_status == 0 ? 'checked' : '' }}>
                                            <span class="selectgroup-button">{{ __('Deactive') }}</span>
                                        </label>
                                    </div>
                                    <p id="errmaintenance_status" class="mb-0 text-danger em"></p>
                                </div>

                                <div class="form-group">
                                    <label>{{ __('Maintenance Message') }} <span
                                            class="text-danger">{{ '*' }}</span> </label>
                                    <textarea class="form-control" name="maintenance_msg" rows="3" cols="80">{!! $data->maintenance_msg !!}</textarea>
                                    <p id="errmaintenance_msg" class="mb-0 text-danger em"></p>
                                </div>

                                <div class="form-group">
                                    <label>{{ __('Bypass Token') }}</label>
                                    <input type="text" class="form-control" name="bypass_token"
                                        value="{{ $data->bypass_token }}" placeholder="{{ __('Enter Bypass Token') }}">
                                    <p class="mt-2 mb-0 text-info">
                                        {{ __('During maintenance, you can access the system through this token') . '.' }}
                                        <br>
                                        <strong>{{ __('Example') }}:</strong>
                                        <span
                                            class="text-warning">{{ url('/') . '/' . \Illuminate\Support\Facades\Auth::user()->username . '/{secrect_path}' }}</span><br>
                                        {{ __('Please Do not use special character in token') . '.' }}
                                    </p>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="row">
                        <div class="col-12 text-center">
                            <button type="submit" id="submitBtn" class="btn btn-success">
                                {{ __('Update') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
