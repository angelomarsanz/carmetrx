@extends('tenant_frontend.layout')

@php $title =$keywords['Property Wishlist'] ??__('Property Wishlist') ; @endphp

@section('pageHeading')
    {{ $title }}
@endsection

@section('content')
    @includeIf('tenant_frontend.partials.breadcrumb', [
        'breadcrumb' => $breadcrumb,
        'title' => $title,
        'subtitle' => $keywords['My Wishlist'] ?? __('Property Wishlist'),
    ])

    <section class="user-dashboard pt-100 pb-60">
        <div class="container">
            <div class="row">
                @includeIf('tenant_frontend.user.side-navbar')

                <div class="col-lg-9">
                    <div class="row">

                        <div class="account-info radius-md mb-40">
                            <div class="title">
                                <h4>{{ $keywords['Property Wishlist'] ?? __('Property Wishlist') }}</h4>
                            </div>

                            <div class="main-info">
                                @if (count($wishlists) == 0)
                                    <div class="row text-center mt-2">
                                        <div class="col">
                                            <h4>{{ $keywords['No Property Wishlist Found'] ?? __('No Property Wishlist Found') }}
                                            </h4>
                                        </div>
                                    </div>
                                @else
                                    <div class="main-table">
                                        <div class="table-responsive">
                                            <table id="user-datatable"
                                                class="dataTables_wrapper dt-responsive table-striped w-100 dataTable no-footer">
                                                <thead>
                                                    <tr>
                                                        <th>{{ $keywords['Property'] }}</th>
                                                        <th>{{ $keywords['Action'] }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($wishlists as $item)
                                                        <tr id="service-{{ $item->id }}">
                                                            @php
                                                                $title = $item->propertyContent?->title;
                                                                $slug = $item->propertyContent?->slug;
                                                                $propertyId = $item->property_id;
                                                            @endphp

                                                            <td class="pl-3">
                                                                <a href="{{ route('frontend.property.details', [getParam(), 'slug' => $slug]) }}"
                                                                    target="_blank">
                                                                    {{ strlen($title) > 50 ? mb_substr($title, 0, 50, 'UTF-8') . '...' : $title }}
                                                                </a>
                                                            </td>
                                                            <td class="pl-3">
                                                                <a href="{{ route('frontend.property.details', [getParam(), 'slug' => $slug]) }}"
                                                                    class="btn btn-primary " target="_blank">
                                                                    {{ $keywords['Details'] ?? __('Details') }}
                                                                </a>

                                                                <form
                                                                    action="{{ route('frontend.user.property.remove.wishlist', [getParam(), 'property' => $propertyId]) }}"
                                                                    method="GET" class="d-inline">
                                                                    @csrf
                                                                    <button type="submit"
                                                                        class="btn btn-primary radius-sm">
                                                                        {{ $keywords['Remove'] ?? __('Remove') }}
                                                                    </button>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
