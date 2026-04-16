@extends('user.layout')

@section('content')
    <div class="page-header">
        <h4 class="page-title">{{ __('Choose Property Type') }}</h4>
        <ul class="breadcrumbs">
            <li class="nav-home">
                <a href="#">
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
                <a href="#">{{ __('Property Type') }}</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">

            <div class="card">
                <div class="card-header">
                    <h3>{{ __('Choose Property Type') }}</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <a href="{{ route('user.property_management.create_property') . '?type=autos_camionetas' }}"
                                class="d-block text-decoration-none" style="text-decoration: none !important;">
                                <div class="card card-stats card-round">
                                    <div class="card-body text-decoration-none">
                                        <div class="row align-items-center ">
                                            <div class="col-12">
                                                <div class="col-icon mx-auto">
                                                    <div class="icon-big text-center icon-primary bubble-shadow-small">
                                                        <i class="fas fa-car"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col col-stats ml-3 ml-sm-0">
                                                <div class="numbers mx-auto text-center">
                                                    <h2 class="card-title mt-2 mb-4 text-uppercase ">
                                                        Autos y Camionetas
                                                    </h2>
                                                    <p class="card-category">
                                                        <strong>Total:</strong>
                                                        {{ $autosCamionetasCount ?? 0 }}
                                                        Vehículos
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-lg-6">
                            <a href="{{ route('user.property_management.create_property') . '?type=camiones' }}"
                                class="d-block text-decoration-none" style="text-decoration: none !important;">
                                <div class="card card-stats card-round">
                                    <div class="card-body text-decoration-none">
                                        <div class="row align-items-center ">
                                            <div class="col-12">
                                                <div class="col-icon mx-auto">
                                                    <div class="icon-big text-center icon-warning bubble-shadow-small">
                                                        <i class="fas fa-truck"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col col-stats ml-3 ml-sm-0">
                                                <div class="numbers mx-auto text-center">
                                                    <h2 class="card-title mt-2 mb-4 text-uppercase ">
                                                        Camiones
                                                    </h2>
                                                    <p class="card-category">
                                                        <strong>Total:</strong>
                                                        {{ $camionesCount ?? 0 }}
                                                        Vehículos
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
