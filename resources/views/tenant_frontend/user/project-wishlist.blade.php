@extends('tenant_frontend.layout')

@php $title =$keywords['Project Wishlist'] ??__('Project Wishlist') ; @endphp

@section('pageHeading')
    {{ $title }}
@endsection

@section('content')
    @includeIf('tenant_frontend.partials.breadcrumb', [
        'breadcrumb' => $breadcrumb,
        'title' => $title,
        'subtitle' => $keywords['Project Wishlist'] ?? __('Project Wishlist'),
    ])

    <section class="user-dashboard pt-100 pb-60">
        <div class="container">
            <div class="row">
                @includeIf('tenant_frontend.user.side-navbar')

                <div class="col-lg-9">
                    <div class="row">

                        <div class="account-info radius-md mb-40">
                            <div class="title">
                                <h4>{{ $keywords['Project Wishlist'] ?? __('Project Wishlist') }}</h4>
                            </div>

                            <div class="main-info">
                                @if (count($wishlists) == 0)
                                    <div class="row text-center mt-2">
                                        <div class="col">
                                            <h4>{{ $keywords['No Project Wishlist Found'] ?? __('No Project Wishlist Found') }}
                                            </h4>
                                        </div>
                                    </div>
                                @else
                                    <div class="main-table">
                                        <div class="table-responsive">
                                            <table id="user-datatable"
                                                class="dataTables_wrapper dt-responsive table-striped dt-bootstrap4"
                                                style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th>{{ $keywords['Project'] ?? __('Project') }}</th>
                                                        <th>{{ $keywords['Action'] }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($wishlists as $item)
                                                        <tr id="service-{{ $item->id }}">
                                                            @php
                                                                $title = $item->projectContent?->title;
                                                                $slug = $item->projectContent?->slug;
                                                                $projectId = $item->project_id;
                                                            @endphp

                                                            <td class="pl-3">
                                                                <a href="{{ route('frontend.project.details', [getParam(), 'slug' => $slug]) }}"
                                                                    target="_blank">
                                                                    {{ strlen($title) > 50 ? mb_substr($title, 0, 50, 'UTF-8') . '...' : $title }}
                                                                </a>
                                                            </td>
                                                            <td class="pl-3">
                                                                <a href="{{ route('frontend.project.details', [getParam(), 'slug' => $slug]) }}"
                                                                    class="btn  " target="_blank">
                                                                    {{ $keywords['details'] ?? __('details') }}
                                                                </a>

                                                                <form
                                                                    action="{{ route('frontend.user.project.remove.wishlist', [getParam(), 'project' => $projectId]) }}"
                                                                    method="GET" class="d-inline">
                                                                    @csrf
                                                                    <button type="submit" class="btn">
                                                                        {{ $keywords['remove'] ?? __('remove') }}
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
