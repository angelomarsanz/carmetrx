 <div class="d-flex {{ $attributes->get('class') }}">
     <div class="product-default radius-md mb-30 flex-fill  flex-column" {{ $animation ? 'data-aos="fade-up" data-aos-delay="100"' : '' }}>
         <figure class="product-img">
             <a href="{{ route('frontend.property.details', [getParam(), 'slug' => $property->slug ?? $property->propertyContent->slug]) }}"
                 class="lazy-container ratio ratio-1-1">
                 <img class="lazyload" src="assets/images/placeholder.png"
                     data-src="{{ asset('assets/img/property/featureds/' . $property->featured_image) }}">
             </a>
         </figure>
         <div class="product-details">
             <div class="d-flex align-items-center justify-content-between mb-10">
                        <div class="author">
                            @if ($property->authorIsAgent && $property->agent)
                                <a class="color-medium"
                                   href="{{ route('frontend.agent.details', [getParam(), 'agentusername' => $property->agent->username]) }}"
                                   target="_self">
                                    <img class="blur-up ls-is-cached lazyloaded" src="{{ asset(@$property->agent->image) }}">
                                    <span>{{ $keywords['By'] ?? __('By') }} {{ $property->agent->username }}</span>
                                </a>
                            @else
                                <a class="color-medium"
                                   href="{{ route('frontend.tenant.details', [getParam()]) }}"
                                   target="_self">
                                    <img class="blur-up ls-is-cached lazyloaded" src="{{ asset($property->user->photo) }}">
                                    <span>{{ $keywords['By'] ?? __('By') }} {{ $property->user->username }}</span>
                                </a>
                            @endif
                        </div>

                 <a class="product-category text-sm"
                     href="{{ route('frontend.properties', [getParam(), 'type' => $property->type]) }}">{{ $keywords[ucfirst($property->type)] ?? __(ucfirst($property->type)) }}</a>
             </div>
             <h3 class="product-title">
                 <a
                     href="{{ route('frontend.property.details', [getParam(), 'slug' => $property->slug ?? $property->propertyContent->slug]) }}">{{ $property->title ?? $property->propertyContent->title }}</a>
             </h3>

             <span class="product-location icon-start"> <i class="fal fa-map-marker-alt"></i>

                 {{ $property->city?->getContent($adminLang->id)?->name }}
                 {{ $property->isStateActive ? ', ' . $property->state->getContent($adminLang->id)?->name : '' }}
                 {{ $property->isCountryActive ? ', ' . $property->country?->getContent($adminLang->id)?->name : '' }}
             </span>
             <div class="product-price">
                 <span class="new-price">{{ ($keywords['Price'] ?? __('Price')) . ':' }}
                     @if (!empty($property->price))
                         {{ propertySymbol($property->price,$property->currency_symbol) }}
                     @else
                         {{ $keywords['Negotiable'] ?? __('Negotiable') }}
                     @endif
                 </span>
             </div>
             <ul class="product-info p-0 list-unstyled d-flex align-items-center">
                 <li class="icon-start" data-tooltip="tooltip" data-bs-placement="top"
                     title="{{ $keywords['Area'] ?? __('Area') }}">
                     <i class="fal fa-vector-square"></i>
                     <span>{{ $property->area }} {{ $keywords['Sqft'] ?? __('Sqft') }}</span>
                 </li>
                 @if ($property->type == 'residential')
                     <li class="icon-start" data-tooltip="tooltip" data-bs-placement="top"
                         title="{{ $keywords['Beds'] ?? __('Beds') }}">
                         <i class="fal fa-bed"></i>
                         <span>{{ $property->beds }} {{ $keywords['Beds'] ?? __('Beds') }}</span>
                     </li>
                     <li class="icon-start" data-tooltip="tooltip" data-bs-placement="top"
                         title="{{ $keywords['Baths'] ?? __('Baths') }}">
                         <i class="fal fa-bath"></i>
                         <span>{{ $property->bath }} {{ $keywords['Baths'] ?? __('Baths') }}</span>
                     </li>
                 @endif
             </ul>
         </div>
         <span class="label">{{ $keywords[ucfirst($property->purpose)] ?? __(ucfirst($property->purpose)) }}</span>
         @if (!empty($permissions) && in_array('User', $permissions))
             @if (Auth::guard('customer')->check())
                 @php
                     $customer_id = Auth::guard('customer')->user()->id;
                     $checkWishList = checkWishList($property->id, $customer_id);
                 @endphp
             @else
                 @php
                     $checkWishList = false;
                 @endphp
             @endif
             <a href="{{ $checkWishList == false ? route('frontend.user.property.addto.wishlist', [getParam(), 'property' => $property->id]) : route('frontend.user.property.remove.wishlist', [getParam(), 'property' => $property->id]) }}"
                 class="btn-wishlist {{ $checkWishList == false ? '' : 'wishlist-active' }}" data-tooltip="tooltip"
                 data-bs-placement="top"
                 title="{{ $checkWishList == false ? $keywords['Add to Wishlist'] : $keywords['Saved'] }}">
                 <i class="fal fa-heart"></i>
             </a>
         @endif
     </div><!-- product-default -->
 </div>
