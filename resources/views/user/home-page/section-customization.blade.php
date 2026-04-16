@extends('user.layout')

@section('content')
    <div class="page-header">
        <h4 class="page-title">{{ __('Section Show/Hide') }}</h4>
        <ul class="breadcrumbs">
            <li class="nav-home">
                <a href="{{ route('user-dashboard') }}">
                    <i class="flaticon-home"></i>
                </a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="#">{{ __('Pages') }}</a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="#">{{ __('Home Page') }}</a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="#">{{ __('Section Show/Hide') }}</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <form action="{{ route('user.home_page.update_section_status') }}" method="POST">
                    @csrf
                    <div class="card-header">
                        <div class="card-title d-inline-block">
                            {{ __('Home Page Sections') }}</div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6 offset-lg-3">

                                @if ($themeVersion != 1)
                                    <div class="form-group">
                                        <label>{{ __('Work Steps Section Status') }} <span
                                                class="text-danger">{{ '*' }}</span></label>
                                        <div class="selectgroup w-100">
                                            <label class="selectgroup-item">
                                                <input type="radio" name="work_steps_section_status" value="1"
                                                    class="selectgroup-input"
                                                    {{ $sectionInfo->work_steps_section_status == 1 ? 'checked' : '' }}>
                                                <span class="selectgroup-button">{{ __('Enable') }}</span>
                                            </label>

                                            <label class="selectgroup-item">
                                                <input type="radio" name="work_steps_section_status" value="0"
                                                    class="selectgroup-input"
                                                    {{ $sectionInfo->work_steps_section_status == 0 ? 'checked' : '' }}>
                                                <span class="selectgroup-button">{{ __('Disable') }}</span>
                                            </label>
                                        </div>
                                        @error('work_steps_section_status')
                                            <p class="mb-0 text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                @endif
                                @if ($themeVersion != 1)
                                    <div class="form-group">
                                        <label>{{ __('Category Section Status') }} <span
                                                class="text-danger">{{ '*' }}</span></label>
                                        <div class="selectgroup w-100">
                                            <label class="selectgroup-item">
                                                <input type="radio" name="category_section_status" value="1"
                                                    class="selectgroup-input"
                                                    {{ $sectionInfo->category_section_status == 1 ? 'checked' : '' }}>
                                                <span class="selectgroup-button">{{ __('Enable') }}</span>
                                            </label>

                                            <label class="selectgroup-item">
                                                <input type="radio" name="category_section_status" value="0"
                                                    class="selectgroup-input"
                                                    {{ $sectionInfo->category_section_status == 0 ? 'checked' : '' }}>
                                                <span class="selectgroup-button">{{ __('Disable') }}</span>
                                            </label>
                                        </div>
                                        @error('category_section_status')
                                            <p class="mb-0 text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                @endif
                                @if ($themeVersion != 3)
                                    <div class="form-group">
                                        <label>{{ __('Featured Properties Section Status') }} <span
                                                class="text-danger">{{ '*' }}</span></label>
                                        <div class="selectgroup w-100">
                                            <label class="selectgroup-item">
                                                <input type="radio" name="featured_properties_section_status"
                                                    value="1" class="selectgroup-input"
                                                    {{ $sectionInfo->featured_properties_section_status == 1 ? 'checked' : '' }}>
                                                <span class="selectgroup-button">{{ __('Enable') }}</span>
                                            </label>

                                            <label class="selectgroup-item">
                                                <input type="radio" name="featured_properties_section_status"
                                                    value="0" class="selectgroup-input"
                                                    {{ $sectionInfo->featured_properties_section_status == 0 ? 'checked' : '' }}>
                                                <span class="selectgroup-button">{{ __('Disable') }}</span>
                                            </label>
                                        </div>
                                        @error('featured_properties_section_status')
                                            <p class="mb-0 text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                @endif

                                <div class="form-group">
                                    <label>{{ __('Property Section Status') }} <span
                                            class="text-danger">{{ '*' }}</span></label>
                                    <div class="selectgroup w-100">
                                        <label class="selectgroup-item">
                                            <input type="radio" name="property_section_status" value="1"
                                                class="selectgroup-input"
                                                {{ $sectionInfo->property_section_status == 1 ? 'checked' : '' }}>
                                            <span class="selectgroup-button">{{ __('Enable') }}</span>
                                        </label>

                                        <label class="selectgroup-item">
                                            <input type="radio" name="property_section_status" value="0"
                                                class="selectgroup-input"
                                                {{ $sectionInfo->property_section_status == 0 ? 'checked' : '' }}>
                                            <span class="selectgroup-button">{{ __('Disable') }}</span>
                                        </label>
                                    </div>
                                    @error('property_section_status')
                                        <p class="mb-0 text-danger">{{ $message }}</p>
                                    @enderror
                                </div>

                                @if ($themeVersion == 3)
                                    <div class="form-group">
                                        <label>{{ __('Projects Section Status') }} <span
                                                class="text-danger">{{ '*' }}</span></label>
                                        <div class="selectgroup w-100">
                                            <label class="selectgroup-item">
                                                <input type="radio" name="project_section_status" value="1"
                                                    class="selectgroup-input"
                                                    {{ $sectionInfo->project_section_status == 1 ? 'checked' : '' }}>
                                                <span class="selectgroup-button">{{ __('Enable') }}</span>
                                            </label>

                                            <label class="selectgroup-item">
                                                <input type="radio" name="project_section_status" value="0"
                                                    class="selectgroup-input"
                                                    {{ $sectionInfo->project_section_status == 0 ? 'checked' : '' }}>
                                                <span class="selectgroup-button">{{ __('Disable') }}</span>
                                            </label>
                                        </div>
                                        @error('project_section_status')
                                            <p class="mb-0 text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                @endif
                                @if ($themeVersion != 2)
                                    <div class="form-group">
                                        <label>{{ __('About Us Section Status') }} <span
                                                class="text-danger">{{ '*' }}</span></label>
                                        <div class="selectgroup w-100">
                                            <label class="selectgroup-item">
                                                <input type="radio" name="about_section_status" value="1"
                                                    class="selectgroup-input"
                                                    {{ $sectionInfo->about_section_status == 1 ? 'checked' : '' }}>
                                                <span class="selectgroup-button">{{ __('Enable') }}</span>
                                            </label>

                                            <label class="selectgroup-item">
                                                <input type="radio" name="about_section_status" value="0"
                                                    class="selectgroup-input"
                                                    {{ $sectionInfo->about_section_status == 0 ? 'checked' : '' }}>
                                                <span class="selectgroup-button">{{ __('Disable') }}</span>
                                            </label>
                                        </div>
                                        @error('about_section_status')
                                            <p class="mb-0 text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                @endif
                                @if ($themeVersion != 2)
                                    <div class="form-group">
                                        <label>{{ __('Counter Section Status') }} <span
                                                class="text-danger">{{ '*' }}</span> </label>
                                        <div class="selectgroup w-100">
                                            <label class="selectgroup-item">
                                                <input type="radio" name="counter_section_status" value="1"
                                                    class="selectgroup-input"
                                                    {{ $sectionInfo->counter_section_status == 1 ? 'checked' : '' }}>
                                                <span class="selectgroup-button">{{ __('Enable') }}</span>
                                            </label>

                                            <label class="selectgroup-item">
                                                <input type="radio" name="counter_section_status" value="0"
                                                    class="selectgroup-input"
                                                    {{ $sectionInfo->counter_section_status == 0 ? 'checked' : '' }}>
                                                <span class="selectgroup-button">{{ __('Disable') }}</span>
                                            </label>
                                        </div>
                                        @error('counter_section_status')
                                            <p class="mb-0 text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                @endif
                                @if ($themeVersion == 1)
                                    <div class="form-group">
                                        <label>{{ __('Agent Section Status') }} <span
                                                class="text-danger">{{ '*' }}</span></label>
                                        <div class="selectgroup w-100">
                                            <label class="selectgroup-item">
                                                <input type="radio" name="agent_section_status" value="1"
                                                    class="selectgroup-input"
                                                    {{ $sectionInfo->agent_section_status == 1 ? 'checked' : '' }}>
                                                <span class="selectgroup-button">{{ __('Enable') }}</span>
                                            </label>

                                            <label class="selectgroup-item">
                                                <input type="radio" name="agent_section_status" value="0"
                                                    class="selectgroup-input"
                                                    {{ $sectionInfo->agent_section_status == 0 ? 'checked' : '' }}>
                                                <span class="selectgroup-button">{{ __('Disable') }}</span>
                                            </label>
                                        </div>
                                        @error('agent_section_status')
                                            <p class="mb-0 text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                @endif

                                @if ($themeVersion != 1)
                                    <div class="form-group">
                                        <label>{{ __('Partner Section Status') }} <span
                                                class="text-danger">{{ '*' }}</span></label>
                                        <div class="selectgroup w-100">
                                            <label class="selectgroup-item">
                                                <input type="radio" name="partner_section_status" value="1"
                                                    class="selectgroup-input"
                                                    {{ $sectionInfo->partner_section_status == 1 ? 'checked' : '' }}>
                                                <span class="selectgroup-button">{{ __('Enable') }}</span>
                                            </label>

                                            <label class="selectgroup-item">
                                                <input type="radio" name="partner_section_status" value="0"
                                                    class="selectgroup-input"
                                                    {{ $sectionInfo->partner_section_status == 0 ? 'checked' : '' }}>
                                                <span class="selectgroup-button">{{ __('Disable') }}</span>
                                            </label>
                                        </div>
                                        @error('partner_section_status')
                                            <p class="mb-0 text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                @endif
                                <div class="form-group">
                                    <label>{{ __('Testimonial Section Status') }} <span
                                            class="text-danger">{{ '*' }}</span></label>
                                    <div class="selectgroup w-100">
                                        <label class="selectgroup-item">
                                            <input type="radio" name="testimonial_section_status" value="1"
                                                class="selectgroup-input"
                                                {{ $sectionInfo->testimonial_section_status == 1 ? 'checked' : '' }}>
                                            <span class="selectgroup-button">{{ __('Enable') }}</span>
                                        </label>

                                        <label class="selectgroup-item">
                                            <input type="radio" name="testimonial_section_status" value="0"
                                                class="selectgroup-input"
                                                {{ $sectionInfo->testimonial_section_status == 0 ? 'checked' : '' }}>
                                            <span class="selectgroup-button">{{ __('Disable') }}</span>
                                        </label>
                                    </div>
                                    @error('testimonial_section_status')
                                        <p class="mb-0 text-danger">{{ $message }}</p>
                                    @enderror
                                </div>

                                @if ($themeVersion == 2)
                                    <div class="form-group">
                                        <label>{{ __('Video Section Status') }} <span
                                                class="text-danger">{{ '*' }}</span></label>
                                        <div class="selectgroup w-100">
                                            <label class="selectgroup-item">
                                                <input type="radio" name="video_section_status" value="1"
                                                    class="selectgroup-input"
                                                    {{ $sectionInfo->video_section_status == 1 ? 'checked' : '' }}>
                                                <span class="selectgroup-button">{{ __('Enable') }}</span>
                                            </label>

                                            <label class="selectgroup-item">
                                                <input type="radio" name="video_section_status" value="0"
                                                    class="selectgroup-input"
                                                    {{ $sectionInfo->video_section_status == 0 ? 'checked' : '' }}>
                                                <span class="selectgroup-button">{{ __('Disable') }}</span>
                                            </label>
                                        </div>
                                        @error('video_section_status')
                                            <p class="mb-0 text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                @endif
                                @if ($themeVersion == 1)
                                    <div class="form-group">
                                        <label>{{ __('Cities Section Status') }} <span
                                                class="text-danger">{{ '*' }}</span></label>
                                        <div class="selectgroup w-100">
                                            <label class="selectgroup-item">
                                                <input type="radio" name="cities_section_status" value="1"
                                                    class="selectgroup-input"
                                                    {{ $sectionInfo->cities_section_status == 1 ? 'checked' : '' }}>
                                                <span class="selectgroup-button">{{ __('Enable') }}</span>
                                            </label>

                                            <label class="selectgroup-item">
                                                <input type="radio" name="cities_section_status" value="0"
                                                    class="selectgroup-input"
                                                    {{ $sectionInfo->cities_section_status == 0 ? 'checked' : '' }}>
                                                <span class="selectgroup-button">{{ __('Disable') }}</span>
                                            </label>
                                        </div>
                                        @error('cities_section_status')
                                            <p class="mb-0 text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                @endif
                                @if ($themeVersion == 1)
                                    <div class="form-group">
                                        <label>{{ __('Newsletter Section Status') }} <span
                                                class="text-danger">{{ '*' }}</span></label>
                                        <div class="selectgroup w-100">
                                            <label class="selectgroup-item">
                                                <input type="radio" name="newsletter_section_status" value="1"
                                                    class="selectgroup-input"
                                                    {{ $sectionInfo->newsletter_section_status == 1 ? 'checked' : '' }}>
                                                <span class="selectgroup-button">{{ __('Enable') }}</span>
                                            </label>

                                            <label class="selectgroup-item">
                                                <input type="radio" name="newsletter_section_status" value="0"
                                                    class="selectgroup-input"
                                                    {{ $sectionInfo->newsletter_section_status == 0 ? 'checked' : '' }}>
                                                <span class="selectgroup-button">{{ __('Disable') }}</span>
                                            </label>
                                        </div>
                                        @error('newsletter_section_status')
                                            <p class="mb-0 text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                @endif
                                @if ($themeVersion == 1)
                                    <div class="form-group">
                                        <label>{{ __('Why Choose Us Section Status') }} <span
                                                class="text-danger">{{ '*' }}</span></label>
                                        <div class="selectgroup w-100">
                                            <label class="selectgroup-item">
                                                <input type="radio" name="why_choose_us_section_status" value="1"
                                                    class="selectgroup-input"
                                                    {{ $sectionInfo->why_choose_us_section_status == 1 ? 'checked' : '' }}>
                                                <span class="selectgroup-button">{{ __('Enable') }}</span>
                                            </label>

                                            <label class="selectgroup-item">
                                                <input type="radio" name="why_choose_us_section_status" value="0"
                                                    class="selectgroup-input"
                                                    {{ $sectionInfo->why_choose_us_section_status == 0 ? 'checked' : '' }}>
                                                <span class="selectgroup-button">{{ __('Disable') }}</span>
                                            </label>
                                        </div>
                                        @error('why_choose_us_section_status')
                                            <p class="mb-0 text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                @endif
                                <div class="form-group">
                                    <label>{{ __('Footer Section Status') }} <span
                                            class="text-danger">{{ '*' }}</span></label>
                                    <div class="selectgroup w-100">
                                        <label class="selectgroup-item">
                                            <input type="radio" name="footer_section_status" value="1"
                                                class="selectgroup-input"
                                                {{ $sectionInfo->footer_section_status == 1 ? 'checked' : '' }}>
                                            <span class="selectgroup-button">{{ __('Enable') }}</span>
                                        </label>

                                        <label class="selectgroup-item">
                                            <input type="radio" name="footer_section_status" value="0"
                                                class="selectgroup-input"
                                                {{ $sectionInfo->footer_section_status == 0 ? 'checked' : '' }}>
                                            <span class="selectgroup-button">{{ __('Disable') }}</span>
                                        </label>
                                    </div>
                                    @error('footer_section_status')
                                        <p class="mb-0 text-danger">{{ $message }}</p>
                                    @enderror
                                </div>

                                @if (count($customSectons) > 0)
                                    @foreach ($customSectons as $customSecton)
                                        @php
                                            $content = App\Models\User\AdditionalSectionContent::where(
                                                'addition_section_id',
                                                $customSecton->id,
                                            )->first();
                                            $customStatus = $customSectonStatus;
                                            $sectionStatus = isset($customStatus[$customSecton->id])
                                                ? $customStatus[$customSecton->id]
                                                : 0;
                                        @endphp
                                        <div class="form-group">
                                            <label>{{ $content->section_name }} {{ __('Status') }} <span
                                                    class="text-danger">{{ '*' }}</span></label>
                                            <div class="selectgroup w-100">
                                                <label class="selectgroup-item">
                                                    <input type="radio"
                                                        name="additional_section_status[{{ $customSecton->id }}]"
                                                        value="1" class="selectgroup-input"
                                                        {{ $sectionStatus == 1 ? 'checked' : '' }}>
                                                    <span class="selectgroup-button">{{ __('Enable') }}</span>
                                                </label>

                                                <label class="selectgroup-item">
                                                    <input type="radio"
                                                        name="additional_section_status[{{ $customSecton->id }}]"
                                                        value="0" class="selectgroup-input"
                                                        {{ $sectionStatus == 0 ? 'checked' : '' }}>
                                                    <span class="selectgroup-button">{{ __('Disable') }}</span>
                                                </label>
                                            </div>
                                            @error('additional_section_status')
                                                <p class="mb-0 text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="row">
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-success">
                                    {{ __('Update') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
