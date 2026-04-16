@extends('admin.layout')

@section('content')
    <div class="page-header">
        <h4 class="page-title">{{ __('Sections Hide/Show') }}</h4>
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
                <a href="#">{{ __('About Page') }}</a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="#">{{ __('Sections Hide/Show') }}</a>
            </li>


        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="card-title d-inline-block">{{ __('Sections Hide/Show') }}</div>


                        </div>


                    </div>
                </div>
                <form class="" action="{{ route('admin.about_us.section.hide_show.update') }}" method="post">
                    @csrf
                    <div class="card-body pt-5 pb-5">
                        <div class="row">
                            <div class="col-lg-6 offset-lg-3">
                                @csrf

                                <div class="form-group">
                                    <label>{{ __('Intro Section') }} <span class="text-danger">*</span></label>
                                    <div class="selectgroup w-100">
                                        <label class="selectgroup-item">
                                            <input type="radio" name="intro_section" value="1"
                                                class="selectgroup-input" {{ $abs->intro_section == 1 ? 'checked' : '' }}>
                                            <span class="selectgroup-button">{{ __('Active') }}</span>
                                        </label>
                                        <label class="selectgroup-item">
                                            <input type="radio" name="intro_section" value="0"
                                                class="selectgroup-input" {{ $abs->intro_section == 0 ? 'checked' : '' }}>
                                            <span class="selectgroup-button">{{ __('Deactive') }}</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>{{ __('Work Process Section') }} <span class="text-danger">*</span></label>
                                    <div class="selectgroup w-100">
                                        <label class="selectgroup-item">
                                            <input type="radio" name="work_process_section" value="1"
                                                class="selectgroup-input"
                                                {{ $abs->work_process_section == 1 ? 'checked' : '' }}>
                                            <span class="selectgroup-button">{{ __('Active') }}</span>
                                        </label>
                                        <label class="selectgroup-item">
                                            <input type="radio" name="work_process_section" value="0"
                                                class="selectgroup-input"
                                                {{ $abs->work_process_section == 0 ? 'checked' : '' }}>
                                            <span class="selectgroup-button">{{ __('Deactive') }}</span>
                                        </label>
                                    </div>
                                </div>



                                <div class="form-group">
                                    <label>{{ __('Testimonial Section') }}<span class="text-danger">*</span></label>
                                    <div class="selectgroup w-100">
                                        <label class="selectgroup-item">
                                            <input type="radio" name="testimonial_section" value="1"
                                                class="selectgroup-input"
                                                {{ $abs->testimonial_section == 1 ? 'checked' : '' }}>
                                            <span class="selectgroup-button">{{ __('Active') }}</span>
                                        </label>
                                        <label class="selectgroup-item">
                                            <input type="radio" name="testimonial_section" value="0"
                                                class="selectgroup-input"
                                                {{ $abs->testimonial_section == 0 ? 'checked' : '' }}>
                                            <span class="selectgroup-button">{{ __('Deactive') }}</span>
                                        </label>
                                    </div>
                                </div>



                                {{-- custom section start --}}
                                @if (count($additional_section_statuses) > 0)
                                    @foreach ($additional_section_statuses as $key => $about_additional_section_status)
                                        @php
                                            $section_content = App\Models\AdditionalSectionContent::where([
                                                ['language_id', $langid],
                                                ['addition_section_id', $key],
                                            ])->first();
                                        @endphp
                                        @if ($section_content)
                                            <div class="form-group">
                                                <label>{{ $section_content->section_name . ' ' . __('Section') }} <span
                                                        class="text-danger">*</span></label>
                                                <div class="selectgroup w-100">
                                                    <label class="selectgroup-item">
                                                        <input type="radio"
                                                            name="additional_sections[{{ $key }}]" value="1"
                                                            class="selectgroup-input"
                                                            {{ $about_additional_section_status == 1 ? 'checked' : '' }}>
                                                        <span class="selectgroup-button">{{ __('Active') }}</span>
                                                    </label>
                                                    <label class="selectgroup-item">
                                                        <input type="radio"
                                                            name="additional_sections[{{ $key }}]" value="0"
                                                            class="selectgroup-input"
                                                            {{ $about_additional_section_status == 0 ? 'checked' : '' }}>
                                                        <span class="selectgroup-button">{{ __('Deactive') }}</span>
                                                    </label>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                @endif
                                {{-- custom section end --}}
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="form">
                            <div class="form-group   row">
                                <div class="col-12 text-center">
                                    <button type="submit" id=""
                                        class="btn btn-success">{{ __('Update') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>


@endsection
