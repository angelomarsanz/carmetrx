@extends('user.layout')

{{-- this style will be applied when the direction of language is right-to-left --}}
@includeIf('user.partials.rtl-style')

@section('content')
    <div class="page-header">
        <h4 class="page-title">{{ __('Images & Texts') }}</h4>
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
                <a href="#">{{ __('Images & Texts') }}</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <form
                    action="{{ route('user.home_page.update_images_&_texts', ['language' => request()->input('language')]) }}"
                    method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="card-header">
                        <div class="row">
                            <div class="col-lg-10">
                                <div class="card-title">{{ __('Update Images & Texts') }}</div>
                            </div>

                            <div class="col-lg-2">
                                @includeIf('user.partials.languages')
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row justify-content-center">
                            <div class="col-lg-10">
                                <div class="row">
                                    @if ($themeVersion != 1)
                                        <div class="col-12 my-3">
                                            <h2 class="text-warning">{{ __('Category Section') }}</h2>
                                            <hr />
                                            <div class="row">
                                                <div class="col-lg-6  ">
                                                    <div class="form-group">
                                                        <label>{{ __('Category Section Title') }}</label>
                                                        <input class="form-control" name="category_section_title"
                                                            value="{{ $data->category_section_title ?? '' }}"
                                                            placeholder="{{ __('Enter Title') }}">
                                                    </div>
                                                </div>
                                                @if ($themeVersion != 2)
                                                    <div class="col-lg-6  ">
                                                        <div class="form-group">
                                                            <label>{{ __('Category Section Subtitle') }}</label>
                                                            <input class="form-control" name="category_section_subtitle"
                                                                value="{{ $data->category_section_subtitle ?? '' }}"
                                                                placeholder="{{ __('Enter Subtitle') }}">
                                                        </div>

                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif

                                    <div class="col-12 my-3">
                                        <h2 class="text-warning">{{ __('Property Section') }}</h2>
                                        <hr />
                                        <div class="row">
                                            <div class="col-lg-6  ">
                                                <div class="form-group">
                                                    <label>{{ __('Property Section Title') }}</label>
                                                    <input class="form-control" name="property_section_title"
                                                        value="{{ $data->property_section_title ?? '' }}"
                                                        placeholder="{{ __('Enter Title') }}">
                                                </div>


                                            </div>
                                        </div>
                                    </div>
                                    @if ($themeVersion == 3)
                                        <div class="col-12 my-3">
                                            <h2 class="text-warning">{{ __('Project Section') }}</h2>
                                            <hr />
                                            <div class="row">
                                                <div class="col-lg-6  ">
                                                    <div class="form-group">
                                                        <label>{{ __('Project Section Title') }}</label>
                                                        <input class="form-control" name="project_section_title"
                                                            value="{{ $data->project_section_title ?? '' }}"
                                                            placeholder="{{ __('Enter Title') }}">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6  ">
                                                    <div class="form-group">
                                                        <label>{{ __('Project Section Subtitle') }}</label>
                                                        <input class="form-control" name="project_section_subtitle"
                                                            value="{{ $data->project_section_subtitle ?? '' }}"
                                                            placeholder="{{ __('Enter Subtitle') }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if ($themeVersion != 3)
                                        <div class="col-12 my-3">
                                            <h2 class="text-warning">{{ __('Featured Property Section') }}</h2>
                                            <hr />
                                            <div class="row">
                                                <div class="col-lg-6  ">
                                                    <div class="form-group">
                                                        <label>{{ __('Featured Property Section Title') }}</label>
                                                        <input class="form-control" name="featured_property_section_title"
                                                            value="{{ $data->featured_property_section_title ?? '' }}"
                                                            placeholder="{{ __('Enter Title') }}">
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    @endif
                                     
                                    @if ($themeVersion == 3)
                                        <div class="col-12 my-3">
                                            <h2 class="text-warning">{{ __('Counter Section') }}</h2>
                                            <hr />
                                            <div class="row">
                                                <div class="col-lg-6  ">
                                                    <div class="form-group">
                                                        <label for="">{{ __('Background Image') }} <span
                                                                class="text-danger">{{ '*' }}</span> </label>
                                                        <br>
                                                        <div class="thumb-preview">
                                                            @if (empty($homePage->counter_bg_img))
                                                                <img src="{{ asset('assets/img/noimage.jpg') }}"
                                                                    alt="..." class="uploaded-section-img">
                                                            @else
                                                                <img src="{{ asset(\App\Constants\Constant::WEBSITE_COUNTER_SEC_IMAGE . '/' . $homePage->counter_bg_img) }}"
                                                                    alt="image" class="uploaded-section-img">
                                                            @endif
                                                        </div>

                                                        <div class="mt-3">
                                                            <div role="button" class="btn btn-primary btn-sm upload-btn">
                                                                {{ __('Choose Image') }}
                                                                <input type="file" class="section-img-input"
                                                                    name="counter_bg_img">
                                                            </div>
                                                        </div>
                                                        @error('counter_bg_img')
                                                            <p class="mt-2 mb-0 text-danger">{{ $message }}</p>
                                                        @enderror
                                                    </div>


                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    @if ($themeVersion == 1)
                                        <div class="col-12 my-3">
                                            <h2 class="text-warning">{{ __('City Section') }}</h2>
                                            <hr />
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="">{{ __('Background Image') }}</label>
                                                        <br>
                                                        <div class="thumb-preview">
                                                            @if (empty($homePage?->city_bg_img))
                                                                <img src="{{ asset('assets/img/noimage.jpg') }}"
                                                                    alt="..." class="uploaded-section-img">
                                                            @else
                                                                <img src="{{ asset(\App\Constants\Constant::WEBSITE_CITY_SECTION_IMAGE . '/' . $homePage?->city_bg_img) }}"
                                                                    alt="image" class="uploaded-section-img">
                                                            @endif
                                                        </div>

                                                        <div class="mt-3">
                                                            <div role="button" class="btn btn-primary btn-sm upload-btn">
                                                                {{ __('Choose Image') }}
                                                                <input type="file" class="section-img-input"
                                                                    name="city_bg_img">
                                                            </div>
                                                        </div>
                                                        @error('city_bg_img')
                                                            <p class="mt-2 mb-0 text-danger">{{ $message }}</p>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>{{ __('City Section Title') }}</label>
                                                        <input class="form-control" name="city_section_title"
                                                            value="{{ $data->city_section_title ?? '' }}"
                                                            placeholder="{{ __('Enter Title') }}">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>{{ __('City Section Subtitle') }}</label>
                                                        <input class="form-control" name="city_section_subtitle"
                                                            value="{{ $data->city_section_subtitle ?? '' }}"
                                                            placeholder="{{ __('Enter Subtitle') }}">
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    @endif
                                     
                                    @if ($themeVersion == 1)
                                        <div class="col-12 my-3">
                                            <h2 class="text-warning">{{ __('Newsletter Section') }}</h2>
                                            <hr />
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="">{{ __('Background Image') }} <span
                                                                class="text-danger">{{ '*' }}</span> </label>
                                                        <br>
                                                        <div class="thumb-preview">
                                                            @if (empty($homePage?->newsletter_bg_img))
                                                                <img src="{{ asset('assets/img/noimage.jpg') }}"
                                                                    alt="..." class="uploaded-section-img">
                                                            @else
                                                                <img src="{{ asset(Constant::WEBSITE_NEWSLETTER_IMAGE . '/' . $homePage?->newsletter_bg_img) }}"
                                                                    alt="image" class="uploaded-section-img">
                                                            @endif
                                                        </div>

                                                        <div class="mt-3">
                                                            <div role="button" class="btn btn-primary btn-sm upload-btn">
                                                                {{ __('Choose Image') }}
                                                                <input type="file" class="section-img-input"
                                                                    name="newsletter_bg_img">
                                                            </div>
                                                        </div>
                                                        @error('newsletter_bg_img')
                                                            <p class="mt-2 mb-0 text-danger">{{ $message }}</p>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label
                                                            for="">{{ __('Newsletter Section Title') }}</label>
                                                        <input type="text" class="form-control"
                                                            name="newsletter_title"
                                                            value="{{ empty($newsletterInfo->title) ? '' : $newsletterInfo->title }}"
                                                            placeholder="{{ __('Enter Title') }}">
                                                        @error('title')
                                                            <p class="mt-2 mb-0 text-danger">{{ $message }}</p>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label
                                                            for="">{{ __('Newsletter Section Subtitle') }}</label>
                                                        <input type="text" class="form-control"
                                                            name="newsletter_subtitle"
                                                            value="{{ empty($newsletterInfo->subtitle) ? '' : $newsletterInfo->subtitle }}"
                                                            placeholder="{{ __('Enter Subtitle') }}">
                                                        @error('subtitle')
                                                            <p class="mt-2 mb-0 text-danger">{{ $message }}</p>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label
                                                            for="">{{ __('Newsletter Section Button Name') }}</label>
                                                        <input type="text" class="form-control"
                                                            name="newsletter_button_name"
                                                            value="{{ empty($newsletterInfo->btn_name) ? '' : $newsletterInfo->btn_name }}"
                                                            placeholder="{{ __('Enter button name') }}">
                                                        @error('button_name')
                                                            <p class="mt-2 mb-0 text-danger">{{ $message }}</p>
                                                        @enderror
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    @endif
                                    @if ($themeVersion == 2)
                                        <div class="col-12 my-3">
                                            <h2 class="text-warning">{{ __('Video Section') }}</h2>
                                            <hr />
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="">{{ __('Background Image') }}</label>
                                                        <br>
                                                        <div class="thumb-preview">
                                                            @if (empty($homePage?->video_bg_img))
                                                                <img src="{{ asset('assets/img/noimage.jpg') }}"
                                                                    alt="..." class="uploaded-section-img">
                                                            @else
                                                                <img src="{{ asset(Constant::WEBSITE_VIDEO_SECTION_IMAGE . '/' . $homePage?->video_bg_img) }}"
                                                                    alt="image" class="uploaded-section-img">
                                                            @endif
                                                        </div>

                                                        <div class="mt-3">
                                                            <div role="button" class="btn btn-primary btn-sm upload-btn">
                                                                {{ __('Choose Image') }}
                                                                <input type="file" class="section-img-input"
                                                                    name="video_bg_img">
                                                            </div>
                                                        </div>
                                                        @error('video_bg_img')
                                                            <p class="mt-2 mb-0 text-danger">{{ $message }}</p>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label for="">{{ __('Video Section Title') }}</label>
                                                        <input type="text" class="form-control"
                                                            name="videosection_title"
                                                            value="{{ empty($videoInfo->title) ? '' : $videoInfo->title }}"
                                                            placeholder="{{ __('Enter Title') }}">
                                                        @error('title')
                                                            <p class="mt-2 mb-0 text-danger">{{ $message }}</p>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label for="">{{ __('Video Section Subtitle') }}</label>
                                                        <input type="text" class="form-control"
                                                            name="videosection_subtitle"
                                                            value="{{ empty($videoInfo->subtitle) ? '' : $videoInfo->subtitle }}"
                                                            placeholder="{{ __('Enter Subtitle') }}">
                                                        @error('subtitle')
                                                            <p class="mt-2 mb-0 text-danger">{{ $message }}</p>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label for="">{{ __('Video Section Video Url') }}</label>
                                                        <input type="url" class="form-control  "
                                                            name="videosection_video_url"
                                                            value="{{ empty($videoInfo->url) ? '' : $videoInfo->url }}"
                                                            placeholder="{{ __('Enter video url') }}">
                                                        @error('video_url')
                                                            <p class="mt-2 mb-0 text-danger">{{ $message }}</p>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label for="">{{ __('Video Section Text') }}</label>
                                                        <textarea name="videosection_text" class="form-control" rows="3" placeholder="{{ __('Enter Text') }}">{{ empty($videoInfo->text) ? '' : $videoInfo->text }}</textarea>
                                                        @error('text')
                                                            <p class="mt-2 mb-0 text-danger">{{ $message }}</p>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if ($themeVersion == 1)
                                        <div class="col-12 my-3">
                                            <h2 class="text-warning">{{ __('Agent Section') }}</h2>
                                            <hr />
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>{{ __('Agent Section Title') }}</label>
                                                        <input class="form-control" name="agent_section_title"
                                                            value="{{ $data->agent_section_title ?? '' }}"
                                                            placeholder="{{ __('Enter Title') }}">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>{{ __('Agent Section Subtitle') }}</label>
                                                        <input class="form-control" name="agent_section_subtitle"
                                                            value="{{ $data->agent_section_subtitle ?? '' }}"
                                                            placeholder="{{ __('Enter Subtitle') }}">
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    @endif
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
