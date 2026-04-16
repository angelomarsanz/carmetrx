@extends('user.layout')

@section('content')
    <div class="page-header">
        <h4 class="page-title">{{ __('Breadcrumb') }}</h4>
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
                <a href="#">{{ __('Breadcrumb') }}</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-10">
                            <div class="card-title">{{ __('Update Breadcrumb') }}</div>
                        </div>
                       
                    </div>
                </div>

                <div class="card-body pt-5 pb-4">
                    <div class="row">
                        <div class="col-lg-6 offset-lg-3">
                            <form id="ajaxForm" action="{{ route('user.update_breadcrumb') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <div class="col-12 mb-2">
                                        <label for="">{{ __('Breadcrumb') }} <span
                                                class="text-danger">{{ '*' }}</span></label>
                                    </div>
                                    <div class="col-md-12 showImage mb-3">
                                        <img src="{{ !empty($basic_setting->breadcrumb) ? asset(Constant::WEBSITE_BREADCRUMB . '/' . $basic_setting->breadcrumb) : asset('assets/img/noimage.jpg') }}"
                                            alt="..." class="img-thumbnail">
                                    </div>
                                    <input type="file" name="breadcrumb" id="image" class="form-control image">
                                    <p class="text-warning mb-0">
                                        {{ __('Only JPG, JPEG, PNG images are allowed') }}
                                    </p>
                                    <p id="errbreadcrumb" class="mb-0 text-danger em"></p>
                                    <p class="text-warning mb-0">
                                        {{ __('Upload 1920 * 820 image for best quality') }}
                                    </p>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="row">
                        <div class="col-12 text-center">
                            <button type="button" id="submitBtn" class="btn btn-success">
                                {{ __('Update') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
