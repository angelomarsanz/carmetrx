 @extends('tenant_frontend.layout')

 @section('pageHeading')
     {{ $agent->username }}
 @endsection
 @section('metaKeywords')
     {{ $agent->username }}, {{ @$agentInfo->full_name }}
 @endsection

 @section('metaDescription')
     {{ @$agentInfo->details }}
 @endsection

 @section('content')
     @includeIf('tenant_frontend.partials.breadcrumb', [
         'breadcrumb' => $breadcrumb,
         'title' => !empty(@$agentInfo->full_name)
             ? @$agentInfo->full_name
             : $keywords['Agent Details'] ?? __('Agent Details'),
         'subtitle' => $keywords['Agent Details'] ?? __('Agent Details'),
     ])

     <div class="agent-single pt-100 pb-70">
         <div class="container">
             <div class="row gx-xl-5">
                 <div class="col-lg-9">
                     <div class="row agent-single-box align-items-center mb-10 gx-xl-5" data-aos="fade-up">
                         <figure class="agent-img col-lg-6 mb-30">
                             <a href="#" class="lazy-container radius-md ratio ratio-1-1">

                                 <img class="lazyload" data-src="{{ asset($agent->image) }}">


                             </a>
                         </figure>
                         <div class="agent-details col-lg-6 mb-30">
                             <span class="label radius-sm">{{ $agent->username }}</span>
                             <div class="mb-15"></div>
                             <h2 class="agent-title m-0">
                                 {{ @$agentInfo->first_name . ' ' . @$agentInfo->last_name }}</h2>


                             <ul class="agent-info list-unstyled p-0">
                                 @if ($agent->show_phone_number == 1 && !is_null($agent->phone))
                                     <li class="icon-start">
                                         <div>
                                             <i class="fal fa-mobile-android"></i>
                                             <span>{{ ($keywords['Phone'] ?? __('Phone')) . ':' }}</span>
                                         </div>
                                         <div> <a href="tel:{{ $agent->phone }}">{{ $agent->phone }}</a>
                                         </div>
                                     </li>
                                 @endif

                                 @if ($agent->show_email_addresss == 1 && !is_null($agent->email))
                                     <li class="icon-start">
                                         <div>
                                             <i class="fal fa-envelope"></i>
                                             <span>{{ ($keywords['Email'] ?? __('Email')) . ':' }}</span>
                                         </div>
                                         <div>
                                             {{ $agent->email }}

                                         </div>

                                     </li>
                                 @endif

                                 @if (!is_null(@$agentInfo->city))
                                     <li class="icon-start">
                                         <div>
                                             <i class="fal fa-map-marker-alt"></i>
                                             <span>{{ ($keywords['City'] ?? __('City')) . ':' }}</span>
                                         </div>
                                         <div>
                                             {{ @$agentInfo->city }}
                                         </div>
                                     </li>
                                 @endif

                                 @if (!is_null(@$agentInfo->state))
                                     <li class="icon-start">
                                         <div>
                                             <i class="fal fa-map-marker-alt"></i>
                                             <span>{{ ($keywords['State'] ?? __('State')) . ':' }}</span>
                                         </div>
                                         <div>
                                             {{ @$agentInfo->state }}
                                         </div>
                                     </li>
                                 @endif

                                 @if (!is_null(@$agentInfo->country))
                                     <li class="icon-start">
                                         <div>
                                             <i class="fal fa-map-marker-alt"></i>
                                             <span>{{ ($keywords['Country'] ?? __('Country')) . ':' }}</span>
                                         </div>
                                         <div>
                                             {{ @$agentInfo->country }}
                                         </div>
                                     </li>
                                 @endif

                                 @if (!is_null(@$agentInfo->address))
                                     <li class="icon-start text-break">
                                         <div>
                                             <i class="fal fa-map-marker-alt"></i>
                                             <span>{{ ($keywords['Address'] ?? __('Address')) . ' : ' }}</span>
                                         </div>
                                         <div>
                                             {{ @$agentInfo->address }}
                                         </div>

                                     </li>
                                 @endif

                                 <li class="icon-start">
                                     <div>
                                         <i class="fal fa-calendar-day"></i>
                                         <span>{{ ($keywords['Member since'] ?? __('Member since')) . ':' }}</span>
                                     </div>
                                     <div>
                                         {{ $agent->member_since }}
                                     </div>
                                 </li>
                             </ul>

                             <div class="d-flex flex-wrap lign-items-center mt-20 gap-15">
                                 @if ($agent->show_phone_number == 1 && !is_null($agent->email))
                                     <a href="mailTo:{{ $agent->email }}"
                                         class="btn btn-lg btn-primary">{{ $keywords['Send Email'] ?? __('Send Email') }}</a>
                                 @endif
                                 @if ($agent->show_email_addresss == 1 && !is_null($agent->phone))
                                     <a href="tel:{{ $agent->phone }}"
                                         class="btn btn-lg btn-outline">{{ $keywords['Call Now'] ?? __('Call Now') }}</a>
                                 @endif
                             </div>
                         </div>
                     </div>
                     <div class="agent-single-details">
                         @if (!is_null(@$agentInfo->details))
                             <div class="mb-20"></div>
                             <div class="agent-desc mb-40">
                                 <h3 class="mb-20">{{ $keywords['About'] ?? __('About') }}</h3>
                                 <p>
                                     {!! $agentInfo->details !!}
                                 </p>
                             </div>
                         @endif
                         @if ($agent->propertyCount() > 0)
                             <div class="agent-listing mb-40">
                                 <h3 class="mb-20">
                                     {{ ($keywords['My Properties'] ?? __('My Properties')) . ' (' . $agent->propertyCount() . ')' }}
                                 </h3>

                                 <div class="row ">
                                     <div class="col-lg-12">

                                         <div class="tabs-navigation tabs-navigation-2 mb-20">
                                             <ul class="nav nav-tabs">
                                                 <li class="nav-item">
                                                     <button class="nav-link active btn-md" data-bs-toggle="tab"
                                                         data-bs-target="#tab_all"
                                                         type="button">{{ $keywords['All Properties'] ?? __('All Properties') }}</button>
                                                 </li>

                                                 @foreach ($categories as $category)
                                                     @if ($category->properties()->count() > 0 && $category->categoryContent)
                                                         <li class="nav-item">
                                                             <button class="nav-link btn-md" data-bs-toggle="tab"
                                                                 data-bs-target="#tab_{{ $category->id }}"
                                                                 type="button">{{ $category->categoryContent?->name }}</button>
                                                         </li>
                                                     @endif
                                                 @endforeach
                                             </ul>
                                         </div>
                                         <div class="tab-content" data-aos="fade-up">
                                             <div class="tab-pane fade show active" id="tab_all">
                                                 <div class="row">
                                                     @if (count($all_properties) > 0)
                                                         @foreach ($all_properties as $property)
                                                             @if ($property)
                                                                 <x-tenant.frontend.property :adminLang="$adminLang" :property="$property"
                                                                     class="col-lg-4 col-md-6" />
                                                             @endif
                                                         @endforeach
                                                     @else
                                                         <h4 class="text-center mt-4 mb-4">
                                                             {{ $keywords['No Property Found'] ?? __('No Property Found') }}
                                                         </h4>
                                                     @endif
                                                 </div>
                                             </div>

                                             @foreach ($categories as $category)
                                                 <div class="tab-pane fade" id="tab_{{ $category->id }}">

                                                     <div class="row">
                                                         @forelse ($all_properties as $property)
                                                             @if ($property->category_id == $category->id)
                                                                 <x-tenant.frontend.property :adminLang="$adminLang" :property="$property"
                                                                     class="col-lg-4 col-md-6" />
                                                             @endif
                                                         @empty
                                                             <div class="col-12 text-center">
                                                                 <h3>{{ __('No Properties Found') }}</h3>
                                                             </div>
                                                         @endforelse
                                                     </div>
                                                 </div>
                                             @endforeach
                                         </div>
                                     </div>

                                 </div>

                             </div>
                         @endif
                         @if ($agent->projectCount() > 0)
                             <div class="agent-listing projects-area mb-40">
                                 <h3 class="mb-20">
                                     {{ ($keywords['My Projects'] ?? __('My Projects')) . ' (' . $agent->projectCount() . ')' }}
                                 </h3>
                                 <div class="row">
                                     @forelse ($all_projects as $project)
                                         <x-tenant.frontend.project :project="$project" class="col-lg-4 col-md-6"
                                             data-aos="fade-up" data-aos-delay="100" />
                                     @empty
                                         <div class="col-lg-12">
                                             <h3 class="text-center mt-5">
                                                 {{ $keywords['No Project Found'] ?? __('No Project Found') }}</h3>
                                         </div>
                                     @endforelse
                                 </div>
                             </div>
                         @endif
                     </div>

                 </div>
                 <div class="col-lg-3">
                     <aside class="sidebar-widget-area" data-aos="fade-up">
                         @if ($agent->show_contact_form == 1)
                             <div class="widget widget-form radius-md mb-30">
                                 <div class="user mb-20">
                                     <div class="user-img">
                                         <div class="lazy-container ratio ratio-1-1 rounded-pill">
                                             <img class="lazyload" src="{{ asset($agent->image) }}">
                                         </div>
                                     </div>
                                     <div class="user-info">
                                         <h5 class="m-0">
                                             {{ $agentInfo?->full_name }}
                                         </h5>
                                         <a class="d-block" href="tel:{{ $agent->phone }}"> {{ $agent->phone }}</a>
                                         <a href="mailto:{{ $agent->email }}"> {{ $agent->email }} </a>
                                     </div>
                                 </div>
                                 <form action="{{ route('frontend.contact_agent', getParam()) }}" method="POST">
                                     @csrf
                                     <input type="hidden" name="agent_id" value="{{ $agent->id }}">
                                     <x-tenant.frontend.agentContact />


                                 </form>
                             </div>
                         @endif
                         @if (!empty(showAd(1)))
                             <div class="text-center mb-40">
                                 {!! showAd(1) !!}
                             </div>
                         @endif


                     </aside>
                 </div>
             </div>
         </div>
     </div>
 @endsection
