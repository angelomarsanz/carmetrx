@extends('tenant_frontend.layout')

@section('pageHeading')
    {{ !empty($pageHeading) ? $pageHeading->about_page_title : __('About Us') }}
@endsection

@section('metaKeywords')
    @if (!empty($seoInfo))
        {{ $seoInfo->meta_keyword_about_page }}
    @endif
@endsection

@section('metaDescription')
    @if (!empty($seoInfo))
        {{ $seoInfo->meta_description_about_page }}
    @endif
@endsection

@section('content')
    @includeIf('tenant_frontend.partials.breadcrumb', [
        'breadcrumb' => $breadcrumb,
        'title' => !empty($pageHeading) ? $pageHeading->about_page_title : __('About Us'),
        'subtitle' => __('About Us'),
    ])

    @if ($secInfo->about_info_section == 1)
        <x-tenant.frontend.sections.about :$aboutInfo :$aboutImg class="mt-30" />
    @endif

    @if (count($after_about_info) > 0)
        @foreach ($after_about_info as $customAbout)
            @if (isset($aboutSec[$customAbout->id]))
                @if ($aboutSec[$customAbout->id] == 1)
                    <x-tenant.frontend.sections.additional :sectionId="$customAbout->id" />
                @endif
            @endif
        @endforeach
    @endif

    @if ($secInfo->why_choose_us_section == 1)
        <x-tenant.frontend.sections.why-choose-us :$whyChooseUsInfo :$whyChooseUsImg />
    @endif

    @if (count($after_why_choose_us) > 0)
        @foreach ($after_why_choose_us as $customAbout)
            @if (isset($aboutSec[$customAbout->id]))
                @if ($aboutSec[$customAbout->id] == 1)
                    <x-tenant.frontend.sections.additional :sectionId="$customAbout->id" />
                @endif
            @endif
        @endforeach
    @endif
@if (!empty(showAd(3)))
        <div class="text-center mt-4">
            {!! showAd(3) !!}
        </div>
        <div class="pb-100"></div>
    @endif

    @if ($secInfo->work_steps_section == 1)
        <x-tenant.frontend.sections.work-steps :$workStepsSecInfo :$steps :$workStepsSecImg />
    @endif

    @if (count($after_work_steps) > 0)
        @foreach ($after_work_steps as $customAbout)
            @if (isset($aboutSec[$customAbout->id]))
                @if ($aboutSec[$customAbout->id] == 1)
                    <x-tenant.frontend.sections.additional :sectionId="$customAbout->id" />
                @endif
            @endif
        @endforeach
    @endif

    @if ($secInfo->testimonial_section == 1)
        <section class="testimonial-area pt-100 pb-70">
            <div class="overlay-bg d-none d-lg-block">
                <img class="lazyload blur-up"
                    data-src="{{ asset(\App\Constants\Constant::WEBSITE_TESTIMONIAL_SECTION_IMAGE . '/' . $testimonialSecImage) }}">
            </div>
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-4">
                        <div class="content mb-30" data-aos="fade-up">
                            <div class="content-title">
                                <span class="subtitle"><span class="line"></span>{{ $testimonialSecInfo?->title }}</span>
                                <h2 class="title">
                                    {{ $testimonialSecInfo?->subtitle }}</h2>
                            </div>
                            <p class="text mb-30">
                                {{ $testimonialSecInfo?->content }}</p>
                            <!-- Slider navigation buttons -->
                            <div class="slider-navigation scroll-animate">
                                <button type="button" title="Slide prev" class="slider-btn slider-btn-prev">
                                    <i class="fal fa-angle-left"></i>
                                </button>
                                <button type="button" title="Slide next" class="slider-btn slider-btn-next">
                                    <i class="fal fa-angle-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-8" data-aos="fade-up">
                        <div class="swiper" id="testimonial-slider-1">
                            <div class="swiper-wrapper">
                                @forelse ($testimonials as $testimonial)
                                    <div class="swiper-slide pb-30" data-aos="fade-up">
                                        <div class="slider-item">
                                            <div class="client-img">
                                                <div class="lazy-container ratio ratio-1-1">
                                                    @if (is_null($testimonial->image))
                                                        <img data-src="{{ asset('assets/img/profile.jpg') }}"
                                                            class="lazyload">
                                                    @else
                                                        <img class="lazyload"
                                                            data-src="{{ asset(\App\Constants\Constant::WEBSITE_TESTIMONIAL_IMAGE . '/' . $testimonial->image) }}">
                                                    @endif


                                                </div>
                                            </div>
                                            <div class="client-content mt-30">
                                                <div class="quote">
                                                    <p class="text">{{ $testimonial->comment }}</p>
                                                </div>
                                                <div
                                                    class="client-info d-flex flex-wrap gap-10 align-items-center justify-content-between">
                                                    <div class="content">
                                                        <h6 class="name">{{ $testimonial->name }}</h6>
                                                        <span class="designation">{{ $testimonial->occupation }}</span>
                                                    </div>
                                                    <div class="ratings">

                                                        <div class="rate">
                                                            <div class="rating-icon"
                                                                style="width: {{ $testimonial->rating * 20 }}%"></div>
                                                        </div>
                                                        <span class="ratings-total">({{ $testimonial->rating }}) </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="bg-light p-3 text-center mb-30 w-100">
                                        <h3 class="mb-0">
                                            {{ $keywords['No Testimonial Found'] ?? __('No Testimonial Found') }}</h3>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif
    @if (count($after_testimonial) > 0)
        @foreach ($after_testimonial as $customAbout)
            @if (isset($aboutSec[$customAbout->id]))
                @if ($aboutSec[$customAbout->id] == 1)
                    <x-tenant.frontend.sections.additional :sectionId="$customAbout->id" />
                @endif
            @endif
        @endforeach
    @endif

    @if (!empty(showAd(3)))
        <div class="text-center mt-4">
            {!! showAd(3) !!}
        </div>
        <div class="pb-100"></div>
    @endif

@endsection
