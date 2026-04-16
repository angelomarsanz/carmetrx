@extends('admin.layout')

@if (!empty($abe->language) && $abe->language->rtl == 1)
    @section('styles')
        <style>
            form input,
            form textarea,
            form select {
                direction: rtl;
            }

            form .note-editor.note-frame .note-editing-area .note-editable {
                direction: rtl;
                text-align: right;
            }
        </style>
    @endsection
@endif

@section('content')
    <div class="page-header">
        <h4 class="page-title">{{ __('General Settings') }}</h4>
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
                <a href="#">{{ __('General Settings') }}</a>
            </li>

        </ul>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <form class="" action="{{ route('admin.basicinfo.update') }}" method="post"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="card-header">
                        <div class="row">
                            <div class="col-lg-10">
                                <div class="card-title">{{ __('Update General Settings') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-5 pb-5">

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
                                                            class="text-danger">{{ '*' }}</span></label>
                                                    <input class="form-control" name="website_title"
                                                        value="{{ $abs->website_title }}">
                                                    @if ($errors->has('website_title'))
                                                        <p class="mb-0 text-danger">{{ $errors->first('website_title') }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label>{{ __('Preloader Status') }} <span
                                                            class="text-danger">{{ '*' }}</span></label>
                                                    <div class="selectgroup w-100">
                                                        <label class="selectgroup-item">
                                                            <input type="radio" name="preloader_status" value="1"
                                                                class="selectgroup-input"
                                                                {{ $bs->preloader_status == 1 ? 'checked' : '' }}>
                                                            <span class="selectgroup-button">{{ __('Active') }}</span>
                                                        </label>
                                                        <label class="selectgroup-item">
                                                            <input type="radio" name="preloader_status" value="0"
                                                                class="selectgroup-input"
                                                                {{ $bs->preloader_status == 0 ? 'checked' : '' }}>
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
                                                <label for="image"> <strong>{{ __('Favicon') }}
                                                    </strong> <span class="text-danger">{{ '*' }}</span> </label>
                                            </div>
                                            <div class="col-md-12 showImage mb-3">
                                                <img src="{{ $bs->favicon ? asset('assets/front/img/' . $bs->favicon) : asset('assets/admin/img/noimage.jpg') }}"
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
                                                <label for="image"><strong> {{ __('Logo') }} </strong> <span
                                                        class="text-danger">{{ '*' }}</span> </label>
                                            </div>
                                            <div class="col-md-12 showImage3 mb-3" id="">
                                                <img src="{{ $bs->logo ? asset('assets/front/img/' . $bs->logo) : asset('assets/admin/img/noimage.jpg') }}"
                                                    alt="..." class="img-thumbnail">
                                            </div>
                                            <input type="file" name="logo" id="image3" data-text="farhan sdtext" lang="ar"
                                                class="form-control">
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
                                                <label for="image"><strong> {{ __('Preloader') }} </strong> <span
                                                        class="text-danger">{{ '*' }}</span> </label>
                                            </div>
                                            <div class="col-md-12 showImage2 mb-3">
                                                <img src="{{ $bs->preloader ? asset('assets/front/img/' . $bs->preloader) : asset('assets/admin/img/noimage.jpg') }}"
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
                                    <div class="col-12 py-3">

                                        <h3 class="text-warning">{{ __('Set Timezone') }}</h3>
                                        <hr class="divider"><br>




                                        <div class="form-group">
                                            <label>{{ __('Timezone') }} <span
                                                    class="text-danger">{{ '*' }}</span> </label>
                                            <select name="timezone" class="form-control select2">
                                                @foreach ($timezones as $timezone)
                                                    <option value="{{ $timezone->timezone }}"
                                                        {{ $timezone->timezone == $abe->timezone ? 'selected' : '' }}>
                                                        {{ $timezone->timezone }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('timezone'))
                                                <p class="mb-0 text-danger">{{ $errors->first('timezone') }}</p>
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
                                                    <label>{{ __('Base Color Code') }} <span
                                                            class="text-danger">{{ '*' }}</span> </label>
                                                    <input class="jscolor form-control " name="base_color"
                                                        value="{{ $abs->base_color }}">
                                                    @if ($errors->has('base_color'))
                                                        <p class="mb-0 text-danger">{{ $errors->first('base_color') }}</p>
                                                    @endif
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
                                                value="{{ $abe->base_currency_symbol }}">
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
                                                    {{ $abe->base_currency_symbol_position == 'left' ? 'selected' : '' }}>
                                                    {{ __('Left') }}</option>
                                                <option value="right"
                                                    {{ $abe->base_currency_symbol_position == 'right' ? 'selected' : '' }}>
                                                    {{ __('Right') }}</option>
                                            </select>
                                            @if ($errors->has('base_currency_symbol_position'))
                                                <p class="mb-0 text-danger">
                                                    {{ $errors->first('base_currency_symbol_position') }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>



                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label>{{ __('Base Currency Text') }} <span
                                                    class="text-danger">{{ '*' }}</span> </label>
                                            <input type="text" class="form-control " name="base_currency_text"
                                                value="{{ $abe->base_currency_text }}">
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
                                                    {{ $abe->base_currency_text_position == 'left' ? 'selected' : '' }}>
                                                    {{ __('Left') }}</option>
                                                <option value="right"
                                                    {{ $abe->base_currency_text_position == 'right' ? 'selected' : '' }}>
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
                                                    class="text-danger">{{ '*' }}</span></label>
                                            <div class="input-group mb-2">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">{{ __('1 USD') }} =</span>
                                                </div>
                                                <input type="text" name="base_currency_rate" class="form-control "
                                                    value="{{ $abe->base_currency_rate }}">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">{{ $abe->base_currency_text }}</span>
                                                </div>
                                            </div>

                                            @if ($errors->has('base_currency_rate'))
                                                <p class="mb-0 text-danger">{{ $errors->first('base_currency_rate') }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="form">
                            <div class="form-group from-show-notify row">
                                <div class="col-12 text-center">
                                    <button type="submit" id="displayNotif"
                                        class="btn btn-success">{{ __('Update') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
