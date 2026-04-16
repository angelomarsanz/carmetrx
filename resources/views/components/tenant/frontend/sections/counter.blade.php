   <section {{ $attributes->merge(['class' => 'counter-area pt-100 pb-70']) }}>
       @if ($themeVersion == 3)
           <img class="lazyload bg-img blur-up"
               src="{{ asset(\App\Constants\Constant::WEBSITE_COUNTER_SEC_IMAGE . '/' . $counterSectionImage) }}">
       @endif
       <div class="container">
           <div class="row gx-xl-5" data-aos="fade-up">
               @forelse ($counters as $counter)
                   <div class="col-sm-6 col-lg-3">
                       <div class="card mb-30">
                           <div class="d-flex align-items-center justify-content-center mb-10">
                               <div
                                   class="card-icon me-2 @if ($themeVersion == 3) color-primary  @else color-secondary @endif ">
                                   <i class="{{ $counter->icon }}"></i>
                               </div>
                               <h2
                                   class="m-0  @if ($themeVersion == 3) color-primary  @else color-secondary @endif">
                                   <span class="counter">{{ $counter->amount }}</span>+
                               </h2>
                           </div>
                           <p class="card-text text-center">{{ $counter->title }}</p>
                       </div>
                   </div>
               @empty
                   <div class="col-12">
                       <h3 class="text-center mt-20">
                           {{ $keywords['No Counter Information Found'] ?? __('No Counter Information Found') }} </h3>
                   </div>
               @endforelse
           </div>
       </div>
   </section>
