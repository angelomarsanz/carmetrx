@extends('tenant_frontend.layout')

@section('pageHeading')
    {{ __('Home') }}
@endsection

@section('metaKeywords')
    @if (!empty($seoInfo))
        {{ $seoInfo->meta_keyword_home }}
    @endif
@endsection

@section('metaDescription')
    @if (!empty($seoInfo))
        {{ $seoInfo->meta_description_home }}
    @endif
@endsection


@section('content')

    <section class="home-banner home-banner-2">
        <div class="container">
            <div class="swiper home-slider" id="home-slider-1">
                <div class="swiper-wrapper">
                    @forelse ($sliderInfos as $slider)
                        <div class="swiper-slide">
                            <div class="content">
                                <span class="subtitle color-white">{{ $slider->title }}</span>
                                <h1 class="title color-white mb-0">{{ $slider->text }}</h1>
                            </div>
                        </div>
                    @empty
                        <div class="swiper-slide">
                            <div class="content">
                                <span class="subtitle color-white">{{ __('Your Trusted Real Estate Partner') }}</span>
                                <h1 class="title color-white mb-0">{{ __('Buy Land They’re Not Making It Anymore') }}</h1>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
            <div class="banner-filter-form mt-40" data-aos="fade-up">
                <div class="row justify-content-center">
                    <div class="col-xxl-10">
                        <div class="tabs-navigation">
                            <ul class="nav nav-tabs">
                                <li class="nav-item">
                                    <button class="nav-link btn-md rounded-pill active" data-bs-toggle="tab"
                                        data-bs-target="#rent" type="button">{{ $keywords['Rent'] ?? __('Rent') }}</button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link btn-md rounded-pill" data-bs-toggle="tab" data-bs-target="#sale"
                                        type="button">{{ $keywords['Sale'] ?? __('Sale') }}</button>
                                </li>

                            </ul>
                        </div>
                        <div class="tab-content form-wrapper radius-md">
                            <input type="hidden" id="currency_symbol" value="{{ $basicInfo->base_currency_symbol }}">
                            <input type="hidden" name="min" value="{{ $min }}" id="min">
                            <input type="hidden" name="max" value="{{ $max }}" id="max">
                            <input class="form-control" type="hidden" value="{{ $min }}" id="o_min">
                            <input class="form-control" type="hidden" value="{{ $max }}" id="o_max">
                            <div class="tab-pane fade show active" id="rent">
                                <form action="{{ route('frontend.properties', getParam()) }}" method="get">
                                    <input type="hidden" name="purpose" value="rent">
                                    <input type="hidden" name="min" value="{{ $min }}" id="min1">
                                    <input type="hidden" name="max" value="{{ $max }}" id="max1">
                                    <div class="grid">
                                        <div class="grid-item">
                                            <div class="form-group">
                                                <label for="search1">{{ $keywords['Location'] ?? __('Location') }}</label>
                                                <input type="text" id="search1" name="location" class="form-control"
                                                    placeholder="{{ $keywords['Enter Location'] ?? __('Enter Location') }}">
                                            </div>
                                        </div>
                                        <div class="grid-item">
                                            <div class="form-group">
                                                <label for="type"
                                                    class="icon-end">{{ $keywords['Property Type'] ?? __('Property Type') }}</label>
                                                <select aria-label="#" name="type" class="form-control select2 type"
                                                    id="type">
                                                    <option selected disabled value="">
                                                        {{ $keywords['Select Property'] ?? __('Select Property') }}
                                                    </option>
                                                    <option value="all">{{ $keywords['All'] ?? __('All') }}</option>
                                                    <option value="residential">
                                                        {{ $keywords['Residential'] ?? __('Residential') }}</option>
                                                    <option value="commercial">
                                                        {{ $keywords['Commercial'] ?? __('Commercial') }}</option>

                                                </select>
                                            </div>
                                        </div>
                                        <div class="grid-item">
                                            <div class="form-group">
                                                <label for="category"
                                                    class="icon-end">{{ $keywords['Categories'] ?? __('Categories') }}</label>
                                                <select aria-label="#" class="form-control select2 bringCategory"
                                                    id="category" name="category">
                                                    <option selected disabled value="">
                                                        {{ $keywords['Select Category'] ?? __('Select Category') }}
                                                    </option>
                                                    <option value="all">{{ $keywords['All'] ?? __('All') }}</option>
                                                    @foreach ($all_proeprty_categories as $category)
                                                        <option value="{{ @$category->categoryContent->slug }}">
                                                            {{ @$category->categoryContent->name }}
                                                        </option>
                                                    @endforeach

                                                </select>
                                            </div>
                                        </div>

                                        <div class="grid-item city">
                                            <div class="form-group">
                                                <label for="city"
                                                    class="icon-end">{{ $keywords['City'] ?? __('City') }}</label>
                                                <select aria-label="#" name="state"
                                                    class="form-control select2 city_id" id="city">
                                                    <option selected disabled value="">
                                                        {{ $keywords['Select City'] ?? __('Select City') }}
                                                    </option>
                                                    <option value="all">{{ $keywords['All'] ?? __('All') }}</option>
                                                 @foreach ($all_states as $state)
                                                        <option data-id="{{ $state->id }}"
                                                            value="{{ @$state->stateContent->slug }}">
                                                            {{ @$state->stateContent->name }}</option>
                                                    @endforeach

                                                </select>
                                            </div>
                                        </div>
                                        <div class="grid-item">
                                            <label class="price-value">{{ ($keywords['Price'] ?? __('Price')) . ':' }}
                                                <br>
                                                <span data-range-value="filterPriceSlider2Value">{{ $min }}
                                                    -
                                                    {{ $max }}</span>
                                            </label>
                                            <div data-range-slider="filterPriceSlider2"></div>
                                        </div>
                                        <div class="grid-item">
                                            <button type="submit"
                                                class="btn btn-lg btn-primary bg-primary icon-start w-100">
                                                {{ $keywords['Search'] ?? __('Search') }}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="tab-pane fade" id="sale">
                                <form action="{{ route('frontend.properties', getParam()) }}" method="get">
                                    <input type="hidden" name="purpose" value="sale">
                                    <input type="hidden" name="min" value="{{ $min }}" id="min2">
                                    <input type="hidden" name="max" value="{{ $max }}" id="max2">
                                    <div class="grid">
                                        <div class="grid-item">
                                            <div class="form-group">
                                                <label
                                                    for="search1">{{ $keywords['Location'] ?? __('Location') }}</label>
                                                <input type="text" id="search1" name="location"
                                                    class="form-control"
                                                    placeholder="{{ $keywords['Enter Location'] ?? __('Enter Location') }}">
                                            </div>
                                        </div>
                                        <div class="grid-item">
                                            <div class="form-group">
                                                <label for="type1"
                                                    class="icon-end">{{ $keywords['Property Type'] ?? __('Property Type') }}</label>
                                                <select aria-label="#" name="type" class="form-control select2 type"
                                                    id="type1">
                                                    <option selected disabled value="">{{ __('Select Property') }}
                                                    </option>
                                                    <option value="all">{{ $keywords['All'] ?? __('All') }}</option>
                                                    <option value="residential">
                                                        {{ $keywords['Residential'] ?? __('Residential') }}</option>
                                                    <option value="commercial">
                                                        {{ $keywords['Commercial'] ?? __('Commercial') }}</option>

                                                </select>
                                            </div>
                                        </div>
                                        <div class="grid-item">
                                            <div class="form-group">
                                                <label for="category1"
                                                    class="icon-end">{{ $keywords['Categories'] ?? __('Categories') }}</label>
                                                <select aria-label="#" class="form-control select2 bringCategory"
                                                    id="category1" name="category">
                                                    <option selected disabled value="">
                                                        {{ $keywords['Select Category'] ?? __('Select Category') }}
                                                    </option>
                                                    <option value="all">{{ $keywords['All'] ?? __('All') }}</option>
                                                    @foreach ($all_proeprty_categories as $category)
                                                        <option value="{{ @$category->categoryContent->slug }}">
                                                            {{ @$category->categoryContent->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="grid-item city">
                                            <div class="form-group">
                                                <label for="city1"
                                                    class="icon-end">{{ $keywords['City'] ?? __('City') }}</label>
                                                <select aria-label="#" name="state"
                                                    class="form-control select2 city_id" id="city1">
                                                    <option selected disabled value="">
                                                        {{ $keywords['Select City'] ?? __('Select City') }}
                                                    </option>
                                                    <option value="all">{{ $keywords['All'] ?? __('All') }}</option>

                                                    @foreach ($all_states as $state)
                                                        <option data-id="{{ $state->id }}"
                                                            value="{{ @$state->stateContent->slug }}">
                                                            {{ @$state->stateContent->name }}</option>
                                                    @endforeach

                                                </select>
                                            </div>
                                        </div>
                                        <div class="grid-item">
                                            <label class="price-value">{{ ($keywords['Price'] ?? __('Price')) . ':' }}
                                                <br>
                                                <span data-range-value="filterPriceSlider2Value">{{ $min }}
                                                    -
                                                    {{ $max }}</span>
                                            </label>
                                            <div data-range-slider="filterPriceSlider2"></div>
                                        </div>
                                        <div class="grid-item">
                                            <button type="submit"
                                                class="btn btn-lg btn-primary bg-primary icon-start w-100">
                                                {{ $keywords['Search'] ?? __('Search') }}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="swiper-pagination pagination-fraction mt-40" id="home-slider-1-pagination"></div>
        </div>

        <div class="swiper home-img-slider" id="home-img-slider-1">
            <div class="swiper-wrapper">
                @forelse  ($sliderInfos as $slider)
                    <div class="swiper-slide">
                        <img class="lazyload bg-img"
                            src="{{ asset(\App\Constants\Constant::WEBSITE_SLIDER_IMAGE . '/' . $slider->image) }}">
                    </div>
                @empty
                    <div class="swiper-slide">
                        <img class="lazyload bg-img"
                            src="{{ asset('assets/tenant-front/images/default/slider-one.jpg') }}">
                    </div>
                @endforelse
            </div>
        </div>
    </section>
    @if (count($after_hero) > 0)
        @foreach ($after_hero as $customAbout)
            @if (isset($homecusSec[$customAbout->id]))
                @if ($homecusSec[$customAbout->id] == 1)
                    <x-tenant.frontend.sections.additional :sectionId="$customAbout->id" />
                @endif
            @endif
        @endforeach
    @endif

    @if ($secInfo->category_section_status == 1)
        <x-tenant.frontend.sections.categories :$catgorySecInfo :propertyCategories="$property_categories" />
    @endif
    @if (count($after_category) > 0)
        @foreach ($after_category as $customAbout)
            @if (isset($homecusSec[$customAbout->id]))
                @if ($homecusSec[$customAbout->id] == 1)
                    <x-tenant.frontend.sections.additional :sectionId="$customAbout->id" />
                @endif
            @endif
        @endforeach
    @endif

    @if ($secInfo->featured_properties_section_status == 1)
        <section class="featured-product pt-100 pb-70">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="section-title title-inline mb-40" data-aos="fade-up">
                            <h2 class="title">{{ $featuredSecInfo?->featured_property_section_title }}</h2>
                            <!-- Slider navigation buttons -->
                            <div class="slider-navigation">
                                <button type="button" title="Slide prev"
                                    class="slider-btn product-slider-btn-prev rounded-pill">
                                    <i class="fal fa-angle-left"></i>
                                </button>
                                <button type="button" title="Slide next"
                                    class="slider-btn product-slider-btn-next rounded-pill">
                                    <i class="fal fa-angle-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-12" data-aos="fade-up">
                        <div class="swiper product-slider">
                            <div class="swiper-wrapper">
                                @forelse ($featured_properties as $property)
                                    {{-- property component --}}
                                    <div class="swiper-slide">
                                        <x-tenant.frontend.property :adminLang="$adminLang" :property="$property" />
                                    </div>
                                @empty
                                    <div class=" p-3 text-center mb-30 w-100">
                                        <h3 class="mb-0">
                                            {{ $keywords['No Featured Property Found'] ?? __('No Featured Property Found') }}
                                        </h3>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    @if (count($after_featured_properties) > 0)
        @foreach ($after_featured_properties as $customAbout)
            @if (isset($homecusSec[$customAbout->id]))
                @if ($homecusSec[$customAbout->id] == 1)
                    <x-tenant.frontend.sections.additional :sectionId="$customAbout->id" />
                @endif
            @endif
        @endforeach
    @endif
    @if (!empty(showAd(3)))
        <div class="text-center mb-40">
            {!! showAd(3) !!}
        </div>
    @endif
    @if ($secInfo->video_section_status == 1)
        <section class="video-banner with-radius pt-100 pb-70">
            <!-- Background Image -->
            <div class="bg-overlay">
                <img class="lazyload bg-img"
                    src="{{ asset(\App\Constants\Constant::WEBSITE_VIDEO_SECTION_IMAGE . '/' . $vodeoSecImg) }}">
            </div>
            <div class="container">
                @if (!empty($videoSecInfo?->title) || !empty($videoSecInfo?->subtitle) || !empty($videoSecInfo?->text))
                    <div class="row align-items-center">
                        <div class="col-lg-5">
                            <div class="content mb-30" data-aos="fade-up">
                                <span class="subtitle text-white">{{ $videoSecInfo?->title }}</span>
                                <h2 class="title text-white mb-10">{{ $videoSecInfo?->subtitle }}</h2>
                                <p class="text-white m-0 w-75 w-sm-100">{{ $videoSecInfo?->text }}</p>
                            </div>
                        </div>
                        <div class="col-lg-7">
                            @if (!empty($videoSecInfo?->url))
                                <div class="d-flex align-items-center justify-content-center h-100 mb-30"
                                    data-aos="fade-up">
                                    <a href="{{ $videoSecInfo->url }}" class="video-btn youtube-popup">
                                        <i class="fas fa-play"></i>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="col-lg-12">
                        <h3 class="text-center">
                            {{ $keywords['No Video Information Found'] ?? __('No Video Information Found') }}</h3>
                    </div>
                @endif
            </div>
        </section>
    @endif

    @if (count($after_video) > 0)
        @foreach ($after_video as $customAbout)
            @if (isset($homecusSec[$customAbout->id]))
                @if ($homecusSec[$customAbout->id] == 1)
                    <x-tenant.frontend.sections.additional :sectionId="$customAbout->id" />
                @endif
            @endif
        @endforeach
    @endif

    @if ($secInfo->property_section_status == 1)
        <section class="popular-product pt-100 pb-70">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="section-title title-inline mb-40" data-aos="fade-up">
                            <h2 class="title">{{ $propertySecInfo?->property_section_title }}</h2>
                            @if ($properties && $properties->isNotEmpty())
                                <div class="tabs-navigation">
                                    <ul class="nav nav-tabs">
                                        <li class="nav-item">
                                            <button class="nav-link active btn-md rounded-pill" data-bs-toggle="tab"
                                                data-bs-target="#forAll"
                                                type="button">{{ $keywords['All Properties'] ?? __('All Properties') }}</button>
                                        </li>
                                        <li class="nav-item">
                                            <button class="nav-link btn-md rounded-pill" data-bs-toggle="tab"
                                                data-bs-target="#forRent"
                                                type="button">{{ $keywords['For Rent'] ?? __('For Rent') }}</button>
                                        </li>
                                        <li class="nav-item">
                                            <button class="nav-link btn-md rounded-pill" data-bs-toggle="tab"
                                                data-bs-target="#forSell"
                                                type="button">{{ $keywords['For Sale'] ?? __('For Sale') }}</button>
                                        </li>
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="tab-content" data-aos="fade-up">
                            <div class="tab-pane fade show active" id="forAll">
                                <div class="row">
                                    @forelse ($properties as $property)
                                        {{-- property component --}}
                                        <x-tenant.frontend.property :adminLang="$adminLang" :property="$property"
                                            class="col-xxl-3 col-lg-4 col-sm-6" />
                                    @empty
                                        <div class="p-3 text-center mb-30">
                                            <h3 class="mb-0">
                                                {{ $keywords['No Property Found'] ?? __('No Property Found') }}</h3>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                            <div class="tab-pane fade" id="forRent">
                                <div class="row">
                                    @forelse ($properties as $property)
                                        @if ($property->purpose == 'rent')
                                            {{-- property component --}}
                                            <x-tenant.frontend.property :adminLang="$adminLang" :property="$property"
                                                class="col-xxl-3 col-lg-4 col-sm-6" />
                                        @endif
                                    @empty
                                        <div class=" p-3 text-center mb-30">
                                            <h3 class="mb-0">
                                                {{ $keywords['No Properties Found'] ?? __('No Properties Found') }}</h3>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                            <div class="tab-pane fade" id="forSell">
                                <div class="row">
                                    @forelse ($properties as $property)
                                        @if ($property->purpose == 'sale')
                                            {{-- property component --}}
                                            <x-tenant.frontend.property :adminLang="$adminLang" :property="$property"
                                                class="col-xxl-3 col-lg-4 col-sm-6" />
                                        @endif
                                    @empty
                                        <div class=" p-3 text-center mb-30">
                                            <h3 class="mb-0">
                                                {{ $keywords['No Property Found'] ?? __('No Property Found') }}</h3>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    @if (count($after_property) > 0)
        @foreach ($after_property as $customAbout)
            @if (isset($homecusSec[$customAbout->id]))
                @if ($homecusSec[$customAbout->id] == 1)
                    <x-tenant.frontend.sections.additional :sectionId="$customAbout->id" />
                @endif
            @endif
        @endforeach
    @endif

    @if ($secInfo->work_steps_section_status == 1)
        <x-tenant.frontend.sections.work-steps :$workStepsSecInfo :$steps :$workStepsSecImg />
    @endif

    @if (count($after_work_steps) > 0)
        @foreach ($after_work_steps as $customAbout)
            @if (isset($homecusSec[$customAbout->id]))
                @if ($homecusSec[$customAbout->id] == 1)
                    <x-tenant.frontend.sections.additional :sectionId="$customAbout->id" />
                @endif
            @endif
        @endforeach
    @endif


    @if (!empty(showAd(3)))
        <div class="text-center mt-40 mb-40">
            {!! showAd(3) !!}
        </div>
    @endif
    @if ($secInfo->testimonial_section_status == 1)
        <x-tenant.frontend.sections.testimonial :$testimonialSecInfo :$testimonials :$testimonialSecImage />
    @endif
    @if (count($after_testimonial) > 0)
        @foreach ($after_testimonial as $customAbout)
            @if (isset($homecusSec[$customAbout->id]))
                @if ($homecusSec[$customAbout->id] == 1)
                    <x-tenant.frontend.sections.additional :sectionId="$customAbout->id" />
                @endif
            @endif
        @endforeach
    @endif

    @if ($secInfo->partner_section_status == 1)
        <x-tenant.frontend.sections.partners :$partners />
    @endif

    @if (count($after_partner) > 0)
        @foreach ($after_partner as $customAbout)
            @if (isset($homecusSec[$customAbout->id]))
                @if ($homecusSec[$customAbout->id] == 1)
                    <x-tenant.frontend.sections.additional :sectionId="$customAbout->id" />
                @endif
            @endif
        @endforeach
    @endif
    @if (!empty(showAd(3)))
        <div class="text-center mb-40">
            {!! showAd(3) !!}
        </div>
    @endif
@endsection
