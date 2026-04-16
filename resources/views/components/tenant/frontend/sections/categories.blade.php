@if ($themeVersion == 2)
    <section class="category pt-100 pb-70 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="section-title title-inline mb-40" data-aos="fade-up">
                        <h2 class="title">{{ $catgorySecInfo?->category_section_title }}</h2>
                        <!-- Slider navigation buttons -->
                        <div class="slider-navigation">
                            <button type="button" title="Slide prev" class="slider-btn cat-slider-btn-prev rounded-pill">
                                <i class="fal fa-angle-left"></i>
                            </button>
                            <button type="button" title="Slide next"
                                class="slider-btn cat-slider-btn-next rounded-pill">
                                <i class="fal fa-angle-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-12" data-aos="fade-up">
                    <div class="swiper" id="category-slider-1">
                        <div class="swiper-wrapper">
                            @forelse ($property_categories as $category)
                                <div class="swiper-slide mb-30 color-1">
                                    <a
                                        href="{{ route('frontend.properties', [getParam(), 'category' => $category->categoryContent?->slug]) }}">
                                        <div class="category-item bg-white radius-md text-center">
                                            <div class="category-icons mb-30">
                                                <img
                                                    src="{{ asset('assets/img/property-category/' . $category->image) }}">
                                            </div>
                                            <span
                                                class="category-title m-0 color-medium">{{ $category->categoryContent?->name }}</span>
                                        </div>
                                    </a>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class=" p-3 text-center mb-30">
                                        <h3 class="mb-0">
                                            {{ $keywords['No Category Found'] ?? __('No Category Found') }}</h3>
                                    </div>
                                </div>
                            @endforelse

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@else
    <section class="category category-2 pb-100">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="section-title title-center mb-40" data-aos="fade-up">
                        <span class="subtitle">{{ $catgorySecInfo?->category_section_title }}</span>
                        <h2 class="title">{{ $catgorySecInfo?->category_section_subtitle }}</h2>
                    </div>
                </div>
                <div class="col-12" data-aos="fade-up">
                    <div class="swiper" id="category-slider-2">
                        <div class="swiper-wrapper">
                            @forelse ($property_categories as $category)
                                <div class="swiper-slide color-1">
                                    <a
                                        href="{{ route('frontend.properties', [getParam(), 'category' => $category->categoryContent?->slug]) }}">
                                        <div class="category-item radius-md text-center">
                                            <div class="category-icons">
                                                <img src="{{ asset('assets/img/property-category/' . $category->image) }}"
                                                    alt="">
                                            </div>
                                            <span
                                                class="category-title m-0 d-block mt-3 color-medium">{{ $category?->categoryContent?->name }}</span>
                                        </div>
                                    </a>
                                </div>
                            @empty
                                <div class="p-3 text-center w-100">
                                    <h3 class="mb-0">
                                        {{ $keywords['No Category Found'] ?? __('No Category Found') }}</h3>
                                </div>
                            @endforelse
                        </div>
                        <div class="swiper-pagination position-static mt-30" id="category-slider-2-pagination"></div>
                    </div>
                </div>
            </div>
        </div>


        <div class="shape">
            <div class="shape-1">
                <svg data-src="{{ asset('assets/tenant-front/images/shape/shape-1.svg') }}" data-unique-ids="disabled"
                    data-cache="disabled"></svg>
            </div>
            <div class="shape-2">
                <svg data-src="{{ asset('assets/tenant-front/images/shape/shape-2.svg') }}" data-unique-ids="disabled"
                    data-cache="disabled"></svg>
            </div>
            <div class="shape-3">
                <svg data-src="{{ asset('assets/tenant-front/images/shape/shape-3.svg') }}" data-unique-ids="disabled"
                    data-cache="disabled"></svg>
            </div>
            <div class="shape-4">
                <svg data-src="{{ asset('assets/tenant-front/images/shape/shape-4.svg') }}" data-unique-ids="disabled"
                    data-cache="disabled"></svg>
            </div>
            <div class="shape-5">
                <svg data-src="{{ asset('assets/tenant-front/images/shape/shape-10.svg') }}" data-unique-ids="disabled"
                    data-cache="disabled"></svg>
            </div>

        </div>
    </section>
@endif
