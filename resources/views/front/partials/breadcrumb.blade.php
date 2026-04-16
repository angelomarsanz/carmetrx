<div class="page-title-area bg-primary-light">
    <div class="container">
        <div class="content text-center">
            <h2> {{ $title }}</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('front.index') }}">{{ __('Home') }}</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $link }} </li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- Bg Overlay -->
    <img class="lazyload bg-overlay-1" data-src="{{ asset('assets/front/images/shadow-1.png') }}" >
    <img class="lazyload bg-overlay-2" data-src="{{ asset('assets/front/images/shadow-2.png') }}">

    <!-- Bg Shape -->
    <div class="shape">
        <div class="shape-1">
            <svg data-src="{{ asset('assets/front/svg/shape4.svg') }}" data-unique-ids="disabled"
                data-cache="disabled"></svg>
        </div>
        <div class="shape-2">
            <svg data-src="{{ asset('assets/front/svg/shape5.svg') }}" data-unique-ids="disabled"
                data-cache="disabled"></svg>
        </div>
        <div class="shape-3">
            <svg data-src="{{ asset('assets/front/svg/shape6.svg') }}" data-unique-ids="disabled"
                data-cache="disabled"></svg>
        </div>
        <div class="shape-4">
            <svg data-src="{{ asset('assets/front/svg/shape7.svg') }}" data-unique-ids="disabled"
                data-cache="disabled"></svg>
        </div>
        <div class="shape-5">
            <svg data-src="{{ asset('assets/front/svg/shape8.svg') }}" data-unique-ids="disabled"
                data-cache="disabled"></svg>
        </div>
        <div class="shape-6">
            <svg data-src="{{ asset('assets/front/svg/shape9.svg') }}" data-unique-ids="disabled"
                data-cache="disabled"></svg>
        </div>
    </div>
</div>
