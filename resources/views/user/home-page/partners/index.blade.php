@extends('user.layout')

@section('content')
    <div class="page-header">
        <h4 class="page-title">{{ __('Partners Section') }}</h4>
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
                <a href="#">{{ __('Partners Section') }}</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-5">
                            <div class="card-title d-inline-block">{{ __('Partners') }}</div>
                        </div>
                        <div class="col-lg-3">
                        </div>
                        <div class="col-lg-4 mt-2 mt-lg-0">
                            <a href="#" data-toggle="modal" data-target="#createModal"
                                class="btn btn-primary btn-sm float-lg-right float-left">
                                <i class="fas fa-plus"></i> {{ __('Add Partner') }}
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            @if (count($partners) == 0)
                                <h3 class="text-center mt-2">{{ __('NO PARTNER FOUND') }}
                                </h3>
                            @else
                                <div class="row">
                                    @foreach ($partners as $partner)
                                        <div class="col-md-3">
                                            <div class="card">
                                                <div class="card-body">
                                                    <img src="{{ asset(Constant::WEBSITE_PARTNERS_IMAGE . '/' . $partner->image) }}"
                                                        alt="image" style="width:100%;">
                                                </div>

                                                <div class="card-footer text-center">
                                                    <a class="editbtn btn btn-secondary btn-sm mr-2" href="#"
                                                        data-toggle="modal" data-target="#editModal"
                                                        data-id="{{ $partner->id }}"
                                                        data-image="{{ asset(Constant::WEBSITE_PARTNERS_IMAGE . '/' . $partner->image) }}"
                                                        data-url="{{ $partner->url }}"
                                                        data-serial_number="{{ $partner->serial_number }}">
                                                        <span class="btn-label">
                                                            <i class="fas fa-edit"></i>
                                                        </span>
                                                        {{ __('Edit') }}
                                                    </a>

                                                    <form class="deleteForm d-inline-block"
                                                        action="{{ route('user.home_page.delete_partner', ['id' => $partner->id]) }}"
                                                        method="post">
                                                        @csrf
                                                        <button type="submit" class="btn btn-danger btn-sm deleteBtn">
                                                            <span class="btn-label">
                                                                <i class="fas fa-trash"></i>
                                                            </span>
                                                            {{ __('Delete') }}
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- create modal --}}
    @includeIf('user.home-page.partners.create')

    {{-- edit modal --}}
    @includeIf('user.home-page.partners.edit')
@endsection
