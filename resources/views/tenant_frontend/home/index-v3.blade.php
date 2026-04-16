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

    <section class="home-banner home-banner-3 with-radius">
        <img class="lazyload bg-img blur-up"
            src=" {{ !empty($heroImg) ? asset(\App\Constants\Constant::WEBSITE_SLIDER_IMAGE . '/' . $heroImg) : asset('assets/tenant-front/images/default/banner-two.jpg') }}">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-xl-7 col-lg-7">
                    <div class="content mb-40" data-aos="fade-up">
                        <h1 class="title color-white">{{ $heroStatic?->title }}</h1>
                        <p class="text color-white m-0">
                            {{ $heroStatic?->text }}
                        </p>
                    </div>
                </div>
                <div class="col-xl-5 col-lg-5">
                    <div class="filter-form mb-40" data-aos="fade-up">
                        <div class="tabs-navigation">
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
                        </div>
                        <div class="tab-content">
                            <input type="hidden" id="currency_symbol" value="{{ $basicInfo->base_currency_symbol }}">
                            <input type="hidden" name="min" value="{{ $min }}" id="min">
                            <input type="hidden" name="max" value="{{ $max }}" id="max">
                            <input class="form-control" type="hidden" value="{{ $min }}" id="o_min">
                            <input class="form-control" type="hidden" value="{{ $max }}" id="o_max">
                            <div class="tab-pane fade show active" id="rent">
                                <form action="{{ route('frontend.properties', getParam()) }}" method="get">
                                    <div class="row">
                                        <input type="hidden" name="purpose" value="rent">
                                        <input type="hidden" name="min" value="{{ $min }}" id="min1">
                                        <input type="hidden" name="max" value="{{ $max }}" id="max1">
                                        <div class="col-lg-12 col-md-6">
                                            <div class="form-group mb-20">
                                                <input type="text" id="search1" name="location" class="form-control"
                                                    placeholder="{{ $keywords['Location'] ?? __('Location') }}">

                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group mb-20">
                                                <select aria-label="#" name="type" class="form-control select2 type"
                                                    id="type">
                                                    <option selected disabled>
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
                                        <div class="  col-sm-6">
                                            <div class="form-group mb-20">
                                                <select aria-label="#" class="form-control select2 bringCategory"
                                                    id="category" name="category">
                                                    <option selected disabled>
                                                        {{ $keywords['Select Category'] ?? __('Select Category') }}
                                                    </option>
                                                    <option value="all">{{ $keywords['All'] ?? __('All') }}</option>
                                                    @foreach ($all_proeprty_categories as $category)
                                                        <option value="{{ $category?->categoryContent?->slug }}">
                                                            {{ $category?->categoryContent?->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group city mb-20">

                                            <select aria-label="#" name="state" class="form-control select2 city_id"
                                                id="city">
                                                <option disabled selected>
                                                    {{ $keywords['Select City'] ?? __('Select City') }}</option>
                                                <option value="all">{{ $keywords['All'] ?? __('All') }}</option>
                                            @foreach ($all_states as $state)
                                                        <option data-id="{{ $state->id }}"
                                                            value="{{ @$state->stateContent->slug }}">
                                                            {{ @$state->stateContent->name }}</option>
                                                    @endforeach
                                            </select>
                                        </div>

                                        <div class="col-lg-12 col-md-6">
                                            <div class="form-group mb-20">
                                                <div class="form-control price-slider">
                                                    <div data-range-slider="filterPriceSlider2"></div>
                                                    <span data-range-value="filterPriceSlider2Value"
                                                        class="w-60">{{ $min }}
                                                        -
                                                        {{ $max }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-12 col-md-6 text-center">
                                            <button type="submit" class="btn btn-lg btn-primary icon-start">
                                                <i class="fal fa-search"></i>
                                                {{ $keywords['Search'] ?? __('Search') }}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="tab-pane fade" id="sale">
                                <form action="{{ route('frontend.properties', getParam()) }}" method="get">
                                    <div class="row">
                                        <input type="hidden" name="purpose" value="sale">
                                        <input type="hidden" name="min" value="{{ $min }}"
                                            id="min2">
                                        <input type="hidden" name="max" value="{{ $max }}"
                                            id="max2">
                                        <div class="col-lg-12 col-md-6">
                                            <div class="form-group mb-20">
                                                <input type="text" id="search1" name="location"
                                                    class="form-control"
                                                    placeholder="{{ $keywords['Location'] ?? __('Location') }}">

                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group mb-20">
                                                <select aria-label="#" name="type" class="form-control select2 type"
                                                    id="type1">
                                                    <option selected disabled>
                                                        {{ $keywords['Select Property'] ?? __('Select Property') }}
                                                    </option>
                                                    <option selected value="all">{{ $keywords['All'] ?? __('All') }}
                                                    </option>
                                                    <option value="residential">
                                                        {{ $keywords['Residential'] ?? __('Residential') }}</option>
                                                    <option value="commercial">
                                                        {{ $keywords['Commercial'] ?? __('Commercial') }}</option>

                                                </select>
                                            </div>
                                        </div>
                                        <div class="  col-sm-6">
                                            <div class="form-group mb-20">
                                                <select aria-label="#" class="form-control select2 bringCategory"
                                                    id="category1" name="category">
                                                    <option selected disabled>
                                                        {{ $keywords['Select Category'] ?? __('Select Category') }}
                                                    </option>
                                                    <option value="all">{{ $keywords['All'] ?? __('All') }}</option>
                                                    @foreach ($all_proeprty_categories as $category)
                                                        <option value="{{ $category?->categoryContent?->slug }}">
                                                            {{ $category?->categoryContent?->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group city mb-20">

                                            <select aria-label="#" name="state" class="form-control select2 city_id"
                                                id="city1">
                                                <option disabled selected>
                                                    {{ $keywords['Select City'] ?? __('Select City') }}</option>
                                                <option selected value="all">{{ $keywords['All'] ?? __('All') }}
                                                </option>
                                              @foreach ($all_states as $state)
                                                        <option data-id="{{ $state->id }}"
                                                            value="{{ @$state->stateContent->slug }}">
                                                            {{ @$state->stateContent->name }}</option>
                                                    @endforeach
                                            </select>
                                        </div>

                                        <div class="col-lg-12 col-md-6">
                                            <div class="form-group mb-20">
                                                <div class="form-control price-slider">
                                                    <div data-range-slider="filterPriceSlider2"></div>
                                                    <span data-range-value="filterPriceSlider2Value"
                                                        class="w-60">{{ $min }}
                                                        -
                                                        {{ $max }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-12 col-md-6 text-center">
                                            <button type="submit" class="btn btn-lg btn-primary icon-start">
                                                <i class="fal fa-search"></i>
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
    @if (!empty(showAd(3)))
        <div class="text-center mb-40">
            {!! showAd(3) !!}
        </div>
    @endif
    @if ($secInfo->property_section_status == 1)
        <section class="product-area popular-product pb-70">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="section-title title-inline mb-10" data-aos="fade-up">
                            <h2 class="title mb-20">{{ $propertySecInfo?->property_section_title }}</h2>
                            <div class="slider-navigation mb-20">
                                <button type="button" title="Slide prev" class="slider-btn product-slider-btn-prev">
                                    <i class="fal fa-angle-left"></i>
                                </button>
                                <button type="button" title="Slide next" class="slider-btn product-slider-btn-next">
                                    <i class="fal fa-angle-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-12" data-aos="fade-up">
                        <div class="swiper product-slider">
                            <div class="swiper-wrapper">
                                @forelse ($featured_properties as $property)
                                    <div class="swiper-slide">

                                        <x-tenant.frontend.property  :adminLang="$adminLang" :property="$property" />
                                    </div>
                                @empty
                                    <div class="p-3 text-center mb-30 w-100">
                                        <h3 class="mb-0"> {{ __('No Properties Found') }}</h3>
                                    </div>
                                @endforelse
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


    @if ($secInfo->about_section_status == 1)
        <x-tenant.frontend.sections.about :$aboutInfo :$aboutImg class="mt-30" />
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

    @if ($secInfo->work_steps_section_status == 1)
        <x-tenant.frontend.sections.work-steps :$workStepsSecInfo :$steps />
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

    @if ($secInfo->counter_section_status == 1)
        <x-tenant.frontend.sections.counter :$counters :$counterSectionImage class="with-radius border" />
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
        <div class="text-center mt-40">
            {!! showAd(3) !!}
        </div>
    @endif
    @if ($secInfo->project_section_status == 1)
        <section class="projects-area pt-100 pb-70">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="section-title title-center mb-40" data-aos="fade-up">
                            <span class="subtitle">{{ $projectInfo?->project_section_title }}</span>
                            <h2 class="title mb-20">{{ $projectInfo?->project_section_subtitle }}</h2>
                            <div class="tabs-navigation mb-20">
                                <ul class="nav nav-tabs">

                                    <li class="nav-item">
                                        <button class="nav-link active btn-md" data-bs-toggle="tab"
                                            data-bs-target="#forAll"
                                            type="button">{{ $keywords['All Projects'] ?? __('All Projects') }}</button>
                                    </li>
                                    @foreach ($projectCategories as $category)
                                        <li class="nav-item">
                                            <button class="nav-link btn-md" data-bs-toggle="tab"
                                                data-bs-target="#for{{ $category->id . $category->user_id }}"
                                                type="button">{{ $category->name }}</button>
                                        </li>
                                    @endforeach

                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="tab-content pb-70" data-aos="fade-up">
                            <div class="tab-pane fade show active" id="forAll">
                                <div class="row masonry-gallery grid">
                                    <div class="col-lg-4 col-md-6 grid-sizer"></div>
                                    @forelse ($allFeaturedProjects as $project)
                                        <x-tenant.frontend.project :project="$project"
                                            class="col-lg-4 col-sm-6 grid-item mb-30" />
                                    @empty
                                        <div class="p-3 text-center mb-30 w-100">
                                            <h3 class="mb-0">
                                                {{ $keywords['No Project Found'] ?? __('No Project Found') }}</h3>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                            @foreach ($featuredProjectCate as $item)
                                <div class="tab-pane fade" id="for{{ $item->id . $item->user_id }}">
                                    <div class="row masonry-gallery grid">
                                        <div class="col-lg-4 col-md-6 grid-sizer"></div>

                                        @forelse ($item->projects as $project)
                                            <x-tenant.frontend.project :project="$project"
                                                class="col-lg-4 col-sm-6 grid-item mb-30" />
                                        @empty
                                            <div class="p-3 text-center   w-100">
                                                <h3 class="mb-0">
                                                    {{ $keywords['No Project Found'] ?? __('No Project Found') }}</h3>
                                            </div>
                                        @endforelse

                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>
            </div>
        </section>
    @endif

    @if (count($after_project) > 0)
        @foreach ($after_project as $customAbout)
            @if (isset($homecusSec[$customAbout->id]))
                @if ($homecusSec[$customAbout->id] == 1)
                    <x-tenant.frontend.sections.additional :sectionId="$customAbout->id" />
                @endif
            @endif
        @endforeach
    @endif

    @if ($secInfo->testimonial_section_status == 1)
        <x-tenant.frontend.sections.testimonial :$testimonialSecInfo :$testimonials />
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
    @if (!empty(showAd(3)))
        <div class="text-center mb-40">
            {!! showAd(3) !!}
        </div>
    @endif
@endsection
