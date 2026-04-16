@extends('user.layout')

{{-- this style will be applied when the direction of language is right-to-left --}}
@includeIf('user.partials.rtl-style')


@section('content')
    <div class="page-header">
        <h4 class="page-title">{{ __('Countries') }}</h4>
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
                <a href="#">{{ __('Project Management') }}</a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="#">{{ __('Specifications') }}</a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="#">{{ __('Countries') }}</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-3">
                            <div class="card-title d-inline-block">{{ __('Project Countries') }}</div>
                        </div>

                        <div class="col-lg-3">
                            @includeIf('user.partials.languages')
                        </div>
                        <div class="col-lg-3">
                            <form action="{{ route('user.project_management.countries') }}" method="get"
                                id="carSearchForm">
                                <div class="row">


                                    <input type="text" name="name" value="{{ request('name') }}" class="form-control"
                                        placeholder="{{ __('Search by country name') }}">
                                    <input type="hidden" name="language" value="{{ request('language') }}">

                                </div>
                            </form>
                        </div>
                        <div class="col-lg-3 mt-2 mt-lg-0">
                            <a href="#" data-toggle="modal" data-target="#createModal"
                                class="btn btn-primary btn-sm float-lg-right float-left"><i class="fas fa-plus"></i>
                                {{ __('Add') }}</a>

                            <button class="btn btn-danger btn-sm float-right mr-2 d-none bulk-delete"
                                data-href="{{ route('user.project_management.bulk_delete_country') }}">
                                <i class="flaticon-interface-5"></i> {{ __('Delete') }}
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12">
                            @if (count($countries) == 0)
                                <h3 class="text-center mt-2">
                                    {{ __('NO COUNTRIES FOUND') }}</h3>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-striped mt-3">
                                        <thead>
                                            <tr>
                                                <th scope="col">
                                                    <input type="checkbox" class="bulk-check" data-val="all">
                                                </th>
                                                <th scope="col">{{ __('Name') }}</th>
                                                <th scope="col">{{ __('Actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($countries as $country)
                                                <tr>
                                                    <td>
                                                        <input type="checkbox" class="bulk-check"
                                                            data-val="{{ $country->id }}">
                                                    </td>
                                                    <td>
                                                        {{ strlen($country?->name) > 50 ? mb_substr($country?->name, 0, 50, 'UTF-8') . '...' : $country?->name }}
                                                    </td>

                                                    <td>
                                                        <a class="btn btn-secondary btn-sm mr-1  mt-1 editBtn"
                                                            href="#" data-toggle="modal" data-target="#editModal"
                                                            data-id="{{ $country->id }}"
                                                            @foreach ($tenantFrontLangs as $lang)
                                                            @php
                                                                
                                                                $counContent = $country->getContent($lang->id);
                                                            @endphp 
                                                            data-{{ $lang->code }}_name="{{ $counContent?->name }}" @endforeach
                                                            <span class="btn-label">
                                                            <i class="fas fa-edit"></i>
                                                            </span>
                                                        </a>

                                                        <form class="deleteform d-inline-block"
                                                            action="{{ route('user.project_management.delete_country') }}"
                                                            method="post">
                                                            @csrf
                                                            <input type="hidden" name="id"
                                                                value="{{ $country->id }}">

                                                            <button type="submit"
                                                                class="btn btn-danger  mt-1 btn-sm deletebtn">
                                                                <span class="btn-label">
                                                                    <i class="fas fa-trash"></i>
                                                                </span>
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
                        <div class="col-12">
                            {{ $countries->appends(['name' => request()->input('name'), 'language' => request()->input('language')])->links() }}
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>

    {{-- create modal --}}
    @include('user.project-management.country.create')

    {{-- edit modal --}}
    @include('user.project-management.country.edit')
@endsection
