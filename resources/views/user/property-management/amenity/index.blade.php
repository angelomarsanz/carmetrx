@extends('user.layout')

{{-- this style will be applied when the direction of language is right-to-left --}}
@includeIf('user.partials.rtl-style')

@section('content')
    <div class="page-header">
        <h4 class="page-title">{{ __('Amenities') }}</h4>
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
                <a href="#">{{ __('Property Management') }}</a>
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
                <a href="#">{{ __('Amenities') }}</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-3">
                            <div class="card-title d-inline-block">{{ __('Amenities') }}</div>
                        </div>

                        <div class="col-lg-3">
                            @includeIf('user.partials.languages')
                        </div>
                        <div class="col-lg-3">
                            <form action="{{ route('user.property_management.amenities') }}" method="get"
                                id="carSearchForm">
                                <div class="row">


                                    <input type="text" name="name" value="{{ request()->input('name') }}"
                                        class="form-control" placeholder="{{ __('Search by amenity name') }}">
                                    <input type="hidden" name="language" value="{{ request('language') }}">

                                </div>
                            </form>
                        </div>
                        <div class="col-lg-3 mt-2 mt-lg-0">
                            <a href="#" data-toggle="modal" data-target="#createModal"
                                class="btn btn-primary btn-sm float-lg-right float-left"><i class="fas fa-plus"></i>
                                {{ __('Add') }}</a>

                            <button class="btn btn-danger btn-sm float-right mr-2 d-none bulk-delete"
                                data-href="{{ route('user.property_management.bulk_delete_amenity') }}">
                                <i class="flaticon-interface-5"></i> {{ __('Delete') }}
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12">


                            @if (count($amenities) > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped mt-3">
                                        <thead>
                                            <tr>
                                                <th scope="col">
                                                    <input type="checkbox" class="bulk-check" data-val="all">
                                                </th>
                                                <th scope="col">{{ __('Icon') }}</th>
                                                <th scope="col">{{ __('Name') }}</th>
                                                <th scope="col">{{ __('Status') }}</th>
                                                <th scope="col">{{ __('Serial Number') }}
                                                </th>
                                                <th scope="col">{{ __('Actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($amenities as $amenity)
                                                <tr>
                                                    <td>
                                                        <input type="checkbox" class="bulk-check"
                                                            data-val="{{ $amenity->id }}">
                                                    </td>
                                                    <td>
                                                        <i class="{{ $amenity->icon }}"></i>
                                                    </td>
                                                    <td>
                                                        {{ strlen($amenity?->name) > 50 ? mb_substr($amenity?->name, 0, 50, 'UTF-8') . '...' : $amenity?->name }}
                                                    </td>
                                                    <td>
                                                        @if ($amenity->status == 1)
                                                            <h2 class="d-inline-block"><span
                                                                    class="badge badge-success">{{ __('Active') }}</span>
                                                            </h2>
                                                        @else
                                                            <h2 class="d-inline-block"><span
                                                                    class="badge badge-danger">{{ __('Deactive') }}</span>
                                                            </h2>
                                                        @endif
                                                    </td>
                                                    <td>{{ $amenity->serial_number }}</td>
                                                    <td>
                                                        <a class="btn btn-secondary btn-sm mr-1 mt-1 editBtn" href="#"
                                                            data-toggle="modal" data-target="#editModal"
                                                            @foreach ($tenantFrontLangs as $lang)

                                                            @php
                                                                $amContent = $amenity->getContent($lang->id);
                                                            @endphp

                                                            data-{{ $lang->code }}_name="{{ $amContent?->name }}" @endforeach
                                                            data-amenity_id="{{ $amenity->id }}"
                                                            data-icon="{{ $amenity->icon }}"
                                                            data-status="{{ $amenity->status }}"
                                                            data-serial_number="{{ $amenity->serial_number }}">
                                                            <span class="btn-label">
                                                                <i class="fas fa-edit"></i>
                                                            </span>
                                                        </a>



                                                        <form class="deleteform d-inline-block"
                                                            action="{{ route('user.property_management.delete_amenity') }}"
                                                            method="post">
                                                            @csrf
                                                            <input type="hidden" name="id"
                                                                value="{{ $amenity->id }}">

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
                            @else
                                <h3 class="text-center mt-2">{{ __('NO AMENITY FOUND') }}
                                </h3>
                            @endif
                        </div>
                        <div class="col-12">
                            {{ $amenities->appends(['name' => request()->input('name'), 'language' => request()->input('language')])->links() }}
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>

    {{-- create modal --}}
    @include('user.property-management.amenity.create')

    {{-- edit modal --}}
    @include('user.property-management.amenity.edit')
@endsection
