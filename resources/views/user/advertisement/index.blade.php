@extends('user.layout')

@section('content')
    <div class="page-header">
        <h4 class="page-title">{{ __('Advertisements') }}</h4>
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
                <a href="#">{{ __('Advertisements') }}</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="card-title d-inline-block">{{ __('Ads') }}</div>
                        </div>

                        <div class="col-lg-8 mt-2 mt-lg-0">
                            <a href="#" data-toggle="modal" data-target="#createModal"
                                class="btn btn-primary btn-sm float-lg-right float-left"><i class="fas fa-plus"></i>
                                {{ __('Add') }}</a>

                            <button class="btn btn-danger float-right btn-sm mr-2 d-none bulk-delete"
                                data-href="{{ route('user.advertise.bulk_delete_advertisement') }}">
                                <i class="flaticon-interface-5"></i> {{ __('Delete') }}
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12">
                            @if (count($ads) == 0)
                                <h3 class="text-center">
                                    {{ __('NO ADVERTISEMENT FOUND') }}</h3>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-striped mt-3" id="basic-datatables">
                                        <thead>
                                            <tr>
                                                <th scope="col">
                                                    <input type="checkbox" class="bulk-check" data-val="all">
                                                </th>
                                                <th scope="col">{{ __('Ad Type') }}</th>
                                                <th scope="col">{{ __('Resolution') }}</th>
                                                <th scope="col">{{ __('Image') }}</th>
                                                <th scope="col">{{ __('Views') }}</th>
                                                <th scope="col">{{ __('Actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($ads as $ad)
                                                <tr>
                                                    <td>
                                                        <input type="checkbox" class="bulk-check"
                                                            data-val="{{ $ad->id }}">
                                                    </td>
                                                    <td>{{ __(ucfirst($ad->ad_type)) }}</td>
                                                    <td>
                                                        @if ($ad->resolution_type == 1)
                                                            {{ __('300 * 250') }}
                                                        @elseif ($ad->resolution_type == 2)
                                                            {{ __('300 * 600') }}
                                                        @else
                                                            {{ __('728 * 90') }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($ad->ad_type == 'banner')
                                                            <img src="{{ asset(Constant::WEBSITE_ADVERTISEMENT_IMAGE.'/'. $ad->image) }}"
                                                                alt="ad image" width="45">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>{{ $ad->views }}</td>
                                                    <td>
                                                        <a class="btn btn-secondary btn-sm mr-1 editbtn" href="#"
                                                            data-toggle="modal" data-target="#editModal"
                                                            data-id="{{ $ad->id }}"
                                                            data-ad_type="{{ $ad->ad_type }}"
                                                            data-resolution_type="{{ $ad->resolution_type }}"
                                                            data-image="{{ $ad->ad_type == 'banner' ? asset(Constant::WEBSITE_ADVERTISEMENT_IMAGE.'/'. $ad->image) : asset('assets/tenant/image/default.jpg') }}"
                                                            data-url="{{ $ad->url }}" data-slot="{{ $ad->slot }}"
                                                            data-edit="editAdvertisement">
                                                            <span class="btn-label">
                                                                <i class="fas fa-edit"></i>
                                                            </span>
                                                            {{  __('Edit') }}
                                                        </a>

                                                        <form class="deleteform d-inline-block"
                                                            action="{{ route('user.advertise.delete_advertisement', ['id' => $ad->id]) }}"
                                                            method="post">

                                                            @csrf
                                                            <button type="submit" class="btn btn-danger btn-sm deletebtn">
                                                                <span class="btn-label">
                                                                    <i class="fas fa-trash"></i>
                                                                </span>
                                                                {{  __('Delete') }}
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
    @include('user.advertisement.create')

    {{-- edit modal --}}
    @include('user.advertisement.edit')
@endsection

@section('scripts')
    <script type="text/javascript" src="{{ asset('assets/tenant/js/partial.js') }}"></script>
@endsection
