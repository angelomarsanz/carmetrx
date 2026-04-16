@extends('tenant_frontend.layout')

@section('pageHeading')
    {{ !empty($pageHeading) ? $pageHeading->agents_page_title : $keywords['Agents'] ?? __('Agents') }}
@endsection

@section('metaKeywords')
    @if (!empty($seoInfo))
        {{ $seoInfo->meta_keyword_agents }}
    @endif
@endsection

@section('metaDescription')
    @if (!empty($seoInfo))
        {{ $seoInfo->meta_description_agents }}
    @endif
@endsection

@section('content')
    @includeIf('tenant_frontend.partials.breadcrumb', [
        'breadcrumb' => $breadcrumb,
        'title' => !empty($pageHeading) ? $pageHeading->agents_page_title : $keywords['Team'] ?? __('Team'),
        'subtitle' => $keywords['Team'] ?? __('Team'),
    ])

    <div class="agent-grid pt-100 pb-70">
        <div class="container">
            <div class="row gx-xl-5">
                <div class="col-lg-9">
                    <div class="row">

                        @if ($tenant->show_profile && (!request()->has('name') || !request()->has('type') || !request()->has('location')))
                            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                                <div class="agent-box radius-md mb-30">
                                    <div class="agent-img">
                                        <figure>
                                            <a href="#" class="lazy-container ratio ratio-1-2">
                                                <img class="lazyload" src="{{ asset($tenant->photo) }}"
                                                    data-src="{{ asset($tenant->photo) }}">
                                            </a>
                                        </figure>

                                    </div>
                                    <div class="agent-details text-center">

                                        <span class="color-primary font-sm">{{ $tenant->properties->count() }}
                                            {{ $keywords['Properties'] ?? __('Properties') }}</span> |

                                        <span class="color-primary font-sm">{{ $tenant->projects->count() }}
                                            {{ $keywords['Projects'] ?? __('Projects') }}</span>


                                        <h4 class="agent-title"><a
                                                href="{{ route('frontend.tenant.details', [getParam()]) }}">{{ $tenant->full_name }}</a>
                                        </h4>
                                        <ul class="agent-info list-unstyled p-0">

                                            @if ($tenant->show_phone_number == 1)
                                                @if (!is_null($tenant->phone))
                                                    <li class="icon-start ">
                                                        <a href="tel:{{ $tenant->phone }}"> <i
                                                                class="fal fa-phone-plus"></i>
                                                            {{ $tenant->phone }}</a>
                                                    </li>
                                                @endif
                                            @endif

                                            @if ($tenant->show_email_addresss == 1)
                                                <li class="icon-start font-sm">
                                                    <a href="mailto:{{ $tenant->email }}"> <i class="fal fa-envelope"></i>
                                                        {{ $tenant->email }}</a>
                                                </li>
                                            @endif
                                        </ul>
                                        <a href="{{ route('frontend.tenant.details', [getParam()]) }}"
                                            class="btn-text">{{ $keywords['View Profile'] ?? __('View Profile') }}</a>
                                    </div>
                                </div>
                            </div>
                        @endif



                        @forelse ($agents as $agent)
                            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                                <x-tenant.frontend.agent :$agent />
                            </div>
                        @empty
                            @if ($tenant->show_profile != 1)
                                <h3 class="text-center">
                                    {{ $keywords['No Team Member Found'] ?? __('No Team Member Found') }}</h3>
                            @endif
                        @endforelse
                         
                            {{ $agents->links() }}
 
                    </div>

                    @if (!empty(showAd(3)))
                        <div class="text-center mt-4">
                            {!! showAd(3) !!}
                        </div>
                    @endif
                </div>
                <div class="col-lg-3">
                    <aside class="sidebar-widget-area" data-aos="fade-up">
                        
                        <x-tenant.frontend.agentSearch />
                        @if (!empty(showAd(2)))
                            <div class="text-center mb-40">
                                {!! showAd(2) !!}
                            </div>
                        @endif
                    </aside>
                </div>
            </div>
        </div>
    </div>
@endsection
