@extends('user.layout')

@section('content')
    <div class="page-header">
        <h4 class="page-title">{{ __('Information') }}</h4>
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
                <a href="#">{{ __('General Settings') }}</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <form action="{{ route('user.basic_settings.update_info') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="card-header">
                        <div class="row">
                            <div class="col-lg-10">
                                <div class="card-title">{{ __('Update General Information') }}
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="card-body">


                        <div class="row">
                            <div class="col-lg-10 offset-lg-1">


                                <div class="row">
                                    <div class="col-12">

                                        <h3 class="text-warning">{{ __('Information') }}</h3>
                                        <hr class="divider"><br>
                                    </div>
                                    <div class="col-12">


                                        <div class="row">


                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label>{{ __('Website Title') }} <span
                                                            class="text-danger">{{ '*' }}</span> </label>
                                                    <input class="form-control" name="website_title"
                                                        value="{{ $data->website_title }}">
                                                    @if ($errors->has('website_title'))
                                                        <p class="mb-0 text-danger">{{ $errors->first('website_title') }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label>{{ __('Preloader Status') }} <span
                                                            class="text-danger">{{ '*' }}</span> </label>
                                                    <div class="selectgroup w-100">
                                                        <label class="selectgroup-item">
                                                            <input type="radio" name="preloader_status" value="1"
                                                                class="selectgroup-input"
                                                                {{ $data->preloader_status == 1 ? 'checked' : '' }}>
                                                            <span class="selectgroup-button">{{ __('Active') }}</span>
                                                        </label>
                                                        <label class="selectgroup-item">
                                                            <input type="radio" name="preloader_status" value="0"
                                                                class="selectgroup-input"
                                                                {{ $data->preloader_status == 0 ? 'checked' : '' }}>
                                                            <span class="selectgroup-button">{{ __('Deactive') }}</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <div class="col-12 mb-2">
                                                <label for="image"><strong>{{ __('Favicon') }} </strong></label>
                                            </div>
                                            <div class="col-md-12 showImage mb-3">
                                                <img src="{{ $data->favicon ? asset($data->favicon) : asset('assets/admin/img/noimage.jpg') }}"
                                                    alt="..." class="img-thumbnail">
                                            </div>
                                            <input type="file" name="favicon" id="image" class="form-control">
                                            @error('favicon')
                                                <p id="text-danger" class="mb-0 text-danger em">{{ $message }}
                                                </p>
                                            @enderror
                                            <p class="text-warning mb-0">
                                                {{ __('Upload 40 * 40 image for best quality') }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <div class="col-12 mb-2">
                                                <label for="image"><strong> {{ __('Logo') }} </strong></label>
                                            </div>
                                            <div class="col-md-12 showImage3 mb-3" id="">
                                                <img src="{{ $data->logo ? asset($data->logo) : asset('assets/admin/img/noimage.jpg') }}"
                                                    alt="..." class="img-thumbnail">
                                            </div>
                                            <input type="file" name="logo" id="image3" class="form-control">

                                            @error('logo')
                                                <p class="text-danger mb-0 em">{{ $message }}</p>
                                            @enderror
                                            <p class="text-warning mb-0">{{ __('Upload 180 * 50 image for best quality') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">

                                        <div class="form-group">
                                            <div class="col-12 mb-2">
                                                <label for="image"><strong> {{ __('Preloader') }} </strong></label>
                                            </div>
                                            <div class="col-md-12 showImage2 mb-3">
                                                <img src="{{ $data->preloader ? asset($data->preloader) : asset('assets/admin/img/noimage.jpg') }}"
                                                    alt="..." class="img-thumbnail">
                                            </div>
                                            <input type="file" name="preloader" id="image2" class="form-control">
                                            @if ($errors->has('preloader'))
                                                <p class="text-danger mb-0">{{ $errors->first('file') }}</p>
                                            @endif
                                            <p class="text-warning mb-0">
                                                {{ __('Only GIF, JPG, JPEG, PNG file formats are allowed') }}</p>
                                        </div>


                                    </div>



                                </div>
                                <div class="row">
                                    <div class="col-12 py-4">

                                        <h3 class="text-warning">{{ __('Language Management') }}</h3>
                                        <hr class="divider"><br>

                                        <div class="col-12 ">
                                            <div class="row pb-4">

                                                <div class="col-md-6 col-lg-4">
                                                    @if (!empty($adminLangs))

                                                            <p for=""> <strong>
                                                                    {{ __('Dahboard Language') }}</strong>
                                                                <strong class="text-danger">{{ '*' }}</strong>
                                                            </p>
                                                            <div class="input-group mb-2 ">
                                                                <div class="input-group-prepend">
                                                                    <div class="input-group-text text-secondary "><i
                                                                            class="fas fa-language"></i></div>
                                                                </div>

                                                                <select name="adminLanguage"
                                                                    class="form-control language-select"
                                                                    onchange="window.location='{{ route('user.change.dashboard_language') . '?language=' }}'+this.value">

                                                                    <option disabled> {{ __('Select a Language') }}
                                                                    </option>
                                                                    @foreach ($adminLangs as $lang)
                                                                        <option value="{{ $lang->code }}"
                                                                            {{ $lang->code == $currentLang->code ? 'selected' : '' }}>
                                                                            {{ $lang->name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>



                                <div class="row">
                                    <div class="col-12 py-3">

                                        <h3 class="text-warning ">{{ __('Theme & Home') }}</h3>
                                        <hr class="divider"><br>
                                        <div class="form-group">

                                            <div class="row">
                                                <div class="col">
                                                    <div class="form-group">
                                                        <div class="row mt-2  ">

                                                            @foreach ($themes as $theme)
                                                                <div class="col-md-3">
                                                              
                                                                    <label class="imagecheck height200">
                                                                        <input name="theme_version" type="radio"
                                                                            value="{{ $theme->static_name }}"
                                                                            class="imagecheck-input"
                                                                            {{ isset($data) && $data->theme_version == $theme->static_name ? 'checked' : '' }}>
                                                                        <figure class="imagecheck-figure">
                                                                            <img src="{{ asset(\App\Constants\Constant::WEBSITE_THEMES . '/' . $theme->image) }}"
                                                                                alt="theme" class="imagecheck-image">
                                                                        </figure>
                                                                    </label>
                                                                    <h4 class="text-center">{{ __($theme->name) }}</h4>
                                                                </div>
                                                            @endforeach


                                                            @if ($errors->has('theme_version'))
                                                                <p class="mb-0 ml-3 text-danger">
                                                                    {{ $errors->first('theme_version') }}</p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>



                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <br>
                                            <h3 class="text-warning">{{ __('Currency Settings') }}</h3>
                                            <hr class="divider">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">

                                            <label>{{ __('Base Currency Symbol') }} <span
                                                    class="text-danger">{{ '*' }}</span> </label>
                                            <input type="text" class="form-control " name="base_currency_symbol"
                                                value="{{ $data->base_currency_symbol }}">
                                            @if ($errors->has('base_currency_symbol'))
                                                <p class="mb-0 text-danger">{{ $errors->first('base_currency_symbol') }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label>{{ __('Base Currency Symbol Position') }} <span
                                                    class="text-danger">{{ '*' }}</span> </label>
                                            <select name="base_currency_symbol_position" class="form-control ">
                                                <option value="left"
                                                    {{ $data->base_currency_symbol_position == 'left' ? 'selected' : '' }}>
                                                    {{ __('Left') }}</option>
                                                <option value="right"
                                                    {{ $data->base_currency_symbol_position == 'right' ? 'selected' : '' }}>
                                                    {{ __('Right') }}</option>
                                            </select>
                                            @if ($errors->has('base_currency_symbol_position'))
                                                <p class="mb-0 text-danger">
                                                    {{ $errors->first('base_currency_symbol_position') }}</p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label>{{ __('Base Currency Text') }} <span
                                                    class="text-danger">{{ '*' }}</span> </label>
                                            <input type="text" class="form-control " name="base_currency_text"
                                                value="{{ $data->base_currency_text }}">
                                            @if ($errors->has('base_currency_text'))
                                                <p class="mb-0 text-danger">{{ $errors->first('base_currency_text') }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label>{{ __('Base Currency Text Position') }} <span
                                                    class="text-danger">{{ '*' }}</span> </label>
                                            <select name="base_currency_text_position" class="form-control ">
                                                <option value="left"
                                                    {{ $data->base_currency_text_position == 'left' ? 'selected' : '' }}>
                                                    {{ __('Left') }}</option>
                                                <option value="right"
                                                    {{ $data->base_currency_text_position == 'right' ? 'selected' : '' }}>
                                                    {{ __('Right') }}</option>
                                            </select>
                                            @if ($errors->has('base_currency_text_position'))
                                                <p class="mb-0 text-danger">
                                                    {{ $errors->first('base_currency_text_position') }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label>{{ __('Base Currency Rate') }} <span
                                                    class="text-danger">{{ '*' }}</span> </label>
                                            <div class="input-group mb-2">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">{{ __('1 USD') }} =</span>
                                                </div>
                                                <input type="number" name="base_currency_rate" class="form-control "
                                                    value="{{ $data->base_currency_rate }}">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">{{ $data->base_currency_text }}</span>
                                                </div>
                                            </div>

                                            @if ($errors->has('base_currency_rate'))
                                                <p class="mb-0 text-danger">{{ $errors->first('base_currency_rate') }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12 py-3">

                                        <h3 class="text-warning ">{{ __('Website Appearance') }}</h3>
                                        <hr class="divider"><br>
                                        <div class="form-group">

                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>{{ __('Primary Color') }} <span
                                                                class="text-danger">{{ '*' }}</span></label>
                                                        <input class="jscolor form-control ltr" name="primary_color"
                                                            value="{{ $data->primary_color }}">
                                                        <p id="errprimary_color" class="mb-0 text-danger em"></p>
                                                    </div>
                                                </div>
                                                @if ($userBs->theme_version == 1)
                                                    <div class="col-lg-6  ">
                                                        <div class="form-group">
                                                            <label>{{ __('Secondary Color') }} <span
                                                                    class="text-danger">{{ '*' }}</span>
                                                            </label>
                                                            <input class="jscolor form-control ltr" name="secondary_color"
                                                                value="{{ $data->secondary_color }}">
                                                            <p id="errsecondary_color" class="mb-0 text-danger em"></p>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>

                                        </div>
                                    </div>
                                </div>

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
