@extends('agent.layout')


@section('content')
    <div class="page-header">
        <h4 class="page-title">{{ __('Edit Project') }}</h4>
        <ul class="breadcrumbs">
            <li class="nav-home">
                <a href="{{ route('agent.dashboard', getParam()) }}">
                    <i class="flaticon-home"></i>
                </a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="#">{{ __('Project Management') }}</a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="#">{{ __('Edit Project') }}</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title d-inline-block">{{ __('Edit Project') }}</div>
                    <a class="btn btn-info btn-sm float-right d-inline-block"
                        href="{{ route('agent.project_management.projects', [getParam(), 'language' => $language->code]) }}">
                        <span class="btn-label">
                            <i class="fas fa-backward"></i>
                        </span>
                        {{ __('Back') }}
                    </a>
                    @php
                        $dContent = $project->getContent($language->id);

                        $slug = !empty($dContent) ? $dContent->slug : '';
                    @endphp
                    @if ($dContent)
                        <a class="btn btn-success btn-sm float-right mr-1 d-inline-block"
                            href="
                          
                             "
                            target="_blank">
                            <span class="btn-label">
                                <i class="fas fa-eye"></i>
                            </span>
                            {{ __('Preview') }}
                        </a>
                    @endif

                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-10 offset-lg-1">
                            <div class="alert alert-danger pb-1 dis-none" id="propertyErrors">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                <ul></ul>
                            </div>


                            <div class="row">
                                <div class="col-lg-6">
                                    <label for="" class="mb-2"><strong>{{ __('Gallery Images') }} </strong> <span
                                            class="text-danger">{{ '*' }}</span> </label>

                                    <table class="table table-striped" id="imgtable">

                                        @foreach ($gallery_images as $item)
                                            <tr class="trdb table-row" id="trdb{{ $item->id }}">
                                                <td>
                                                    <div class="">
                                                        <img class="thumb-preview wf-150"
                                                            src="{{ asset('assets/img/project/gallery-images/' . $item->image) }}"
                                                            alt="Ad Image">
                                                    </div>
                                                </td>
                                                <td>
                                                    <i class="fa fa-times rmvbtndb" data-indb="{{ $item->id }}"></i>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </table>

                                    <form action="{{ route('agent.project.gallery_image_store', getParam()) }}"
                                        id="my-dropzone" enctype="multipart/formdata" class="dropzone create">
                                        @csrf
                                        <div class="fallback">
                                            <input name="file" type="file" multiple />
                                        </div>
                                        <input type="hidden" value="{{ $project->id }}" name="project_id">
                                    </form>
                                    <p class="em text-danger mb-0" id="errgallery_images"></p>



                                </div>

                                <div class="col-lg-6">
                                    <label for="" class="mb-2"><strong>{{ __('Floor Planning Images') }}
                                        </strong></label>

                                    <table class="table table-striped" id="imgtable">

                                        @foreach ($floor_plan_images as $item)
                                            <tr class="trdb table-row" id="trdb{{ $item->id }}">
                                                <td>
                                                    <div class="">
                                                        <img class="thumb-preview wf-150"
                                                            src="{{ asset('assets/img/project/floor-paln-images/' . $item->image) }}"
                                                            alt="Ad Image">
                                                    </div>
                                                </td>
                                                <td>
                                                    <i class="fa fa-times rmvbtndb2" data-indb="{{ $item->id }}"></i>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </table>

                                    <form action="{{ route('agent.project.floor_plan_image_store', getParam()) }}"
                                        id="my-dropzone2" enctype="multipart/formdata" class="dropzone create">
                                        @csrf
                                        <div class="fallback">
                                            <input name="file" type="file" multiple />
                                        </div>
                                        <input type="hidden" value="{{ $project->id }}" name="project_id">
                                    </form>
                                    <p class="em text-danger mb-0" id="errfloor_plan_images"></p>
                                </div>
                            </div>

                            <form id="propertyForm"
                                action="{{ route('agent.project_management.update_project', [getParam(), $project->id]) }}"
                                method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="project_id" value="{{ $project->id }}">

                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="">{{ __('Featured Image') }} <span
                                                    class="text-danger">{{ '*' }}</span></label>
                                            <br>
                                            <div class="thumb-preview">
                                                <img src="{{ $project->featured_image ? asset('assets/img/project/featured/' . $project->featured_image) : asset('assets/admin/img/noimage.jpg') }}"
                                                    alt="..." class="uploaded-img">
                                            </div>
                                            <div class="mt-3">
                                                <div role="button" class="btn btn-primary btn-sm upload-btn">
                                                    {{ __('Choose Image') }}
                                                    <input type="file" class="img-input" name="featured_image">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div class="row">


                                    <div class="col-lg-4">
                                        <div class="form-group ">
                                            <label>{{ __('Category') }} <span
                                                    class="text-danger">{{ '*' }}</span></label>
                                            <select name="category_id" class="form-control category">
                                                <option disabled selected>
                                                    {{ __('Select a Category') }}
                                                </option>

                                                @foreach ($projectCategories as $category)
                                                    <option value="{{ $category->id }}"
                                                        {{ $project->category_id == $category->id ? 'selected' : '' }}>
                                                        {{ $category->categoryContent->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    @if ($settings->project_country_status == 1)
                                        <div class="col-lg-4">
                                            <div class="form-group ">

                                                <label>{{ __('Country') }} <span
                                                        class="text-danger">{{ '*' }}</span> </label>
                                                <select name="country_id"
                                                    class="form-control country js-example-basic-single3">
                                                    <option disabled selected>
                                                        {{ __('Select Country') }}
                                                    </option>

                                                    @foreach ($projectCountries as $country)
                                                        <option value="{{ $country->id }}"
                                                            {{ $project->country_id == $country->id ? 'selected' : '' }}>
                                                            {{ $country?->getContent($language->id)?->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    @endif
                                    @if ($settings->project_country_status == 1 && $settings->project_state_status == 1)
                                        <div class="col-lg-4 state"
                                            @if (empty($project->state_id)) style="display:none;"@else style="display:block !important;" @endif>
                                            <div class="form-group">

                                                <label>{{ __('State') }} <span
                                                        class="text-danger">{{ '*' }}</span> </label>
                                                <select onchange="getCities(event)" name="state_id"
                                                    class="form-control  state_id states js-example-basic-single3">
                                                    <option disabled selected>{{ __('Select State') }}
                                                    </option>
                                                    @if ($project->state_id)
                                                        @foreach ($projectStates as $state)
                                                            <option value="{{ $state->id }}"
                                                                {{ $project->state_id == $state->id ? 'selected' : '' }}>
                                                                {{ $state?->getContent($language->id)?->name }}</option>
                                                        @endforeach
                                                    @endif


                                                </select>
                                            </div>
                                        </div>
                                    @elseif ($settings->project_country_status == 0 && $settings->project_state_status == 1)
                                        <div class="col-lg-4 state ">
                                            <div class="form-group">

                                                <label>{{ __('State') }} <span
                                                        class="text-danger">{{ '*' }}</span></label>
                                                <select onchange="getCities(event)" name="state_id"
                                                    class="form-control state_id states js-example-basic-single3">
                                                    <option disabled selected>{{ __('Select State') }}
                                                    </option>

                                                    @foreach ($states as $state)
                                                        <option value="{{ $state->id }}"
                                                            {{ $project->state_id == $state->id ? 'selected' : '' }}>
                                                            {{ $state?->getContent($language->id)?->name }}</option>
                                                    @endforeach


                                                </select>
                                            </div>
                                        </div>

                                    @endif
                                    <div class="col-lg-4 city"
                                        @if (empty($project->city_id)) style="display:none;"@else style="display:block;" @endif>
                                        <div class="form-group ">

                                            <label>{{ __('City') }} <span
                                                    class="text-danger">{{ '*' }}</span> </label>
                                            <select name="city_id" class="form-control city_id js-example-basic-single3">
                                                <option value="" disabled>
                                                    {{ __('Select City') }}
                                                </option>
                                                @if ($project->city_id)
                                                    @foreach ($projectCities as $city)
                                                        <option value="{{ $project->city_id }}"
                                                            {{ $project->city_id == $city->id ? 'selected' : '' }}>
                                                            {{ $city?->getContent($language->id)?->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>


                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label>{{ __('Minimum Price') . ' (' . $settings->base_currency_text . ')' }}
                                                <span class="text-danger">{{ '*' }}</span> </label>
                                            <input type="number" class="form-control" name="min_price"
                                                placeholder="{{ __('Enter minimum price') }}"
                                                value="{{ $project->min_price }}">
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label>{{ __('Maximum Price') . ' (' . $settings->base_currency_text . ')' }}
                                            </label>
                                            <input type="number" class="form-control" name="max_price"
                                                placeholder="{{ __('Enter maximum price') }}"
                                                value="{{ $project->max_price }}">
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label>{{ __('Status') }} <span
                                                    class="text-danger">{{ '*' }}</span> </label>
                                            <select name="status" id="" class="form-control">
                                                <option {{ $project->complete_status == 1 ? 'selected' : '' }}
                                                    value="1">
                                                    {{ __('Complete') }}</option>
                                                <option {{ $project->complete_status == 0 ? 'selected' : '' }}
                                                    value="0">
                                                    {{ __('Under Construction') }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    {{-- <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>{{ __('Featured') }} *</label>
                                            <select name="featured" id="" class="form-control">
                                                <option value="0">{{ __('No') }}</option>
                                                <option value="1">{{ __('Yes') }}</option>
                                            </select>
                                        </div>
                                    </div> --}}

                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label>{{ __('Latitude') }} <span
                                                    class="text-danger">{{ '*' }}</span></label>
                                            <input type="text" class="form-control" value="{{ $project->latitude }}"
                                                name="latitude" placeholder="{{ __('Enter Latitude') }}">
                                            <p> <span class="text-warning">
                                                    {{ __('The Latitude must be between -90 to 90.') }}
                                                </span>
                                                <a href="https://tinyurl.com/2hjtmtvc "> <i class="fas fa-eye"> </i>
                                                    {{ __('See Example') }}</a>
                                            </p>
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label>{{ __('Longitude') }} <span
                                                    class="text-danger">{{ '*' }}</span></label>
                                            <input type="text" class="form-control" value="{{ $project->longitude }}"
                                                name="longitude" placeholder="{{ __('Enter Longitude') }}">
                                            <p>
                                                <span class="text-warning">
                                                    {{ __('The Longitude must be between -180 to 180.') }}
                                                </span>
                                                <a href="https://tinyurl.com/2hjtmtvc "> <i class="fas fa-eye"> </i>
                                                    {{ __('See Example') }} </a>
                                            </p>
                                        </div>
                                    </div>

                                </div>

                                <div id="accordion" class="mt-3">
                                    @foreach ($tenantLangs as $language)
                                        @php
                                            $projectContent = $project->getContent($language->id);
                                        @endphp
                                        <div class="version">
                                            <div class="version-header" id="heading{{ $language->id }}">
                                                <h5 class="mb-0">
                                                    <button type="button" class="btn btn-link" data-toggle="collapse"
                                                        data-target="#collapse{{ $language->id }}"
                                                        aria-expanded="{{ $language->is_default == 1 ? 'true' : 'false' }}"
                                                        aria-controls="collapse{{ $language->id }}">
                                                        {{ $language->name . ' ' . __('Language') }}
                                                        {{ $language->is_default == 1 ? '(Default)' : '' }}
                                                    </button>
                                                </h5>
                                            </div>

                                            <div id="collapse{{ $language->id }}"
                                                class="collapse {{ $language->is_default == 1 ? 'show' : '' }}"
                                                aria-labelledby="heading{{ $language->id }}" data-parent="#accordion">
                                                <div class="version-body">
                                                    <div class="row">
                                                        <div class="col-lg-8">
                                                            <div
                                                                class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                                                <label>{{ __('Title') }} <span
                                                                        class="text-danger">{{ '*' }}</span>
                                                                </label>
                                                                <input type="text" class="form-control"
                                                                    name="{{ $language->code }}_title"
                                                                    placeholder="{{ __('Enter Title') }}"
                                                                    value="{{ $projectContent ? $projectContent->title : '' }}">
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-4">
                                                            <div
                                                                class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                                                <label>{{ __('Address') }} <span
                                                                        class="text-danger">{{ '*' }}</span>
                                                                </label>
                                                                <input type="text"
                                                                    name="{{ $language->code }}_address"
                                                                    placeholder="{{ __('Enter Address') }}"
                                                                    value="{{ @$projectContent->address }}"
                                                                    class="form-control">
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div
                                                                class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                                                <label>{{ __('Description') }} <span
                                                                        class="text-danger">{{ '*' }}</span>
                                                                </label>
                                                                <textarea class="form-control summernote " id="{{ $language->code }}_description"
                                                                    placeholder="{{ __('Enter Description') }}" name="{{ $language->code }}_description" data-height="300">{{ @$projectContent->description }}</textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div
                                                                class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                                                <label>{{ __('Meta Keywords') }} </label>
                                                                <input class="form-control"
                                                                    name="{{ $language->code }}_meta_keyword"
                                                                    placeholder=" {{ __('Enter Meta Keywords') }}"
                                                                    data-role="tagsinput"
                                                                    value="{{ $projectContent ? $projectContent->meta_keyword : '' }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div
                                                                class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                                                <label>{{ __('Meta Description') }} </label>
                                                                <textarea class="form-control" name="{{ $language->code }}_meta_description" rows="5"
                                                                    placeholder="{{ __('Enter Meta Description') }}">{{ $projectContent ? $projectContent->meta_description : '' }}</textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col">
                                                            @php $currLang = $language; @endphp

                                                            @foreach ($tenantLangs as $language)
                                                                @continue($language->id == $currLang->id)

                                                                <div class="form-check py-0 pt-2">
                                                                    <label class="form-check-label">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            onchange="cloneInput('collapse{{ $currLang->id }}', 'collapse{{ $language->id }}', event)">
                                                                        <span
                                                                            class="form-check-sign text-focus">{{ __('Clone for') }}
                                                                            <strong
                                                                                class="text-capitalize text-secondary">{{ $language->name }}</strong>
                                                                            {{ __('language') }}</span>
                                                                    </label>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="row">
                                    <div class="col-lg-12" id="variation_pricing">
                                        <h4 for="">
                                            {{ __('Additional Features') . ' (' . __('Optional') . ')' }}
                                        </h4>
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('Label') }}</th>
                                                    <th>{{ __('Value') }}</th>
                                                    <th><a href="javascrit:void(0)"
                                                            class="btn  btn-sm btn-success addRow"><i
                                                                class="fas fa-plus-circle"></i></a></th>
                                                </tr>
                                            <tbody id="tbody">

                                                @if (count($specifications) > 0)
                                                    @foreach ($specifications as $specification)
                                                        <tr>
                                                            <td>
                                                                @foreach ($tenantLangs as $language)
                                                                    @php
                                                                        $sp_content = \App\Models\User\Project\ProjectSpecificationContent::where(
                                                                            [
                                                                                ['language_id', $language->id],
                                                                                [
                                                                                    'project_spacification_id',
                                                                                    $specification->id,
                                                                                ],
                                                                            ],
                                                                        )->first();
                                                                    @endphp
                                                                    <div
                                                                        class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                                                        <input type="text"
                                                                            name="{{ $language->code }}_label[]"
                                                                            value="{{ @$sp_content->label }}"
                                                                            class="form-control"
                                                                            placeholder="{{ __('Label for') . ' ' . $language->name . ' ' . __('language') }}">
                                                                    </div>
                                                                @endforeach
                                                            </td>
                                                            <td>
                                                                @foreach ($tenantLangs as $language)
                                                                    @php
                                                                        $sp_content = \App\Models\User\Project\ProjectSpecificationContent::where(
                                                                            [
                                                                                ['language_id', $language->id],
                                                                                [
                                                                                    'project_spacification_id',
                                                                                    $specification->id,
                                                                                ],
                                                                            ],
                                                                        )->first();
                                                                    @endphp
                                                                    <div
                                                                        class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                                                        <input type="text"
                                                                            name="{{ $language->code }}_value[]"
                                                                            value="{{ @$sp_content->value }}"
                                                                            class="form-control"
                                                                            placeholder="{{ __('Value for') . ' ' . $language->name . ' ' . __('language') }}">
                                                                    </div>
                                                                @endforeach
                                                            </td>
                                                            <td>
                                                                <a href="javascript:void(0)"
                                                                    data-specification="{{ $specification->id }}"
                                                                    class="btn  btn-sm btn-danger deleteSpecification">
                                                                    <i class="fas fa-minus"></i></a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td>
                                                            @foreach ($tenantLangs as $language)
                                                                <div
                                                                    class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                                                    <input type="text"
                                                                        name="{{ $language->code }}_label[]"
                                                                        class="form-control"
                                                                        placeholder="{{ __('Label for') . ' ' . $language->name . ' ' . __('language') }}">
                                                                </div>
                                                            @endforeach
                                                        </td>
                                                        <td>
                                                            @foreach ($tenantLangs as $language)
                                                                <div
                                                                    class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                                                    <input type="text"
                                                                        name="{{ $language->code }}_value[]"
                                                                        class="form-control"
                                                                        placeholder="{{ __('Value for') . ' ' . $language->name . ' ' . __('language') }}">
                                                                </div>
                                                            @endforeach
                                                        </td>
                                                        <td>
                                                            <a href="javascript:void(0)"
                                                                class="btn btn-danger  btn-sm deleteRow">
                                                                <i class="fas fa-minus"></i></a>
                                                        </td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                            </thead>
                                        </table>
                                    </div>
                                </div>

                                <div id="galleries"></div>
                                <div id="floorPlan"></div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="row">
                        <div class="col-12 text-center">
                            <button type="submit" id="propertySubmit" class="btn btn-primary">
                                {{ __('Update') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@php

    $labels = '';
    $values = '';
    foreach ($tenantLangs as $language) {
        $label_name = $language->code . '_label[]';
        $value_name = $language->code . '_value[]';

        $labels_placeholder = __('Label for') . ' ' . $language->name . ' ' . __('language');
        $values_placeholder = __('Value for') . ' ' . $language->name . ' ' . __('language');

        if ($language->direction == 1) {
            $direction = 'form-group rtl text-right';
        } else {
            $direction = 'form-group';
        }

        $labels .=
            "<div class='$direction'><input type='text' name='" .
            $label_name .
            "' class='form-control' placeholder='$labels_placeholder'></div>";
        $values .= "<div class='$direction'><input type='text' name='$value_name' class='form-control' placeholder='$values_placeholder'></div>";
    }
@endphp

@section('script')
    <script>
        var labels = "{!! $labels !!}";
        var values = "{!! $values !!}";

        var galleryStoreUrl =
            "{{ route('agent.project.gallery_image_store', getParam()) }}";
        var galleryRemoveUrl = "{{ route('agent.project.gallery_imagermv', getParam()) }}";
        var floorPlanStoreUrl = "{{ route('agent.project.floor_plan_image_store', getParam()) }}";
        var floorPlanRemoveUrl = "{{ route('agent.project.floor_plan_imagermv', getParam()) }}";
        var galleryImagRrmvdbUrl = "{{ route('agent.project.gallery_imgdbrmv', getParam()) }}";
        var floorPlanRmvdbUrl = "{{ route('agent.project.floor_plan_imgdbrmv', getParam()) }}";
        var galleryImages =
            {{ $currentPackage->number_of_project_gallery_images - count($gallery_images) }};
        var specificationRmvUrl = "{{ route('agent.project_management.specification_delete', getParam()) }}";
        var stateUrl = "{{ route('agent.project_specification.get_state_cities', getParam()) }}";
        var cityUrl = "{{ route('agent.project_specification.get_cities', getParam()) }}";
        var selectAmenLocal = "{{ $keywords['Select Amenities'] ?? __('Select Amenities') }}";

    </script>
    <script type="text/javascript" src="{{ asset('assets/tenant/js/admin-project-dropzone.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/tenant/js/property.js') }}"></script>

@endsection
