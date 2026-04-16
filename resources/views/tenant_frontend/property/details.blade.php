 @extends('tenant_frontend.layout')

 @section('pageHeading')
     {{ $propertyContent->title }}
 @endsection

 @section('metaKeywords')
     @if ($propertyContent)
         {{ $propertyContent->meta_keyword }}
     @endif
 @endsection

 @section('metaDescription')
     @if ($propertyContent)
         {{ $propertyContent->meta_description }}
     @endif
 @endsection

 @section('og:tag')
     <meta property="og:title" content="{{ $propertyContent->title }}">
     <meta property="og:image" content="{{ asset('assets/img/property/featureds/' . $propertyContent->featured_image) }}">
     <meta property="og:url"
         content="{{ route('frontend.property.details', [getParam(), 'slug' => $propertyContent->slug]) }}">
 @endsection

 @section('content')
     <div class="product-single pt-100 pb-70 border-top header-next">
         <div class="container">
             <div class="row gx-xl-5">
                 <div class="col-lg-9 col-xl-9">
                     <div class="product-single-gallery mb-40">
                         <div class="slider-navigation">
                             <button type="button" title="Slide prev" class="slider-btn slider-btn-prev">
                                 <i class="fal fa-angle-left"></i>
                             </button>
                             <button type="button" title="Slide next" class="slider-btn slider-btn-next">
                                 <i class="fal fa-angle-right"></i>
                             </button>
                         </div>
                         <div class="swiper product-single-slider">
                             <div class="swiper-wrapper">
                                 @foreach ($sliders as $slider)
                                     <div class="swiper-slide">
                                         <figure class="radius-lg lazy-container ratio ratio-16-11">
                                             <a href="{{ asset('assets/img/property/slider-images/' . $slider->image) }}"
                                                 class="lightbox-single">
                                                 <img class="lazyload" src="assets/images/placeholder.png"
                                                     data-src="{{ asset('assets/img/property/slider-images/' . $slider->image) }}">
                                             </a>
                                         </figure>
                                     </div>
                                 @endforeach

                             </div>
                         </div>

                         <div class="swiper slider-thumbnails">
                             <div class="swiper-wrapper">
                                 @foreach ($sliders as $slider)
                                     <div class="swiper-slide">
                                         <div class="thumbnail-img lazy-container radius-md ratio ratio-16-11">
                                             <img class="lazyload" src="assets/images/placeholder.png"
                                                 data-src="{{ asset('assets/img/property/slider-images/' . $slider->image) }}">
                                         </div>
                                     </div>
                                 @endforeach
                             </div>
                         </div>
                     </div>
                     <div class="product-single-details">
                         <div class="row">
                             <div class="col-md-8">
                                 <div class="d-flex align-items-center justify-content-between mb-10">
                                     <span class="product-category text-sm"> <a
                                             href="{{ route('frontend.properties', [getParam(), 'category' => $propertyContent->categoryContent?->slug]) }}">
                                             {{ $propertyContent->categoryContent?->name }}</a></span>
                                 </div>
                                 <h3 class="product-title">
                                     <a href="javascript:void(0);">{{ $propertyContent->title }}</a>
                                 </h3>
                                 <div class="product-location icon-start">
                                     <i class="fal fa-map-marker-alt"></i>
                                     <span>
                                         {{ $propertyContent->address }}
                                     </span>
                                     <span>
                                         {{ $propertyContent->property->city?->getContent($adminLang->id)?->name }}
                                         {{ $propertyContent->property->isStateActive ? ', ' . $propertyContent->property->state?->getContent($adminLang->id)?->name : '' }}
                                         {{ $propertyContent->property->isCountryActive ? ', ' . $propertyContent->property->country?->getContent($adminLang->id)?->name : '' }}
                                     </span>
                                 </div>
                                 <ul class="product-info p-0 list-unstyled d-flex flex-column gap-2 mt-10 mb-30">
                                     <li class="icon-start" data-tooltip="tooltip" data-bs-placement="top"
                                         title="{{ __('Fuel type') }}">
                                         <i class="fal fa-gas-pump"></i>
                                         <span><strong>{{ $keywords['Fuel'] ?? __('Fuel type') }}:</strong> {{ $propertyContent->area }}</span>
                                     </li>
                                     @if (!empty($propertyContent->color))
                                         <li class="icon-start" data-tooltip="tooltip" data-bs-placement="top"
                                             title="{{ $keywords['Color'] ?? __('Color') }}">
                                             <i class="fal fa-fill-drip"></i>
                                             <span><strong>{{ $keywords['Color'] ?? __('Color') }}:</strong> {{ $propertyContent->color }}</span>
                                         </li>
                                     @endif
                                     @if (!empty($propertyContent->traction))
                                         <li class="icon-start" data-tooltip="tooltip" data-bs-placement="top"
                                             title="{{ $keywords['Traction'] ?? __('Traction') }}">
                                             <i class="fal fa-road"></i>
                                             <span><strong>{{ $keywords['Traction'] ?? __('Traction') }}:</strong> {{ $propertyContent->traction }}</span>
                                         </li>
                                     @endif
                                     @if (!empty($propertyContent->engine))
                                         <li class="icon-start" data-tooltip="tooltip" data-bs-placement="top"
                                             title="{{ $keywords['Engine'] ?? __('Engine') }}">
                                             <i class="fal fa-cogs"></i>
                                             <span><strong>{{ $keywords['Engine'] ?? __('Engine') }}:</strong> {{ $propertyContent->engine }}</span>
                                         </li>
                                     @endif
                                     @if (!empty($propertyContent->transmision))
                                         <li class="icon-start" data-tooltip="tooltip" data-bs-placement="top"
                                             title="{{ $keywords['Transmission'] ?? __('Transmission') }}">
                                             <i class="fal fa-cog"></i>
                                             <span><strong>{{ $keywords['Transmission'] ?? __('Transmission') }}:</strong> {{ $propertyContent->transmision }}</span>
                                         </li>
                                     @endif
                                     @if (!empty($propertyContent->km) || $propertyContent->km === 0)
                                         <li class="icon-start" data-tooltip="tooltip" data-bs-placement="top"
                                             title="{{ $keywords['Km'] ?? __('Km') }}">
                                             <i class="fal fa-tachometer-alt"></i>
                                             <span><strong>{{ $keywords['Km'] ?? __('Km') }}:</strong> {{ is_numeric($propertyContent->km) ? number_format($propertyContent->km) . ' km' : $propertyContent->km }}</span>
                                         </li>
                                     @endif
                                    {{--
                                    @if ($propertyContent->type == 'residential')
                                         <li class="icon-start" data-tooltip="tooltip" data-bs-placement="top"
                                             title="{{ $keywords['Beds'] ?? __('Beds') }}">
                                             <i class="fal fa-bed"></i>
                                             <span>{{ $propertyContent->beds }}
                                                 {{ $keywords['Beds'] ?? __('Beds') }}</span>
                                         </li>
                                         <li class="icon-start" data-tooltip="tooltip" data-bs-placement="top"
                                             title="{{ $keywords['Baths'] ?? __('Baths') }}">
                                             <i class="fal fa-bath"></i>
                                             <span>{{ $propertyContent->bath }}
                                                 {{ $keywords['Baths'] ?? __('Baths') }}</span>
                                         </li>
                                    @endif
                                    --}}
                                 </ul>
                             </div>
                             @php
                                 $symbol = $propertyContent->currency_symbol ?? '';
                                 $price = $propertyContent->price
                                     ? $propertyContent->price
                                     : $keywords['Negotiable'] ?? __('Negotiable');

                                 if (is_numeric($propertyContent->price)) {
                                     $formattedPrice = propertySymbol($propertyContent->price , $symbol);
                                 } else {
                                     $formattedPrice = $price;
                                 }
                             @endphp
                             <div class="col-md-4">
                                 <div class="product-price mb-10">
                                     <span class="new-price">{{ $keywords['Price'] ?? __('Price') }}:
                                         {{ $formattedPrice }}</span>
                                 </div>
                                 @if (!empty($agent))
                                     <a
                                         href="{{ route('frontend.agent.details', [getParam(), 'agentusername' => $agent->username]) }}">
                                     @else
                                         <a
                                             href="{{ route('frontend.agent.details', [getParam(), 'agentusername' => $user->username, 'admin' => 'true']) }}">
                                 @endif
                                 <div class="user mb-20">
                                     <div class="user-img">

                                         <div class="lazy-container ratio ratio-1-1 rounded-pill">
                                             <img class="lazyload" src="{{ asset('assets/img/blank-user.jpg') }}"
                                                 data-src="@if (!empty($propertyContent->$agent)) {{ $agent->image }}
                                            @elseif(!empty($user))
                                                {{ !empty($user->photo) ? asset($user->photo) : asset('assets/img/blank-user.jpg') }} @endif">


                                         </div>
                                     </div>
                                     <div class="user-info">
                                         <h5 class="m-0">

                                             @if (!empty($agent))
                                                 {{ $agent->agentInfo?->full_name }}
                                             @elseif(!empty($user))
                                                 {{ $user->first_name . ' ' . $user->last_name }}
                                             @endif
                                         </h5>

                                     </div>
                                 </div>
                                 </a>

                                 <ul class="share-link list-unstyled mb-30">
                                     <li>
                                         <a class="btn blue" href="#" data-bs-toggle="modal"
                                             data-bs-target="#socialMediaModal">
                                             <i class="far fa-share-alt"></i>
                                         </a>
                                         <span>{{ $keywords['Share'] ?? __('Share') }}</span>

                                     </li>


                                     <li>
                                         @if (Auth::guard('customer')->check())
                                             @php
                                                 $user_id = Auth::guard('customer')->user()->id;
                                                 $checkWishList = checkWishList($propertyContent->propertyId, $user_id);
                                             @endphp
                                         @else
                                             @php
                                                 $checkWishList = false;
                                             @endphp
                                         @endif
                                         <a href="{{ $checkWishList == false ? route('frontend.user.property.addto.wishlist', [getParam(), 'property' => $propertyContent->propertyId]) : route('frontend.user.property.remove.wishlist', [getParam(), 'property' => $propertyContent->propertyId]) }}"
                                             class="btn red " data-tooltip="tooltip" data-bs-placement="top"
                                             title="{{ $checkWishList == false ? __('Add to Wishlist') : __('Saved') }}">

                                             @if ($checkWishList == false)
                                                 <i class="fal fa-heart"></i>
                                             @else
                                                 <i class="fas fa-heart"></i>
                                             @endif
                                         </a>
                                         <span>{{ $checkWishList == false ? __('Save') : __('Saved') }}</span>

                                     </li>

                                 </ul>
                             </div>
                         </div>
                         <div class="mb-20"></div>
                         <div class="product-desc mb-40">
                             <h3 class="mb-20">{{ $keywords['Property Description'] ?? __('Property Description') }}</h3>
                             <p class=" summernote-content">{!! $propertyContent->description !!}</p>
                         </div>
                         @if (!empty(showAd(3)))
                             <div class="text-center mb-3 mt-3">
                                 {!! showAd(3) !!}
                             </div>
                         @endif

                         @if (count($propertyContent->propertySpacifications) > 0)
                             <div class="row" class="mb-20">
                                 <div class="col-12">
                                     <h3 class="mb-20"> {{ $keywords['Features'] ?? __('Features') }}</h3>
                                 </div>

                                 @foreach ($propertyContent->propertySpacifications as $specification)
                                     @php

                                         $ps_content = $specification->getContent($language->id);
                                     @endphp

                                     <div class="col-lg-3 col-sm-6 col-md-4 mb-20">
                                         <strong class="mb-1 text-dark d-block">{{ $ps_content?->label }}</strong>
                                         <span>{{ $ps_content?->value }}</span>
                                     </div>
                                 @endforeach
                             </div>
                             <div class="pb-20"></div>
                         @endif

                         <div class="product-featured mb-40">
                             <h3 class="mb-20">{{ $keywords['Amenities'] ?? __('Amenities') }}</h3>
                             <ul class="featured-list list-unstyled p-0 m-0">
                                 @foreach ($amenities as $amenity)
                                     <li class="d-inline-block icon-start">
                                         <i class="{{ $amenity->amenity->icon }}"></i>
                                         <span>{{ $amenity->amenityContent?->name }}</span>
                                     </li>
                                 @endforeach

                             </ul>
                         </div>
                         @if (!empty($propertyContent->video_url))
                             <div class="product-video mb-40">
                                 <h3 class="mb-20"> {{ $keywords['Video'] ?? __('Video') }}</h3>
                                 <div class="lazy-container radius-lg ratio ratio-16-11">
                                     <img class="lazyload" src="{{ asset('assets/front/images/placeholder.png') }}"
                                         data-src="{{ $propertyContent->video_image ? asset('assets/img/property/video/' . $propertyContent->video_image) : asset('assets/front/images/placeholder.png') }}">
                                     <a href="{{ $propertyContent->video_url }}"
                                         class="video-btn youtube-popup p-absolute">
                                         <i class="fas fa-play"></i>
                                     </a>
                                 </div>
                             </div>
                         @endif
                         @if (!empty($propertyContent->floor_planning_image))
                             <div class="product-planning mb-40">
                                 <h3 class="mb-20">{{ $keywords['Floor Planning'] ?? __('Floor Planning') }}</h3>
                                 <div class="lazy-container radius-lg ratio ratio-16-11 border">
                                     <img class="lazyload" src="assets/images/placeholder.png"
                                         data-src="{{ asset('assets/img/property/plannings/' . $propertyContent->floor_planning_image) }}">
                                 </div>
                             </div>
                         @endif
                         @if (!empty($propertyContent->latitude) && !empty($propertyContent->longitude))
                             <div class="product-location mb-40">
                                 <h3 class="mb-20">{{ $keywords['Location'] ?? __('Location') }}</h3>
                                 <div class="map-wrapper" style="border-radius:10px; overflow:hidden;">
                                     <div id="map"></div>
                                 </div>
                             </div>
                         @endif
                         @if (!empty(showAd(3)))
                             <div class="text-center mb-3 mt-3">
                                 {!! showAd(3) !!}
                             </div>
                         @endif
                     </div>
                 </div>

                 <div class="col-lg-3 col-xl-3">
                     <aside class="sidebar-widget-area mb-10" data-aos="fade-up">
                         @if ((!empty($agent) && $agent->show_contact_form) || (empty($agent) && !empty($user) && $user->show_contact_form))
                             <div class="widget widget-form radius-md mb-30">
                                 <div class="user mb-20">
                                     <div class="user-img">
                                         <div class="lazy-container ratio ratio-1-1 rounded-pill">
                                             @if (!empty($agent))
                                                 <a
                                                     href="{{ route('frontend.agent.details', [getParam(), 'agentusername' => $agent->username]) }}">

                                                     <img class="lazyload" src="{{ asset($agent->image) }}">
                                                 </a>
                                             @else
                                                 <a href="{{ route('frontend.tenant.details', [getParam()]) }}">

                                                     <img class="lazyload" src="{{ asset($user->photo) }}">
                                                 </a>
                                             @endif

                                         </div>
                                     </div>
                                     <div class="user-info">
                                         <h4 class="mb-0">
                                             <a @if (!empty($agent)) href="{{ route('frontend.agent.details', [getParam(), 'agentusername' => $agent->username]) }}"> {{ $agent->agentInfo?->full_name }}

                                            @else
                                              href="{{ route('frontend.tenant.details', [getParam()]) }}">   {{ $user->first_name . ' ' . $user->last_name }} @endif
                                                 </a>
                                         </h4>

                                         <a class="d-block"
                                             href="tel:@if (!empty($agent) && $agent->show_phone_number) {{ $agent->phone }}
                                         @elseif (empty($agent) && $user->show_phone_number && !empty($user->phone))
                                            {{ $user->phone }} @endif
                                        ">
                                             @if (!empty($agent) && $agent->show_phone_number)
                                                 {{ $agent->phone }}
                                             @elseif (empty($agent) && $user->show_phone_number && !empty($user->phone))
                                                 {{ $user->phone }}
                                             @endif
                                         </a>


                                         <a
                                             href="mailto:@if (!empty($agent) && $agent->show_email_addresss) {{ $agent->email }}
                                         @elseif (empty($agent) && $user->show_email_addresss && !empty($user->email))
                                            {{ $user->email }} @endif">
                                             @if (!empty($agent) && $agent->show_email_addresss)
                                                 {{ $agent->email }}
                                             @elseif (empty($agent) && $user->show_email_addresss && !empty($user->email))
                                                 {{ $user->email }}
                                             @endif
                                         </a>
                                     </div>
                                 </div>

                                 <form action="{{ route('frontend.property_contact', getParam()) }}" method="POST">
                                     @csrf
                                     @if (!empty($agent))
                                         <input type="hidden" name="user_id" value="{{ $agent->user_id }}">
                                         <input type="hidden" name="agent_id"
                                             value="{{ !empty($agent) ? $agent->id : '' }}">
                                     @else
                                         <input type="hidden" name="user_id" value="{{ $user->id }}">
                                     @endif
                                     <input type="hidden" name="property_id"
                                         value="{{ $propertyContent->propertyId }}">

                                     <x-tenant.frontend.agentContact />
                                 </form>
                             </div>
                         @endif

                         @if ($relatedProperty->count() > 0)
                             <div class="widget widget-recent radius-md mb-30 ">
                                 <h3 class="title">
                                     <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                         data-bs-target="#products" aria-expanded="true" aria-controls="products">
                                         {{ $keywords['Related Property'] ?? __('Related Property') }}
                                     </button>
                                 </h3>
                                 <div id="products" class="collapse show">
                                     <div class="accordion-body p-0">
                                         @foreach ($relatedProperty as $property)
                                             @php
                                                 $symbol = $property->currency_symbol ?? '';
                                                 $price = $property->price
                                                     ? $property->price
                                                     : $keywords['Negotiable'] ?? __('Negotiable');

                                                 if (is_numeric($property->price)) {
                                                     $formattedPrice = $symbol . $price;
                                                 } else {
                                                     $formattedPrice = $price;
                                                 }
                                             @endphp

                                             <div class="product-default product-inline mt-20">
                                                 <figure class="product-img">
                                                     <a href="{{ route('frontend.property.details', $property->slug) }}"
                                                         class="lazy-container ratio ratio-1-1 radius-md">
                                                         <img class="lazyload" src="assets/images/placeholder.png"
                                                             data-src="{{ asset('assets/img/property/featureds/' . $property->featured_image) }}">
                                                     </a>
                                                 </figure>
                                                 <div class="product-details">
                                                     <h6 class="product-title"><a
                                                             href="{{ route('frontend.property.details', [getParam(), 'slug' => $property->slug]) }}">{{ $property->title }}</a>
                                                     </h6>
                                                     <span class="product-location icon-start"> <i
                                                             class="fal fa-map-marker-alt"></i>
                                                         {{ $property->city->getContent($property->language_id)?->name }}
                                                         {{ $property->isStateActive ? ', ' . $property->state?->getContent($property->language_id)?->name : '' }}
                                                         {{ $property->isCountryActive ? ', ' . $property->country?->getContent($property->language_id)?->name : '' }}</span>
                                                     <div class="product-price">

                                                         <span
                                                             class="new-price">{{ ($keywords['Price'] ?? __('Price')) . ':' }}
                                                             {{ $formattedPrice }}</span>
                                                     </div>
                                                     <ul class="product-info p-0 list-unstyled d-flex align-items-center">
                                                         <li class="icon-start" data-tooltip="tooltip"
                                                             data-bs-placement="top"
                                                             title="{{ $keywords['Area'] ?? __('Area') }}">
                                                             <i class="fal fa-vector-square"></i>
                                                             <span>{{ $property->area }}</span>
                                                         </li>
                                                         @if ($property->type == 'residential')
                                                             <li class="icon-start" data-tooltip="tooltip"
                                                                 data-bs-placement="top"
                                                                 title="{{ $keywords['Bed'] ?? __('Bed') }}">
                                                                 <i class="fal fa-bed"></i>
                                                                 <span>{{ $property->beds }} </span>
                                                             </li>
                                                             <li class="icon-start" data-tooltip="tooltip"
                                                                 data-bs-placement="top"
                                                                 title="{{ $keywords['Bath'] ?? __('Bath') }}">
                                                                 <i class="fal fa-bath"></i>
                                                                 <span>{{ $property->bath }} </span>
                                                             </li>
                                                         @endif

                                                     </ul>
                                                 </div>
                                             </div>
                                         @endforeach
                                     </div>
                                 </div>
                             </div>
                         @endif
                         @if (!empty(showAd(2)))
                             <div class="text-center mb-3 mt-3">
                                 {!! showAd(2) !!}
                             </div>
                         @endif
                         @if (!empty(showAd(1)))
                             <div class="text-center mb-3 mt-3">
                                 {!! showAd(1) !!}
                             </div>
                         @endif
                     </aside>
                 </div>

             </div>
         </div>
     </div>


     <x-tenant.frontend.social-share />
 @endsection
 @section('script')
     <script>
         'use strict';
         var google_map_type = "{{ $basicInfo->google_map_type }}";
         var google_map_radius = "{{ $basicInfo->google_map_radius }}";
         var google_map_apiKey = "{{ $basicInfo->google_map_api_key }}";
         var website_title = "{{ $basicInfo->website_title }}";
     </script>

     <script>
         function loadGoogleMap(callback) {
             const script = document.createElement('script');
             script.src = `https://maps.googleapis.com/maps/api/js?key=${google_map_apiKey}&callback=${callback.name}`;
             script.async = true;
             script.defer = true;
             document.head.appendChild(script);
         }

         function initMap() {
             const location = {
                 lat: parseFloat("{{ $propertyContent->latitude }}"),
                 lng: parseFloat("{{ $propertyContent->longitude }}")
             };

             //initialize map
             const map = new google.maps.Map(document.getElementById("map"), {
                 center: location,
                 zoom: 18,
                 mapTypeId: "roadmap",

                 zoomControl: true,
                 mapTypeControl: true,
                 streetViewControl: true,
                 rotateControl: true,
                 fullscreenControl: true,
             });

             if (google_map_type === 'exact_location') {
                 //create marker
                 const marker = new google.maps.Marker({
                     position: location,
                     map: map,
                     title: "Exact Property Location",
                     label: {
                         color: "white",
                         fontSize: "14px",
                         fontWeight: "bold"
                     },
                     icon: "https://img.icons8.com/color/48/000000/marker.png"
                 });
                 marker.setAnimation(google.maps.Animation.BOUNCE);

                 const infoWindow = new google.maps.InfoWindow({
                     content: `
                <div style="font-size:14px; padding:10px; max-width:250px; border-radius:8px;">
                    <h4>${website_title}</h4>
                    <i class="fas fa-home" style="color:#FF0000; margin-right:5px;"></i>
                    <strong>Property Location</strong><br>
                    ${@json($propertyContent->address ?? 'Exact Location')}
                </div>
            `
                 });

                 marker.addListener("click", () => infoWindow.open(map, marker));

             } else if (google_map_type === 'approximate_area') {
                 //  console.log(location, google_map_radius);
                 let radius = google_map_radius ? parseInt(google_map_radius) : 40;
                 let growing = true;

                 const circle = new google.maps.Circle({
                     strokeColor: "#FF0000",
                     strokeOpacity: 0.8,
                     strokeWeight: 2,
                     fillColor: "#FF0000",
                     fillOpacity: 0.35,
                     map,
                     center: location,
                     radius: radius
                 });

                 // Animate circle (pulse effect)
                 setInterval(() => {
                     if (growing) {
                         radius += 1;
                         if (radius >= (parseInt(google_map_radius) + 5)) growing = false;
                     } else {
                         radius -= 1;
                         if (radius <= (parseInt(google_map_radius) - 5)) growing = true;
                     }
                     circle.setRadius(radius);
                 }, 100);
             }
         }

         try {
             loadGoogleMap(initMap);
         } catch (e) {
             console.warn("Google Map load blocked or failed:", e);
         }
     </script>
 @endsection
