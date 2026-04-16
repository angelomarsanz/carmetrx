 <section class="choose-area pb-70">
     <div class="container">
         <div class="row gx-xl-5">
             @if (
                 !empty($whyChooseUsInfo?->title) ||
                     !empty($whyChooseUsInfo?->subtitle) ||
                     !empty($whyChooseUsInfo?->description) ||
                     !empty($whyChooseUsImg?->why_choose_us_section_img1) ||
                     !empty($whyChooseUsImg?->why_choose_us_section_img2))

                 <div class="col-lg-7">
                     <div class="img-content mb-30 image-right" data-aos="fade-up">
                         <div class="img-1">
                             <img class="lazyload blur-up"
                                 data-src="{{ !empty($whyChooseUsImg?->why_choose_us_section_img1) ? asset(\App\Constants\Constant::WEBSITE_WHY_CHOOSE_US_SECTION_IMAGE . '/' . $whyChooseUsImg?->why_choose_us_section_img1) : '' }}"
                                 alt="Image">
                             @if (!empty($whyChooseUsImg?->why_choose_us_section_video_link))
                                 <a href="{{ $whyChooseUsImg?->why_choose_us_section_video_link }}"
                                     class="video-btn youtube-popup p-absolute">
                                     <i class="fas fa-play"></i>
                                 </a>
                             @endif
                         </div>
                         <div class="img-2">
                             <img class="lazyload blur-up"
                                 data-src="{{ !empty($whyChooseUsImg?->why_choose_us_section_img2) ? asset(\App\Constants\Constant::WEBSITE_WHY_CHOOSE_US_SECTION_IMAGE . '/' . $whyChooseUsImg?->why_choose_us_section_img2) : '' }}"
                                 alt="Image">
                         </div>
                     </div>
                 </div>
                 <div class="col-lg-5 order-lg-first">
                     <div class="content" data-aos="fade-up">
                         <div class="content-title">
                             <span class="subtitle"><span class="line"></span>{{ $whyChooseUsInfo?->title }}</span>
                             <h2>{{ $whyChooseUsInfo?->subtitle }}</h2>
                         </div>
                         <div class="text">{!! $whyChooseUsInfo?->description !!}</div>
                     </div>
                 </div>
             @else
                 <div class="col-lg-12">
                     <h3 class="text-center">
                         {{ $keywords['No Why Choose Us Information Found'] ?? __('No Why Choose Us Information Found') }}
                     </h3>
                 </div>
             @endif
         </div>
     </div>
 </section>
