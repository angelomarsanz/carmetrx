@extends('admin.layout')
@section('content')
    <div class="page-header">
        <h4 class="page-title">{{ __('Plugins') }}</h4>
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
                <a href="#">{{ __('Settings') }}</a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="#">{{ __('Plugins') }}</a>
            </li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-12">
            <form id="scriptForm" class="" action="{{ route('admin.script.update') }}" method="post">
                @csrf
                <div class="row">

                    <div class="col-lg-4 d-flex">
                        <div class="card flex-fill d-flex flex-column">

                            <div class="card-header">
                                <div class="card-title">
                                    {{ __('AI API Settings') }}
                                </div>
                            </div>
                            <div class="card-body flex-grow-1">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label>{{ __('AI API Key') }}</label>
                                            <input class="form-control" name="ai_api_key"
                                                value="{{ $data->ai_api_key ?? '' }}">
                                            @if ($errors->has('ai_api_key'))
                                                <p class="mb-0 text-danger">{{ $errors->first('ai_api_key') }}</p>
                                            @endif
                                        </div>

                                        <div class="form-group">
                                            <label>{{ __('AI Model Name') }}</label>
                                            <input class="form-control" name="ai_api_model"
                                                value="{{ $data->ai_api_model ?? '' }}">
                                            <small class="form-text text-warning">
                                                {{ __('e.g., gpt-4, gemini-pro, claude-3-sonnet') }}</small>
                                            @if ($errors->has('ai_api_model'))
                                                <p class="mb-0 text-danger">{{ $errors->first('ai_api_model') }}</p>
                                            @endif
                                        </div>

                                        <div class="form-group">
                                            <label>{{ __('Max Tokens') }}</label>
                                            <input class="form-control" type="number" name="ai_api_token"
                                                value="{{ $data->ai_api_token ?? 400 }}">
                                            <p class="form-text">
                                                <strong class="text-warning"> {{ __('Note') }}:</strong>
                                                <span class="text-warning">{{ __('Set the') }}</span>
                                                <strong class="text-danger"> {{ __('Max Length') }}</strong>
                                                <span
                                                    class="text-warning">{{ __('of the response here. Lower values give faster, shorter, and more cost-effective answers. Increase it for detailed responses.') }}</span>
                                            </p>
                                            @if ($errors->has('ai_api_token'))
                                                <p class="mb-0 text-danger">{{ $errors->first('ai_api_token') }}</p>
                                            @endif
                                        </div>

                                        <div class="form-group">
                                            <label>{{ __('Temperature') }}</label>
                                            <input class="form-control" type="number" step="0.1"
                                                name="ai_api_temperature" value="{{ $data->ai_api_temperature ?? 0.8 }}">
                                            <small class="form-text">
                                                <strong class="text-warning"> {{ __('Note') }}:</strong>
                                                <span class="text-warning"> {{ __("This controls the AI's") }}</span>
                                                <strong class="text-danger">
                                                    {{ __('creativity and randomness') }}
                                                </strong>.
                                                <br>
                                                <span class="text-warning"> {{ __('Use a') }}</span>
                                                <strong class="text-danger">
                                                    {{ __('low value') }}
                                                </strong>
                                                <span class="text-info">($\approx 0.2$)</span>
                                                <span class="text-warning">
                                                    {{ __('for factual, predictable answers, and a') }}
                                                </span>
                                                <strong class="text-danger">
                                                    {{ __('high value') }}
                                                </strong>
                                                <span class="text-info">($\approx 0.8+$)</span><span
                                                    class="text-warning">{{ __('for stories, brainstorming, or unique content.') }}</span>
                                            </small>
                                            @if ($errors->has('ai_api_temperature'))
                                                <p class="mb-0 text-danger">{{ $errors->first('ai_api_temperature') }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer mt-auto">
                                <div class="row">
                                    <div class="col-12 text-center">
                                        <button type="submit" class="btn btn-success">
                                            {{ __('Update') }}
                                        </button>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <div class="card-title">
                                    {{ __('Google Recaptcha') }}
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>{{ __('Google Recaptcha Status') }}</label>
                                    <div class="selectgroup w-100">
                                        <label class="selectgroup-item">
                                            <input type="radio" name="is_recaptcha" value="1"
                                                class="selectgroup-input" {{ $data->is_recaptcha == 1 ? 'checked' : '' }}>
                                            <span class="selectgroup-button">{{ __('Active') }}</span>
                                        </label>
                                        <label class="selectgroup-item">
                                            <input type="radio" name="is_recaptcha" value="0"
                                                class="selectgroup-input" {{ $data->is_recaptcha == 0 ? 'checked' : '' }}>
                                            <span class="selectgroup-button">{{ __('Deactive') }}</span>
                                        </label>
                                    </div>
                                    @if ($errors->has('is_recaptcha'))
                                        <p class="mb-0 text-danger">{{ $errors->first('is_recaptcha') }}</p>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>{{ __('Google Recaptcha Site key') }}</label>
                                    <input class="form-control" name="google_recaptcha_site_key"
                                        value="{{ $data->google_recaptcha_site_key }}">
                                    @if ($errors->has('google_recaptcha_site_key'))
                                        <p class="mb-0 text-danger">{{ $errors->first('google_recaptcha_site_key') }}</p>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>{{ __('Google Recaptcha Secret key') }}</label>
                                    <input class="form-control" name="google_recaptcha_secret_key"
                                        value="{{ $data->google_recaptcha_secret_key }}">
                                    @if ($errors->has('google_recaptcha_secret_key'))
                                        <p class="mb-0 text-danger">{{ $errors->first('google_recaptcha_secret_key') }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <div class="card-title">{{ __('Disqus') }}</div>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>{{ __('Disqus Status') }} ({{ __('Website Blog Details') }})</label>
                                    <div class="selectgroup w-100">
                                        <label class="selectgroup-item">
                                            <input type="radio" name="is_disqus" value="1"
                                                class="selectgroup-input" {{ $data->is_disqus == 1 ? 'checked' : '' }}>
                                            <span class="selectgroup-button">{{ __('Active') }}</span>
                                        </label>
                                        <label class="selectgroup-item">
                                            <input type="radio" name="is_disqus" value="0"
                                                class="selectgroup-input" {{ $data->is_disqus == 0 ? 'checked' : '' }}>
                                            <span class="selectgroup-button">{{ __('Deactive') }}</span>
                                        </label>
                                    </div>
                                    @if ($errors->has('is_disqus'))
                                        <p class="mb-0 text-danger">{{ $errors->first('is_disqus') }}</p>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>{{ __('Disqus Status') }}({{ __('User Profile Blog Details') }})</label>
                                    <div class="selectgroup w-100">
                                        <label class="selectgroup-item">
                                            <input type="radio" name="is_user_disqus" value="1"
                                                class="selectgroup-input"
                                                {{ $data->is_user_disqus == 1 ? 'checked' : '' }}>
                                            <span class="selectgroup-button">{{ __('Active') }}</span>
                                        </label>
                                        <label class="selectgroup-item">
                                            <input type="radio" name="is_user_disqus" value="0"
                                                class="selectgroup-input"
                                                {{ $data->is_user_disqus == 0 ? 'checked' : '' }}>
                                            <span class="selectgroup-button">{{ __('Deactive') }}</span>
                                        </label>
                                    </div>
                                    @if ($errors->has('is_user_disqus'))
                                        <p class="mb-0 text-danger">{{ $errors->first('is_user_disqus') }}</p>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>{{ __('Disqus Shortname') }}</label>
                                    <input class="form-control" name="disqus_shortname"
                                        value="{{ $data->disqus_shortname }}">
                                    @if ($errors->has('disqus_shortname'))
                                        <p class="mb-0 text-danger">{{ $errors->first('disqus_shortname') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <div class="card-title">{{ __('Tawk.to') }}</div>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>{{ __('Tawk.to Status') }}</label>
                                    <div class="selectgroup w-100">
                                        <label class="selectgroup-item">
                                            <input type="radio" name="is_tawkto" value="1"
                                                class="selectgroup-input" {{ $data->is_tawkto == 1 ? 'checked' : '' }}>
                                            <span class="selectgroup-button">{{ __('Active') }}</span>
                                        </label>
                                        <label class="selectgroup-item">
                                            <input type="radio" name="is_tawkto" value="0"
                                                class="selectgroup-input" {{ $data->is_tawkto == 0 ? 'checked' : '' }}>
                                            <span class="selectgroup-button">{{ __('Deactive') }}</span>
                                        </label>
                                    </div>
                                    <p class="mb-0 text-warning">
                                        {{ __('If you enable Tawk.to, then WhatsApp must be disabled') }}</p>
                                    @if ($errors->has('is_tawkto'))
                                        <p class="mb-0 text-danger">{{ $errors->first('is_tawkto') }}</p>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>{{ __('Tawk.to Direct Chat Link') }}</label>
                                    <input class="form-control" name="tawkto_chat_link"
                                        value="{{ $data->tawkto_chat_link }}">
                                    @if ($errors->has('tawkto_chat_link'))
                                        <p class="mb-0 text-danger">{{ $errors->first('tawkto_chat_link') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <div class="card-title">{{ __('WhatsApp Chat Button') }}</div>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>{{ __('Status') }}</label>
                                    <div class="selectgroup w-100">
                                        <label class="selectgroup-item">
                                            <input type="radio" name="is_whatsapp" value="1"
                                                class="selectgroup-input" {{ $data->is_whatsapp == 1 ? 'checked' : '' }}>
                                            <span class="selectgroup-button">{{ __('Active') }}</span>
                                        </label>
                                        <label class="selectgroup-item">
                                            <input type="radio" name="is_whatsapp" value="0"
                                                class="selectgroup-input" {{ $data->is_whatsapp == 0 ? 'checked' : '' }}>
                                            <span class="selectgroup-button">{{ __('Deactive') }}</span>
                                        </label>
                                    </div>
                                    <p class="text-warning mb-0">
                                        {{ __('If you enable WhatsApp, then Tawk.to must be disabled') }}</p>
                                </div>
                                <div class="form-group">
                                    <label>{{ __('WhatsApp Number') }}</label>
                                    <input class="form-control" type="text" name="whatsapp_number"
                                        value="{{ $data->whatsapp_number }}">
                                    <p class="text-warning mb-0">{{ __('Enter Phone number with Country Code') }}</p>
                                </div>
                                <div class="form-group">
                                    <label>{{ __('WhatsApp Header Title') }}</label>
                                    <input class="form-control" type="text" name="whatsapp_header_title"
                                        value="{{ $data->whatsapp_header_title }}">
                                    @if ($errors->has('whatsapp_header_title'))
                                        <p class="mb-0 text-danger">{{ $errors->first('whatsapp_header_title') }}</p>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>{{ __('WhatsApp Popup Message') }}</label>
                                    <textarea class="form-control" name="whatsapp_popup_message" rows="2">{{ $data->whatsapp_popup_message }}</textarea>
                                    @if ($errors->has('whatsapp_popup_message'))
                                        <p class="mb-0 text-danger">{{ $errors->first('whatsapp_popup_message') }}</p>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>{{ __('Popup') }}</label>
                                    <div class="selectgroup w-100">
                                        <label class="selectgroup-item">
                                            <input type="radio" name="whatsapp_popup" value="1"
                                                class="selectgroup-input"
                                                {{ $data->whatsapp_popup == 1 ? 'checked' : '' }}>
                                            <span class="selectgroup-button">{{ __('Active') }}</span>
                                        </label>
                                        <label class="selectgroup-item">
                                            <input type="radio" name="whatsapp_popup" value="0"
                                                class="selectgroup-input"
                                                {{ $data->whatsapp_popup == 0 ? 'checked' : '' }}>
                                            <span class="selectgroup-button">{{ __('Deactive') }}</span>
                                        </label>
                                    </div>
                                    @if ($errors->has('whatsapp_popup'))
                                        <p class="mb-0 text-danger">{{ $errors->first('whatsapp_popup') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-footer">
                        <div class="form">
                            <div class="form-group from-show-notify row">
                                <div class="col-12 text-center">
                                    <button type="submit" form="scriptForm"
                                        class="btn btn-success">{{ __('Update') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
