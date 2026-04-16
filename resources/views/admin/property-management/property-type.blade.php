@extends('admin.layout')


@section('content')
    <div class="page-header">
        <h4 class="page-title">{{ __('Property Types') }}</h4>
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
                <a href="#">{{ __('Property Specifications') }}</a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="#">{{ __('Property Types') }}</a>
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
                                {{ __('Property Types') }}</div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ route('admin.property_management.property_types_update') }}" method="post" id="SubmitForm">
                        @csrf
                        <div class="col-lg-6 m-auto">
                            <div class="form-group">
                                <label class="form-label">{{ __('Rent') }} <span
                                        class="text-danger">{{ '*' }}</span> </label>
                                <div class="selectgroup w-100">
                                    <label class="selectgroup-item">
                                        <input type="radio" name="is_rent_active" value="1" class="selectgroup-input"
                                            {{ @$bs->is_rent_active == 1 ? 'checked' : '' }}>
                                        <span class="selectgroup-button">{{ __('Yes') }}</span>
                                    </label>
                                    <label class="selectgroup-item">
                                        <input type="radio" name="is_rent_active" value="0" class="selectgroup-input"
                                            {{ @$bs->is_rent_active == 0 ? 'checked' : '' }}>
                                        <span class="selectgroup-button">{{ __('No') }}</span>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">{{ __('Sale') }} <span
                                        class="text-danger">{{ '*' }}</span> </label>
                                <div class="selectgroup w-100">
                                    <label class="selectgroup-item">
                                        <input type="radio" name="is_sale_active" value="1" class="selectgroup-input"
                                            {{ @$bs->is_sale_active == 1 ? 'checked' : '' }}>
                                        <span class="selectgroup-button">{{ __('Yes') }}</span>
                                    </label>
                                    <label class="selectgroup-item">
                                        <input type="radio" name="is_sale_active" value="0" class="selectgroup-input"
                                            {{ @$bs->is_sale_active == 0 ? 'checked' : '' }}>
                                        <span class="selectgroup-button">{{ __('No') }}</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card-footer">
                    <div class="d-flex justify-content-center">
                        <button class="btn btn-success" form="SubmitForm">{{ __('Update') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
