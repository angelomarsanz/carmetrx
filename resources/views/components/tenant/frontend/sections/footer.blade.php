<footer {{ $attributes->merge(['class' => 'footer-area border']) }}>

    <img class="lazyload blur-up bg-img"
        src="{{ !empty($basicInfo->footer_bg_img) ? asset(\App\Constants\Constant::WEBSITE_FOOTER_LOGO . '/' . $basicInfo->footer_bg_img) : asset('assets/tenant-front/images/default/footer.png') }}">
    <div class="footer-top">
        <div class="container">
            <div class="row gx-xl-5 justify-content-xl-between">
                <div class="col-lg-4">
                    <div class="footer-widget">
                        <div class="navbar-brand">
                            @if (!empty($basicInfo->footer_logo))
                                <a href="{{ route('frontend.user.index', getParam()) }}">
                                    <img
                                        src="{{ asset(\App\Constants\Constant::WEBSITE_FOOTER_LOGO . '/' . $basicInfo->footer_logo) }}">
                                </a>
                            @endif
                        </div>
                        <p class="text"> {{ !empty($footerInfo) ? $footerInfo->about_company : '' }} </p>
                        @if (count($socialMediaInfos) > 0)
                            <div class="social-link">
                                @foreach ($socialMediaInfos as $socialMediaInfo)
                                    <a href="{{ $socialMediaInfo->url }}" target="_blank"><i
                                            class="{{ $socialMediaInfo->icon }}"></i></a>
                                @endforeach
                            </div>
                        @endif

                    </div>
                </div>
                <div class="col-lg-3 col-xl-2 col-sm-6">
                    <div class="footer-widget">
                        <h3>{{ $keywords['Useful Links'] ?? __('Useful Links') }}</h3>
                        @if (count($quickLinkInfos) == 0)
                        @else
                            <ul class="footer-links">
                                @foreach ($quickLinkInfos as $quickLinkInfo)
                                    <li>
                                        <a href="{{ $quickLinkInfo->url }}">{{ $quickLinkInfo->title }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
                <div class="col-lg-3 col-xl-3 col-sm-6">
                    <div class="footer-widget">
                        <h3>{{ $keywords['Contact Us'] ?? __('Contact Us') }}</h3>
                        <ul class="footer-links">
                            @if (!empty($basicInfo->address))
                                <li>
                                    <i class="fal fa-map-marker-alt"></i>
                                    <span>{{ $basicInfo->address }}</span>
                                </li>
                            @endif
                            @if (!empty($basicInfo->contact_number))
                                @php
                                    $numbers = explode(',', $basicInfo->contact_number);
                                @endphp

                                <li>
                                    <i class="fal fa-phone-plus"></i>
                                    @foreach ($numbers as $number)
                                        <a href="tel:{{ $number }}">{{ $number }}</a>
                                        {{ !$loop->last ? ',' : '' }}
                                    @endforeach
                                </li>
                            @endif
                            @if (!empty($basicInfo->email_address))
                                @php
                                    $emails = explode(',', $basicInfo->email_address);
                                @endphp
                                <li>
                                    <i class="fal fa-envelope"></i>
                                    @foreach ($emails as $email)
                                        <a href="mailto:{{ $email }}">{{ $email }}</a>
                                        {{ !$loop->last ? ',' : '' }}
                                    @endforeach
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>

                <div class="col-lg-3 col-xl-3 col-sm-6">
                    <div class="footer-widget mb-0">
                        <h3>{{ $keywords['Latest Blog Posts'] ?? __('Latest Blog Posts') }}</h3>
                        <aside class="sidebar-widget-area">
                            <div class="widget widget-post radius-md mb-10">
                                @foreach ($recent_blogs as $blog)
                                    <article class="article-item mb-10">
                                        <div class="image">
                                            <a href="{{ route('frontend.blog.post_details', [getParam(), 'slug' => $blog->slug]) }}"
                                                class="lazy-container ratio ratio-1-1">

                                                <img class="lazyload"
                                                    src="{{ asset('assets/front/images/placeholder.png') }}"
                                                    data-src="{{ asset(\App\Constants\Constant::WEBSITE_BLOG_IMAGE . '/' . $blog->image) }}">
                                            </a>
                                        </div>
                                        <div class="content">
                                            <ul class="info-list ">

                                                <li><i class="fal fa-user"></i>{{ $blog->author }}</li>
                                                <li><i class="fal fa-calendar-alt"></i>
                                                    {{ $blog->created_at->format('d M Y') }}
                                                </li>


                                            </ul>
                                            <h6>
                                                <a
                                                    href="{{ route('frontend.blog.post_details', [getParam(), 'slug' => $blog->slug]) }}">
                                                    {{ $blog->title }}
                                                </a>
                                            </h6>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        </aside>
                    </div>
                </div>
            </div>
        </div>
        <div class="copy-right-area border-top">
            <div class="container">
                <div class="copy-right-content">
                    <span> {!! @$footerInfo->copyright_text !!} </span>
                </div>
            </div>
        </div>
</footer>
