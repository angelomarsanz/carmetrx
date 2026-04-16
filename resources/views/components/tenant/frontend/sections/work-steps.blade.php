@if ($themeVersion == 2)
    <section class="work-process pt-100 pb-70">
        <!-- Bg image -->
        <img class="lazyload bg-img"
            src="{{ asset(\App\Constants\Constant::WEBSITE_WORK_PROCESS_IMAGE . '/' . $workStepsSecImg) }}">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="section-title title-center mb-40" data-aos="fade-up">
                        <span class="subtitle">{{ $workStepsSecInfo?->work_process_title }}</span>
                        <h2 class="title">{{ $workStepsSecInfo?->work_process_subtitle }}</h2>
                    </div>
                </div>
                <div class="col-12">
                    <div class="row gx-xl-5">
                        @forelse ($steps as $step)
                            <div class="col-xl-3 col-lg-4 col-sm-6" data-aos="fade-up">
                                <div class="process-item text-center mb-30">
                                    @php
                                        $rgba = hex2rgb($step->color);
                                    @endphp
                                    <div class="process-icon">
                                        <div class="progress-content"
                                            style="background:rgba({{ $rgba['red'] }}, {{ $rgba['green'] }},{{ $rgba['blue'] }},0.13);">
                                            <span class="h2 lh-1">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                                            </span>
                                            <i style="color: #{{ $step->color }}; " class="{{ $step->icon }}"></i>
                                        </div>
                                        <div class="progressbar-line-inner" style="color: #{{ $step->color }};">
                                            <svg>
                                                <circle class="progressbar-circle" r="96" cx="100"
                                                    stroke="#{{ $step->color }}" cy="100" stroke-dasharray="500"
                                                    stroke-dashoffset="180" stroke-width="6" fill="none"
                                                    transform="rotate(-5 100 100)">
                                                </circle>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="process-content mt-20">
                                        <h3 class="process-title">{{ $step->title }}</h3>
                                        <p class="text m-0">{{ $step->text }}</p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-3 text-center mb-30 w-100">
                                <h3 class="mb-0"> {{ $keywords['No Work Steps Found'] ?? __('No Work Steps Found') }}
                                </h3>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </section>
@else
    <section class="work-process work-process-2 pb-70">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="section-title title-center mb-40" data-aos="fade-up">
                        <span class="subtitle">{{ $workStepsSecInfo?->work_process_title }}</span>
                        <h2 class="title">{{ $workStepsSecInfo?->work_process_subtitle }}</h2>
                    </div>
                </div>
                <div class="col-12" data-aos="fade-up">
                    <div class="row gx-xl-5">
                        @forelse ($steps as $step)
                            <div class="col-xl-3 col-lg-4 col-md-6">
                                <div class="card mb-30 color-1">
                                    <div class="card-content border text-center">
                                        @php
                                            $rgba = hex2rgb($step->color);
                                        @endphp
                                        <div class="card-step h3 lh-1">
                                            <span
                                                style="background:rgba({{ $rgba['red'] }}, {{ $rgba['green'] }},{{ $rgba['blue'] }},0.13);">{{ $loop->iteration }}</span>
                                        </div>
                                        <div class="card-icon"
                                            style="background:rgba({{ $rgba['red'] }}, {{ $rgba['green'] }},{{ $rgba['blue'] }},0.13);">
                                            <i style="color: #{{ $step->color }}; " class="{{ $step->icon }}"></i>
                                        </div>
                                        <h3 class="card-title">{{ $step->title }}</h3>
                                        <p class="card-text m-0">{{ $step->text }}</p>
                                    </div>
                                    <span class="line line-top"></span>
                                    <span class="line line-right"></span>
                                    <span class="line line-bottom"></span>
                                </div>
                            </div>
                        @empty
                            <div class="p-3 text-center mb-30 w-100">
                                <h3 class="mb-0"> {{ $keywords['No Work Steps Found'] ?? __('No Work Steps Found') }}
                                </h3>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif
