@extends('tenant_frontend.layout')

@section('pageHeading')
    {{ !empty($pageHeading) ? $pageHeading->project_page_title : $keywords['Projects'] ?? __('Projects') }}
@endsection
@section('metaKeywords')
    @if (!empty($seoInfo))
        {{ $seoInfo->meta_keyword_projects }}
    @endif
@endsection

@section('metaDescription')
    @if (!empty($seoInfo))
        {{ $seoInfo->meta_description_projects }}
    @endif
@endsection

@section('content')

    <div class="map-area border-top header-next pt-30 d-none d-lg-block">
        <div class="container">
            <div class="lazy-container radius-md ratio border">
                <div id="project-map"></div>
            </div>
        </div>
    </div>

    <div class="listing-grid pt-40 pb-70">
        <div class="container">
            <div class="row gx-xl-5">

                <div class="col-xl-3">
                    <div class="widget-offcanvas offcanvas-xl offcanvas-start" tabindex="-1" id="widgetOffcanvas"
                        aria-labelledby="widgetOffcanvas">
                        <div class="offcanvas-header px-20">
                            <h4 class="offcanvas-title">{{ $keywords['Filter'] ?? __('Filter') }}</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"
                                data-bs-target="#widgetOffcanvas" aria-label="Close"></button>
                        </div>
                        <div class="offcanvas-body p-3 p-xl-0">

                            <aside class="sidebar-widget-area w-100" data-aos="fade-up">

                                <div class="widget widget-categories radius-md mb-30">
                                    <h3 class="title">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#categories" aria-expanded="true" aria-controls="categories">
                                            {{ $keywords['Categories'] ?? __('Categories') }}
                                        </button>
                                    </h3>
                                    <div id="categories" class="collapse show">
                                        <div class="accordion-body">
                                            <ul class="list-group">
                                                <li class="list-item">

                                                    <a class="{{ request()->filled('category') && request()->input('category') == 'all' ? 'active' : '' }}"
                                                        onclick="updateURL('category=all')">
                                                        {{ $keywords['All'] ?? __('All') }} </a>
                                                </li>
                                                <div id="catogoryul" class="toggle-list" data-toggle-list="amenitiesToggle"
                                                    data-toggle-show="5">
                                                    @foreach ($categories as $category)
                                                        @if ($category->categoryContent)
                                                            <li class="list-item">

                                                                <a class="{{ request()->filled('category') && request()->input('category') == $category->categoryContent?->slug ? 'active' : '' }}"
                                                                    onclick="updateURL('category={{ $category->categoryContent?->slug }}');">
                                                                    {{ $category->categoryContent?->name }}</a>
                                                            </li>
                                                        @endif
                                                    @endforeach
                                                </div>
                                                <span class="show-more"
                                                    data-toggle-btn="toggleListBtn">{{ __('Show More') }} + </span>

                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <form action="{{ route('frontend.properties', getParam()) }}" method="get"
                                    id="searchForm" class="w-100">


                                    <div class="widget widget-select radius-md mb-30">
                                        <h3 class="title">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#select" aria-expanded="true" aria-controls="select">
                                                {{ $keywords['Project Info'] ?? __('Project Info') }}
                                            </button>
                                        </h3>
                                        <div id="select" class="collapse show">
                                            <div class="accordion-body">
                                                <div class="form-group mb-20">
                                                    <label class="mb-10">{{ $keywords['Title'] ?? __('Title') }}</label>
                                                    <input type="text" class="form-control" name="title"
                                                        placeholder="{{ $keywords['Enter title'] ?? __('Enter title') }}"
                                                        onkeydown="if (event.keyCode == 13) updateURL('title='+$(this).val())">
                                                </div>
                                                @if ($basicInfo->project_country_status == 1)
                                                    <div class="form-group mb-20">
                                                        <label class="mb-10">{{ __('Country') }}</label>
                                                        <select name="country" id=""
                                                            class="form-control project_country form-select "
                                                            onchange="updateURL('country='+$(this).val())">
                                                            <option selected disabled>
                                                                {{ $keywords['Select Country'] ?? __('Select Country') }}
                                                            </option>
                                                            <option value="all" data-id="0">
                                                                {{ $keywords['All'] ?? __('All') }}
                                                            </option>
                                                            @foreach ($all_countries as $country)
                                                                <option data-id="{{ $country->id }}"
                                                                    value="{{ $country->countryContent?->name }}">
                                                                    {{ $country->countryContent?->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                @endif
                                                @if ($basicInfo->project_state_status == 1)
                                                    <div class="form-group mb-20 state">
                                                        <label
                                                            class="mb-10">{{ $keywords['State'] ?? __('State') }}</label>
                                                        <select name="state_id" id=""
                                                            class="form-control form-select  state_id states"
                                                            onchange="updateURL('state='+$(this).val());getCities(this)">
                                                            <option>{{ $keywords['Select State'] ?? __('Select State') }}
                                                            </option>
                                                            @if ($basicInfo->project_country_status != 1 && $basicInfo->project_state_status == 1)
                                                                @foreach ($all_states as $state)
                                                                    <option data-id="{{ $state->id }}"
                                                                        value="{{ $state->stateContent?->name }}">
                                                                        {{ $state->stateContent?->name }}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                @endif
                                                <div class="form-group mb-20 city">
                                                    <label class="mb-10">{{ $keywords['City'] ?? __('City') }}</label>
                                                    <select name="city_id" id=""
                                                        class="form-control form-select  city_id"
                                                        onchange="updateURL('city='+$(this).val())">
                                                        <option>{{ $keywords['Select City'] ?? __('Select City') }}
                                                        </option>
                                                        @if ($basicInfo->project_country_status != 1 && $basicInfo->project_state_status != 1)
                                                            @foreach ($all_cities as $city)
                                                                <option data-id="{{ $city->id }}"
                                                                    value="{{ $city->cityContent?->name }}">
                                                                    {{ $city->cityContent?->name }}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                                <div class="form-group mb-20">
                                                    <label
                                                        class="mb-10">{{ $keywords['Location'] ?? __('Location') }}</label>
                                                    <input type="text" class="form-control" name="location"
                                                        placeholder="{{ $keywords['Enter Location'] ?? __('Enter Location') }}"
                                                        onkeydown="if (event.keyCode == 13) updateURL('location='+$(this).val())">
                                                </div>

                                            </div>
                                        </div>
                                    </div>


                                    <div class="widget widget-price radius-md mb-30">
                                        <h3 class="title">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#price" aria-expanded="true" aria-controls="price">
                                                {{ $keywords['Pricing Filter'] ?? __('Pricing Filter') }}
                                            </button>
                                        </h3>
                                        <input class="form-control" type="hidden"
                                            value="{{ request()->filled('min') ? request()->input('min') : $min }}"
                                            name="min" id="min">
                                        <input class="form-control" type="hidden" value="{{ $min }}"
                                            id="o_min">
                                        <input class="form-control" type="hidden" value="{{ $max }}"
                                            id="o_max">
                                        <input class="form-control" type="hidden" value="{{ $min }}"
                                            id="min1">
                                        <input class="form-control" type="hidden" value="{{ $max }}"
                                            id="max1">

                                        <input class="form-control"
                                            value="{{ request()->filled('max') ? request()->input('max') : $max }}"
                                            type="hidden" name="max" id="max">
                                        <input type="hidden" id="currency_symbol"
                                            value="{{ $basicInfo->base_currency_symbol }}">
                                        <div id="price" class="collapse show">
                                            <div class="accordion-body">
                                                <div class="price-item">
                                                    <div data-range-slider='priceSlider'></div>
                                                    <div class="price-value">
                                                        <span
                                                            class="color-primary">{{ ($keywords['Price'] ?? __('Price')) . ':' }}
                                                            <span data-range-value="priceSliderValue">
                                                                {{ tenantCurrencySymbol($tenant->id, $min) }}
                                                                -
                                                                {{ tenantCurrencySymbol($tenant->id, $max) }}

                                                            </span></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="cta">

                                        <div class="row">
                                            <div class="col-sm-12">
                                                <button onclick="resetURL()" type="button"
                                                    class="btn-text color-primary icon-start mt-10"><i
                                                        class="fal fa-redo"></i>{{ $keywords['Reset Search'] ?? __('Reset Search') }}</button>
                                            </div>

                                        </div>
                                    </div>
                                </form>
                            </aside>
                        </div>
                    </div>
                </div>
                <div class="col-xl-9">
                    <div class="product-sort-area mt-4 mt-lg-0  mb-10" data-aos="fade-up">
                        <div class="row align-items-center">

                            <div class="col-6 d-lg-none">
                                <button type="button" class="btn btn-sm btn-primary radius-sm mb-20"
                                    data-bs-toggle="modal" data-bs-target="#mapModal">
                                    {{ __('View Map') }}
                                </button>
                            </div>
                            <div class="col-6 d-xl-none">
                                <div class="text-lg-start text-end ">
                                    <button class="btn btn-sm btn-outline icon-end radius-sm mb-15" type="button"
                                        data-bs-toggle="offcanvas" data-bs-target="#widgetOffcanvas"
                                        aria-controls="widgetOffcanvas">
                                        {{ $keywords['Filter'] ?? __('Filter') }} <i class="fal fa-filter"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-sm-12 col-lg-12">
                                <ul class="product-sort-list text-sm-end list-unstyled mb-15">
                                    <li class="item">
                                        <div class="sort-item d-flex align-items-center">
                                            <label
                                                class="color-dark me-2 font-sm flex-auto">{{ $keywords['Sort By'] ?? __('Sort By') }}
                                                :</label>
                                            <select class="form-select form_control" name="sort"
                                                onchange="updateURL('sort='+$(this).val())">
                                                <option
                                                    {{ request()->filled('sort') && request()->input('sort') == 'new' ? 'selected' : '' }}
                                                    value="new">{{ $keywords['Newest'] ?? __('Newest') }}</option>
                                                <option
                                                    {{ request()->filled('sort') && request()->input('sort') == 'old' ? 'selected' : '' }}
                                                    value="old">{{ $keywords['Oldest'] ?? __('Oldest') }}</option>
                                                <option
                                                    {{ request()->filled('sort') && request()->input('sort') == 'low-to-high' ? 'selected' : '' }}
                                                    value="low-to-high">
                                                    {{ $keywords['Price : Low to High'] ?? __('Price : Low to High') }}
                                                </option>
                                                <option
                                                    {{ request()->filled('sort') && request()->input('sort') == 'high-to-low' ? 'selected' : '' }}
                                                    value="high-to-low">
                                                    {{ $keywords['Price : High to Low'] ?? __('Price : High to Low') }}
                                                </option>
                                            </select>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="projects-area">
                        <div class="row">
                            @forelse ($projects as $project)
                                <x-tenant.frontend.project :project="$project" class="col-lg-4 col-sm-6" data-aos="fade-up"
                                    data-aos-delay="100" />

                            @empty
                                <div class="col-lg-12">
                                    <h3 class="text-center mt-5">
                                        {{ $keywords['No Project Found'] ?? __('No Project Found') }}
                                    </h3>
                                </div>
                            @endforelse

                        </div>

                        {{ $projects->links() }}

                        @if (!empty(showAd(3)))
                            <div class="text-center mt-4">
                                {!! showAd(3) !!}
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="mapModal" tabindex="-1" aria-labelledby="mapModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="mapModalLabel">{{ __('Map') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="modal-main-map" style="height: 600px; width: 100%;"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        'use strict';
        var jsonProjects = @json($projects);
        var projects = jsonProjects.data;
        var imgUrl = "{{ asset('/') }}";
        const categoryUrl = "{{ route('frontend.project.get_categories', getParam()) }}";
    </script>
    <!-- Leaflet Map JS -->
    <script src="{{ asset('/assets/tenant-front/js/vendors/leaflet.js') }}"></script>
    <script src="{{ asset('/assets/tenant-front/js/vendors/leaflet.markercluster.js') }}"></script>
    <!-- Map JS -->
    <script src="{{ asset('/assets/tenant-front/js/project-map.js') }}"></script>
    <script src="{{ asset('/assets/tenant-front/js/projects.js') }}"></script>
@endsection
