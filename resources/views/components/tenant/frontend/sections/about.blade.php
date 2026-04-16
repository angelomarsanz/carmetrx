 @if ($themeVersion == 1)
     <section {{ $attributes->merge(['class' => 'about-area pb-70 pt-30']) }}>
         <div class="container">
             <div class="row gx-xl-5">
                 @if (!empty($aboutInfo?->title) && !empty($aboutInfo?->subtitle))
                     <div class="col-lg-6">
                         <div class="img-content mb-30" data-aos="fade-up">
                             <div class="image">
                                 <img class="lazyload blur-up"
                                     data-src="{{ !empty($aboutImg?->about_section_image) ? asset(\App\Constants\Constant::WEBSITE_ABOUT_US_SECTION_IMAGE . '/' . $aboutImg?->about_section_image) : '' }}">

                                 <img class="lazyload blur-up"
                                     data-src="{{ !empty($aboutImg?->about_section_image2) ? asset(\App\Constants\Constant::WEBSITE_ABOUT_US_SECTION_IMAGE . '/' . $aboutImg?->about_section_image2) : '' }}">
                             </div>
                             <div class="absolute-text bg-secondary">
                                 <div class="center-text">
                                     <span class="h2 color-primary">{{ $aboutInfo?->years_of_expricence }}+</span>
                                     <span>{{ $keywords['Years'] ?? __('Years') }}</span>
                                 </div>
                                 <div id="curveText">
                                     {{ $keywords['We are highly experience'] ?? __('We are highly experience') }}</div>
                             </div>
                         </div>
                     </div>

                     <div class="col-lg-6">
                         <div class="content mb-30" data-aos="fade-up">
                             <div class="content-title">
                                 <span class="subtitle"><span class="line"></span>
                                     {{ $aboutInfo?->title }}</span>
                                 <h2>{{ $aboutInfo?->subtitle }}</h2>
                             </div>
                             <div class="summernote-content"> {!! $aboutInfo?->description !!} </div>
                             <div class="d-flex align-items-center flex-wrap gap-15">
                                 @if (!empty($aboutInfo->btn_url))
                                     <a href="{{ $aboutInfo?->btn_url }}" target="_blank"
                                         class="btn btn-lg btn-primary bg-secondary">{{ $aboutInfo?->btn_name }}</a>
                                 @endif

                                 <span>{{ $aboutInfo?->client_text }}</span>
                             </div>

                         </div>
                     </div>
                 @else
                     <div class="col-lg-12">
                         <h3 class="text-center">
                             {{ $keywords['No About Information Found'] ?? __('No About Information Found') }}</h3>
                     </div>
                 @endif
             </div>
         </div>
     </section>
 @else
     <section {{ $attributes->merge(['class' => 'about-area about-2 pb-70']) }}>
         <div class="container">
             <div class="row align-items-center gx-xl-5">
                 <div class="col-lg-5">
                     <div class="content mb-30" data-aos="fade-up">
                         @if (!empty($aboutInfo?->title) && !empty($aboutInfo?->subtitle))
                             <div class="content-title">
                                 <span class="subtitle">{{ $aboutInfo?->title }}</span>
                                 <h2>{{ $aboutInfo?->subtitle }}</h2>
                             </div>
                             <div class="text summernote-content">{!! $aboutInfo?->description !!}</div>

                             <div class="d-flex align-items-center flex-wrap gap-15">
                                 @if (!empty($aboutInfo->btn_url))
                                     <a href="{{ $aboutInfo->btn_url }}" target="_blank"
                                         class="btn btn-lg btn-primary bg-primary">{{ $aboutInfo?->btn_name }}</a>
                                 @endif
                                 @if (!empty($aboutInfo->client_text))
                                     <div class="clients">
                                         <span class="color-primary">{{ $aboutInfo?->client_text }}</span>

                                     </div>
                                 @endif
                             </div>
                         @else
                             <h3 class="text-center">
                                 {{ $keywords['No About Information Found'] ?? __('No About Information Found') }}</h3>
                         @endif
                     </div>
                 </div>
                 <div class="col-lg-7">
                     <div class="img-content img-right mb-30" data-aos="fade-up">
                         <div class="img-1">
                             <img class="lazyload blur-up"
                                 src="{{ asset('assets/tenant-front/images/placeholder.png') }}"
                                 data-src="{{ asset(\App\Constants\Constant::WEBSITE_ABOUT_US_SECTION_IMAGE . '/' . $aboutImg?->about_section_image) }}"
                                 alt="Image">
                         </div>
                         <div class="img-2">
                             <img class="lazyload blur-up"
                                 src="{{ asset('assets/tenant-front/images/placeholder.png') }}"
                                 data-src="{{ asset(\App\Constants\Constant::WEBSITE_ABOUT_US_SECTION_IMAGE . '/' . $aboutImg?->about_section_image2) }}"
                                 alt="Image">
                             @if (!empty($aboutImg->about_section_video_link))
                                 <a href="{{ $aboutImg->about_section_video_link }}"
                                     class="video-btn youtube-popup p-absolute">
                                     <i class="fas fa-play"></i>
                                 </a>
                             @endif
                         </div>
                     </div>
                 </div>
             </div>
         </div>
         <!-- Bg shape -->
         <div class="shape">
             <div class="shape-1">
                 <svg data-src="{{ asset('assets/tenant-front/images/shape/shape-2.svg') }}" data-unique-ids="disabled"
                     data-cache="disabled"></svg>
             </div>
             <div class="shape-2">
                 <svg data-src="{{ asset('assets/tenant-front/images/shape/shape-9.svg') }}" data-unique-ids="disabled"
                     data-cache="disabled"></svg>
             </div>
             <div class="shape-3">
                 <svg data-src="{{ asset('assets/tenant-front/images/shape/shape-8.svg') }}" data-unique-ids="disabled"
                     data-cache="disabled"></svg>
             </div>
             <div class="shape-4">
                 <svg data-src="{{ asset('assets/tenant-front/images/shape/shape-3.svg') }}" data-unique-ids="disabled"
                     data-cache="disabled"></svg>
             </div>

         </div>
     </section>
 @endif
