@extends('user.layout')


@section('content')
    <div class="page-header">
        <h4 class="page-title">{{ __('Edit Car') }}</h4>
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
                <a href="#">{{ __('Car Management') }}</a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="#">{{ __('Edit Car') }}</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title d-inline-block">{{ __('Edit Car') }}</div>

                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-10 offset-lg-1">
                            <div class="alert alert-danger pb-1 dis-none" id="propertyErrors">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                <ul></ul>
                            </div>

                            <div class="col-lg-12">
                                <label for="" class="mb-2"><strong>{{ __('Gallery Images') }}</strong> <span
                                        class="text-danger">{{ '*' }}</span> </label>
                                <div id="reload-slider-div">
                                    <div class="row">

                                        <div class="col-12">
                                            <table class="table table-striped" id="imgtable">

                                                @foreach ($galleryImages as $item)
                                                    <tr class="trdb table-row" id="trdb{{ $item->id }}">
                                                        <td>
                                                            <div class="">
                                                                <img class="thumb-preview wf-150"
                                                                    src="{{ asset('assets/img/property/slider-images/' . $item->image) }}"
                                                                    alt="Ad Image">
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <i class="fa fa-times rmvbtndb"
                                                                data-indb="{{ $item->id }}"></i>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <form action="{{ route('user.property.imagesstore') }}" id="my-dropzone"
                                    enctype="multipart/formdata" class="dropzone create">
                                    @csrf
                                    <div class="fallback">
                                        <input name="file" type="file" multiple />
                                    </div>
                                    <input type="hidden" value="{{ $property->id }}" name="property_id">
                                </form>
                                <p class="em text-danger mb-0" id="errslider_images"></p>

                            </div>

                            <form id="propertyForm"
                                action="{{ route('user.property_management.update_property', $property->id) }}"
                                method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="property_id" value="{{ $property->id }}">
                                <input type="hidden" name="type" value="{{ $property->type }}">
                                <input type="hidden" name="vendor_id" value="{{ $property->vendor_id }}">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="">{{ __('Thumbnail Image') }} <span
                                                    class="text-danger">{{ '*' }}</span> </label>
                                            <br>
                                            <div class="thumb-preview">
                                                <img src="{{ $property->featured_image ? asset('assets/img/property/featureds/' . $property->featured_image) : asset('assets/img/noimage.jpg') }}"
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
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="">{{ __('Engine photo') }}</label>
                                            <br>
                                            <div class="thumb-preview remove">

                                                <img src="{{ !empty($property->floor_planning_image) ? asset('assets/img/property/plannings/' . $property->floor_planning_image) : asset('assets/img/noimage.jpg') }}"
                                                    alt="..." class="uploaded-img2">
                                                @if (!empty($property->floor_planning_image))
                                                    <i class="fas fa-times text-danger rmvflrImg"
                                                        data-indb="{{ $property->id }}"></i>
                                                @endif
                                            </div>

                                            <div class="mt-3">
                                                <div role="button" class="btn btn-primary btn-sm upload-btn">
                                                    {{ __('Choose Image') }}
                                                    <input type="file" class="img-input2" name="floor_planning_image">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="">{{ __('Video Image') }}</label>
                                            <br>
                                            <div class="thumb-preview remove">

                                                <img src="{{ !empty($property->video_image) ? asset('assets/img/property/video/' . $property->video_image) : asset('assets/img/noimage.jpg') }}"
                                                    alt="..." class="uploaded-img3">
                                                @if (!empty($property->video_image))
                                                    <i class="fas fa-times text-danger rmvvdoImg"
                                                        data-indb="{{ $property->id }}"></i>
                                                @endif
                                            </div>

                                            <div class="mt-3">
                                                <div role="button" class="btn btn-primary btn-sm upload-btn">
                                                    {{ __('Choose Image') }}
                                                    <input type="file" class="img-input3" name="video_image">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>{{ __('Video Url') }} </label>
                                            <input type="text" class="form-control" name="video_url"
                                                placeholder="{{ __('Enter video url') }}"
                                                value="{{ $property->video_url }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>{{ __('Condition') }} <span
                                                    class="text-danger">{{ '*' }}</span> </label>

                                            <select name="purpose" class="form-control">
                                                <option disabled>
                                                    {{ __('Select Condition') }} </option>
                                                <option value="rent" @if ($property->purpose == 'rent') 'selected' @endif>
                                                    {{ __('Rent') }}
                                                </option>
                                                <option value="sale" @if ($property->purpose == 'sale') 'selected' @endif>
                                                    {{ __('Sale') }}
                                                </option>
                                            </select>
                                        </div>

                                    </div>

                                    <div class="col-lg-3">
                                        <div class="form-group ">
                                            <label>{{ __('Category') }} <span
                                                    class="text-danger">{{ '*' }}</span> </label>
                                            <select name="category_id" class="form-control category">
                                                <option disabled selected>
                                                    {{ __('Select a Category') }}
                                                </option>

                                                @foreach ($propertyCategories as $category)
                                                    @php
                                                        $catContent = $category->categoryContents()->first();
                                                    @endphp
                                                    <option value="{{ $category->id }}"
                                                        {{ $property->category_id == $category->id ? 'selected' : '' }}>
                                                        {{ $catContent?->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    @if ($adminBs->property_country_status == 1)
                                        <div class="col-lg-3">
                                            <div class="form-group ">

                                                <label>{{ __('Province') }} <span
                                                        class="text-danger">{{ '*' }}</span> </label>
                                                <select name="country_id" class="form-control country">
                                                    <option disabled selected>
                                                        {{ __('Select Province') }}
                                                    </option>

                                                    @foreach ($propertyCountries as $country)
                                                        @php
                                                            $countryContent = $country->contents()->first();
                                                        @endphp
                                                        <option value="{{ $country->id }}"
                                                            {{ $property->country_id == $country->id ? 'selected' : '' }}>
                                                            {{ @$countryContent->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    @endif
                                    @if ($adminBs->property_country_status == 1 && $adminBs->property_state_status == 1)
                                        <div class="col-lg-3 state"
                                            @if (is_null($property->state_id)) style="display:none !important;" @else style="display:block !important;" @endif>
                                            <div class="form-group">

                                                <label>{{ __('City') }} <span
                                                        class="text-danger">{{ '*' }}</span> </label>
                                                <select onchange="getCities(event)" name="state_id"
                                                    class="form-control  state_id states">
                                                    <option disabled>{{ __('Select City') }}
                                                    </option>
                                                    @if ($property->state_id)
                                                        @foreach ($propertyStates as $state)
                                                            @php
                                                                $stateContent = $state->contents()->first();
                                                            @endphp

                                                            <option value="{{ $state->id }}"
                                                                {{ $property->state_id == $state->id ? 'selected' : '' }}>
                                                                {{ @$stateContent->name }}</option>
                                                        @endforeach
                                                    @endif


                                                </select>
                                            </div>
                                        </div>
                                    @elseif ($adminBs->property_country_status == 0 && $adminBs->property_state_status == 1)
                                        <div class="col-lg-3 state ">
                                            <div class="form-group">
                                                <label>{{ __('City') }} <span
                                                        class="text-danger">{{ '*' }}</span> </label>
                                                <select onchange="getCities(event)" name="state_id"
                                                    class="form-control state_id states js-example-basic-single3">
                                                    <option disabled selected>{{ __('Select City') }}
                                                    </option>
                                                    @foreach ($states as $state)
                                                        <option value="{{ $state->id }}"
                                                            {{ $property->state_id == $state->id ? 'selected' : '' }}>
                                                            {{ $state?->getContent($language->id)?->name }}</option>
                                                    @endforeach

                                                </select>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="col-lg-3 city"
                                        @if (empty($property->city_id)) style="display:none;"@else style="display:block;" @endif>
                                        <div class="form-group ">

                                            <label>{{ __('Area') }} </label>
                                            <select name="city_id" class="form-control city_id">
                                                <option value="" disabled>
                                                    {{ __('Select Area') }}
                                                </option>
                                                @if ($property->city_id)
                                                    @foreach ($propertyCities as $city)
                                                        @php
                                                            $cityContent = $city->contents()->first();
                                                        @endphp
                                                        <option value="{{ $property->city_id }}"
                                                            {{ $property->city_id == $city->id ? 'selected' : '' }}>
                                                            {{ @$cityContent->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="">{{ __('Amenities') }} <span
                                                    class="text-danger">{{ '*' }}</span> </label>
                                            <select name="amenities[]"
                                                class="form-control js-example-basic-single2 amenities-values"
                                                multiple="multiple">
                                                @foreach ($amenities as $amenity)
                                                    @php
                                                        $amContent = $amenity->contents()->first();
                                                    @endphp
                                                    <option data-value="{{ $amContent?->name }}"
                                                        value="{{ $amenity->id }}"
                                                        @foreach ($propertyAmenities as $propertyAmenity)
                                                            {{ $propertyAmenity->amenity_id == $amenity->id ? 'selected' : '' }} @endforeach>
                                                        {{ $amContent?->name }}</option>
                                                @endforeach
                                            </select>

                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>{{ __('Price') }}
                                            </label>
                                            <input type="number" class="form-control" name="price"
                                                placeholder="{{ __('Enter Current Price') }}"
                                                value="{{ $property->price }}">
                                            <p class="text-warning">
                                                {{ __('If you leave it blank, price will be negotiable') }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>{{ __('Currency') }}
                                            </label>
                                            <select name="currency_symbol" class="form-control">
                                                <option value="UY$" @selected($property->currency_symbol == 'UY$')>
                                                    UY$
                                                </option>
                                                <option value="U$D" @selected($property->currency_symbol == 'U$D')>
                                                    U$D
                                                </option>
                                            </select>
                                        </div>
                                    </div>


                                    @if ($property->type == 'residential')
                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label>{{ __('Beds') }} <span
                                                        class="text-danger">{{ '*' }}</span> </label>
                                                <input type="text" class="form-control" name="beds"
                                                    value="{{ $property->beds }}"
                                                    placeholder="{{ __('Enter number of bed') }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label>{{ __('Baths') }} <span
                                                        class="text-danger">{{ '*' }}</span> </label>
                                                <input type="text" class="form-control" name="bath"
                                                    value="{{ $property->bath }}"
                                                    placeholder="{{ __('Enter number of bath') }}">
                                            </div>
                                        </div>
                                    @endif

                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>{{ __('Fuel type') }} <span
                                                    class="text-danger">{{ '*' }}</span></label>
                                            <select name="area" class="form-control">
                                                <option value="" {{ !$property->area ? 'selected' : '' }} disabled>{{ __('Select Fuel type') }}</option>
                                                <option value="Nafta" {{ $property->area == 'Nafta' ? 'selected' : '' }}>Nafta</option>
                                                <option value="Diesel" {{ $property->area == 'Diesel' ? 'selected' : '' }}>Diésel</option>
                                                <option value="Electrico" {{ $property->area == 'Electrico' ? 'selected' : '' }}>Eléctrico</option>
                                                <option value="Hibrido" {{ $property->area == 'Hibrido' ? 'selected' : '' }}>Híbrido</option>
                                                <option value="Hibrido/Diesel" {{ $property->area == 'Hibrido/Diesel' ? 'selected' : '' }}>Híbrido/Diesel</option>
                                                <option value="Nafta/GNC" {{ $property->area == 'Nafta/GNC' ? 'selected' : '' }}>Nafta/GNC</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>{{ __('Brand') }} <span class="text-danger">{{ '*' }}</span></label>
                                            <select name="brand_id" class="form-control brand js-example-basic-single3" onchange="getModels(event)">
                                                <option disabled {{ !$property->brand_id ? 'selected' : '' }}>{{ __('Select Brand') }}</option>
                                                @if(isset($brands))
                                                @foreach ($brands as $brand)
                                                    <option value="{{ $brand->id }}" {{ $property->brand_id == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                                                @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>{{ __('Model') }} <span class="text-danger">{{ '*' }}</span></label>
                                            <select name="model_id" class="form-control model_id js-example-basic-single3" onchange="getVersions(event)">
                                                <option disabled {{ !$property->model_id ? 'selected' : '' }}>{{ __('Select Model') }}</option>
                                                @if($property->model_id)
                                                    @foreach (\App\Models\User\UserCarModel::where('brand_id', $property->brand_id)->get() as $model)
                                                        <option value="{{ $model->id }}" {{ $property->model_id == $model->id ? 'selected' : '' }}>{{ $model->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>{{ __('Version') }}</label>
                                            <select name="version_id" class="form-control version_id js-example-basic-single3">
                                                <option disabled {{ !$property->version_id ? 'selected' : '' }}>{{ __('Select Version') }}</option>
                                                @if($property->version_id)
                                                    @foreach (\App\Models\User\UserCarVersion::where('model_id', $property->model_id)->get() as $version)
                                                        <option value="{{ $version->id }}" {{ $property->version_id == $version->id ? 'selected' : '' }}>{{ $version->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>{{ __('Mileage') }} <span class="text-danger">{{ '*' }}</span></label>
                                            <input type="number" name="km" class="form-control" placeholder="{{ __('Enter Mileage in km') }}" min="0" value="{{ $property->km }}">
                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>{{ __('Year') }} <span class="text-danger">{{ '*' }}</span></label>
                                            <input type="number" name="year" class="form-control" placeholder="{{ __('Enter Year') }}" min="1900" max="{{ now()->year }}" value="{{ $property->year }}">
                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>{{ __('Transmission') }} <span class="text-danger">{{ '*' }}</span></label>
                                            <select name="transmision" class="form-control">
                                                <option disabled {{ !$property->transmision ? 'selected' : '' }}>{{ __('Select Transmission') }}</option>
                                                <option value="Manual" {{ $property->transmision == 'Manual' ? 'selected' : '' }}>{{ __('Manual') }}</option>
                                                <option value="Automática" {{ $property->transmision == 'Automática' ? 'selected' : '' }}>{{ __('Automatic') }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>{{ __('Color') }} <span class="text-danger">{{ '*' }}</span></label>
                                            <select name="color" class="form-control">
                                                <option disabled {{ !$property->color ? 'selected' : '' }}>{{ __('Select Color') }}</option>
                                                <option value="Blanco" {{ $property->color == 'Blanco' ? 'selected' : '' }}>{{ __('White') }}</option>
                                                <option value="Negro" {{ $property->color == 'Negro' ? 'selected' : '' }}>{{ __('Black') }}</option>
                                                <option value="Rojo" {{ $property->color == 'Rojo' ? 'selected' : '' }}>{{ __('Red') }}</option>
                                                <option value="Azul" {{ $property->color == 'Azul' ? 'selected' : '' }}>{{ __('Blue') }}</option>
                                                <option value="Gris" {{ $property->color == 'Gris' ? 'selected' : '' }}>{{ __('Gray') }}</option>
                                                <option value="Plata" {{ $property->color == 'Plata' ? 'selected' : '' }}>{{ __('Silver') }}</option>
                                                <option value="Verde" {{ $property->color == 'Verde' ? 'selected' : '' }}>{{ __('Green') }}</option>
                                                <option value="Marrón" {{ $property->color == 'Marrón' ? 'selected' : '' }}>{{ __('Brown') }}</option>
                                                <option value="Beige" {{ $property->color == 'Beige' ? 'selected' : '' }}>{{ __('Beige') }}</option>
                                                <option value="Dorado" {{ $property->color == 'Dorado' ? 'selected' : '' }}>{{ __('Gold') }}</option>
                                                <option value="Naranja" {{ $property->color == 'Naranja' ? 'selected' : '' }}>{{ __('Orange') }}</option>
                                                <option value="Amarillo" {{ $property->color == 'Amarillo' ? 'selected' : '' }}>{{ __('Yellow') }}</option>
                                                <option value="Rosado" {{ $property->color == 'Rosado' ? 'selected' : '' }}>{{ __('Pink') }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>{{ __('Traction') }}</label>
                                            <select name="traction" class="form-control">
                                                <option disabled {{ !$property->traction ? 'selected' : '' }}>{{ __('Select Traction') }}</option>
                                                <option value="4x2" {{ $property->traction == '4x2' ? 'selected' : '' }}>4x2</option>
                                                <option value="4x4" {{ $property->traction == '4x4' ? 'selected' : '' }}>4x4</option>
                                                <option value="AWD" {{ $property->traction == 'AWD' ? 'selected' : '' }}>AWD</option>
                                                <option value="4wd" {{ $property->traction == '4wd' ? 'selected' : '' }}>4wd</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>{{ __('Engine') }}</label>
                                            <input type="number" step="0.1" class="form-control" name="engine"
                                                placeholder="{{ __('Enter Engine Size') }}" value="{{ $property->engine }}">
                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>{{ __('Cost') }}</label>
                                            <input type="number" step="0.01" class="form-control" name="cost"
                                                placeholder="{{ __('Enter Purchase Cost') }}" value="{{ $property->cost }}">
                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>{{ __('Status') }} <span
                                                    class="text-danger">{{ '*' }}</span> </label>
                                            <select name="status" id="" class="form-control">
                                                <option {{ $property->status == 1 ? 'selected' : '' }} value="1">
                                                    {{ __('Active') }}</option>
                                                <option {{ $property->status == 0 ? 'selected' : '' }} value="0">
                                                    {{ __('Deactive') }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="form-group agent  ">
                                            <label for="">{{ __('Agent') }}</label>
                                            <select name="agent_id" class="form-control agent_id">
                                                <option value="" selected>
                                                    {{ __('Select Agent') }}
                                                </option>
                                                @foreach ($agents as $agent)
                                                    <option value="{{ $agent->id }}"
                                                        {{ $agent->id == $property->agent_id ? 'selected' : '' }}>
                                                        {{ $agent->username }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <p class="text-warning">
                                                {{ __('If you do not select any agent, then this property will be listed under you') }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label class="d-block">
                                                <input type="checkbox" name="share" id="shareCar" value="1"
                                                    {{ $property->shared == 1 ? 'checked' : '' }}>
                                                {{ __('Share this car') }}
                                            </label>
                                        </div>
                                    </div>


                                </div>

                                <div id="accordion" class="mt-3">
                                    @foreach ($languages as $language)
                                        @php
                                            $peopertyContent = $propertyContents
                                                ->where('language_id', $language->id)
                                                ->first();

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
                                                        <div class="col-lg-12">
                                                            <div
                                                                class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                                                <label>{{ __('Ad Title') }} <span
                                                                        class="text-danger">{{ '*' }}</span>
                                                                </label>
                                                                <input type="text" class="form-control"
                                                                    name="{{ $language->code }}_title"
                                                                    id="{{ $language->code }}_title"
                                                                    placeholder="{{ __('Enter Title') }}"
                                                                    value="{{ $peopertyContent ? $peopertyContent->title : '' }}">
                                                            </div>
                                                        </div>



                                                        <div class="col-lg-12">
                                                            <div
                                                                class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                                                <label>{{ __('Address') }} <span
                                                                        class="text-danger">{{ '*' }}</span>
                                                                </label>
                                                                <input type="text"
                                                                    name="{{ $language->code }}_address"
                                                                    placeholder="{{ __('Enter Address') }}"
                                                                    value="{{ @$peopertyContent->address }}"
                                                                    class="form-control {{ $language->is_default == 1 ? 'search-address' : '' }} ">

                                                            </div>
                                                        </div>

                                                        @if ($userBs->google_map_api_status == 1 && $language->is_default == 1)
                                                            <div class="col-lg-12">
                                                                <div class="form-group">
                                                                    <div id="map"
                                                                        style="height:400px; background:#f0f0f0; position:relative; overflow:hidden;">
                                                                        <!-- Loading Placeholder -->
                                                                        <div id="map-loading"
                                                                            style="position:absolute; top:0; left:0; width:100%; height:100%; display:flex; flex-direction:column; justify-content:center; align-items:center; background:#f8f9fa; z-index:10;">
                                                                            <div
                                                                                style="border:4px solid #f3f3f3; border-top:4px solid #3498db; border-radius:50%; width:40px; height:40px; animation:spin 1s linear infinite;">
                                                                            </div>
                                                                            <p style="margin-top:15px; color:#666;">
                                                                                {{ __('Loading Map') }}...</p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif

                                                        @if ($language->is_default == 1)
                                                            <div class="col-lg-6">
                                                                <div class="form-group">
                                                                    <label>{{ __('Latitude') }} <span
                                                                            class="text-danger">{{ '*' }}</span>
                                                                    </label>
                                                                    <input type="text" class="form-control latitude"
                                                                        name="latitude"
                                                                        value="{{ $property->latitude }}"
                                                                        placeholder="{{ __('Enter Latitude') }}">
                                                                    <p> <span class="text-warning">
                                                                            {{ __('The Latitude must be between -90 to 90') }}
                                                                        </span> <br>
                                                                        <a href="https://tinyurl.com/2hjtmtvc "> <i
                                                                                class="fas fa-eye"> </i>
                                                                            {{ __('See Example') }}</a>
                                                                    </p>
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-6">
                                                                <div class="form-group">
                                                                    <label>{{ __('Longitude') }} <span
                                                                            class="text-danger">{{ '*' }}</span>
                                                                    </label>
                                                                    <input type="text" class="form-control longitude"
                                                                        name="longitude"
                                                                        value="{{ $property->longitude }}"
                                                                        placeholder="{{ __('Enter Longitude') }}">
                                                                    <p>
                                                                        <span class="text-warning">
                                                                            {{ __('The Longitude must be between -180 to 180') }}
                                                                        </span> <br>
                                                                        <a href="https://tinyurl.com/2hjtmtvc "> <i
                                                                                class="fas fa-eye"> </i>
                                                                            {{ __('See Example') }} </a>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        @endif

                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div
                                                                class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                                                <label>{{ __('Summary') . ' (' . __('Optional') . ')'}}
                                                                    <span class="text-danger">{{ '*' }}</span>
                                                                </label>
                                                                <textarea id="{{ $language->code }}_summary" class="form-control" rows="6"
                                                                    placeholder="{{ __('Enter Summary') }}" name="{{ $language->code }}_summary" data-height="300">{{ @$peopertyContent->summary }}</textarea>
                                                            </div>
                                                        </div>
                                                    </div>


                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div
                                                                class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                                                <div
                                                                    class="d-flex justify-content-between align-items-center mb-2">
                                                                    <label class="mb-0">
                                                                        {{ __('Description') }}
                                                                        <span class="text-danger">*</span>
                                                                    </label>

                                                                    <!-- Generate Button -->
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-outline-primary d-flex align-items-center generateDescriptionBtn"
                                                                        data-lang="{{ $language->name }}"
                                                                        data-langcode="{{ $language->code }}">
                                                                        <i class="fas fa-magic mr-1"></i>
                                                                        {{ __('Generate Description') }}
                                                                    </button>

                                                                </div>

                                                                <textarea id="{{ $language->code }}_description" class="form-control summernote"
                                                                    placeholder="{{ __('Enter Description') }}" name="{{ $language->code }}_description" data-height="300">{{ @$peopertyContent->description }}</textarea>
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
                                                                    placeholder="{{ __('Enter Meta Keywords') }}"
                                                                    data-role="tagsinput"
                                                                    value="{{ $peopertyContent ? $peopertyContent->meta_keyword : '' }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div
                                                                class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                                                <label>{{ __('Meta Description') }} </label>
                                                                <textarea class="form-control" name="{{ $language->code }}_meta_description" rows="5"
                                                                    placeholder=" {{ __('Enter Meta Description') }}">{{ $peopertyContent ? $peopertyContent->meta_description : '' }}</textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col">
                                                            @php $currLang = $language; @endphp

                                                            @foreach ($languages as $language)
                                                                @continue($language->id == $currLang->id)

                                                                <div class="form-check py-0">
                                                                    <label class="form-check-label">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            onchange="cloneInput('collapse{{ $currLang->id }}', 'collapse{{ $language->id }}', event)">
                                                                        <span
                                                                            class="form-check-sign">{{ __('Clone for') }}
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
                                        <table class="table table-bordered ">
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
                                                                @foreach ($languages as $language)
                                                                    @php
                                                                        $sp_content = $specification->getContent(
                                                                            $language->id,
                                                                        );
                                                                    @endphp
                                                                    <div
                                                                        class="form-group  {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                                                        <input type="text"
                                                                            name="{{ $language->code }}_label[]"
                                                                            value="{{ $sp_content?->label }}"
                                                                            class="form-control"
                                                                            plplaceholder="{{ __('Label for') . ' ' . $language->name . ' ' . __('language') }}">
                                                                    </div>
                                                                @endforeach
                                                            </td>
                                                            <td>
                                                                @foreach ($languages as $language)
                                                                    @php
                                                                        $sp_content = $specification->getContent(
                                                                            $language->id,
                                                                        );
                                                                    @endphp
                                                                    <div
                                                                        class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                                                        <input type="text"
                                                                            name="{{ $language->code }}_value[]"
                                                                            value="{{ $sp_content?->value }}"
                                                                            class="form-control {{ $language->code }}_features"
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
                                                            @foreach ($languages as $language)
                                                                <div
                                                                    class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                                                    <input type="text"
                                                                        name="{{ $language->code }}_label[]"
                                                                        class="form-control {{ $language->code }}_features"
                                                                        placeholder="{{ __('Label for') . ' ' . $language->name . ' ' . __('language') }}">
                                                                </div>
                                                            @endforeach
                                                        </td>
                                                        <td>
                                                            @foreach ($languages as $language)
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

                                <div id="sliders"></div>
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

    @include('map.generate-prompt-modal')
    @include('map.generate-content-modal')
@endsection

@php

    $labels = '';
    $values = '';
    foreach ($languages as $language) {
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

@section('scripts')
    <script>
        'use strict';
        var labels = "{!! $labels !!}";
        var values = "{!! $values !!}";
        var stateUrl = "{{ route('user.property_management.get_state_cities') }}";
        var cityUrl = "{{ route('user.property_management.get_cities') }}";
        var storeUrl = "{{ route('user.property.imagesupdate') }}";
        var removeUrl = "{{ route('user.property.imagermv') }}";
        var rmvdbUrl = "{{ route('user.property.imgdbrmv') }}";
        var specificationRmvUrl = "{{ route('user.property_management.specification_delete') }}";
        let galleryImages = "{{ $userCurrentPackage->number_of_property_gallery_images }}";
        let flrimgrmv = "{{ route('user.property_management.floorImgrmv') }}";
        let vdoimgrmv = "{{ route('user.property_management.videoImgrmv') }}";
        var selectAmenLocal = "{{ $keywords['Select Amenities'] ?? __('Select Amenities') }}";
        var genPromptUrl = "{{ route('generate.prompt') }}"
        var genContentUrl = "{{ route('generate.content') }}"
    </script>
    @if ($userBs->google_map_api_status == 1)
        <script>
            var defaultLang = "{{ $currentLang->code }}";
            var address = "{{ @$propertyAddress->address }}"
        </script>
        <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
        <script src="https://maps.googleapis.com/maps/api/js?key={{ $userBs->google_map_api_key }}&libraries=places" async
            defer></script>
        <script src="{{ asset('assets/tenant/js/map/edit-map-init.js') }}"></script>
    @endif
    <script type="text/javascript" src="{{ asset('assets/tenant/js/ai-generate.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/tenant/js/admin-partial.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/tenant/js/admin-dropzone.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/tenant/js/property.js') }}"></script>
@endsection
