@extends('user.layout')

@section('content')
    <div class="page-header">
        <h4 class="page-title">{{ __('Footer Logo') }}</h4>
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
                <a href="#">{{ __('Basic Settings') }}</a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="#">{{ __('Footer Logo') }}</a>
            </li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-10">
                            <div class="card-title">{{ __('Update Footer Logo') }}</div>
                        </div>
                        
                    </div>

                </div>
                <div class="card-body pt-5 pb-4">
                    <div class="row">
                        <div class="col-lg-6 offset-lg-3">
                            <form id="ajaxForm" action="{{ route('user.footer.logo.update') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <div class="col-12 mb-2">
                                                <label for="image"><strong>
                                                        {{ __('Logo') }} </strong> <span
                                                        class="text-danger">{{ '*' }}</span> </label>
                                            </div>
                                            <div class="col-md-12 showImage mb-3">
                                                <img src="{{ asset(Constant::WEBSITE_FOOTER_LOGO . '/' . $basic_setting->footer_logo) }}"
                                                    alt="..." class="img-thumbnail">
                                            </div>
                                            <input type="file" name="footer_logo" id="image" class="form-control">
                                            <p class="text-warning mb-0">
                                                {{   __('Only JPG, JPEG, PNG images are allowed') }}
                                            </p>
                                            <p id="errfooter_logo" class="mb-0 text-danger em"></p>
                                            <p class="text-warning mb-0">
                                                {{ __('Upload 170 * 50 image for best quality') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-footer">
                                    <div class="form">
                                        <div class="form-group from-show-notify row">
                                            <div class="col-12 text-center">
                                                <button type="button" id="submitBtn"
                                                    class="btn btn-success">{{   __('Update') }}</button>
                                            </div>
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
@endsection
