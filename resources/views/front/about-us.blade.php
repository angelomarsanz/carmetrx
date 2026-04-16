@extends('front.layout')

@section('pagename')
    - {{ __('About Us') }}
@endsection

@section('meta-description', !empty($seo) ? $seo->faqs_meta_description : '')
@section('meta-keywords', !empty($seo) ? $seo->faqs_meta_keywords : '')


@section('content')
    @includeIf('front.partials.breadcrumb', [
        'title' => __('About Us'),
        'link' => __('About Us'),
    ])
    @if ($bs->intro_section == 1)
        <section class="choose-area pt-120 pb-90">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <div class="choose-content mb-30 pe-lg-5" data-aos="fade-right">
                            <span class="subtitle">{{ $bs->intro_title }}</span>
                            <h2 class="title">{{ $bs->intro_subtitle }}</h2>
                            <p class="text">{!! nl2br($bs->intro_text) !!}</p>
                            @if ($bs->intro_section_button_url)
                                <a href="{{ $bs->intro_section_button_url }}"
                                    class="btn btn-lg btn-primary">{{ $bs->intro_section_button_text }}</a>
                            @endif

                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="row justify-content-center">
                            @forelse ($features as $feature)
                                <div class="col-sm-6 item" data-aos="fade-up">
                                    <div class="card mb-30">
                                        <div class="card-icon">
                                            <img src="{{ asset('assets/front/img/feature/' . $feature->icon) }}"
                                                alt="Icon">
                                        </div>
                                        <div class="card-content">
                                            <a href="#">
                                                <h4 class="card-title lc-1">{{ $feature->title }}</h4>
                                            </a>
                                            <p class="card-text">{{ $feature->text }}</p>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <h4 class="text-center">{{ __('NO FEATURES FOUND') }}</h4>
                            @endforelse

                        </div>
                    </div>
                </div>
            </div>

            <div class="shape">

                <div class="shape-1">
                    <svg data-src="{{ asset('assets/front/svg/shape9.svg') }}" data-unique-ids="disabled"
                        data-cache="disabled"></svg>
                </div>
                <div class="shape-2">
                    <svg data-src="{{ asset('assets/front/svg/shape2.svg') }}" data-unique-ids="disabled"
                        data-cache="disabled"></svg>
                </div>
                <div class="shape-3">
                    <svg data-src="{{ asset('assets/front/svg/shape4.svg') }}" data-unique-ids="disabled"
                        data-cache="disabled"></svg>
                </div>
                <div class="shape-4">
                    <svg data-src="{{ asset('assets/front/svg/shape6.svg') }}" data-unique-ids="disabled"
                        data-cache="disabled"></svg>
                </div>
                <div class="shape-5">
                    <svg data-src="{{ asset('assets/front/svg/shape3.svg') }}" data-unique-ids="disabled"
                        data-cache="disabled"></svg>
                </div>
                <div class="shape-6">
                    <svg data-src="{{ asset('assets/front/svg/shape5.svg') }}" data-unique-ids="disabled"
                        data-cache="disabled"></svg>
                </div>

            </div>
        </section>
    @endif
    @if (count($after_intro) > 0)
        @foreach ($after_intro as $customSec)
            @if (isset($aboutcusSec[$customSec->id]))
                @if ($aboutcusSec[$customSec->id] == 1)
                    <x-front.sections.additionl-section :sectionId="$customSec->id" />
                @endif
            @endif
        @endforeach
    @endif

    @if ($bs->work_process_section == 1)
        <section class="store-area pt-120 pb-90">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12">
                        <div class="section-title title-inline mb-50" data-aos="fade-up">
                            <h2 class="title">{{ $bs->work_process_title }}</h2>

                             
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="row justify-content-center">
                            @forelse ($processes as $process)
                                <div class="col-sm-6 col-lg-4 col-xl-3 mb-30 item" data-aos="fade-up">
                                    <div class="card">
                                        <div class="card-icon">
                                            <i class=" {{ $process->icon }} "></i>
                                        </div>
                                        <div class="card-content">
                                            <a href="javaScript:void(0)">
                                                <h4 class="card-title lc-1">{{ $process->title }}</h4>
                                            </a>
                                            <p class="card-text lc-2">{{ $process->text }}</p>
                                            <a href="{{ route('front.pricing') }}" class="btn-text color-primary"
                                                title="{{ __('Purchase Now') }}"
                                                target="_self">{{ __('Purchase Now') }}</a>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <h4 class="text-center"> {{ __('NO WORK PROCESS FOUND') }} </h4>
                            @endforelse

                        </div>
                    </div>
                </div>
            </div>
            <!-- Bg Shape -->
            <div class="shape">

                <div class="shape-1">
                    <svg data-src="{{ asset('assets/front/svg/shape4v2.svg') }}" data-unique-ids="disabled"
                        data-cache="disabled"></svg>
                </div>
                <div class="shape-2">
                    <svg data-src="{{ asset('assets/front/svg/shape7v2.svg') }}" data-unique-ids="disabled"
                        data-cache="disabled"></svg>
                </div>
                <div class="shape-3">
                    <svg data-src="{{ asset('assets/front/svg/shape6.svg') }}" data-unique-ids="disabled"
                        data-cache="disabled"></svg>
                </div>
                <div class="shape-4">
                    <svg data-src="{{ asset('assets/front/svg/shape1.svg') }}" data-unique-ids="disabled"
                        data-cache="disabled"></svg>
                </div>
                <div class="shape-5">
                    <svg data-src="{{ asset('assets/front/svg/shape3.svg') }}" data-unique-ids="disabled"
                        data-cache="disabled"></svg>
                </div>

            </div>
        </section>
    @endif
    @if (count($after_work_process) > 0)
        @foreach ($after_work_process as $customSec)
            @if (isset($aboutcusSec[$customSec->id]))
                @if ($aboutcusSec[$customSec->id] == 1)
                    <x-front.sections.additionl-section :sectionId="$customSec->id" />
                @endif
            @endif
        @endforeach
    @endif


    @if ($bs->testimonial_section == 1)
        <section class="testimonial-area">
            <div class="container">
                <div class="row align-items-center gx-xl-5">
                    <div class="col-lg-6">
                        <div class="content mb-30" data-aos="fade-up">
                            <h2 class="title">{{ $bs->testimonial_title }}</h2>

                        </div>
                        @if ($testimonials && $testimonials->isNotEmpty())
                            <div class="swiper testimonial-slider mb-30" data-aos="fade-up">
                                <div class="swiper-wrapper">
                                    @php
                                        $totalTestomanials = count($testimonials);
                                    @endphp
                                    @foreach ($testimonials as $testimonial)
                                        <div class="swiper-slide">
                                            <div class="slider-item bg-primary-light">
                                                <div class="ratings justify-content-between size-md">
                                                    <div class="rate">
                                                        <div class="rating-icon"
                                                            style="width:{{ $testimonial->rating * 20 }}%"></div>
                                                    </div>

                                                    <span class="ratings-total">{{ $testimonial->rating }} star of
                                                        {{ $totalTestomanials }}
                                                        review</span>
                                                </div>
                                                <div class="quote">
                                                    <p class="text mb-0">
                                                        {{ $testimonial->comment }}
                                                    </p>
                                                </div>
                                                <div class="client flex-wrap">
                                                    <div class="client-info d-flex align-items-center">
                                                        <div class="client-img">
                                                            <div class="lazy-container ratio ratio-1-1">
                                                                <img class="lazyload"
                                                                    data-src="{{ $testimonial->image ? asset('assets/front/img/testimonials/' . $testimonial->image) : asset('assets/front/img/thumb-1.jpg') }}"
                                                                    alt="Person Image">
                                                            </div>
                                                        </div>
                                                        <div class="content">
                                                            <h6 class="name">{{ $testimonial->name }}</h6>
                                                            <span class="designation">{{ $testimonial->rank }}</span>
                                                        </div>
                                                    </div>
                                                    <span class="icon"><i class="fas fa-quote-right"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach

                                </div>
                                <div class="swiper-pagination" id="testimonial-slider-pagination" data-min data-max></div>
                            </div>
                        @else
                            <h4 class="text-center"> {{ __('NO TESTIMONIAL FOUND') }}</h4>
                        @endif
                    </div>
                    <div class="col-lg-6">
                        <div class="image mb-30 img-right" data-aos="fade-left">
                            <img
                                src="{{ !empty($be->testimonial_img) ? asset('assets/front/img/testimonials/' . $be->testimonial_img) : '' }}">
                        </div>
                    </div>
                </div>
            </div>


            <div class="shape">

                <div class="shape-1">
                    <svg data-src="{{ asset('assets/front/svg/shape8.svg') }}" data-unique-ids="disabled"
                        data-cache="disabled"></svg>
                </div>
                <div class="shape-2">
                    <svg data-src="{{ asset('assets/front/svg/shape3.svg') }}" data-unique-ids="disabled"
                        data-cache="disabled"></svg>
                </div>
                <div class="shape-3">
                    <svg data-src="{{ asset('assets/front/svg/shape4.svg') }}" data-unique-ids="disabled"
                        data-cache="disabled"></svg>
                </div>
                <div class="shape-4">
                    <svg data-src="{{ asset('assets/front/svg/shape7.svg') }}" data-unique-ids="disabled"
                        data-cache="disabled"></svg>
                </div>
                <div class="shape-5">
                    <svg data-src="{{ asset('assets/front/svg/shape6.svg') }}" data-unique-ids="disabled"
                        data-cache="disabled"></svg>
                </div>
                <div class="shape-6">
                    <svg data-src="{{ asset('assets/front/svg/shape4v2.svg') }}" data-unique-ids="disabled"
                        data-cache="disabled"></svg>
                </div>

            </div>
        </section>
    @endif
    @if (count($after_testimonial) > 0)
        @foreach ($after_testimonial as $customSec)
            @if (isset($aboutcusSec[$customSec->id]))
                @if ($aboutcusSec[$customSec->id] == 1)
                    <x-front.sections.additionl-section :sectionId="$customSec->id" />
                @endif
            @endif
        @endforeach
    @endif

@endsection
