@extends('user.layout')

@section('content')
    <div class="page-header">
        <h4 class="page-title">{{ __('Add Popup') }}</h4>
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
                <a href="#">{{ __('Announcement Popups') }}</a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="#">{{ __('Add Popup') }}</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-3">
                            <div class="card-title d-inline-block">
                                {{ __('Add Popup') . ' (' . __('Type') . ' - ' . $popupType . ')' }}
                            </div>
                        </div>
                        <div class="col-lg-3">
                           
                        </div>
                        <div class="col-lg-3">
                          
                        </div>
                        <div class="col-lg-3">
                            <a class="btn btn-info btn-sm float-right d-inline-block"
                                href="{{ route('user.announcement_popups.select_popup_type') }}">
                                <span class="btn-label">
                                    <i class="fas fa-backward"></i>
                                </span>
                                {{ __('Back') }}
                            </a>
                        </div>
                    </div>


                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-8 offset-lg-2">
                            <form id="ajaxForm" class="create"
                                action="{{ route('user.announcement_popups.store_popup', ['language' => request()->input('language')]) }}"
                                method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="type" value="{{ $popupType }}">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <div class="col-12 mb-2">
                                                <label for="image"><strong>{{ __('Image') }}</strong> <span
                                                        class="text-danger">{{ '*' }}</span></label>
                                            </div>
                                            <div class="col-md-12 showImage mb-3">
                                                <img src="{{ asset('assets/tenant/image/default.jpg') }}" alt="..."
                                                    class="img-thumbnail">
                                            </div>
                                            <input type="file" name="image" id="image" class="form-control">
                                            <p id="errimage" class="mb-0 text-danger em"></p>
                                            @if ($popupType == 1)
                                                <p class="text-warning mb-0">
                                                    {{ __('Upload 960 * 600 image for best quality') }}
                                                </p>
                                            @elseif ($popupType == 2 || $popupType == 3)
                                                <p class="text-warning mb-0">
                                                    {{ __('Upload 1145 * 765 image for best quality') }}
                                                </p>
                                            @elseif ($popupType == 4 || $popupType == 5)
                                                <p class="text-warning mb-0">
                                                    {{ __('Upload 517 * 689 image for best quality') }}
                                                </p>
                                            @elseif ($popupType == 6)
                                                <p class="text-warning mb-0">
                                                    {{ __('Upload 960 * 660 image for best quality') }}
                                                </p>
                                            @elseif ($popupType == 7)
                                                <p class="text-warning mb-0">
                                                    {{ __('Upload 550 * 765 image for best quality') }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                               
                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            <label>{{ __('Name') }} <span
                                                    class="text-danger">{{ '*' }}</span> </label>
                                            <input type="text" class="form-control" name="name"
                                                placeholder="{{ __('Enter popup name') }}">
                                            <p id="errname" class="mt-2 mb-0 text-danger em"></p>
                                            <p class="text-warning mt-2 mb-0">
                                                <small>{{ __('This name will not appear in UI. Rather then, it will help the admin to identify the popup') }}</small>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                @if ($popupType == 2 || $popupType == 3 || $popupType == 7)
                                    <div class="row">
                                        <div class="col">
                                            <div class="form-group">
                                                <label>{{ __('Background Color Code') }} <span
                                                        class="text-danger">{{ '*' }}</span></label>
                                                <input class="jscolor form-control ltr" name="background_color">
                                                <p id="errbackground_color" class="mt-2 mb-0 text-danger em"></p>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if ($popupType == 2 || $popupType == 3)
                                    <div class="row">
                                        <div class="col">
                                            <div class="form-group">
                                                <label>{{ __('Background Color Opacity') }} <span
                                                        class="text-danger">{{ '*' }}</span> </label>
                                                <input type="number" class="form-control ltr" step="0.01"
                                                    name="background_color_opacity">
                                                <p id="errbackground_color_opacity" class="mt-2 mb-0 text-danger em"></p>
                                                <p class="mt-2 mb-0 text-warning">
                                                    {{ __('This will decide the transparency level of the color') }}
                                                    <br>
                                                    {{ __('Value must be between 0 to 1') }}<br>
                                                    {{ __('Transparency level will be lower with the increment of the value') }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if ($popupType == 2 || $popupType == 3 || $popupType == 4 || $popupType == 5 || $popupType == 6 || $popupType == 7)
                                    <div class="row">
                                        <div class="col">
                                            <div class="form-group">
                                                <label>{{ __('Title') }} <span
                                                        class="text-danger">{{ '*' }}</span></label>
                                                <input type="text" class="form-control" name="title"
                                                    placeholder="{{ __('Enter Title') }} ">
                                                <p id="errtitle" class="mt-2 mb-0 text-danger em"></p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col">
                                            <div class="form-group">
                                                <label>{{ __('Text') }} <span
                                                        class="text-danger">{{ '*' }}</span></label>
                                                <textarea class="form-control" name="text" placeholder="{{ __('Enter Text') }}" rows="5"></textarea>
                                                <p id="errtext" class="mt-2 mb-0 text-danger em"></p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>{{ __('Button Text') }} <span
                                                        class="text-danger">{{ '*' }}</span></label>
                                                <input type="text" class="form-control" name="button_text"
                                                    placeholder="{{ __('Enter button text') }}">
                                                <p id="errbutton_text" class="mt-2 mb-0 text-danger em"></p>
                                            </div>
                                        </div>

                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>{{ __('Button Color Code') }} <span
                                                        class="text-danger">{{ '*' }}</span></label>
                                                <input class="jscolor form-control " name="button_color">
                                                <p id="errbutton_color" class="mt-2 mb-0 text-danger em"></p>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if ($popupType == 2 || $popupType == 4 || $popupType == 6 || $popupType == 7)
                                    <div class="row">
                                        <div class="col">
                                            <div class="form-group">
                                                <label>{{ __('Button URL') }} <span
                                                        class="text-danger">{{ '*' }}</span></label>
                                                <input type="url" class="form-control " name="button_url"
                                                    placeholder="{{ __('Enter button url') }}">
                                                <p id="errbutton_url" class="mt-2 mb-0 text-danger em"></p>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if ($popupType == 6 || $popupType == 7)
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>{{ __('End Date') }} <span
                                                        class="text-danger">{{ '*' }}</span></label>
                                                <input type="text" class="form-control datepicker " name="end_date"
                                                    placeholder="{{ __('Enter end date') }}" readonly autocomplete="off">
                                                <p id="errend_date" class="mt-2 mb-0 text-danger em"></p>
                                            </div>
                                        </div>

                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>{{ __('End Time') }} <span
                                                        class="text-danger">{{ '*' }}</span> </label>
                                                <input type="text" class="form-control timepicker " name="end_time"
                                                    placeholder="{{ __('Enter end time') }}" readonly autocomplete="off">
                                                <p id="errend_time" class="mt-2 mb-0 text-danger em"></p>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            <label>{{ __('Delay') . ' (' . ($keywords['milliseconds'] ?? __('milliseconds')) . ')' }}
                                                <span class="text-danger">{{ '*' }}</span></label>
                                            <input type="number" class="form-control " name="delay"
                                                placeholder="{{ __('Enter popup delay') }}">
                                            <p id="errdelay" class="mt-2 mb-0 text-danger em"></p>
                                            <p class="text-warning mt-2 mb-0">
                                                <small>{{ __('Popup will appear in UI after this delay time') }}</small>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            <label>{{ __('Serial Number') }} <span
                                                    class="text-danger">{{ '*' }}</span> </label>
                                            <input type="number" class="form-control " name="serial_number"
                                                placeholder="{{ __('Enter serial number') }}">
                                            <p id="errserial_number" class="mt-2 mb-0 text-danger em"></p>
                                            <p class="mt-2 mb-0 text-warning">
                                                {{ __('If there are multiple active popups, then popups will be shown in UI according to serial number') }}
                                                <br>
                                                {{ __('The higher the serial number will be shown') }}
                                            </p>
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
                            <button type="button" class="btn btn-success" id="submitBtn">
                                {{ __('Save') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
