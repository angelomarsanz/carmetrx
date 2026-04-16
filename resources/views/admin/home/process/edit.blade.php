@extends('admin.layout')

@if (!empty($process->language) && $process->language->rtl == 1)
    @section('styles')
        <style>
            form input,
            form textarea,
            form select {
                direction: rtl;
            }

            .nicEdit-main {
                direction: rtl;
                text-align: right;
            }
        </style>
    @endsection
@endif

@section('content')
    <div class="page-header">
        <h4 class="page-title">{{ __('Work Process') }}</h4>
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
                <a href="#">{{ __('Work Process') }}</a>
            </li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <form action="{{ route('admin.process.update') }}" method="post" enctype="multipart/form-data">
                    <div class="card-header">
                        <div class="card-title d-inline-block">{{ __('Edit Process') }}</div>
                        <a class="btn btn-info btn-sm float-right d-inline-block"
                            href="{{ route('admin.process.index') . '?language=' . request()->input('language') }}">
                            <span class="btn-label">
                                <i class="fas fa-backward"></i>
                            </span>
                            {{ __('Back') }}
                        </a>
                    </div>
                    <div class="card-body pt-5 pb-5">
                        <div class="row">
                            <div class="col-lg-6 offset-lg-3">
                                @csrf
                                <input type="hidden" name="process_id" value="{{ $process->id }}">
                                <div class="form-group">
                                    <label for="">{{ __('Icon') }} <span class="text-danger">{{ '*' }}</span> </label>
                                    <div class="btn-group d-block">
                                        <button type="button" class="btn btn-primary iconpicker-component"><i
                                                class="{{ $process->icon }}"></i></button>
                                        <button type="button" class="icp icp-dd btn btn-primary dropdown-toggle"
                                            data-selected="fa-car" data-toggle="dropdown">
                                        </button>
                                        <div class="dropdown-menu"></div>
                                    </div>
                                    <input id="inputIcon" type="hidden" name="icon" value="{{ $process->icon }}">
                                    @if ($errors->has('icon'))
                                        <p class="mb-0 text-danger">{{ $errors->first('icon') }}</p>
                                    @endif
                                    <div class="mt-2">
                                        <small class="text-warning">
                                            {{ __('Click on the dropdown sign to select a icon') }} </small>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="">{{ __('Title') }} <span class="text-danger">{{ '*' }}</span> </label>
                                    <input class="form-control" name="title" placeholder="{{ __('Enter title') }}"
                                        value="{{ $process->title }}">
                                    @if ($errors->has('title'))
                                        <p class="mb-0 text-danger">{{ $errors->first('title') }}</p>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="">{{ __('Text') }} <span class="text-danger">{{ '*' }}</span> </label>
                                    <input class="form-control" name="text"
                                        placeholder="{{ __('Enter Text') }}"value="{{ $process->text }}">
                                    @if ($errors->has('text'))
                                        <p class="mb-0 text-danger">{{ $errors->first('text') }}</p>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="">{{ __('Serial Number') }} <span class="text-danger">{{ '*' }}</span> </label>
                                    <input type="number" class="form-control " name="serial_number"
                                        value="{{ $process->serial_number }}"
                                        placeholder="{{ __('Enter Serial Number') }}">
                                    @if ($errors->has('serial_number'))
                                        <p class="mb-0 text-danger">{{ $errors->first('serial_number') }}</p>
                                    @endif
                                    <p class="text-warning">
                                        <small>{{ __('The higher the serial number is, the later will be shown') }}</small>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer pt-3">
                        <div class="form">
                            <div class="form-group from-show-notify row">
                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-success">{{ __('Update') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
