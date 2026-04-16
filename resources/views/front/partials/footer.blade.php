<!-- Footer Area -->
<footer class="footer-area bg-primary-light bg-img" data-bg-image="{{ asset('/') }}assets/front/images/footer-bg.png">
    @if ($bs->top_footer_section == 1)
        <div class="footer-top pt-120 pb-90">
            <div class="container">
                <div class="row">
                    <div class="col-xl-3 col-md-6 col-sm-12">
                        <div class="footer-widget" data-aos="fade-up" data-aos-delay="100">
                            <div class="navbar-brand">
                                <a href="{{ route('front.index') }}">
                                    <img src="{{ asset('assets/front/img/' . $bs->footer_logo) }}" alt="Logo">
                                </a>
                            </div>
                            <p class="text">{{ $bs->footer_text }}</p>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-3 col-sm-6">
                        <div class="footer-widget" data-aos="fade-up" data-aos-delay="200">
                            @php
                                $ulinks = App\Models\Ulink::where('language_id', $currentLang->id)
                                    ->orderby('id', 'desc')
                                    ->get();
                            @endphp
                            <h5>{{ $bs->useful_links_title }}</h5>

                            <ul class="footer-links">
                                @foreach ($ulinks as $ulink)
                                    <li><a href="{{ $ulink->url }}">{{ $ulink->name }}</a></li>
                                @endforeach

                            </ul>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-3 col-sm-6">
                        <div class="footer-widget" data-aos="fade-up" data-aos-delay="300">
                            <h5>{{ __('Contanct Us') }}</h5>
                            <ul class="footer-links">
                                @if (!empty($be?->contact_addresses))
                                    <li> <i class="fa fa-home"></i>
                                        @php
                                            $addresses = explode(PHP_EOL, $be?->contact_addresses);
                                        @endphp
                                        <span>
                                            @foreach ($addresses as $address)
                                                {{ $address }}
                                                @if (!$loop->last)
                                                    <br>
                                                @endif
                                            @endforeach
                                        </span>
                                    </li>
                                @endif
                                @if (!empty($be?->contact_numbers))
                                    <li><i class="fa fa-phone"></i>
                                        @php
                                            $phones = explode(',', $be?->contact_numbers);
                                        @endphp
                                        <span>
                                            @foreach ($phones as $phone)
                                                {{ $phone }}
                                                @if (!$loop->last)
                                                    ,
                                                @endif
                                            @endforeach
                                        </span>
                                    </li>
                                @endif
                                @php
                                    $mails = explode(',', $be?->contact_mails);
                                @endphp
                                @if (!empty($be?->contact_mails))
                                    <li><i class="far fa-envelope"></i>

                                        <span>
                                            @foreach ($mails as $mail)
                                                {{ $mail }}
                                                @if (!$loop->last)
                                                    ,
                                                @endif
                                            @endforeach
                                        </span>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>

                    <div class="col-xl-4 col-md-6 col-sm-6">
                        <div class="footer-widget" data-aos="fade-up" data-aos-delay="500">
                            <h5>{{ $bs->newsletter_title }}</h5>
                            <p class="lh-1 mb-20">{{ $bs->newsletter_subtitle }}</p>
                            <div class="newsletter-form">
                                <form id="newsletterForm" action="{{ route('front.subscribe') }}" method="POST"
                                    class="subscribeForm">
                                    @csrf
                                    <div class="form-group">
                                        <input class="form-control radius-sm" name="email"
                                            placeholder="{{ __('Enter email') }}" type="email" name="EMAIL"
                                            required="" autocomplete="off">

                                        <button class="btn btn-md btn-primary radius-sm no-animation" type="submit"><i
                                                class="fal fa-paper-plane"></i></button>
                                    </div>
                                    @error('email')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                    @if ($bs->is_recaptcha == 1)
                                        <div class="form-group mb-30">
                                            {!! NoCaptcha::renderJs() !!}
                                            {!! NoCaptcha::display() !!}

                                            @error('g-recaptcha-response')
                                                <p class="mt-1 text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    @endif
                                </form>
                            </div>
                            <div class="social-link mt-3">

                                @foreach ($socials as $social)
                                    <a href="{{ $social->url }}" target="_blank" title="instagram"><i
                                            class="{{ $social->icon }}"></i></a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    @if ($bs->copyright_section == 1)
        <div class="copy-right-area border-top">
            <div class="container">
                <div class="copy-right-content">
                    <span>
                        {!! replaceBaseUrl($bs->copyright_text) !!}
                    </span>

                </div>
            </div>
        </div>
    @endif
</footer>
<!-- Footer Area -->
