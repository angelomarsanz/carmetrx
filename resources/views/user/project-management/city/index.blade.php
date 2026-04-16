@extends('user.layout')

{{-- this style will be applied when the direction of language is right-to-left --}}
@includeIf('user.partials.rtl-style')

@section('content')
    <div class="page-header">
        <h4 class="page-title">{{ __('Cities') }}</h4>
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
                <a href="#">{{ __('Cities') }}</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-3">
                            <div class="card-title d-inline-block">{{ __('Project Cities') }}</div>
                        </div>

                        <div class="col-lg-3">
                            @includeIf('user.partials.languages')
                        </div>
                        <div class="col-lg-3">
                            <form action="{{ route('user.project_management.cities') }}" method="get" id="carSearchForm">
                                <div class="row">

                                   
                                    <input type="text" name="name" value="{{ request()->input('name') }}"
                                        class="form-control" placeholder="{{ __('Search by city name') }}">
                                    <input type="hidden" name="language" value="{{ request('language') }}">
                                  
                                </div>
                            </form>
                        </div>
                        <div class="col-lg-3 mt-2 mt-lg-0">
                            <a href="#" data-toggle="modal" data-target="#createModal"
                                class="btn btn-primary btn-sm float-lg-right float-left"><i class="fas fa-plus"></i>
                                {{ __('Add') }}</a>

                            <button class="btn btn-danger btn-sm float-right mr-2 d-none bulk-delete"
                                data-href="{{ route('user.project_management.bulk_delete_city') }}">
                                <i class="flaticon-interface-5"></i> {{ __('Delete') }}
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12">
                            @if (count($cities) == 0)
                                <h3 class="text-center mt-2">{{ __('NO CITY FOUND') }}
                                </h3>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-striped mt-3" id="basic-datatables">
                                        <thead>
                                            <tr>
                                                <th scope="col">
                                                    <input type="checkbox" class="bulk-check" data-val="all">
                                                </th>
                                                @if ($userBs->property_country_status == 1)
                                                    <th scope="col">{{ __('Country Name') }}
                                                    </th>
                                                @endif
                                                @if ($userBs->property_state_status == 1)
                                                    <th scope="col">{{ __('State Name') }}
                                                    </th>
                                                @endif
                                                <th scope="col">{{ __('City Name') }}</th>
                                                {{-- <th scope="col">{{ __('Featured') }}</th> --}}
                                                <th scope="col">{{ __('Status') }}</th>
                                                <th scope="col">{{ __('Serial Number') }}
                                                </th>
                                                <th scope="col">{{ __('Actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($cities as $city)
                                               
                                                <tr>
                                                    <td>
                                                        <input type="checkbox" class="bulk-check"
                                                            data-val="{{ $city->id }}">
                                                    </td>
                                                    @if ($userBs->property_country_status == 1)
                                                        <td>
                                                            {{ strlen($city->country_name) > 50 ? mb_substr($city->country_name, 0, 50, 'UTF-8') . '...' : $city->country_name }}
                                                        </td>
                                                    @endif
                                                    @if ($userBs->property_state_status == 1)
                                                        <td>
                                                            @if (!is_null($city->state_name))
                                                                {{ strlen($city->state_name) > 50 ? mb_substr($city->name, 0, 50, 'UTF-8') . '...' : $city->state_name }}
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                    @endif
                                                    <td>
                                                        {{ strlen($city?->name) > 50 ? mb_substr($city?->name, 0, 50, 'UTF-8') . '...' : $city?->name }}
                                                    </td>

                                                    

                                                    <td>
                                                        @if ($city->status == 1)
                                                            <h2 class="d-inline-block"><span
                                                                    class="badge badge-success">{{ __('Active') }}</span>
                                                            </h2>
                                                        @else
                                                            <h2 class="d-inline-block"><span
                                                                    class="badge badge-danger">{{ __('Deactive') }}</span>
                                                            </h2>
                                                        @endif
                                                    </td>
                                                    <td>{{ $city->serial_number }}</td>
                                                    <td>
                                                        <a class="btn btn-secondary btn-sm mr-1  mt-1 editBtn"
                                                            href="#" data-toggle="modal" data-target="#editModal"
                                                            data-id="{{ $city->id }}"
                                                            @foreach ($tenantFrontLangs as $lang)
                                                            @php
                                                                 
                                                                $cityContent =  $city->getContent($lang->id);
                                                            @endphp  
                                                            data-{{ $lang->code }}_name="{{ $cityContent?->name }}" @endforeach
                                                            data-status="{{ $city->status }}"
                                                            data-image="{{ asset('assets/img/project-city/' . $city->image) }}"
                                                            data-serial_number="{{ $city->serial_number }}">
                                                            <span class="btn-label">
                                                                <i class="fas fa-edit"></i>
                                                            </span>
                                                        </a>

                                                        <form class="deleteform d-inline-block"
                                                            action="{{ route('user.project_management.delete_city') }}"
                                                            method="post">
                                                            @csrf
                                                            <input type="hidden" name="id"
                                                                value="{{ $city->id }}">

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
                    </div>
                </div>

                
            </div>
        </div>
    </div>

    {{-- create modal --}}
    @include('user.project-management.city.create')

    {{-- edit modal --}}
    @include('user.project-management.city.edit')
@endsection
@section('scripts')
    <script>
        let stateUrl = "{{ route('user.project_management.get_state') }}";
    </script>
    <script type="text/javascript" src="{{ asset('assets/tenant/js/city.js') }}"></script>
@endsection
