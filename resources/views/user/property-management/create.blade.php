@extends('user.layout')

@section('content')
    <div class="page-header">
        <h4 class="page-title">{{ __('Add Car') }}</h4>
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
                <a href="#">{{ __('Add Car') }}</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title d-inline-block">{{ __('Add Car') }}</div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-10 offset-lg-1">
                            <div class="alert alert-danger pb-1 dis-none" id="propertyErrors">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                <ul></ul>
                            </div>
                            <div class="col-lg-12">
                                <label for="" class="mb-2"><strong> {{ __('Gallery Images') }}
                                    </strong> <span class="text-danger">{{ '*' }}</span> </label>
                                <form action="{{ route('user.property.imagesstore') }}" id="my-dropzone"
                                    enctype="multipart/form-data" class="dropzone create">
                                    @csrf
                                    <div class="fallback">
                                        <input name="file" type="file" multiple />
                                    </div>
                                </form>
                                <p class="em text-danger mb-0" id="errslider_images"></p>
                            </div>
                            <form id="propertyForm" action="{{ route('user.property_management.store_property') }}"
                                method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="type" value="{{ request()->type }}">
                                <div id="sliders"></div>
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="">{{ __('Thumbnail Image') }} <span
                                                    class="text-danger">{{ '*' }}</span></label>
                                            <br>
                                            <div class="thumb-preview ">
                                                <img src="{{ asset('assets/img/noimage.jpg') }}" alt="..."
                                                    class="uploaded-img">

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
                                            <label for="">{{ __('Floor Planning Image') }}</label>
                                            <br>
                                            <div class="thumb-preview remove">
                                                <img src="{{ asset('assets/img/noimage.jpg') }}" alt="..."
                                                    class="uploaded-img2">

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
                                            <label for=""> {{ __('Video Image') }}
                                            </label>
                                            <br>
                                            <div class="thumb-preview remove">
                                                <img src="{{ asset('assets/img/noimage.jpg') }}" alt="..."
                                                    class="uploaded-img3">

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
                                                placeholder="{{ __('Enter video url') }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>{{ __('Condition') }}<span
                                                    class="text-danger">{{ '*' }}</span></label>

                                            <select name="purpose" class="form-control">
                                                <option selected disabled value="">
                                                    {{ __('Select Condition') }}
                                                </option>
                                                @if ($bs->is_rent_active == 1)
                                                    <option value="rent">{{ __('Rent') }}</option>
                                                @endif
                                                @if ($bs->is_sale_active == 1)
                                                    <option value="sale">{{ __('Sale') }}</option>
                                                @endif
                                            </select>
                                        </div>

                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group ">
                                            <label>{{ __('Category') }} <span
                                                    class="text-danger">{{ '*' }}</span> </label>
                                            <select name="category_id" class="form-control category">
                                                <option disabled selected>
                                                    {{ __('Select Category') }}
                                                </option>
                                                @foreach ($propertyCategories as $category)
                                                    @php
                                                        $catContent = $category->categoryContents()->first();
                                                    @endphp
                                                    <option value="{{ $category->id }}">
                                                        {{ @$catContent->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    @if ($adminBs->property_country_status == 1)
                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label>{{ __('Province') }} <span
                                                        class="text-danger">{{ '*' }}</span></label>
                                                <select name="country_id"
                                                    class="form-control country js-example-basic-single3">
                                                    <option disabled selected>
                                                        {{ __('Select Province') }}
                                                    </option>

                                                    @foreach ($propertyCountries as $country)
                                                        @php
                                                            $countryContent = $country->contents()->first();
                                                        @endphp
                                                        <option value="{{ $country->id }}">
                                                            {{ @$countryContent->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    @endif
                                    @if ($adminBs->property_state_status == 1)
                                        <div class="col-lg-3 state">
                                            <div class="form-group  ">
                                                <label>{{ __('City') }} <span
                                                        class="text-danger">{{ '*' }}</span></label>
                                                <select onchange="getCities(event)" name="state_id"
                                                    class="form-control state_id states js-example-basic-single3">
                                                    <option selected disabled>
                                                        {{ __('Select City') }}
                                                    </option>
                                                    @foreach ($states as $state)
                                                        @php
                                                            $stateContent = $state->contents()->first();
                                                        @endphp
                                                        <option value="{{ $state->id }}">
                                                            {{ @$stateContent->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="col-lg-3 city">
                                        <div class="form-group ">
                                            <label>{{ __('Area') }} </label>
                                            <select name="city_id" class="form-control city_id js-example-basic-single3">
                                                <option selected disabled>
                                                    {{ __('Select Area') }}
                                                </option>
                                                @if ($adminBs->property_state_status == 0 && $adminBs->property_country_status == 0)
                                                    @foreach ($cities as $city)
                                                        @php
                                                            $cityContent = $city->contents()->first();
                                                        @endphp
                                                        <option value="{{ $city->id }}">
                                                            {{ @$cityContent->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="">{{ __('Amenities') }} <span
                                                    class="text-danger">{{ '*' }}</span></label>
                                            <select name="amenities[]"
                                                class="form-control amenities-values js-example-basic-single2"
                                                multiple="multiple">
                                                @foreach ($amenities as $amenity)
                                                    @php
                                                        $amContent = $amenity->contents()->first();
                                                    @endphp
                                                    <option value="{{ $amenity->id }}"
                                                        data-value="{{ $amContent?->name }}">
                                                        {{ @$amContent->name }}</option>
                                                @endforeach
                                            </select>

                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>{{ __('Price') }}
                                            </label>
                                            <input type="number" class="form-control" name="price"
                                                placeholder="{{ __('Enter Current Price') }}">

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
                                                <option value="UY$">
                                                    UY$
                                                </option>
                                                <option value="U$D">
                                                    U$D
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    @if (request('type') == 'residential')
                                            <div class="form-group">
                                                <label>{{ __('Beds') }} <span
                                                        class="text-danger">{{ '*' }}</span></label>
                                                <input type="text" class="form-control" name="beds"
                                                    placeholder="{{ __('Enter number of bed') }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label>{{ __('Baths') }} <span
                                                        class="text-danger">{{ '*' }}</span></label>
                                                <input type="text" class="form-control" name="bath"
                                                    placeholder="{{ __('Enter number of bath') }}">
                                            </div>
                                        </div>
                                    @endif
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>{{ __('Fuel type') }} <span
                                                    class="text-danger">{{ '*' }}</span></label>
                                            <select name="area" class="form-control">
                                                <option value="" selected disabled>{{ __('Select Fuel type') }}</option>
                                                <option value="Nafta">Nafta</option>
                                                <option value="Diesel">Diésel</option>
                                                <option value="Electrico">Eléctrico</option>
                                                <option value="Hibrido">Híbrido</option>
                                                <option value="Hibrido/Diesel">Híbrido/Diesel</option>
                                                <option value="Nafta/GNC">Nafta/GNC</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-3">                                        <div class="form-group">
                                            <label>{{ __('Brand') }} <span class="text-danger">{{ '*' }}</span></label>
                                            <select name="brand_id" class="form-control brand js-example-basic-single3" onchange="getModels(event)">
                                                <option disabled selected>{{ __('Select Brand') }}</option>
                                                @if(isset($brands))
                                                @foreach ($brands as $brand)
                                                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                                @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>

                                        <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>{{ __('Model') }} <span class="text-danger">{{ '*' }}</span></label>
                                            <select name="model_id" class="form-control model_id js-example-basic-single3" onchange="getVersions(event)">
                                                <option disabled selected>{{ __('Select Model') }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>{{ __('Version') }}</label>
                                            <select name="version_id" class="form-control version_id js-example-basic-single3">
                                                <option disabled selected>{{ __('Select Version') }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>{{ __('Mileage') }} <span class="text-danger">{{ '*' }}</span></label>
                                            <input type="number" name="km" class="form-control" placeholder="{{ __('Enter Mileage in km') }}" min="0">
                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>{{ __('Year') }} <span class="text-danger">{{ '*' }}</span></label>
                                            <input type="number" name="year" class="form-control" placeholder="{{ __('Enter Year') }}" min="1900" max="{{ now()->year }}">
                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>{{ __('Transmission') }} <span class="text-danger">{{ '*' }}</span></label>
                                            <select name="transmision" class="form-control">
                                                <option disabled selected>{{ __('Select Transmission') }}</option>
                                                <option value="Manual">{{ __('Manual') }}</option>
                                                <option value="Automática">{{ __('Automatic') }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>{{ __('Color') }} <span class="text-danger">{{ '*' }}</span></label>
                                            <select name="color" class="form-control">
                                                <option disabled selected>{{ __('Select Color') }}</option>
                                                <option value="Blanco">{{ __('White') }}</option>
                                                <option value="Negro">{{ __('Black') }}</option>
                                                <option value="Rojo">{{ __('Red') }}</option>
                                                <option value="Azul">{{ __('Blue') }}</option>
                                                <option value="Gris">{{ __('Gray') }}</option>
                                                <option value="Plata">{{ __('Silver') }}</option>
                                                <option value="Verde">{{ __('Green') }}</option>
                                                <option value="Marrón">{{ __('Brown') }}</option>
                                                <option value="Beige">{{ __('Beige') }}</option>
                                                <option value="Dorado">{{ __('Gold') }}</option>
                                                <option value="Naranja">{{ __('Orange') }}</option>
                                                <option value="Amarillo">{{ __('Yellow') }}</option>
                                                <option value="Rosado">{{ __('Pink') }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>{{ __('Traction') }}</label>
                                            <select name="traction" class="form-control">
                                                <option disabled selected>{{ __('Select Traction') }}</option>
                                                <option value="4x2">4x2</option>
                                                <option value="4x4">4x4</option>
                                                <option value="AWD">AWD</option>
                                                <option value="4wd">4wd</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>{{ __('Engine') }}</label>
                                            <input type="number" step="0.1" class="form-control" name="engine"
                                                placeholder="{{ __('Enter Engine Size') }}">
                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>{{ __('Cost') }}</label>
                                            <input type="number" step="0.01" class="form-control" name="cost"
                                                placeholder="{{ __('Enter Purchase Cost') }}">
                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>{{ __('Status') }} <span
                                                    class="text-danger">{{ '*' }}</span></label>
                                            <select name="status" id="" class="form-control">
                                                <option value="1">{{ __('Active') }}</option>
                                                <option value="0">{{ __('Deactive') }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    {{-- <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>{{ __('Latitude') }} <span
                                                    class="text-danger">{{ '*' }}</span> </label>
                                            <input type="text" class="form-control" name="latitude"
                                                placeholder="{{ __('Enter Latitude') }}">
                                            <p> <span class="text-warning">
                                                    {{ __('The Latitude must be between -90 to 90') }}
                                                </span> <br>
                                                <a href="https://tinyurl.com/2hjtmtvc "> <i class="fas fa-eye"> </i>
                                                    {{ __('See Example') }}</a>
                                            </p>
                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>{{ __('Longitude') }} <span
                                                    class="text-danger">{{ '*' }}</span> </label>
                                            <input type="text" class="form-control" name="longitude"
                                                placeholder="{{ __('Enter Longitude') }}">
                                            <p>
                                                <span class="text-warning">
                                                    {{ __('The Longitude must be between -180 to 180') }}
                                                </span> <br>
                                                <a href="https://tinyurl.com/2hjtmtvc "> <i class="fas fa-eye"> </i>
                                                    {{ __('See Example') }} </a>
                                            </p>
                                        </div>
                                    </div> --}}


                                    <div class="col-lg-3">
                                        <div class="form-group ">
                                            <label for="">{{ __('Agent') }}</label>
                                            <select name="agent_id"
                                                class="form-control agent_id js-example-basic-single1">
                                                <option value="" selected>
                                                    {{ __('Select Agent') }}
                                                </option>
                                                @foreach ($agents as $agent)
                                                    <option value="{{ $agent->id }}">{{ $agent->username }}</option>
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
                                                <input type="checkbox" name="share" id="shareCar" value="1">
                                                {{ __('Share this car') }}
                                            </label>
                                        </div>
                                    </div>


                                </div>

                                <div id="accordion" class="mt-3">
                                    @foreach ($languages as $language)
                                        <div class="version">
                                            <div class="version-header" id="heading{{ $language->id }}">
                                                <h5 class="mb-0">
                                                    <button type="button" class="btn btn-link" data-toggle="collapse"
                                                        data-target="#collapse{{ $language->id }}"
                                                        aria-expanded="{{ $language->is_default == 1 ? 'true' : 'false' }}"
                                                        aria-controls="collapse{{ $language->id }}">
                                                        {{ $language->name . ' ' . __('language') }}
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
                                                                        class="text-danger">{{ '*' }}</span></label>
                                                                <input type="text" class="form-control"
                                                                    id="{{ $language->code }}_title"
                                                                    name="{{ $language->code }}_title"
                                                                    placeholder="{{ __('Enter Title') }}">
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
                                                                    class="form-control {{ $language->is_default == 1 ? 'search-address' : '' }} "
                                                                    placeholder="{{ __('Enter Address') }}">
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
                                                                </label>
                                                                <textarea id="{{ $language->code }}_summary" class="form-control" rows="6"
                                                                    placeholder="{{ __('Enter Summary') }}" name="{{ $language->code }}_summary" data-height="300"></textarea>
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
                                                                    placeholder="{{ __('Enter Description') }}" name="{{ $language->code }}_description" data-height="300"></textarea>
                                                            </div>
                                                        </div>
                                                    </div>


                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div
                                                                class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                                                <label> {{ __('Meta Keywords') }} </label>
                                                                <input class="form-control"
                                                                    name="{{ $language->code }}_meta_keyword"
                                                                    placeholder="{{ __('Enter Meta Keywords') }}"
                                                                    data-role="tagsinput">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div
                                                                class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                                                <label> {{ __('Meta Description') }} </label>
                                                                <textarea class="form-control" name="{{ $language->code }}_meta_description" rows="5"
                                                                    placeholder=" {{ __('Enter Meta Description') }}"></textarea>
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
                                                    <th><a href="" class="btn btn-sm btn-success addRow"><i
                                                                class="fas fa-plus-circle"></i></a></th>
                                                </tr>
                                            <tbody id="tbody">
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
                                            </tbody>
                                            </thead>
                                        </table>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="row">
                        <div class="col-12 text-center">
                            <button type="submit" id="propertySubmit" class="btn btn-success">
                                {{ __('Save') }}
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
        $labels_placeholder = __('Label for') . ' ' . $language->name . ' ' . __('language');
        $values_placeholder = __('Value for') . ' ' . $language->name . ' ' . __('language');

        $label_name = $language->code . '_label[]';
        $value_name = $language->code . '_value[]';
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
        var storeUrl = "{{ route('user.property.imagesstore') }}";
        var removeUrl = "{{ route('user.property.imagermv') }}";
        var stateUrl = "{{ route('user.property_management.get_state_cities') }}";
        let cityUrl = "{{ route('user.property_management.get_cities') }}";
        let modelUrl = "{{ route('user.property_management.get_models') }}";
        let versionUrl = "{{ route('user.property_management.get_versions') }}";
        let galleryImages = "{{ $userCurrentPackage->number_of_property_gallery_images }}";
        var selectAmenLocal = "{{ __('Select Amenities') }}";
        var genPromptUrl = "{{ route('generate.prompt') }}"
        var genContentUrl = "{{ route('generate.content') }}"
    </script>

    @if ($userBs->google_map_api_status == 1)
        <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
        <script src="https://maps.googleapis.com/maps/api/js?key={{ $userBs->google_map_api_key }}&libraries=places" async defer></script>
        <script src="{{ asset('assets/tenant/js/map/map-init.js') }}"></script>
    @endif
    <script type="text/javascript" src="{{ asset('assets/tenant/js/ai-generate.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/tenant/js/admin-partial.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/tenant/js/admin-dropzone.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/tenant/js/property.js') }}"></script>
@endsection
