@extends('user.layout')

{{-- this style will be applied when the direction of language is right-to-left --}}
@includeIf('user.partials.rtl-style')

@section('content')
    <div class="page-header">
        <h4 class="page-title">{{ __('Work Steps Section') }}</h4>
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
                <a href="#">{{ __('Work Steps Section') }}</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card-title">{{ __('Image') }}</div>
                                </div>

                            </div>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6 offset-lg-3">
                                    <form id="bgImgForm" action="{{ route('user.home_page.update_work_process_bg') }}"
                                        method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="form-group">
                                            <label for="">{{ __('Background Image')  }} <span class="text-danger">{{ '*' }}</span> </label>
                                            <br>
                                            <div class="thumb-preview">
                                                @if (empty($bgImg))
                                                    <img src="{{ asset('assets/img/noimage.jpg') }}" alt="..."
                                                        class="uploaded-background-img">
                                                @else
                                                    <img src="{{ asset(Constant::WEBSITE_WORK_PROCESS_IMAGE . '/' . $bgImg) }}"
                                                        alt="image" class="uploaded-background-img">
                                                @endif
                                            </div>

                                            <div class="mt-3">
                                                <div role="button" class="btn btn-primary btn-sm upload-btn">
                                                    {{ __('Choose Image') }}
                                                    <input type="file" class="background-img-input"
                                                        name="work_process_bg_img">
                                                </div>
                                            </div>
                                            @error('work_process_bg_img')
                                                <p class="mt-2 mb-0 text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer">
                            <div class="row">
                                <div class="col-12 text-center">
                                    <button type="submit" form="bgImgForm" class="btn btn-success">
                                        {{ __('Update') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-lg-8">
                                    <div class="card-title">
                                        {{ __('Update Work Steps Section') }}
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    @includeIf('user.partials.languages')
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <form id="testimonialForm"
                                        action="{{ route('user.home_page.update_work_process_section_info', ['language' => request()->input('language')]) }}"
                                        method="POST" enctype="multipart/form-data">
                                        @csrf


                                        <div class="row">
                                            <div class="col">
                                                <div class="form-group">
                                                    <label for="">{{ __('Title') }}</label>
                                                    <input type="text" class="form-control" name="work_process_title"
                                                        value="{{ empty($workProcessSecInfo->work_process_title) ? '' : $workProcessSecInfo->work_process_title }}"
                                                        placeholder="{{ __('Enter Title') }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <div class="form-group">
                                                    <label for="">{{ __('Subtitle') }}</label>
                                                    <input type="text" class="form-control" name="work_process_subtitle"
                                                        value="{{ empty($workProcessSecInfo->work_process_subtitle) ? '' : $workProcessSecInfo->work_process_subtitle }}"
                                                        placeholder="{{ __('Enter Subtitle') }}">
                                                </div>
                                            </div>
                                        </div>

                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer">
                            <div class="row">
                                <div class="col-12 text-center">
                                    <button type="submit" form="testimonialForm" class="btn btn-success">
                                        {{ __('Update') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="card-title">{{ __('Work Steps Lists') }} </div>
                        </div>


                        <div class="col-lg-4  ">
                            @includeIf('user.partials.languages')
                        </div>

                        <div class="col-lg-4 ">
                            <a href="#" data-toggle="modal" data-target="#createModal"
                                class="btn btn-primary btn-sm float-lg-right float-left">
                                <i class="fas fa-plus"></i> {{ __('Add Work Steps') }}
                            </a>

                            <button class="btn btn-danger btn-sm float-right mr-2 d-none bulk-delete"
                                data-href="{{ route('user.home_page.bulk_delete_work_process') }}">
                                <i class="flaticon-interface-5"></i> {{ __('Delete') }}
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            @if (count($workProcess) == 0)
                                <h3 class="text-center mt-2">
                                    {{ __('NO WORK STEPS LIST FOUND') }}</h3>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-striped mt-3" id="basic-datatables">
                                        <thead>
                                            <tr>
                                                <th scope="col">
                                                    <input type="checkbox" class="bulk-check" data-val="all">
                                                </th>
                                                <th scope="col">{{ __('Icon') }}</th>
                                                <th scope="col">{{ __('Title') }}</th>
                                                <th scope="col">{{ __('Serial Number') }}</th>
                                                <th scope="col">{{ __('Actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($workProcess as $workStep)
                                                <tr>
                                                    <td>
                                                        <input type="checkbox" class="bulk-check"
                                                            data-val="{{ $workStep->id }}">
                                                    </td>
                                                    <td><i class="{{ $workStep->icon }}"></i></td>
                                                    <td>
                                                        {{ strlen($workStep->title) > 30 ? mb_substr($workStep->title, 0, 30, 'UTF-8') . '...' : $workStep->title }}
                                                    </td>
                                                    <td>{{ $workStep->serial_number }}</td>
                                                    <td>
                                                        <a class="btn btn-secondary btn-sm mr-1 editbtn" href="#"
                                                            data-toggle="modal" data-target="#editModal"
                                                            data-id="{{ $workStep->id }}"
                                                            data-icon="{{ $workStep->icon }}"
                                                            data-color="{{ $workStep->color }}"
                                                            data-text="{{ $workStep->text }}"
                                                            data-serial_number="{{ $workStep->serial_number }}"
                                                            data-title="{{ $workStep->title }}">
                                                            <span class="btn-label">
                                                                <i class="fas fa-edit"></i>
                                                            </span>
                                                            {{ __('Edit') }}
                                                        </a>

                                                        <form class="deleteForm d-inline-block"
                                                            action="{{ route('user.home_page.delete_work_process', ['id' => $workStep->id]) }}"
                                                            method="post">
                                                            @csrf
                                                            <button type="submit"
                                                                class="btn btn-danger btn-sm deleteBtn">
                                                                <span class="btn-label">
                                                                    <i class="fas fa-trash"></i>
                                                                </span>
                                                                {{ __('Delete') }}
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card-footer"></div>
            </div>
        </div>
    </div>


    {{-- create modal --}}
    @includeIf('user.home-page.work-process-section.create')

    {{-- edit modal --}}
    @includeIf('user.home-page.work-process-section.edit')
@endsection
