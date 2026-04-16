 @if ($themeVersion == 3)
     <section class="testimonial-area testimonial-3 pb-100">
         <div class="container">
             <div class="row align-items-center">
                 <div class="col-12">
                     <div class="section-title title-center mb-40" data-aos="fade-up">
                         <span class="subtitle">{{ $testimonialSecInfo?->title }}</span>
                         <h2 class="title">{{ $testimonialSecInfo?->subtitle }}</h2>
                     </div>
                 </div>
                 <div class="col-12" data-aos="fade-up">
                     <div class="swiper" id="testimonial-slider-3">
                         <div class="swiper-wrapper">
                             @forelse ($testimonials as $testimonial)
                                 <div class="swiper-slide pb-30">
                                     <div class="slider-item">
                                         <div class="client-content">
                                             <div class="quote">
                                                 <span class="icon"><i class="fas fa-quote-left"></i></span>
                                                 <p class="text m-0">{{ $testimonial?->comment }}
                                                 </p>
                                             </div>
                                             <div class="client-info d-flex align-items-center">
                                                 <div class="client-img position-static">
                                                     <div class="lazy-container rounded-pill ratio ratio-1-1">
                                                         @if (is_null($testimonial->image))
                                                             <img data-src="{{ asset('assets/img/profile.jpg') }}"
                                                                 alt="image" class="lazyload">
                                                         @else
                                                             <img class="lazyload"
                                                                 data-src="{{ asset(\App\Constants\Constant::WEBSITE_TESTIMONIAL_IMAGE . '/' . $testimonial?->image) }}"
                                                                 alt="Person Image">
                                                         @endif
                                                     </div>
                                                 </div>
                                                 <div class="content">
                                                     <h6 class="name mb-0 lh-1">{{ $testimonial->name }}</h6>
                                                     <span class="designation">{{ $testimonial->occupation }}</span>
                                                     <div class="ratings">
                                                         <div class="rate">
                                                             <div class="rating-icon"
                                                                 style="width: {{ $testimonial->rating * 20 }}%">
                                                             </div>
                                                         </div>
                                                         <span class="ratings-total">({{ $testimonial->rating }})
                                                         </span>
                                                     </div>
                                                 </div>
                                             </div>
                                         </div>
                                     </div>
                                 </div>
                             @empty
                                 <div class="p-3 text-center mb-30 w-100">
                                     <h3 class="mb-0">
                                         {{ $keywords['No Testimonial Found'] ?? __('No Testimonial Found') }}</h3>
                                 </div>
                             @endforelse
                         </div>
                         <div class="swiper-pagination position-static text-center"
                             id="testimonial-slider-3-pagination">
                         </div>
                     </div>
                 </div>
             </div>
         </div>
         <div class="shape">
             <div class="shape-1">
                 <svg data-src="{{ asset('assets/tenant-front/images/shape/shape-10.svg') }}"
                     data-unique-ids="disabled" data-cache="disabled"></svg>
             </div>

             <div class="shape-2">
                 <svg data-src="{{ asset('assets/tenant-front/images/shape/shape-6.svg') }}" data-unique-ids="disabled"
                     data-cache="disabled"></svg>
             </div>
             <div class="shape-3">
                 <svg data-src="{{ asset('assets/tenant-front/images/shape/shape-3.svg') }}" data-unique-ids="disabled"
                     data-cache="disabled"></svg>
             </div>
             <div class="shape-4">
                 <svg data-src="{{ asset('assets/tenant-front/images/shape/shape-5.svg') }}" data-unique-ids="disabled"
                     data-cache="disabled"></svg>
             </div>
             <div class="shape-5">
                 <svg data-src="{{ asset('assets/tenant-front/images/shape/shape-2.svg') }}" data-unique-ids="disabled"
                     data-cache="disabled"></svg>
             </div>
         </div>
     </section>
 @else
     <section class="testimonial-area pt-100 pb-70">
         @if ($themeVersion == 1)
             <div class="overlay-bg d-none d-lg-block">
                 <img class="lazyload blur-up"
                     data-src="{{ !empty($testimonialSecImage) ? asset(\App\Constants\Constant::WEBSITE_TESTIMONIAL_SECTION_IMAGE . '/' . $testimonialSecImage) : '' }}">
             </div>
         @else
             <img class="lazyload bg-img"
                 src="{{ !empty($testimonialSecImage) ? asset(\App\Constants\Constant::WEBSITE_TESTIMONIAL_SECTION_IMAGE . '/' . $testimonialSecImage) : '' }}">
         @endif
         <div class="container">
             <div class="row align-items-center">
                 <div class="col-lg-4">
                     <div class="content mb-30" data-aos="fade-up">
                         <div class="content-title">
                             <span class="subtitle"><span
                                     class="line"></span>{{ $testimonialSecInfo?->title }}</span>
                             <h2 class="title">
                                 {{ $testimonialSecInfo?->subtitle }}</h2>
                         </div>
                         <p class="text mb-30">
                             {{ $testimonialSecInfo?->content }}</p>

                         @if ($testimonials && $testimonials->isNotEmpty())
                             <!-- Slider navigation buttons -->
                             <div class="slider-navigation scroll-animate">
                                 <button type="button" title="Slide prev" class="slider-btn slider-btn-prev">
                                     <i class="fal fa-angle-left"></i>
                                 </button>
                                 <button type="button" title="Slide next" class="slider-btn slider-btn-next">
                                     <i class="fal fa-angle-right"></i>
                                 </button>
                             </div>
                         @endif
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
                                                         data-src="{{ asset(\App\Constants\Constant::WEBSITE_TESTIMONIAL_IMAGE . '/' . $testimonial?->image) }}">
                                                 @endif
                                             </div>
                                         </div>
                                         <div class="client-content mt-30">
                                             <div class="quote">
                                                 <p class="text">{{ $testimonial?->comment }}</p>
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
                                         {{ $keywords['No Testimonial Found'] ?? __('No Testimonials Found') }}</h3>
                                 </div>
                             @endforelse
                         </div>
                     </div>
                 </div>
             </div>
         </div>
     </section>
 @endif
