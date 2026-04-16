<div class="sponsor ptb-100" data-aos="fade-up">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="swiper sponsor-slider">
                    <div class="swiper-wrapper">
                        @forelse ($partners as $partner)
                            <div class="swiper-slide">
                                <div class="item-single d-flex justify-content-center">
                                    <div class="sponsor-img">
                                        <a href="{{ $partner->url }}" target="_blank">
                                            <img
                                                src="{{ asset(\App\Constants\Constant::WEBSITE_PARTNERS_IMAGE . '/' . $partner->image) }}">
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-3 text-center mb-30 w-100">
                                <h3 class="mb-0">{{ $keywords['No Partner Found'] ?? __('No Partner Found') }}</h3>
                            </div>
                        @endforelse
                    </div>
                    <!-- Slider pagination -->
                    <div class="swiper-pagination position-static mt-30" id="sponsor-slider-pagination"></div>
                </div>
            </div>
        </div>
    </div>
</div>
