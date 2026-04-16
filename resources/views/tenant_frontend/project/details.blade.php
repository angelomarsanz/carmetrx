@extends('tenant_frontend.layout')

@section('pageHeading')
    {{ $project->title }}
@endsection

@section('metaKeywords')
    @if (!empty($project))
        {{ $project->meta_keyword }}
    @endif
@endsection

@section('metaDescription')
    @if (!empty($project))
        {{ $project->meta_description }}
    @endif
@endsection
@section('og:tag')
    <meta property="og:title" content="{{ $project->title }}">
    <meta property="og:image" content="{{ asset('assets/img//project/featured/' . $project->featured_image) }}">
    <meta property="og:url" content="{{ route('frontend.project.details', [getParam(), 'slug' => $project->slug]) }}">
@endsection

@section('content')
    <!-- Page Title Start-->
    <div class="page-title-area header-next">
        <!-- Background Image -->
        <img class="lazyload blur-up bg-img"
            src="{{ asset( $breadcrumb) }}">
        <div class="container">
            <div class="content text-center">
                <div class="row justify-content-center">
                    <div class="col-lg-12">
                        <h1 class="color-white">
                            {{  $project->title }}
                        </h1>
                        <p class="font-lg color-white mx-auto"> <span class="product-location icon-start"><i
                                    class="fal fa-map-marker-alt"></i>{{ $project->address }}</span>

                            <span>
                                {{ $project->city?->getContent($project->language_id)?->name }}
                                {{ $project->isStateActive ? ', ' . $project->state?->getContent($project->language_id)?->name : '' }}
                                {{ $project->isCountryActive ? ', ' . $project->country?->getContent($project->language_id)?->name : '' }}
                            </span>
                        </p>
                        <p class="font-lg color-white mx-auto"> <span class="product-location icon-start"><i
                                    class="fal fa-user"></i>
                                @if ($project->agent_id == 0)
                                    @if ($project->user->show_profile)
                                        <a class="color-white" href="{{ route('frontend.tenant.details', [getParam()]) }}">
                                            {{ $project->user->username }}</a>
                                    @else
                                        {{ $project->user->username }}
                                    @endif
                                @else
                                    <a class="color-white"
                                        href="{{ route('frontend.agent.details', [getParam(), 'agentusername' => $project->agent->username]) }}">{{ $project->agent->username }}</a>
                                @endif
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Page Title End-->

    <div class="divider">
        <div class="icon"><a href="#tapDown"><i class="fal fa-long-arrow-down"></i></a></div>
        <span class="line"></span>
    </div>

    <div class="projects-details-area pt-100 pb-70" id="tapDown">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="project-desc mb-40" data-aos="fade-up">
                        <h3 class="mb-20">{{ $keywords['Project Overview'] ?? __('Project Overview') }}</h3>
                        <p class="summernote-content">
                            {!! $project->description !!}
                        </p>

                    </div>
                    @if (!empty(showAd(3)))
                        <div class="text-center mb-3 mt-3">
                            {!! showAd(3) !!}
                        </div>
                    @endif
                    <div class="">
                        <p>

                            <a class="btn btn-primary btn-md" href="#" data-bs-toggle="modal"
                                data-bs-target="#socialMediaModal">
                                <i class="far fa-share-alt"></i>
                                <span>{{ $keywords['Share'] ?? __('Share') }} </span>
                            </a>

                            <a class="btn btn-primary btn-md" href="#" data-bs-toggle="modal"
                                data-bs-target="#messages">
                                <i class="far fa-comments"></i>
                                <span>{{ $keywords['Contact'] ?? __('Contact') }} </span>
                            </a>

                        </p>
                    </div>
                    <div class="pb-20"></div>
                    @if (count($project->specifications) > 0)
                        <div class="row" class="mb-20">
                            <div class="col-12">
                                <h3 class="mb-20">{{ $keywords['Features'] ?? __('Features') }}</h3>
                            </div>

                            @foreach ($project->specifications as $specification)
                                @php

                                    $project_specification_content = $specification->getContent($language->id);
                                @endphp
                                <div class="col-lg-3 col-sm-6 col-md-4 mb-20">
                                    <strong class="mb-1 text-dark">{{ $project_specification_content?->label }}</strong>
                                    <br>
                                    <span>{{ $project_specification_content?->value }}</span>
                                </div>
                            @endforeach
                        </div>
                        <div class="pb-20"></div>
                    @endif

                    <div class="pb-20"></div>

                    <div class="project-location mb-40" data-aos="fade-up">
                        <h3 class="mb-20"> {{ $keywords['Location'] ?? __('Location') }}</h3>
                        <div class="lazy-container radius-lg ratio ratio-21-8 border">
                            <iframe class="lazyload"
                                src="https://maps.google.com/maps?q={{ $project->latitude }},{{ $project->longitude }}&hl={{ $currentLanguageInfo->code }};z=15&amp;output=embed"></iframe>
                        </div>
                    </div>

                    <div class="pb-20"></div><!-- Space -->

                    <div class="project-planning mb-10" data-aos="fade-up">
                        <h3 class="mb-20">{{ $keywords['Floor Planning'] ?? __('Floor Planning') }}</h3>
                        <div class="row">
                            @foreach ($floorPlanImages as $floorplan)
                                <div class="col-lg-4">
                                    <div class="mb-30">
                                        <img class="lazyload blur-up radius-lg" src="assets/images/placeholder.png"
                                            data-src="{{ asset('assets/img/project/floor-paln-images/' . $floorplan->image) }}">
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    </div>

                    <div class="pb-20"></div><!-- Space -->
                    @if (count($project->projectTypeContents) > 0)
                        <div class="project-type mb-10" data-aos="fade-up">
                            <h3 class="mb-20">{{ $keywords['Project Types'] ?? __('Project Types') }}</h3>
                            <div class="row">
                                @foreach ($project->projectTypeContents as $typeContent)
                                    <div class="col-lg-4 col-md-6">
                                        <div class="card border mb-30">
                                            <div class="card-content">
                                                <ul class="m-0 p-0">
                                                    <li class="d-flex align-items-center">
                                                        <span
                                                            class="font-lg color-dark">{{ $keywords['Area'] ?? __('Area') }}</span>
                                                        <span class="icon-start"> <i
                                                                class="fal fa-vector-square"></i>{{ $typeContent?->min_area }}
                                                            @if (!empty($typeContent->max_area))
                                                                {{ ' - ' . $typeContent->max_area }}
                                                            @endif
                                                            {{ $keywords['Sqft'] ?? __('Sqft') }}
                                                        </span>
                                                    </li>
                                                    <li class="d-flex align-items-center">
                                                        <span
                                                            class="font-lg color-dark">{{ $keywords['Price'] ?? __('Price') }}</span>
                                                        <span class="icon-start"><i
                                                                class="ico-save-money"></i>{{ $typeContent?->min_price }}
                                                            @if (!empty($typeContent->max_price))
                                                                {{ ' - ' . $typeContent->max_price }}
                                                            @endif
                                                        </span>
                                                    </li>
                                                    <li class="d-flex align-items-center">
                                                        <span
                                                            class="font-lg color-dark">{{ $keywords['Unit'] ?? __('Unit') }}</span>
                                                        <span class="icon-start"><i
                                                                class="ico-home"></i>{{ $typeContent?->unit }}</span>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    @if (!empty(showAd(3)))
                        <div class="text-center mb-3 mt-3">
                            {!! showAd(3) !!}
                        </div>
                    @endif
                    <div class="pb-20"></div><!-- Space -->

                    <div class="project-gallery">
                        <h3 class="mb-20"> {{ $keywords['Project Gallery Images'] ?? __('Project Gallery Images') }} </h3>
                        <div class="row masonry-gallery grid gallery-popup">
                            <div class="col-lg-4 col-md-6 grid-sizer"></div>
                            @foreach ($galleryImages as $gallery)
                                <div class="col-lg-4 col-md-6 grid-item mb-30">
                                    <div class="card radius-md">
                                        <a href="{{ asset('assets/img/project/gallery-images/' . $gallery->image) }}"
                                            class="card-img">
                                            <img
                                                src="{{ asset('assets/img/project/gallery-images/' . $gallery->image) }}">
                                        </a>
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    </div>
                    @if (!empty(showAd(3)))
                        <div class="text-center mb-3 mt-3">
                            {!! showAd(3) !!}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- share on social media modal --}}
    <x-tenant.frontend.social-share />

    <div class="modal fade" id="messages" tabindex="-1" role="dialog" aria-labelledby="messagesModalTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLongTitle">
                        {{ $keywords['Contact for Project'] ?? __('Contact for this project') }}
                    </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('frontend.project.contact', getParam()) }}" method="POST">
                        @csrf
                        @if (!empty($project->agent))
                            <input type="hidden" name="user_id" value="{{ $project->agent->user_id }}">
                            <input type="hidden" name="agent_id"
                                value="{{ !empty($project->agent) ? $project->agent->id : '' }}">
                        @else
                            <input type="hidden" name="user_id" value="{{ $project->user->id }}">
                        @endif
                        <input type="hidden" name="project_id" value="{{ $project->id }}">
                        <x-tenant.frontend.agentContact />
                    </form>
                </div>
            </div>
        </div>
    </div>


@endsection
