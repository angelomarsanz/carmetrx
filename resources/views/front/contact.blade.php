@extends('front.layout')

@section('meta-description', !empty($seo) ? $seo->contact_meta_description : '')
@section('meta-keywords', !empty($seo) ? $seo->contact_meta_keywords : '')

@section('pagename')
    - {{ __('Contact') }}
@endsection

@section('content')
    @includeIf('front.partials.breadcrumb', [
        'title' => __('Contact'),
        'link' => __('Contact'),
    ])

    <div class="contact-area pt-120 pb-90">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col--lg-10">
                    <div class="row justify-content-center">
                        @if (!empty($be->contact_numbers))
                            <div class="col-lg-4 col-sm-6">
                                <div class="card mb-30 blue" data-aos="fade-up" data-aos-delay="100">
                                    <div class="icon">
                                        <i class="fal fa-phone-plus"></i>
                                    </div>
                                    <div class="card-text">
                                        @php
                                            $phones = explode(',', $be->contact_numbers);
                                        @endphp
                                        @foreach ($phones as $phone)
                                            <p><a href="tel:{{ $phone }}">{{ $phone }}</a></p>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if (!empty($be->contact_mails))
                            <div class="col-lg-4 col-sm-6">
                                <div class="card mb-30 green" data-aos="fade-up" data-aos-delay="200">
                                    <div class="icon">
                                        <i class="fal fa-envelope"></i>
                                    </div>
                                    <div class="card-text">
                                        @php
                                            $mails = explode(',', $be->contact_mails);
                                        @endphp
                                        @foreach ($mails as $mail)
                                            <p><a href="mailTo:{{ $mail }}">{{ $mail }}</a></p>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if (!empty($be->contact_addresses))
                            <div class="col-lg-4 col-sm-6">
                                <div class="card mb-50 orange" data-aos="fade-up" data-aos-delay="300">
                                    <div class="icon">
                                        <i class="fal fa-map-marker-alt"></i>
                                    </div>
                                    <div class="card-text">
                                        @php
                                            $addresses = explode(PHP_EOL, $be->contact_addresses);
                                        @endphp
                                        @foreach ($addresses as $address)
                                            <p>{{ $address }}</p>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-lg-6 mb-30" data-aos="fade-up" data-aos-delay="100">
                            <form id="contactForm" action="{{ route('front.admin.contact.message') }}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-30">
                                            <input type="text" name="name" class="form-control" id="name"
                                                required data-error="{{ __('Enter your name') }}"
                                                placeholder="{{ __('Enter your name') }}*" />
                                            @if ($errors->has('name'))
                                                <div class="help-block text-danger">{{ $errors->first('name') }}</div>
                                            @endif

                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group mb-30">
                                            <input type="email" name="email" class="form-control" id="email"
                                                required data-error="{{ __('Enter your email') }}"
                                                placeholder="{{ __('Enter your email') }}*" />
                                            @if ($errors->has('email'))
                                                <div class="help-block text-danger">{{ $errors->first('email') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group mb-30">
                                            <input type="text" name="subject" class="form-control" id="email"
                                                required data-error="{{ __('Enter your subject') }}"
                                                placeholder="{{ __('Enter your subject') }}*" />
                                            @if ($errors->has('subject'))
                                                <div class="help-block text-danger">{{ $errors->first('subject') }}</div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group mb-30">
                                            <textarea name="message" id="message" class="form-control" cols="30" rows="8" required
                                                data-error="{{ __('Enter your message') }}" placeholder="{{ __('Enter your message') }}*"></textarea>
                                            @if ($errors->has('message'))
                                                <div class="help-block text-danger">{{ $errors->first('message') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    @if ($bs->is_recaptcha == 1)
                                        <div class="form-group mb-30">
                                            {!! NoCaptcha::renderJs() !!}
                                            {!! NoCaptcha::display() !!}

                                            @error('g-recaptcha-response')
                                                <p class="mt-1 text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    @endif

                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-lg btn-primary"
                                            title="{{ __('Send message') }}"> {{ __('Send Message') }} </button>
                                        <div id="msgSubmit"></div>
                                    </div>
                                </div>
                            </form>
                        </div>
                       
                        <div class="col-lg-6 mb-30" data-aos="fade-up" data-aos-delay="200">
                            <iframe
                                src="https://www.google.com/maps?q={{ $be->latitude }}, {{ $be->longitude }}&hl={{ app()->getLocale() }}&z=14&amp;output=embed"
                                style="border:0;" allowfullscreen="" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--====== End contacts-section ======-->
@endsection
