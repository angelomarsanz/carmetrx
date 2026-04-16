@extends('admin.layout')

@section('content')
    <div class="page-header">
        <h4 class="page-title">{{ __('Package Features') }}</h4>
        <ul class="breadcrumbs">
            <li class="nav-home">
                <a href="{{ route('admin.dashboard') }}">
                    <i class="flaticon-home"></i>
                </a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="#">{{ __('Packages Management') }}</a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="#">{{ __('Package Features') }}</a>
            </li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-12">

            <div class="card">
                <div class="card-header">
                    <div class="card-title d-inline-block">{{ __('Package Features') }}</div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-8 offset-lg-2">
                            <form id="permissionsForm" class="" action="{{ route('admin.package.features') }}"
                                method="post">
                                {{ csrf_field() }}
                                <div class="alert alert-warning">
                                    {{ __('Only these selected features will be visible in frontend Pricing Section') }}
                                </div>
                                <div class="form-group">

                                    <label class="form-label">{{ __('Package Features') }}</label>
                                    <div class="selectgroup selectgroup-pills">



                                        <label class="selectgroup-item">
                                            <input type="checkbox" name="features[]" value="Custom Domain"
                                                class="selectgroup-input" @if (is_array($features) && in_array('Custom Domain', $features)) checked @endif>
                                            <span class="selectgroup-button">{{ __('Custom Domain') }}</span>
                                        </label>

                                        <label class="selectgroup-item">
                                            <input type="checkbox" name="features[]" value="Subdomain"
                                                class="selectgroup-input" @if (is_array($features) && in_array('Subdomain', $features)) checked @endif>
                                            <span class="selectgroup-button">{{ __('Subdomain') }}</span>
                                        </label>
                                        <label class="selectgroup-item">
                                            <input type="checkbox" name="features[]" value="Additional Language"
                                                class="selectgroup-input" @if (is_array($features) && in_array('Additional Language', $features)) checked @endif>
                                            <span class="selectgroup-button">{{ __('Additional Language') }}</span>
                                        </label>

                                        <label class="selectgroup-item">
                                            <input type="checkbox" name="features[]" value="Agent"
                                                class="selectgroup-input" @if (is_array($features) && in_array('Agent', $features)) checked @endif>
                                            <span class="selectgroup-button">{{ __('Agent') }}</span>
                                        </label>

                                        <label class="selectgroup-item">
                                            <input type="checkbox" name="features[]" value="Property Management"
                                                class="selectgroup-input" @if (is_array($features) && in_array('Property Management', $features)) checked @endif>
                                            <span class="selectgroup-button">{{ __('Property Management') }}</span>
                                        </label>

                                        <label class="selectgroup-item">
                                            <input type="checkbox" name="features[]" value="Project Management"
                                                class="selectgroup-input" @if (is_array($features) && in_array('Project Management', $features)) checked @endif>
                                            <span class="selectgroup-button">{{ __('Project Management') }}</span>
                                        </label>

                                        <label class="selectgroup-item">
                                            <input type="checkbox" name="features[]" value="Blog"
                                                class="selectgroup-input" @if (is_array($features) && in_array('Blog', $features)) checked @endif>
                                            <span class="selectgroup-button">{{ __('Blog') }}</span>
                                        </label>

                                        <label class="selectgroup-item">
                                            <input type="checkbox" name="features[]" value="User"
                                                class="selectgroup-input" @if (is_array($features) && in_array('User', $features)) checked @endif>
                                            <span class="selectgroup-button">{{ __('User') }}</span>
                                        </label>

                                        <label class="selectgroup-item">
                                            <input type="checkbox" name="features[]" value="Advertisement"
                                                class="selectgroup-input" @if (is_array($features) && in_array('Advertisement', $features)) checked @endif>
                                            <span class="selectgroup-button">{{ __('Advertisement') }}</span>
                                        </label>

                                        <label class="selectgroup-item">
                                            <input type="checkbox" name="features[]" value="Additional Page"
                                                class="selectgroup-input" @if (is_array($features) && in_array('Additional Page', $features)) checked @endif>
                                            <span class="selectgroup-button">{{ __('Additional Page') }}</span>
                                        </label>

                                    
                                        <label class="selectgroup-item">
                                            <input type="checkbox" name="features[]" value="Support Ticket"
                                                class="selectgroup-input"
                                                @if (is_array($features) && in_array('Support Ticket', $features)) checked @endif>
                                            <span class="selectgroup-button">{{ __('Support Ticket') }}</span>
                                        </label>

                                        <label class="selectgroup-item">
                                            <input type="checkbox" name="features[]" value="Google Analytics"
                                                class="selectgroup-input"
                                                @if (is_array($features) && in_array('Google Analytics', $features)) checked @endif>
                                            <span class="selectgroup-button">{{ __('Google Analytics') }}</span>
                                        </label>
                                        
                                        <label class="selectgroup-item">
                                            <input type="checkbox" name="features[]" value="Disqus"
                                                class="selectgroup-input"
                                                @if (is_array($features) && in_array('Disqus', $features)) checked @endif>
                                            <span class="selectgroup-button">{{ __('Disqus') }}</span>
                                        </label>
                                        <label class="selectgroup-item">
                                            <input type="checkbox" name="features[]" value="Google Recaptcha"
                                                class="selectgroup-input"
                                                @if (is_array($features) && in_array('Google Recaptcha', $features)) checked @endif>
                                            <span class="selectgroup-button">{{ __('Google Recaptcha') }}</span>
                                        </label>
                                        <label class="selectgroup-item">
                                            <input type="checkbox" name="features[]" value="Whatsapp"
                                                class="selectgroup-input"
                                                @if (is_array($features) && in_array('Whatsapp', $features)) checked @endif>
                                            <span class="selectgroup-button">{{ __('Whatsapp') }}</span>
                                        </label>
                                        <label class="selectgroup-item">
                                            <input type="checkbox" name="features[]" value="Google Login"
                                                class="selectgroup-input"
                                                @if (is_array($features) && in_array('Google Login', $features)) checked @endif>
                                            <span class="selectgroup-button">{{ __('Google Login') }}</span>
                                        </label>
                                     

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
                                <button type="submit" id="permissionBtn"
                                    class="btn btn-success">{{ __('Update') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
