@extends('user.layout')

{{-- this style will be applied when the direction of language is right-to-left --}}
@includeIf('user.partials.rtl-style')

@section('content')
    <div class="page-header">
        <h4 class="page-title">{{ __('Page Headings') }}</h4>
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
                <a href="#">{{ __('Page Headings') }}</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">

                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-10">
                            <div class="card-title">{{ __('Update Page Headings') }}
                            </div>
                        </div>

                        <div class="col-lg-2">
                            @includeIf('user.partials.languages')
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row justify-content-center">
                        <div class="col-lg-10  ">
                           
                            <form id="formSubmit"
                                action="{{ route('user.update_page_headings', ['language' => request()->input('language')]) }}"
                                method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label>{{ __('About Page Title') }} <span
                                                    class="text-danger">{{ '*' }}</span></label>
                                            <input type="text" class="form-control" name="about_page_title"
                                                value="{{ old('about_page_title', $data?->about_page_title) }}">
                                            @error('about_page_title')
                                                <p class="mt-2 mb-0 text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label>{{ __('Blog Page Title') }} <span
                                                    class="text-danger">{{ '*' }}</span></label>
                                            <input type="text" class="form-control" name="blog_page_title"
                                                value="{{ old('blog_page_title', $data?->blog_page_title) }}">
                                            @error('blog_page_title')
                                                <p class="mt-2 mb-0 text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label>{{ __('Contact Page Title') }} <span
                                                    class="text-danger">{{ '*' }}</span> </label>
                                            <input type="text" class="form-control" name="contact_page_title"
                                                value="{{ old('contact_page_title', $data?->contact_page_title) }}">
                                            @error('contact_page_title')
                                                <p class="mt-2 mb-0 text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label>{{ __('Projects Page Title') }} <span
                                                    class="text-danger">{{ '*' }}</span></label>
                                            <input type="text" class="form-control" name="projects_page_title"
                                                value="{{ old('projects_page_title', $data?->projects_page_title) }}">
                                            @error('projects_page_title')
                                                <p class="mt-2 mb-0 text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label>{{ __('Properties Page Title') }} <span
                                                    class="text-danger">{{ '*' }}</span> </label>
                                            <input type="text" class="form-control" name="properties_page_title"
                                                value="{{ old('properties_page_title', $data?->properties_page_title) }}">
                                            @error('properties_page_title')
                                                <p class="mt-2 mb-0 text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                    

                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label>{{ __('FAQ Page Title') }} <span
                                                    class="text-danger">{{ '*' }}</span> </label>
                                            <input type="text" class="form-control" name="faq_page_title"
                                                value="{{ old('faq_page_title', $data?->faq_page_title) }}">
                                            @error('faq_page_title')
                                                <p class="mt-2 mb-0 text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label>{{ __('Forget Password Page Title') }} <span
                                                    class="text-danger">{{ '*' }}</span> </label>
                                            <input type="text" class="form-control" name="forget_password_page_title"
                                                value="{{ old('forget_password_page_title', $data?->forget_password_page_title) }}">
                                            @error('forget_password_page_title')
                                                <p class="mt-2 mb-0 text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label>{{ __('User Login Page Title') }} <span
                                                    class="text-danger">{{ '*' }}</span></label>
                                            <input type="text" class="form-control" name="login_page_title"
                                                value="{{ old('login_page_title', $data?->login_page_title) }}">
                                            @error('login_page_title')
                                                <p class="mt-2 mb-0 text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label>{{ __('Signup Page Title') }} <span
                                                    class="text-danger">{{ '*' }}</span> </label>
                                            <input type="text" class="form-control" name="signup_page_title"
                                                value="{{ old('signup_page_title', $data?->signup_page_title) }}">
                                            @error('signup_page_title')
                                                <p class="mt-2 mb-0 text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label>{{ __('Agent Login Page Title') }} <span
                                                    class="text-danger">{{ '*' }}</span></label>
                                            <input type="text" class="form-control" name="agent_login_page_title"
                                                value="{{ old('agent_login_page_title', $data?->agent_login_page_title) }}">
                                            @error('agent_login_page_title')
                                                <p class="mt-2 mb-0 text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label>{{ __('Agent Forget Password Page Title') }} <span
                                                    class="text-danger">{{ '*' }}</span></label>
                                            <input type="text" class="form-control"
                                                name="agent_forget_password_page_title"
                                                value="{{ old('agent_forget_password_page_title', $data?->agent_forget_password_page_title) }}">
                                            @error('agent_forget_password_page_title')
                                                <p class="mt-2 mb-0 text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label>{{ __('Team') . '/' . __('Agents Page Title') }} <span
                                                    class="text-danger">{{ '*' }}</span> </label>
                                            <input type="text" class="form-control" name="agents_page_title"
                                                value="{{ old('agents_page_title', $data?->agents_page_title) }}">
                                            @error('agents_page_title')
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
                            <button type="submit" form="formSubmit" class="btn btn-success">
                                {{ __('Update') }}
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
