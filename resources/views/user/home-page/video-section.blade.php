@extends('user.layout')

@section('content')
    <div class="page-header">
        <h4 class="page-title">{{ __('Video Section') }}</h4>
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
                <a href="#">{{ __('Home Page') }}</a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="#">{{ __('Video Section') }}</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            <div class="card-title">{{ __('Video Image') }}</div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6 offset-lg-3">
                            <form id="imgForm" action="{{ route('user.home_page.update_video_img') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <label for="">{{ __('Background Image') }} <span
                                            class="text-danger">{{ '*' }}</span> </label>
                                    <br>
                                    <div class="thumb-preview">
                                        @if (empty($bgImg))
                                            <img src="{{ asset('assets/img/noimage.jpg') }}" alt="..."
                                                class="uploaded-background-img">
                                        @else
                                            <img src="{{ asset(Constant::WEBSITE_VIDEO_SECTION_IMAGE . '/' . $bgImg) }}"
                                                alt="image" class="uploaded-background-img">
                                        @endif
                                    </div>

                                    <div class="mt-3">
                                        <div role="button" class="btn btn-primary btn-sm upload-btn">
                                            {{ __('Choose Image') }}
                                            <input type="file" class="background-img-input" name="video_bg_img">
                                        </div>
                                    </div>
                                    @error('video_bg_img')
                                        <p class="mt-2 mb-0 text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="row">
                        <div class="col-12 text-center">
                            <button type="submit" form="imgForm" class="btn btn-success">
                                {{ __('Update') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="card-title">{{ __('Update Video Section') }}
                            </div>
                        </div>

                        <div class="col-lg-4">
                            @includeIf('user.partials.languages')
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <form id="actionForm"
                                action="{{ route('user.home_page.update_video_info', ['language' => request()->input('language')]) }}"
                                method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="">{{ __('Title') }} <span
                                                    class="text-danger">{{ '*' }}</span> </label>
                                            <input type="text" class="form-control" name="title"
                                                value="{{ empty($data->title) ? '' : $data->title }}"
                                                placeholder="{{ __('Enter Title') }}">
                                            @error('title')
                                                <p class="mt-2 mb-0 text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="">{{ __('Subtitle') }}</label>
                                            <input type="text" class="form-control" name="subtitle"
                                                value="{{ empty($data->subtitle) ? '' : $data->subtitle }}"
                                                placeholder="{{ __('Enter Subtitle') }}">
                                            @error('subtitle')
                                                <p class="mt-2 mb-0 text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">


                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">{{ __('Video Url') }}</label>
                                            <input type="url" class="form-control  " name="video_url"
                                                value="{{ empty($data->url) ? '' : $data->url }}"
                                                placeholder="{{ __('Enter video url') }}">
                                            @error('video_url')
                                                <p class="mt-2 mb-0 text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">{{ __('Text') }}</label>
                                            <textarea name="text" class="form-control" rows="3" placeholder="{{ __('Enter Text') }}">{{ empty($data->text) ? '' : $data->text }}</textarea>
                                            @error('text')
                                                <p class="mt-2 mb-0 text-danger">{{ $message }}</p>
                                            @enderror
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
                            <button type="submit" form="actionForm" class="btn btn-success">
                                {{ __('Update') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
