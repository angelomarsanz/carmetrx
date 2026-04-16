@extends('user.layout')

{{-- this style will be applied when the direction of language is right-to-left --}}
@includeIf('user.partials.rtl-style')

@section('content')
    <div class="page-header">
        <h4 class="page-title">{{ __('Section Titles') }}</h4>
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
                <a href="#">{{ __('Section Titles') }}</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <form action="{{ route('user.home_page.update_section_titles') }}" method="post">
                    @csrf
                    <div class="card-header">
                        <div class="row">
                            <div class="col-lg-10">
                                <div class="card-title">{{ __('Section Titles') }}</div>
                            </div>

                            <div class="col-lg-2">
                                @includeIf('user.partials.languages')
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6 offset-lg-3">
                                <div class="form-group">
                                    <label>{{ __('Property Section Title') }}</label>
                                    <input class="form-control" name="property_section_title"
                                        value="{{ $data->property_section_title ?? '' }}"
                                        placeholder="{{ __('Enter Title') }}">
                                </div>

                                <div class="form-group">
                                    <label>{{ __('Featured Property Section Title') }}</label>
                                    <input class="form-control" name="featured_property_section_title"
                                        value="{{ $data->featured_property_section_title ?? '' }}"
                                        placeholder="{{ __('Enter Title') }}">
                                </div>

                                <div class="form-group">
                                    <label>{{ __('Testimonials Section Title') }}</label>
                                    <input class="form-control" name="testimonials_section_title"
                                        value="{{ $data->testimonials_section_title ?? '' }}"
                                        placeholder="{{ __('Enter Title') }}">
                                </div>

                                <div class="form-group">
                                    <label>{{ __('Blog Section Title') }}</label>
                                    <input class="form-control" name="blog_section_title"
                                        value="{{ $data->blog_section_title ?? '' }}"
                                        placeholder="{{ __('Enter Title') }}">
                                </div>

                                {{-- @if ($userBs->theme_version == 2) --}}
                                <div class="form-group">
                                    <label>{{ __('Featured Products Section Title') }}</label>
                                    <input class="form-control" name="featured_products_section_title"
                                        value="{{ $data->featured_products_section_title ?? '' }}"
                                        placeholder="{{ __('Enter Title') }}">
                                </div>

                                <div class="form-group">
                                    <label>{{ __('Newsletter Section Title') }}</label>
                                    <input class="form-control" name="newsletter_section_title"
                                        value="{{ $data->newsletter_section_title ?? '' }}"
                                        placeholder="{{ __('Enter Title') }}">
                                </div>
                                {{-- @endif --}}
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
