@extends('front.layout')

@section('pagename')
    - {{ __('Home') }}
@endsection

@section('meta-description', !empty($seo) ? $seo->home_meta_description : '')
@section('meta-keywords', !empty($seo) ? $seo->home_meta_keywords : '')

@section('content')


    @if ($bs->hero_section == 1)
        <section id="home" class="home-banner bg-img bg-cover header-next border-top"
            data-bg-image="{{ asset('assets/front/images/hero-bg.png') }}">
            <div class="container-fluid">
                <div class="row align-items-center gx-xl-5">
                    <div class="col-xl-6">
                        <div class="fluid-left">
                            <div class="content">
                                @if (!empty($be->hero_section_title) || !empty($be->hero_section_text))
                                    <h1 class="title" data-aos="fade-up" data-aos-delay="100">
                                        {{ $be->hero_section_title }}
                                    </h1>
                                    <p data-aos="fade-up" data-aos-delay="150">
                                        {{ $be->hero_section_text }}
                                    </p>
                                    <div class="btn-groups justify-content-center" data-aos="fade-up" data-aos-delay="200">
                                        @if ($be->hero_section_button_url)
                                            <a href="{{ $be->hero_section_button_url }}"
                                                title="{{ $be->hero_section_button_text }}"
                                                class="btn btn-lg btn-primary">{{ $be->hero_section_button_text }}</a>
                                        @endif
                                        @if ($be->hero_section_snd_btn_url)
                                            <a href="{{ $be->hero_section_snd_btn_url }}"
                                                title="{{ $be->hero_section_snd_btn_text }}"
                                                class="btn btn-lg btn-outline">{{ $be->hero_section_snd_btn_text }}</a>
                                        @endif
                                    </div>
                                @else
                                    <h1 class="title" data-aos="fade-up" data-aos-delay="100">
                                        {{ __('Uncover the Best Real Estate Opportunities Here') }}
                                    </h1>
                                    <p data-aos="fade-up" data-aos-delay="150">
                                        {{ __('The European Space Agency calls the crater a "cold trap," where air moving over the frigid ice is cooled, creating a kind of chilly') }}
                                    </p>

                                    <div class="btn-groups justify-content-center" data-aos="fade-up" data-aos-delay="200">
                                        <a href="#" title="{{ __('Explore Your Plans') }}"
                                            class="btn btn-lg btn-primary">{{ __('Explore Your Plans') }}</a>

                                        <a href="#" title="{{ __('View Demo') }}"
                                            class="btn btn-lg btn-outline">{{ __('View Demo') }}</a>
                                    </div>
                                @endif

                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="hero-image" data-aos="fade-left" data-aos-delay="200">
                            <img src="{{ asset('assets/front/images/hero-img.png') }} " alt="hero-img">
                        </div>
                    </div>
                </div>
            </div>
            <div class="shape">
                <div class="shape-1">
                    <svg data-src="{{ asset('assets/front/svg/shape1.svg') }}" data-unique-ids="disabled"
                        data-cache="disabled"></svg>
                </div>
                <div class="shape-2">
                    <svg data-src="{{ asset('assets/front/svg/shape2.svg') }}" data-unique-ids="disabled"
                        data-cache="disabled"></svg>
                </div>
                <div class="shape-3">
                    <svg data-src="{{ asset('assets/front/svg/shape3.svg') }}" data-unique-ids="disabled"
                        data-cache="disabled"></svg>
                </div>
                <div class="shape-4">
                    <svg data-src="{{ asset('assets/front/svg/shape4.svg') }}" data-unique-ids="disabled"
                        data-cache="disabled"></svg>
                </div>
                <div class="shape-5">
                    <svg data-src="{{ asset('assets/front/svg/shape5.svg') }}" data-unique-ids="disabled"
                        data-cache="disabled"></svg>
                </div>
                <div class="shape-6">
                    <svg data-src="{{ asset('assets/front/svg/shape6.svg') }}" data-unique-ids="disabled"
                        data-cache="disabled"></svg>
                </div>
                <div class="shape-7">
                    <svg data-src="{{ asset('assets/front/svg/shape7.svg') }}" data-unique-ids="disabled"
                        data-cache="disabled"></svg>
                </div>
            </div>
        </section>
    @endif

    @if (count($after_hero) > 0)
        @foreach ($after_hero as $customSec)
            @if (isset($homecusSec[$customSec->id]))
                @if ($homecusSec[$customSec->id] == 1)
                    <x-front.sections.additionl-section :sectionId="$customSec->id" />
                @endif
            @endif
        @endforeach
    @endif

    @if ($bs->partners_section == 1)

        <section class="sponsor pt-120">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="section-title title-center mb-50" data-aos="fade-up">
                            <span class="subtitle">{{ $bs->partner_title }} </span>
                            <h2 class="title">{{ $bs->partner_subtitle }} </h2>
                        </div>
                    </div>
                    <div class="col-12">
                        @if ($partners && $partners->isNotEmpty())
                            <div class="swiper sponsor-slider">
                                <div class="swiper-wrapper">
                                    @foreach ($partners as $partner)
                                        <div class="swiper-slide">
                                            <div class="item-single d-flex justify-content-center">
                                                <div class="sponsor-img">
                                                    <img class="lazyload blur-up"
                                                        src="{{ asset('assets/front/img/partners/' . $partner->image) }}"
                                                        alt="Sponsor">
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach

                                </div>
                                <div class="swiper-pagination position-static mt-30" data-aos="fade-up"></div>
                            </div>
                        @else
                            <div>
                                <h4 class="text-center">{{ __('NO PARTNER FOUND') }}</h4>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    @endif
    @if (count($after_partners) > 0)
        @foreach ($after_partners as $customSec)
            @if (isset($homecusSec[$customSec->id]))
                @if ($homecusSec[$customSec->id] == 1)
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
            @if (isset($homecusSec[$customSec->id]))
                @if ($homecusSec[$customSec->id] == 1)
                    <x-front.sections.additionl-section :sectionId="$customSec->id" />
                @endif
            @endif
        @endforeach
    @endif

    @if ($bs->template_section == 1)

        <section class="template-area bg-primary-light ptb-120">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6">
                        <div class="section-title title-center mb-50" data-aos="fade-up">
                            <span class="subtitle">{{ $bs->preview_templates_title }}</span>
                            <h2 class="title mt-0">{{ $bs->preview_templates_subtitle }}</h2>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="row justify-content-center">
                            @forelse ($templates as $template)
                                <div class="col-lg-4 col-sm-6" data-aos="fade-up">
                                    <div class="card text-center mb-50">
                                        <div class="card-image">
                                            <a href="{{ $template->url }}" class="lazy-container"
                                                title="{{ $template->name }}" target="_self">
                                                <img class="lazyload lazy-image"
                                                    data-src="{{ asset(\App\Constants\Constant::WEBSITE_THEMES . '/' . $template->image) }}"
                                                    alt="Demo Image" />
                                            </a>
                                        </div>
                                        <h4 class="card-title">
                                            <a href="{{ $template->url }}" title="{{ $template->name }}"
                                                target="_self">
                                                {{ $template->name }}
                                            </a>
                                        </h4>
                                    </div>
                                </div>
                            @empty
                                <h4 class="text-center"> {{ __('NO TEMPLATES FOUND') }}</h4>
                            @endforelse

                        </div>
                    </div>
                </div>
            </div>

            <div class="shape">

                <div class="shape-1">
                    <svg data-src="{{ asset('assets/front/svg/shape7.svg') }}" data-unique-ids="disabled"
                        data-cache="disabled"></svg>
                </div>
                <div class="shape-2">
                    <svg data-src="{{ asset('assets/front/svg/shape3.svg') }}" data-unique-ids="disabled"
                        data-cache="disabled"></svg>
                </div>
                <div class="shape-3">
                    <svg data-src="{{ asset('assets/front/svg/shape8v2.svg') }}" data-unique-ids="disabled"
                        data-cache="disabled"></svg>
                </div>
                <div class="shape-4">
                    <svg data-src="{{ asset('assets/front/svg/shape9.svg') }}" data-unique-ids="disabled"
                        data-cache="disabled"></svg>
                </div>
                <div class="shape-5">
                    <svg data-src="{{ asset('assets/front/svg/shape3.svg') }}" data-unique-ids="disabled"
                        data-cache="disabled"></svg>
                </div>
                <div class="shape-6">
                    <svg data-src="{{ asset('assets/front/svg/shape4.svg') }}" data-unique-ids="disabled"
                        data-cache="disabled"></svg>
                </div>
                <div class="shape-7">
                    <svg data-src="{{ asset('assets/front/svg/shape8v2.svg') }}" data-unique-ids="disabled"
                        data-cache="disabled"></svg>
                </div>

            </div>

        </section>

    @endif
    @if (count($after_template) > 0)
        @foreach ($after_template as $customSec)
            @if (isset($homecusSec[$customSec->id]))
                @if ($homecusSec[$customSec->id] == 1)
                    <x-front.sections.additionl-section :sectionId="$customSec->id" />
                @endif
            @endif
        @endforeach
    @endif

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
                                            <a href="javaScript:void(0)">
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
            @if (isset($homecusSec[$customSec->id]))
                @if ($homecusSec[$customSec->id] == 1)
                    <x-front.sections.additionl-section :sectionId="$customSec->id" />
                @endif
            @endif
        @endforeach
    @endif

    @if ($bs->pricing_section == 1)
        <section class="pricing-area pb-90">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="section-title title-center mb-50" data-aos="fade-up">
                            <span class="subtitle">{{ $bs->pricing_title }}</span>
                            <h2 class="title mb-2 mt-0"> {{ $bs->pricing_subtitle }} </h2>
                            <p class="text">{{ $bs->pricing_text }}</p>
                        </div>
                    </div>

                    <div class="col-12">
                        <x-front.sections.pricing :featured='true'/>
                    </div>
                </div>
            </div>

            <div class="shape">

                <div class="shape-1">
                    <svg data-src="{{ asset('assets/front/svg/shape6.svg') }}" data-unique-ids="disabled"
                        data-cache="disabled"></svg>
                </div>
                <div class="shape-2">
                    <svg data-src="{{ asset('assets/front/svg/shape7.svg') }}" data-unique-ids="disabled"
                        data-cache="disabled"></svg>
                </div>
                <div class="shape-3">
                    <svg data-src="{{ asset('assets/front/svg/shape4.svg') }}" data-unique-ids="disabled"
                        data-cache="disabled"></svg>
                </div>
                <div class="shape-4">
                    <svg data-src="{{ asset('assets/front/svg/shape3.svg') }}" data-unique-ids="disabled"
                        data-cache="disabled"></svg>
                </div>
                <div class="shape-5">
                    <svg data-src="{{ asset('assets/front/svg/shape5.svg') }}" data-unique-ids="disabled"
                        data-cache="disabled"></svg>
                </div>
                <div class="shape-6">
                    <svg data-src="{{ asset('assets/front/svg/shape4v2.svg') }}" data-unique-ids="disabled"
                        data-cache="disabled"></svg>
                </div>

            </div>
        </section>
    @endif
    @if (count($after_pricing) > 0)
        @foreach ($after_pricing as $customSec)
            @if (isset($homecusSec[$customSec->id]))
                @if ($homecusSec[$customSec->id] == 1)
                    <x-front.sections.additionl-section :sectionId="$customSec->id" />
                @endif
            @endif
        @endforeach
    @endif

    @if ($bs->featured_users_section == 1)

        <section class="user-profile-area pb-120">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-5">
                        <div class="section-title title-center mb-50" data-aos="fade-up">
                            <span class="subtitle">{{ $bs->featured_users_title }}</span>
                            <h2 class="title">{{ $bs->featured_users_subtitle }}</h2>
                        </div>
                    </div>
                    <div class="col-12">

                        @if (count($featured_users) == 0)
                            <div class=" text-center py-5 d-block w-100">
                                <h4 class="text-center">{{ __('NO FEATURED USERS FOUND') }}</h4>
                            </div>
                        @else
                            <div class="swiper user-slider" data-aos="fade-up">

                                <div class="swiper-wrapper">
                                    @foreach ($featured_users as $featured_user)
                                        <div class="swiper-slide">
                                            <div class="card text-center">

                                                <div class="icon">
                                                    <img class="lazy" src="{{ asset($featured_user->photo) }}"
                                                        alt="user">
                                                </div>
                                                <div class="card-content">
                                                    <h4 class="card-title">
                                                        {{ $featured_user->first_name . ' ' . $featured_user->last_name }}
                                                    </h4>
                                                    <div class="social-link d-flex justify-content-center">

                                                        @foreach ($featured_user->social_media as $social)
                                                            <a href="{{ $social->url }}" target="_blank"><i
                                                                    class="{{ $social->icon }}"></i></a>
                                                        @endforeach
                                                    </div>
                                                    <div class="btn-groups">
                                                        @php
                                                            if (!empty($featured_user)) {
                                                                $currentPackage = App\Http\Helpers\UserPermissionHelper::userPackage(
                                                                    $featured_user->id,
                                                                );
                                                                $preferences = App\Models\User\UserPermission::where([
                                                                    ['user_id', $featured_user->id],
                                                                    ['package_id', $currentPackage->package_id],
                                                                ])->first();
                                                                $permissions = isset($preferences)
                                                                    ? json_decode($preferences->permissions, true)
                                                                    : [];
                                                            }
                                                        @endphp
                                                        <a href="{{ detailsUrl($featured_user) }}"
                                                            class="btn btn-sm btn-outline">{{ __('View Profile') }}</a>
                                                        @guest

                                                            <a href="{{ route('user.follow', ['id' => $featured_user->id]) }}"
                                                                class="btn btn-sm btn-primary"> {{ __('Follow') }}
                                                            </a>

                                                        @endguest
                                                        @if (Auth::check() && Auth::id() != $featured_user->id)
                                                            @if (App\Models\User\Follower::where('follower_id', Auth::id())->where('following_id', $featured_user->id)->count() > 0)
                                                                <a href="{{ route('user.unfollow', $featured_user->id) }}"
                                                                    class="btn btn-sm btn-primary">
                                                                    {{ __('Unfollow') }}
                                                                </a>
                                                            @else
                                                                <a href="{{ route('user.follow', ['id' => $featured_user->id]) }}"
                                                                    class="btn btn-sm btn-primary">
                                                                    {{ __('Follow') }}
                                                            @endif
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach

                                </div>
                                <div class="swiper-pagination position-static mt-30"></div>
                            </div>


                        @endif
                    </div>
                </div>
            </div>

            <div class="shape">

                <div class="shape-1">
                    <svg data-src="{{ asset('assets/front/svg/shape9.svg') }}" data-unique-ids="disabled"
                        data-cache="disabled"></svg>
                </div>
                <div class="shape-2">
                    <svg data-src="{{ asset('assets/front/svg/shape8v2.svg') }}" data-unique-ids="disabled"
                        data-cache="disabled"></svg>
                </div>
                <div class="shape-3">
                    <svg data-src="{{ asset('assets/front/svg/shape7.svg') }}" data-unique-ids="disabled"
                        data-cache="disabled"></svg>
                </div>
                <div class="shape-4">
                    <svg data-src="{{ asset('assets/front/svg/shape4.svg') }}" data-unique-ids="disabled"
                        data-cache="disabled"></svg>
                </div>
                <div class="shape-5">
                    <svg data-src="{{ asset('assets/front/svg/shape6.svg') }}" data-unique-ids="disabled"
                        data-cache="disabled"></svg>
                </div>
                <div class="shape-6">
                    <svg data-src="{{ asset('assets/front/svg/shape8.svg') }}" data-unique-ids="disabled"
                        data-cache="disabled"></svg>
                </div>

            </div>
        </section>

        @if (count($after_featured_users) > 0)
            @foreach ($after_featured_users as $customSec)
                @if (isset($homecusSec[$customSec->id]))
                    @if ($homecusSec[$customSec->id] == 1)
                        <x-front.sections.additionl-section :sectionId="$customSec->id" />
                    @endif
                @endif
            @endforeach
        @endif
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

                                                    <span class="ratings-total">{{ $testimonial->rating }}
                                                        {{ __('star of') }}
                                                        {{ $totalTestomanials }}
                                                        {{ __('review') }}</span>
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
            @if (isset($homecusSec[$customSec->id]))
                @if ($homecusSec[$customSec->id] == 1)
                    <x-front.sections.additionl-section :sectionId="$customSec->id" />
                @endif
            @endif
        @endforeach
    @endif
    @if ($bs->blog_section == 1)
        <section class="blog-area ptb-90">
            <div class="container">
                <div class="section-title title-inline mb-50" data-aos="fade-up">
                    <h2 class="title">{{ $bs->blog_title }}</h2>

                </div>
                <div class="row justify-content-center">
                    @forelse ($blogs as $blog)
                        <div class="col-md-6 col-lg-4">
                            <article class="card mb-30" data-aos="fade-up" data-aos-delay="100">
                                <div class="card-image">
                                    <a href="{{ route('front.blogdetails', ['id' => $blog->id, 'slug' => $blog->slug]) }}"
                                        class="lazy-container ratio-16-9">
                                        <img class="lazyload lazy-image"
                                            src="{{ asset('assets/front/img/blogs/' . $blog->main_image) }}"
                                            data-src="{{ asset('assets/front/img/blogs/' . $blog->main_image) }}"
                                            alt="Blog Image">
                                    </a>
                                    <ul class="info-list">
                                        <li><i class="fal fa-user"></i>{{ __('Admin') }}</li>
                                        <li> <i class="fal fa-calendar"></i>
                                            {{ \Carbon\Carbon::parse($blog->created_at)->locale(app()->getLocale())->translatedFormat('d F, Y') }}

                                        </li>
                                        <li><i class="fal fa-tag"></i>{{ $blog->bcategory->name }}</li>
                                    </ul>
                                </div>
                                <div class="content">
                                    <h5 class="card-title lc-2">
                                        <a
                                            href="{{ route('front.blogdetails', ['id' => $blog->id, 'slug' => $blog->slug]) }}">

                                            {{ $blog->title }}
                                        </a>
                                    </h5>
                                    <p class="card-text lc-2">
                                        {!! substr(strip_tags($blog->content), 0, 150) !!}
                                    </p>
                                    <a href="{{ route('front.blogdetails', ['id' => $blog->id, 'slug' => $blog->slug]) }}"
                                        class="card-btn">{{ __('Read More') }}</a>
                                </div>
                            </article>
                        </div>
                    @empty
                        <h4 class="text-center"> {{ __('NO BLOG POST FOUND') }}</h4>
                    @endforelse

                </div>
            </div>

            <div class="shape">
                <div class="shape-1">
                    <svg data-src="{{ asset('assets/front/svg/shape8.svg') }}" data-unique-ids="disabled"
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
                    <svg data-src="{{ asset('assets/front/svg/shape5.svg') }}" data-unique-ids="disabled"
                        data-cache="disabled"></svg>
                </div>
                <div class="shape-6">
                    <svg data-src="{{ asset('assets/front/svg/shape8v2.svg') }}" data-unique-ids="disabled"
                        data-cache="disabled"></svg>
                </div>
                <div class="shape-5">
                    <svg data-src="{{ asset('assets/front/svg/shape3.svg') }}" data-unique-ids="disabled"
                        data-cache="disabled"></svg>
                </div>
            </div>
        </section>
    @endif
    @if (count($after_blog) > 0)
        @foreach ($after_blog as $customSec)
            @if (isset($homecusSec[$customSec->id]))
                @if ($homecusSec[$customSec->id] == 1)
                    <x-front.sections.additionl-section :sectionId="$customSec->id" />
                @endif
            @endif
        @endforeach
    @endif


@endsection
