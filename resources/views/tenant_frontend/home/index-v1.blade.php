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
    <section class="home-banner home-banner-1">
        <img class="lazyload bg-img"
            src="{{ !empty($heroImg) ? asset(\App\Constants\Constant::WEBSITE_SLIDER_IMAGE . '/' . $heroImg) : asset('assets/tenant-front/images/default/banner.jpg') }}">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-xxl-10">
                    <div class="content mb-40" data-aos="fade-up">
                        <h1 class="title">{{ $heroStatic?->title ?? 'We’re Best Real Estate Agency' }}</h1>
                        <p class="text">
                            {{ $heroStatic?->text }}
                        </p>
                    </div>
                    <div class="banner-filter-form" data-aos="fade-up">
                        <ul class="nav nav-tabs">
                            <li class="nav-item">
                                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#rent"
                                    type="button">{{ $keywords['Rent'] ?? __('Rent') }}</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#sale"
                                    type="button">{{ $keywords['Sale'] ?? __('Sale') }}</button>
                            </li>
                        </ul>
                        <div class="tab-content form-wrapper">
                            <input type="hidden" value="{{ $min }}" id="min">
                            <input type="hidden" value="{{ $max }}" id="max">

                            <input type="hidden" id="currency_symbol" value="{{ $basicInfo->base_currency_symbol }}">
                            <input class="form-control" type="hidden" value="{{ $min }}" id="o_min">
                            <input class="form-control" type="hidden" value="{{ $max }}" id="o_max">

                            <div class="tab-pane fade active show" id="rent">
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
                                                <select aria-label="#" name="state" class="form-control select2 city_id"
                                                    id="city">
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
                                                <span data-range-value="filterPriceSliderValue">{{ $min }}
                                                    -
                                                    {{ $max }}</span>
                                            </label>
                                            <div data-range-slider="filterPriceSlider"></div>
                                        </div>
                                        <div class="grid-item">
                                            <button type="submit"
                                                class="btn btn-lg btn-primary bg-secondary icon-start w-100">
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
                                                        {{ $keywords['Select City'] }}
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
                                                class="btn btn-lg btn-primary bg-secondary icon-start w-100">
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

    @if ($secInfo->counter_section_status == 1)
        <x-tenant.frontend.sections.counter :$counters />
    @endif

    @if (count($after_counter) > 0)
        @foreach ($after_counter as $customAbout)
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

    @if ($secInfo->featured_properties_section_status == 1)
        <section class="product-area featured-product pb-70">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="section-title title-inline mb-40" data-aos="fade-up">
                            <h2 class="title">{{ $featuredSecInfo?->featured_property_section_title }}</h2>
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
                            <!-- Slider pagination -->
                            <div class="swiper-pagination position-static mb-30" id="product-slider-pagination"></div>
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

    @if ($secInfo->about_section_status == 1)
        <x-tenant.frontend.sections.about :$aboutInfo :$aboutImg />
    @endif

    @if (count($after_about) > 0)
        @foreach ($after_about as $customAbout)
            @if (isset($homecusSec[$customAbout->id]))
                @if ($homecusSec[$customAbout->id] == 1)
                    <x-tenant.frontend.sections.additional :sectionId="$customAbout->id" />
                @endif
            @endif
        @endforeach
    @endif

    @if ($secInfo->property_section_status == 1)
        <section class="product-area popular-product product-1 pb-70">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="section-title title-inline mb-40" data-aos="fade-up">
                            <h2 class="title">{{ $propertySecInfo?->property_section_title }}</h2>
                            @if ($properties && $properties->isNotEmpty())
                                <div class="tabs-navigation">
                                    <ul class="nav nav-tabs">
                                        <li class="nav-item">
                                            <button class="nav-link active btn-md" data-bs-toggle="tab"
                                                data-bs-target="#forAll"
                                                type="button">{{ $keywords['All Properties'] ?? __('All Properties') }}</button>
                                        </li>
                                        <li class="nav-item">
                                            <button class="nav-link btn-md" data-bs-toggle="tab"
                                                data-bs-target="#forRent"
                                                type="button">{{ $keywords['For Rent'] ?? __('For Rent') }}</button>
                                        </li>
                                        <li class="nav-item">
                                            <button class="nav-link btn-md" data-bs-toggle="tab"
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
                                            class="col-lg-4 col-xxl-3 col-md-6" />
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
                                                class="col-lg-4 col-xxl-3 col-md-6" />
                                        @endif
                                    @empty
                                        <div class="p-3 text-center mb-30">
                                            <h3 class="mb-0">
                                                {{ $keywords['No Property Found'] ?? __('No Property Found') }}</h3>
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
                                                class="col-lg-4 col-xxl-3 col-md-6" />
                                        @endif
                                    @empty
                                        <div class="p-3 text-center mb-30">
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
    @if (!empty(showAd(3)))
        <div class="text-center mb-40">
            {!! showAd(3) !!}
        </div>
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

    @if ($secInfo->why_choose_us_section_status == 1)
        <x-tenant.frontend.sections.why-choose-us :$whyChooseUsInfo :$whyChooseUsImg />
    @endif

    @if (count($after_why_choose_us) > 0)
        @foreach ($after_why_choose_us as $customAbout)
            @if (isset($homecusSec[$customAbout->id]))
                @if ($homecusSec[$customAbout->id] == 1)
                    <x-tenant.frontend.sections.additional :sectionId="$customAbout->id" />
                @endif
            @endif
        @endforeach
    @endif

    @if ($secInfo->agent_section_status == 1)
        <x-tenant.frontend.sections.agent :$agents :$agentInfo />
    @endif

    @if (count($after_agent) > 0)
        @foreach ($after_agent as $customAbout)
            @if (isset($homecusSec[$customAbout->id]))
                @if ($homecusSec[$customAbout->id] == 1)
                    <x-tenant.frontend.sections.additional :sectionId="$customAbout->id" />
                @endif
            @endif
        @endforeach
    @endif

    @if ($secInfo->cities_section_status == 1)
        <x-tenant.frontend.sections.city :$cities :$citySecInfo :$cityBgImg />
    @endif
    @if (!empty(showAd(3)))
        <div class="text-center mt-40">
            {!! showAd(3) !!}
        </div>
    @endif
    @if (count($after_cities) > 0)
        @foreach ($after_cities as $customAbout)
            @if (isset($homecusSec[$customAbout->id]))
                @if ($homecusSec[$customAbout->id] == 1)
                    <x-tenant.frontend.sections.additional :sectionId="$customAbout->id" />
                @endif
            @endif
        @endforeach
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

    @if ($secInfo->newsletter_section_status == 1)
        <section class="newsletter-area pb-100" data-aos="fade-up">
            <div class="container">
                <div class="newsletter-inner px-4">
                    <img class="lazyload bg-img"
                        src="{{ asset(\App\Constants\Constant::WEBSITE_NEWSLETTER_IMAGE . '/' . $newsletterBgImg) }}">
                    <div class="row justify-content-center text-center" data-aos="fade-up">
                        <div class="col-lg-6 col-xxl-5">
                            <div class="content mb-30">
                                <span class="subtitle color-white mb-10 d-block">{{ $newsletterSecInfo?->title }}</span>
                                <h2 class="color-white">{{ $newsletterSecInfo?->subtitle }}</h2>
                            </div>
                            <form id="newsletterForm" class="subscription-form newsletter-form"
                                action="{{ route('frontend.store_subscriber', getParam()) }}" method="POST">
                                @csrf
                                <div class="input-group radius-md">
                                    <input class="form-control"
                                        placeholder="{{ $keywords['Enter Your Email'] ?? __('Enter Your Email') }}"
                                        type="email" name="email" required>

                                    <button class="btn btn-lg btn-primary" type="submit">
                                        {{ $newsletterSecInfo?->btn_name ?? $keywords['Submit'] }}</button>
                                </div>
                                @error('email')
                                    <p class="text-danger text-left">{{ $message }}</p>
                                @enderror
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    @if (count($after_newsletter) > 0)
        @foreach ($after_newsletter as $customAbout)
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
