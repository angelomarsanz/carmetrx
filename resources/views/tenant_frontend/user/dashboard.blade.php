@extends('tenant_frontend.layout')

@php $title = $keywords['dashboard']; @endphp

@section('pageHeading')
    {{ $title }}
@endsection

@section('content')
    @includeIf('tenant_frontend.partials.breadcrumb', ['breadcrumb' => $breadcrumb, 'title' => $title])

    <section class="user-dashboard pt-100 pb-60">
        <div class="container">
            <div class="row">
                @includeIf('tenant_frontend.user.side-navbar')

                <div class="col-lg-9">
                    <div class="user-profile-details mb-30">
                        <div class="account-info radius-md">
                         
                            <div class="title">
                                <h4>{{ $keywords['Account Information'] ?? __('Account Information') }}</h4>
                            </div>
                            <div class="main-info">
                                <ul class="list">
                                    <li><span>{{ $keywords['Name'] ?? __('Name') }}:</span>
                                        <span>{{ $authUser->name }}</span>
                                    </li>
                                    <li><span>{{ $keywords['Username'] ?? __('Username') }}:</span>
                                        <span>{{ $authUser->username }}</span>
                                    </li>
                                    <li><span>{{ $keywords['Email'] ?? __('Email') }}:</span>
                                        <span>{{ $authUser->email }}</span>
                                    </li>
                                    <li><span>{{ $keywords['Phone'] ?? __('Phone') }}:</span>
                                        <span>{{ $authUser->phone }}</span>
                                    </li>
                                    <li><span>{{ $keywords['City'] ?? __('City') }}:</span>
                                        <span>{{ $authUser->city }}</span>
                                    </li>
                                    <li><span>{{ $keywords['Country'] ?? __('Country') }}:</span>
                                        <span>{{ $authUser->country }}</span>
                                    </li>
                                    <li><span>{{ $keywords['State'] ?? __('State') }}</span>
                                        <span>{{ $authUser->state }}</span>
                                    </li>
                                    <li><span>{{ $keywords['Zip Code'] ?? __('Zip Code') }}:</span>
                                        <span>{{ $authUser->zip_code }}</span>
                                    </li>
                                    <li><span>{{ $keywords['Address'] ?? __('Address') }}:</span>
                                        <span>{{ $authUser->address }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
              

            </div>
        </div>
    </section>
@endsection
