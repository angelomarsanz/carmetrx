@extends('admin.layout')

@section('content')
    <div class="page-header">
        <h4 class="page-title">{{ __('Preview Templates') }}</h4>
        <ul class="breadcrumbs">
            <li class="nav-home">
                <a href="{{ route('admin.dashboard') }}">
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
                <a href="#">{{ __('Preview Templates') }}</a>
            </li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="card-title">{{ __('Preview Templates') }}</div>
                        </div>

                        <div class="col-lg-3">

                        </div>
                        <div class="col-lg-4 offset-lg-1 mt-2 mt-lg-0">
                            <a href="#" class="btn btn-primary float-right btn-sm" data-toggle="modal"
                                data-target="#createModal"><i class="fas fa-plus"></i> {{ __('Add New') }}</a>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-5 pb-4">
                    <div class="table-responsive">
                        @if (count($themes) > 0)
                            <table class="table table-striped mt-3">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">{{ __('Image') }}</th>
                                        <th scope="col">{{ __('Name') }}</th>
                                        <th scope="col">{{ __('Status') }}</th>
                                        <th scope="col">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($themes as $key => $theme)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                @if (!is_null($theme->image))
                                                    <img src="{{ asset(\App\Constants\Constant::WEBSITE_THEMES . '/' . $theme->image) }}"
                                                        width="80">
                                                @endif
                                            </td>
                                            <td>{{ $theme->name }}</td>
                                            <td>

                                                <form id="userFrom{{ $theme->id }}" class="d-inline-block"
                                                    action="{{ route('admin.userThemes.statusChange') }}" method="post">
                                                    @csrf
                                                    <select
                                                        class="form-control {{ $theme->is_active == 1 ? 'bg-success' : 'bg-danger' }}"
                                                        name="status"
                                                        onchange="document.getElementById('userFrom{{ $theme->id }}').submit();">
                                                        <option value="1"
                                                            {{ $theme->is_active == 1 ? 'selected' : '' }}>
                                                            {{ __('Active') }}</option>
                                                        <option value="0"
                                                            {{ $theme->is_active == 0 ? 'selected' : '' }}>
                                                            {{ __('Deactive') }}</option>
                                                    </select>
                                                    <input type="hidden" name="theme_id" value="{{ $theme->id }}">
                                                </form>

                                            </td>
                                            <td>
                                                <a class="btn btn-secondary btn-sm editbtn" href="#editModal"
                                                    data-toggle="modal" data-theme_id="{{ $theme->id }}"
                                                    data-name="{{ $theme->name }}" data-url="{{ $theme->url }}"
                                                    data-serial_number="{{ $theme->serial_number }}"
                                                    data-image=" {{ asset(\App\Constants\Constant::WEBSITE_THEMES . '/' . $theme->image) }}">
                                                    <span class="btn-label">
                                                        <i class="fas fa-edit"></i>
                                                    </span>
                                                    {{ __('Edit') }}
                                                </a>
                                                <form class="deleteform d-inline-block"
                                                    action="{{ route('admin.userThemes.delete') }}" method="post">
                                                    @csrf
                                                    <input type="hidden" name="theme_id" value="{{ $theme->id }}">
                                                    <button type="submit" class="btn btn-danger btn-sm deletebtn">
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
                        @else
                            <h5 class="text-center">
                                {{ __('NO TEMPLATE FOUND') }}!
                            </h5>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Edit Faq Modal -->
    @include('admin.home.templates.create')
    @include('admin.home.templates.edit')
@endsection
