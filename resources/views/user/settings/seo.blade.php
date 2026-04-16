@extends('user.layout')

{{-- this style will be applied when the direction of language is right-to-left --}}
@includeIf('user.partials.rtl-style')

@section('content')
    <div class="page-header">
        <h4 class="page-title">{{ __('SEO Information') }}</h4>
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
                <a href="#">{{ __('SEO Information') }}</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-10">
                            <div class="card-title"></div>
                            {{ __('Update SEO Information') }}
                        </div>

                        <div class="col-lg-2">
                            @includeIf('user.partials.languages')
                        </div>
                    </div>

                </div>
                <form action="{{ route('user.basic_settings.update_seo_informations', ['language' => $language->code]) }}"
                    method="post">
                    @csrf


                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>{{ __('Meta Keywords For Home Page') }}</label>
                                    <input class="form-control" name="meta_keyword_home"
                                        value="{{ is_null($data) ? '' : $data->meta_keyword_home }}"
                                        placeholder="{{ __('Enter Meta Keywords') }}" data-role="tagsinput">
                                </div>

                                <div class="form-group">
                                    <label>{{ __('Meta Description For Home Page') }}</label>
                                    <textarea class="form-control" name="meta_description_home" rows="5"
                                        placeholder="{{  __('Enter Meta Description') }}">{{ is_null($data) ? '' : $data->meta_description_home }}</textarea>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>{{ __('Meta Keywords For About Us Page') }}</label>
                                    <input class="form-control" name="meta_keyword_about_page"
                                        value="{{ is_null($data) ? '' : $data->meta_keyword_about_page }}"
                                        placeholder="{{ __('Enter Meta Keywords') }}" data-role="tagsinput">
                                </div>

                                <div class="form-group">
                                    <label>{{   __('Meta Description For About Us Page') }}</label>
                                    <textarea class="form-control" name="meta_description_about_page" rows="5"
                                        placeholder="{{ __('Enter Meta Description') }}">{{ is_null($data) ? '' : $data->meta_description_about_page }}</textarea>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>{{ __('Meta Keywords For Agents Page') }}</label>
                                    <input class="form-control" name="meta_keyword_agents"
                                        value="{{ is_null($data) ? '' : $data->meta_keyword_agents }}"
                                        placeholder="{{  __('Enter Meta Keywords') }}"
                                        data-role="tagsinput">
                                </div>

                                <div class="form-group">
                                    <label>{{ __('Meta Description For Agents Page') }}</label>
                                    <textarea class="form-control" name="meta_description_agents" rows="5"
                                        placeholder="{{ __('Enter Meta Description') }}">{{ is_null($data) ? '' : $data->meta_description_agents }}</textarea>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>{{ __('Meta Keywords For Projects Page') }}</label>
                                    <input class="form-control" name="meta_keyword_projects"
                                        value="{{ is_null($data) ? '' : $data->meta_keyword_projects }}"
                                        placeholder="{{ __('Enter Meta Keywords') }}" data-role="tagsinput">
                                </div>

                                <div class="form-group">
                                    <label>{{ __('Meta Description For Projects Page') }}</label>
                                    <textarea class="form-control" name="meta_description_projects" rows="5"
                                        placeholder="{{ __('Enter Meta Description') }}">{{ is_null($data) ? '' : $data->meta_description_projects }}</textarea>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>{{ __('Meta Keywords For Property Page') }}</label>
                                    <input class="form-control" name="meta_keyword_properties"
                                        value="{{ is_null($data) ? '' : $data->meta_keyword_properties }}"
                                        placeholder="{{ __('Enter Meta Keywords') }}" data-role="tagsinput">
                                </div>

                                <div class="form-group">
                                    <label>{{ __('Meta Description For Property Page') }}</label>
                                    <textarea class="form-control" name="meta_description_properties" rows="5" placeholder="Enter Meta Description">{{ is_null($data) ? '' : $data->meta_description_properties }}</textarea>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>{{ __('Meta Keywords For Blog Page') }}</label>
                                    <input class="form-control" name="meta_keyword_blog"
                                        value="{{ is_null($data) ? '' : $data->meta_keyword_blog }}"
                                        placeholder="{{ __('Enter Meta Keywords') }}" data-role="tagsinput">
                                </div>

                                <div class="form-group">
                                    <label>{{ __('Meta Description For Blog Page') }}</label>
                                    <textarea class="form-control" name="meta_description_blog" rows="5"
                                        placeholder="{{ __('Enter Meta Description') }}">{{ is_null($data) ? '' : $data->meta_description_blog }}</textarea>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>{{ __('Meta Keywords For FAQ Page') }}</label>
                                    <input class="form-control" name="meta_keyword_faq"
                                        value="{{ is_null($data) ? '' : $data->meta_keyword_faq }}"
                                        placeholder="{{ __('Enter Meta Keywords') }}" data-role="tagsinput">
                                </div>

                                <div class="form-group">
                                    <label>{{ __('Meta Description For FAQ Page') }}</label>
                                    <textarea class="form-control" name="meta_description_faq" rows="5"
                                        placeholder="{{ __('Enter Meta Description') }}">{{ is_null($data) ? '' : $data->meta_description_faq }}</textarea>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>{{ __('Meta Keywords For Contact Page') }}</label>
                                    <input class="form-control" name="meta_keyword_contact"
                                        value="{{ is_null($data) ? '' : $data->meta_keyword_contact }}"
                                        placeholder="{{ __('Enter Meta Keywords') }}" data-role="tagsinput">
                                </div>

                                <div class="form-group">
                                    <label>{{ __('Meta Description For Contact Page') }}</label>
                                    <textarea class="form-control" name="meta_description_contact" rows="5"
                                        placeholder="{{ __('Enter Meta Description') }}">{{ is_null($data) ? '' : $data->meta_description_contact }}</textarea>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>{{ __('Meta Keywords For Login Page') }}</label>
                                    <input class="form-control" name="meta_keyword_login"
                                        value="{{ is_null($data) ? '' : $data->meta_keyword_login }}"
                                        placeholder="{{ __('Enter Meta Keywords') }}" data-role="tagsinput">
                                </div>

                                <div class="form-group">
                                    <label>{{ __('Meta Description For Login Page') }}</label>
                                    <textarea class="form-control" name="meta_description_login" rows="5"
                                        placeholder="{{ __('Enter Meta Description') }}">{{ is_null($data) ? '' : $data->meta_description_login }}</textarea>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>{{ __('Meta Keywords For Signup Page') }}</label>
                                    <input class="form-control" name="meta_keyword_signup"
                                        value="{{ is_null($data) ? '' : $data->meta_keyword_signup }}"
                                        placeholder="{{ __('Enter Meta Keywords') }}" data-role="tagsinput">
                                </div>

                                <div class="form-group">
                                    <label>{{ __('Meta Description For Signup Page') }}</label>
                                    <textarea class="form-control" name="meta_description_signup" rows="5"
                                        placeholder="{{ __('Enter Meta Description') }}">{{ is_null($data) ? '' : $data->meta_description_signup }}</textarea>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>{{ __('Meta Keywords For Forget Password Page') }}</label>
                                    <input class="form-control" name="meta_keyword_forget_password"
                                        value="{{ is_null($data) ? '' : $data->meta_keyword_forget_password }}"
                                        placeholder="{{ __('Enter Meta Keywords') }}" data-role="tagsinput">
                                </div>

                                <div class="form-group">
                                    <label>{{ __('Meta Description For Forget Password Page') }}</label>
                                    <textarea class="form-control" name="meta_description_forget_password" rows="5"
                                        placeholder="{{ __('Enter Meta Description') }}">{{ is_null($data) ? '' : $data->meta_description_forget_password }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="form">
                            <div class="row">
                                <div class="col-12 text-center">
                                    <button type="submit"
                                        class="btn btn-success {{ $data == null ? 'd-none' : '' }}">{{ __('Update') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
