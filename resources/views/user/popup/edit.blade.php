@extends('user.layout')

@section('content')
    <div class="page-header">
        <h4 class="page-title">{{ __('Edit Popup') }}</h4>
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
                <a href="#">{{ __('Edit Popup') }}</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title d-inline-block">
                        {{ __('Edit Popup') . ' (' . __('Type') . ' - ' . $popup->type . ')' }}
                    </div>
                    <a class="btn btn-info btn-sm float-right d-inline-block"
                        href="{{ route('user.announcement_popups') }}">
                        <span class="btn-label">
                            <i class="fas fa-backward"></i>
                        </span>
                        {{ __('Back') }}
                    </a>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-8 offset-lg-2">
                            <form id="ajaxEditForm"
                                action="{{ route('user.announcement_popups.update_popup', ['id' => $popup->id]) }}"
                                method="POST" enctype="multipart/form-data">

                                @csrf
                                <input type="hidden" name="type" value="{{ $popup->type }}">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <div class="col-12 mb-2">
                                                <label for="image"><strong>{{ __('Image') . '*' }}</strong></label>
                                            </div>
                                            <div class="col-md-12 showImage mb-3">
                                                <img src="{{ isset($popup->image) ? asset(Constant::WEBSITE_ANNOUNCEMENT_POPUP_IMAGE . '/' . $popup->image) : asset('assets/tenant/image/default.jpg') }}"
                                                    alt="..." class="img-thumbnail">
                                            </div>
                                            <input type="file" name="image" id="image" class="form-control">
                                            <p id="errimage" class="mb-0 text-danger em"></p>
                                            @if ($popup->type == 1)
                                                <p class="text-warning mb-0">
                                                    {{ __('Upload 960 * 600 image for best quality') }}
                                                </p>
                                            @elseif ($popup->type == 2 || $popup->type == 3)
                                                <p class="text-warning mb-0">
                                                    {{ __('Upload 1145 * 765 image for best quality') }}
                                                </p>
                                            @elseif ($popup->type == 4 || $popup->type == 5)
                                                <p class="text-warning mb-0">
                                                    {{ __('Upload 517 * 689 image for best quality') }}
                                                </p>
                                            @elseif ($popup->type == 6)
                                                <p class="text-warning mb-0">
                                                    {{ __('Upload 960 * 660 image for best quality') }}
                                                </p>
                                            @elseif ($popup->type == 7)
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
                                            <label>{{ __('Name') . '*' }}</label>
                                            <input type="text" class="form-control" name="name"
                                                placeholder="{{ __('Enter popup name') }}" value="{{ $popup->name }}">
                                            <p id="editErr_name" class="mt-2 mb-0 text-danger em"></p>
                                            <p class="text-warning mt-2 mb-0">
                                                <small>{{ __('This name will not appear in UI. Rather then, it will help the admin to identify the popup') }}</small>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                @if ($popup->type == 2 || $popup->type == 3 || $popup->type == 7)
                                    <div class="row">
                                        <div class="col">
                                            <div class="form-group">
                                                <label>{{ __('Background Color Code') . '*' }}</label>
                                                <input class="jscolor form-control " name="background_color"
                                                    value="{{ $popup->background_color }}">
                                                <p id="editErr_background_color" class="mt-2 mb-0 text-danger em"></p>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if ($popup->type == 2 || $popup->type == 3)
                                    <div class="row">
                                        <div class="col">
                                            <div class="form-group">
                                                <label>{{ __('Background Color Opacity') . '*' }}</label>
                                                <input type="number" class="form-control ltr" step="0.01"
                                                    name="background_color_opacity"
                                                    value="{{ $popup->background_color_opacity }}">
                                                <p id="editErr_background_color_opacity" class="mt-2 mb-0 text-danger em">
                                                </p>
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

                                @if (
                                    $popup->type == 2 ||
                                        $popup->type == 3 ||
                                        $popup->type == 4 ||
                                        $popup->type == 5 ||
                                        $popup->type == 6 ||
                                        $popup->type == 7)
                                    <div class="row">
                                        <div class="col">
                                            <div class="form-group">
                                                <label>{{ __('Title') . '*' }}</label>
                                                <input type="text" class="form-control" name="title"
                                                    placeholder="{{ __('Enter Title') }}" value="{{ $popup->title }}">
                                                <p id="editErr_title" class="mt-2 mb-0 text-danger em"></p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col">
                                            <div class="form-group">
                                                <label>{{ __('Text') . '*' }}</label>
                                                <textarea class="form-control" name="text" placeholder="{{ __('Enter Text') }}" rows="5">{{ $popup->text }}</textarea>
                                                <p id="editErr_text" class="mt-2 mb-0 text-danger em"></p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>{{ __('Button Text') . '*' }}</label>
                                                <input type="text" class="form-control" name="button_text"
                                                    placeholder="{{ __('Enter button text') }}"
                                                    value="{{ $popup->button_text }}">
                                                <p id="editErr_button_text" class="mt-2 mb-0 text-danger em"></p>
                                            </div>
                                        </div>

                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>{{ __('Button Color Code') . '*' }}</label>
                                                <input class="jscolor form-control " name="button_color"
                                                    value="{{ $popup->button_color }}">
                                                <p id="editErr_button_color" class="mt-2 mb-0 text-danger em"></p>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if ($popup->type == 2 || $popup->type == 4 || $popup->type == 6 || $popup->type == 7)
                                    <div class="row">
                                        <div class="col">
                                            <div class="form-group">
                                                <label>{{ __('Button URL') . '*' }}</label>
                                                <input type="url" class="form-control" name="button_url"
                                                    placeholder="{{ __('Enter button url') }}"
                                                    value="{{ $popup->button_url }}">
                                                <p id="editErr_button_url" class="mt-2 mb-0 text-danger em"></p>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if ($popup->type == 6 || $popup->type == 7)
                                    @php
                                        $endDate = Carbon\Carbon::parse($popup->end_date);
                                        $endDate = date_format($endDate, 'm/d/Y');
                                        $endTime = date('h:i A', strtotime($popup->end_time));
                                    @endphp

                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>{{ __('End Date') . '*' }}</label>
                                                <input type="text" class="form-control datepicker" name="end_date"
                                                    placeholder="{{ __('Enter end date') }}" readonly autocomplete="off"
                                                    value="{{ $endDate }}">
                                                <p id="editErr_end_date" class="mt-2 mb-0 text-danger em"></p>
                                            </div>
                                        </div>

                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>{{ __('End Time') . '*' }}</label>
                                                <input type="text" class="form-control timepicker " name="end_time"
                                                    placeholder="{{ __('Enter end time') }}" readonly autocomplete="off"
                                                    value="{{ $endTime }}">
                                                <p id="editErr_end_time" class="mt-2 mb-0 text-danger em"></p>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            <label>{{ __('Delay') . ' (' . __('milliseconds') . ')*' }}</label>
                                            <input type="number" class="form-control " name="delay"
                                                placeholder="{{ __('Enter popup delay') }}" value="{{ $popup->delay }}">
                                            <p id="editErr_delay" class="mt-2 mb-0 text-danger em"></p>
                                            <p class="text-warning mt-2 mb-0">
                                                <small>{{ __('Popup will appear in UI after this delay time') }}</small>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            <label>{{ __('Serial Number') . '*' }}</label>
                                            <input type="number" class="form-control " name="serial_number"
                                                placeholder="{{ __('Enter serial number') }}"
                                                value="{{ $popup->serial_number }}">
                                            <p id="editErr_serial_number" class="mt-2 mb-0 text-danger em"></p>
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
                            <button type="submit" class="btn btn-success" id="updateBtn">
                                {{ __('Update') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
