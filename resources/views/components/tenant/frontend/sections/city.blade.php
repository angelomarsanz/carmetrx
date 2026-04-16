 <section class="gallery-area pt-100 pb-70">
     <img class="lazyload bg-img"
         src="{{ asset(\App\Constants\Constant::WEBSITE_CITY_SECTION_IMAGE . '/' . $cityBgImg) }}">
     <div class="container">
         <div class="row">
             <div class="col-12">
                 <div class="section-title title-inline mb-40" data-aos="fade-up">
                     <div>
                         <span class="subtitle"><span class="line"></span>
                             {{ $citySecInfo?->city_section_title }}</span>
                         <h2 class="title">{{ $citySecInfo?->city_section_subtitle }}</h2>
                     </div>
                 </div>
             </div>
             <div class="col-12">
                 <div class="row" data-aos="fade-up">
                     @forelse ($cities as $city)
                         <div class="col-lg-4 col-sm-6">
                             <div class="card radius-md mb-30">
                                 <a href="{{ route('frontend.properties', [getParam(), 'category' => $city->slug]) }}">
                                     <div class="card-img">
                                         <div class="lazy-container ratio ratio-16-11">
                                             <img class="lazyload blur-up"
                                                 data-src="{{ asset('assets/img/property-city/' . $city->image) }}">
                                         </div>
                                     </div>
                                     <div class="card-text text-center">
                                         <h5 class="card-title color-white mb-0">{{ $city->name }}</h5>
                                         <span class="font-sm color-white">{{ $city->propertyCount }}
                                             @if ($city->propertyCount > 0)
                                                 {{ $keywords['Properties'] ?? __('Properties') }}
                                             @else
                                                 {{ $keywords['Property'] ?? __('Property') }}
                                             @endif
                                         </span>
                                     </div>
                                 </a>
                             </div>
                         </div>
                     @empty
                         <div class=" p-3 text-center mb-30 w-100">
                             <h3 class="mb-0"> {{ $keywords['No City Found'] ?? __('No City Found') }}</h3>
                         </div>
                     @endforelse
                 </div>
             </div>
         </div>
     </div>
 </section>
