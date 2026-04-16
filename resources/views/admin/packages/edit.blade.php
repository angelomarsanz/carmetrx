@extends('admin.layout')


@if (!empty($currentLang) && $currentLang->rtl == 1)
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
        <h4 class="page-title">{{ __('Edit package') }}</h4>
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
                <a href="#">{{ __('Package Management') }}</a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="#">{{ __('Edit') }}</a>
            </li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title d-inline-block">{{ __('Edit package') }}</div>
                    <a class="btn btn-info btn-sm float-right d-inline-block" href="{{ route('admin.package.index') }}">
                        <span class="btn-label">
                            <i class="fas fa-backward"></i>
                        </span>
                        {{ __('Back') }}
                    </a>
                </div>
                <div class="card-body pt-5 pb-5">
                    <div class="row">
                        <div class="col-lg-6 offset-lg-3">
                            <form id="ajaxForm" class="" action="{{ route('admin.package.update') }}" method="post"
                                enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="package_id" value="{{ $package->id }}">

                                <div class="form-group">
                                    <label for="">{{ __('Icon') }} <span
                                            class="text-danger">{{ '*' }}</span></label>
                                    <div class="btn-group d-block">
                                        <button type="button" class="btn btn-primary iconpicker-component"><i
                                                class="{{ $package->icon }}"></i></button>
                                        <button type="button" class="icp icp-dd btn btn-primary dropdown-toggle"
                                            data-selected="fa-car" data-toggle="dropdown">
                                        </button>
                                        <div class="dropdown-menu"></div>
                                    </div>
                                    <input id="inputIcon" type="hidden" name="icon" value="{{ $package->icon }}">
                                    @if ($errors->has('icon'))
                                        <p class="mb-0 text-danger">{{ $errors->first('icon') }}</p>
                                    @endif
                                    <div class="mt-2">
                                        <small
                                            class="text-warning">{{ __('Click on the dropdown sign to select a icon') }}</small>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="title">{{ __('Package title') }} <span
                                            class="text-danger">{{ '*' }}</span></label>
                                    <input id="title" type="text" class="form-control" name="title"
                                        value="{{ $package->title }}" placeholder="{{ __('Enter name') }}">
                                    <p id="errtitle" class="mb-0 text-danger em"></p>
                                </div>

                                <div class="form-group">
                                    <label for="price">{{ __('Price') }} ({{ $bex->base_currency_text }}) <span
                                            class="text-danger">{{ '*' }}</span> </label>
                                    <input id="price" type="number" class="form-control" name="price"
                                        placeholder="{{ __('Enter Package price') }}" value="{{ $package->price }}">
                                    <p class="text-warning">
                                        <small>{{ __('If price is 0 , than it will appear as free') }}</small>
                                    </p>
                                    <p id="errprice" class="mb-0 text-danger em"></p>
                                </div>
                                <div class="form-group">
                                    <label for="plan_term">{{ __('Package term') }} <span
                                            class="text-danger">{{ '*' }}</span> </label>
                                    <select id="plan_term" name="term" class="form-control">
                                        <option value="" selected disabled>{{ __('Select a Term') }}</option>
                                        <option value="monthly" {{ $package->term == 'monthly' ? 'selected' : '' }}>
                                            {{ __('monthly') }}</option>
                                        <option value="yearly" {{ $package->term == 'yearly' ? 'selected' : '' }}>
                                            {{ __('yearly') }}</option>
                                        <option value="lifetime" {{ $package->term == 'lifetime' ? 'selected' : '' }}>
                                            {{ __('lifetime') }}</option>
                                    </select>
                                    <p id="errterm" class="mb-0 text-danger em"></p>
                                </div>
                                @php
                                    $permissions = $package->features;
                                    if (!empty($package->features)) {
                                        $permissions = json_decode($permissions, true);
                                    }
                                @endphp

                                <div class="form-group">
                                    <label class="form-label">{{ __('Package Features') }} <span
                                            class="text-danger">{{ '*' }}</span> </label>

                                    <div class="selectgroup selectgroup-pills">
                                        @foreach ($features as $key => $value)
                                            <label class="selectgroup-item">
                                                <input type="checkbox" name="features[]"
                                                    @if (is_array($permissions) && in_array($value, $permissions)) checked @endif
                                                    id="{{ str_replace(' ', '', $value) }}" value="{{ $value }}"
                                                    class="selectgroup-input">
                                                <span class="selectgroup-button">{{ __($value) }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    <p id="errfeatures" class="mb-0 text-danger em"></p>
                                </div>

                                @if (is_array($features) && in_array('Additional Language', $features))
                                    <div class="form-group" id="language_input">
                                        <label for="languages">{{ __('Number of Additional Languages') }} <span
                                                class="text-danger">{{ '*' }}</span> </label>
                                        <input id="languages" type="number" class="form-control"
                                            name="number_of_language" value="{{ $package->number_of_language }}"
                                            placeholder="{{ __('Enter number of additional languages') }}">
                                        <p id="errnumber_of_language" class="mb-0 text-danger em"></p>
                                        <p class="text-warning mb-0">
                                            <small>{{ __('Enter 999999, than it will appear as unlimited') }}</small>
                                        </p>
                                    </div>
                                @endif
                                @if (is_array($features) && in_array('Agent', $features))
                                    <div class="form-group" id="agent_input">
                                        <label for="agent">{{ __('Number of Agents') }} <span
                                                class="text-danger">{{ '*' }}</span> </label>
                                        <input id="agent" type="number" class="form-control" name="number_of_agent"
                                            placeholder="{{ __('Enter number of agents') }}"
                                            value="{{ $package->number_of_agent }}">
                                        <p id="errnumber_of_agent" class="mb-0 text-danger em"></p>
                                        <p class="text-warning mb-0">
                                            <small>{{ __('Enter 999999, than it will appear as unlimited') }}</small>
                                        </p>
                                    </div>
                                @endif
                                @if (is_array($features) && in_array('Property Management', $features))
                                    <div class="form-group" id="property_input">
                                        <label for="property ">{{ __('Number of Properties') }} <span
                                                class="text-danger">{{ '*' }}</span> </label>
                                        <input id="property " type="number" class="form-control"
                                            name="number_of_property" placeholder="{{ __('Enter number of property') }}"
                                            value="{{ $package->number_of_property }}">
                                        <p id="errnumber_of_property" class="mb-0 text-danger em"></p>
                                        <p class="text-warning mb-0">
                                            <small>{{ __('Enter 999999, than it will appear as unlimited') }}</small>
                                        </p>
                                    </div>

                                    <div class="form-group" id="property_featured_input">
                                        <label for="property ">{{ __('Number of Featured Properties') }} <span
                                                class="text-danger">{{ '*' }}</span> </label>
                                        <input id="property " type="number" class="form-control"
                                            name="number_of_property_featured"
                                            placeholder="{{ __('Enter number of featured property') }}"
                                            value="{{ $package->number_of_property_featured }}">
                                        <p id="errnumber_of_property_featured" class="mb-0 text-danger em"></p>
                                        <p class="text-warning mb-0">
                                            <small>{{ __('Enter 999999, than it will appear as unlimited') }}</small>
                                        </p>
                                    </div>

                                    <div class="form-group" id="property_gallery_input">
                                        <label class="form-label">{{ __('Number of Gallery Images (Per Property)') }}
                                            <span class="text-danger">{{ '*' }}</span> </label>
                                        <input type="text" name="number_of_property_gallery_images"
                                            class="form-control"
                                            value="{{ $package->number_of_property_gallery_images }}"
                                            placeholder="{{ __('Enter how many gallery images are added under a property') }}">
                                        <p id="errnumber_of_property_gallery_images" class="mb-0 text-danger em"></p>
                                        <p class="text-warning mb-0">
                                            <small>{{ __('Enter 999999, than it will appear as unlimited') }}</small>
                                        </p>
                                    </div>

                                    <div class="form-group" id="property_features_input">
                                        <label class="form-label">{{ __('Number of Additional Features (Per Property)') }}
                                            <span class="text-danger">{{ '*' }}</span> </label>
                                        <input type="text" class="form-control"
                                            name="number_of_property_additional_features"
                                            value="{{ $package->number_of_property_adittionl_specifications }}"
                                            placeholder="{{ __('Enter how many adittional feature are added under a property') }}">
                                        <p id="errnumber_of_property_additional_features" class="mb-0 text-danger em">
                                        </p>
                                        <p class="text-warning mb-0">
                                            <small>{{ __('Enter 999999, than it will appear as unlimited') }}</small>
                                        </p>
                                    </div>
                                @endif
                                @if (is_array($features) && in_array('Project Management', $features))
                                    <div class="form-group" id="project_input">
                                        <label for="products ">{{ __('Number of Projects') }} <span
                                                class="text-danger">{{ '*' }}</span> </label>
                                        <input id="products " type="number" class="form-control"
                                            name="number_of_projects" placeholder="{{ __('Enter number of projects') }}"
                                            value="{{ $package->number_of_projects }}">
                                        <p id="errnumber_of_projects" class="mb-0 text-danger em"></p>
                                        <p class="text-warning mb-0">
                                            <small>{{ __('Enter 999999, than it will appear as unlimited') }}</small>
                                        </p>
                                    </div>

                                    <div class="form-group" id="project_types_input">
                                        <label class="form-label">{{ __('Number of Project Types (Per Project)') }}
                                            <span class="text-danger">{{ '*' }}</span> </label>
                                        <input type="text" class="form-control" name="number_of_project_types"
                                            value="{{ $package->number_of_project_types }}"
                                            placeholder="{{ __('Enter how many types are added under a project') }}">
                                        <p id="errnumber_of_project_types" class="mb-0 text-danger em"></p>
                                        <p class="text-warning mb-0">
                                            <small>{{ __('Enter 999999, than it will appear as unlimited') }}</small>
                                        </p>
                                    </div>

                                    <div class="form-group" id="project_gallery_input">
                                        <label class="form-label">{{ __('Number of Gallery Images (Per Project)') }}
                                            <span class="text-danger">{{ '*' }}</span> </label>
                                        <input type="text" name="number_of_project_gallery_images"
                                            class="form-control" value="{{ $package->number_of_project_gallery_images }}"
                                            placeholder="{{ __('Enter how many gallery images are added under a project') }}">
                                        <p id="errnumber_of_project_gallery_images" class="mb-0 text-danger em"></p>
                                        <p class="text-warning mb-0">
                                            <small>{{ __('Enter 999999, than it will appear as unlimited') }}</small>
                                        </p>
                                    </div>

                                    <div class="form-group" id="project_additional_input">
                                        <label class="form-label">{{ __('Number of Additional Features (Per Project)') }}
                                            <span class="text-danger">{{ '*' }}</span> </label>
                                        <input type="text" class="form-control"
                                            name="number_of_project_additional_features"
                                            value="{{ $package->number_of_project_additionl_specifications }}"
                                            placeholder="{{ __('Enter how many adittional feature are added under a propject') }}">
                                        <p id="errnumber_of_project_additional_features" class="mb-0 text-danger em">
                                        </p>
                                        <p class="text-warning mb-0">
                                            <small>{{ __('Enter 999999, than it will appear as unlimited') }}</small>
                                        </p>
                                    </div>
                                @endif
                                @if (is_array($features) && in_array('Blog', $features))
                                    <div class="form-group" id="blog_input">
                                        <label for="languagesss">{{ __('Number of Blog Posts') }}<span
                                                class="text-danger">{{ '*' }}</span></label>
                                        <input id="languagesss" type="number" class="form-control"
                                            name="number_of_blog_post" value="{{ $package->number_of_blog_post }}"
                                            placeholder="{{ __('Enter number of blog posts') }}">
                                        <p id="errnumber_of_blog_post" class="mb-0 text-danger em"></p>
                                        <p class="text-warning mb-0">
                                            <small>{{ __('Enter 999999, than it will appear as unlimited') }}</small>
                                        </p>
                                    </div>
                                @endif
                                @if (is_array($features) && in_array('Additional Page', $features))
                                    <div class="form-group" id="custom_input">
                                        <label for="custom">{{ __('Number of Additional Pages') }} <span
                                                class="text-danger">{{ '*' }}</span> </label>
                                        <input id="custom" type="number" class="form-control"
                                            value="{{ $package->number_of_additional_page }}"
                                            name="number_of_additional_page"
                                            placeholder="{{ __('Enter number of additional pages') }}">
                                        <p id="errnumber_of_additional_page" class="mb-0 text-danger em"></p>
                                        <p class="text-warning mb-0">
                                            <small>{{ __('Enter 999999, than it will appear as unlimited') }}</small>
                                        </p>
                                    </div>
                                @endif
                                <div class="form-group">
                                    <label class="form-label">{{ __('Featured') }} <span
                                            class="text-danger">{{ '*' }}</span> </label>
                                    <div class="selectgroup w-100">
                                        <label class="selectgroup-item">
                                            <input type="radio" name="featured" value="1"
                                                class="selectgroup-input" {{ $package->featured == 1 ? 'checked' : '' }}>
                                            <span class="selectgroup-button">{{ __('Yes') }}</span>
                                        </label>
                                        <label class="selectgroup-item">
                                            <input type="radio" name="featured" value="0"
                                                class="selectgroup-input" {{ $package->featured == 0 ? 'checked' : '' }}>
                                            <span class="selectgroup-button">{{ __('No') }}</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">{{ __('Trial') }} <span
                                            class="text-danger">{{ '*' }}</span> </label>
                                    <div class="selectgroup w-100">
                                        <label class="selectgroup-item">
                                            <input type="radio" name="is_trial" value="1"
                                                class="selectgroup-input" {{ $package->is_trial == 1 ? 'checked' : '' }}>
                                            <span class="selectgroup-button">{{ __('Yes') }}</span>
                                        </label>
                                        <label class="selectgroup-item">
                                            <input type="radio" name="is_trial" value="0"
                                                class="selectgroup-input" {{ $package->is_trial == 0 ? 'checked' : '' }}>
                                            <span class="selectgroup-button">{{ __('No') }}</span>
                                        </label>
                                    </div>
                                </div>


                                @if ($package->is_trial == 1)
                                    <div class="form-group dis-block" id="trial_day">
                                        <label for="trial_days_2">{{ __('Trial days') }} <span
                                                class="text-danger">{{ '*' }}</span> </label>
                                        <input id="trial_days_2" type="number" class="form-control" name="trial_days"
                                            placeholder="{{ __('Enter trial days') }}"
                                            value="{{ $package->trial_days }}">
                                    </div>
                                @else
                                    <div class="form-group dis-none" id="trial_day">
                                        <label for="trial_days_1">{{ __('Trial days') }} <span
                                                class="text-danger">{{ '*' }}</span> </label>
                                        <input id="trial_days_1" type="number" class="form-control" name="trial_days"
                                            placeholder="{{ __('Enter trial days') }}"
                                            value="{{ $package->trial_days }}">
                                    </div>
                                @endif
                                <p id="errtrial_days" class="mb-0 text-danger em"></p>

                                <div class="form-group">
                                    <label class="form-label">{{ __('Recommended') }} <span
                                            class="text-danger">{{ '*' }}</span> </label>
                                    <div class="selectgroup w-100">
                                        <label class="selectgroup-item">
                                            <input type="radio" name="recommended" value="1"
                                                class="selectgroup-input"{{ $package->recommended == 1 ? 'checked' : '' }}>
                                            <span class="selectgroup-button">{{ __('Yes') }}</span>
                                        </label>
                                        <label class="selectgroup-item">
                                            <input type="radio" name="recommended" value="0"
                                                class="selectgroup-input"
                                                {{ $package->recommended == 0 ? 'checked' : '' }}>
                                            <span class="selectgroup-button">{{ __('No') }}</span>
                                        </label>
                                    </div>
                                </div>


                                <div class="form-group">
                                    <label for="status">{{ __('Status') }} <span
                                            class="text-danger">{{ '*' }}</span> </label>
                                    <select id="status" class="form-control ltr" name="status">
                                        <option value="" selected disabled>{{ __('Select a status') }}</option>
                                        <option value="1" {{ $package->status == '1' ? 'selected' : '' }}>
                                            {{ __('Active') }}</option>
                                        <option value="0" {{ $package->status == '0' ? 'selected' : '' }}>
                                            {{ __('Deactive') }}</option>
                                    </select>
                                    <p id="errstatus" class="mb-0 text-danger em"></p>
                                </div>


                            </form>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="form">
                        <div class="form-group from-show-notify row">
                            <div class="col-12 text-center">
                                <button type="submit" id="submitBtn"
                                    class="btn btn-success">{{ __('Update') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('scripts')
    <script>
        "use strict";
        var permission = @json($permissions);
    </script>
    <script src="{{ asset('assets/admin/js/edit-package.js') }}"></script>
@endsection
