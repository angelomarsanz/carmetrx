@extends('agent.layout')

@section('content')
    <div class="mt-2 mb-4">
        <h2 class="pb-2 card-title">{{ __('Welcome back') }},
            {{ Auth::guard('agent')->user()->username }}</h2>
    </div>

    {{-- dashboard information start --}}
    <div class="row dashboard-items">
        <div class="col-sm-6 col-md-3">
            <a href="{{ route('agent.property_management.properties', [getParam()]) }}"
                style="text-decoration: none !important;">
                <div class="card card-stats card-primary card-round">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-5">
                                <div class="icon-big text-center">
                                    <i class="far fa-home"></i>
                                </div>
                            </div>

                            <div class="col-7 col-stats">
                                <div class="numbers">
                                    <p class="card-category">{{ __('Properties') }}</p>
                                    <h4 class="card-title">{{ $totalProperties }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-sm-6 col-md-3">
            <a href="{{ route('agent.project_management.projects', [getParam()]) }}"
                style="text-decoration: none !important;">
                <div class="card card-stats card-secondary card-round">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-5">
                                <div class="icon-big text-center">
                                    <i class="far fa-city"></i>
                                </div>
                            </div>

                            <div class="col-7 col-stats">
                                <div class="numbers">
                                    <p class="card-category">{{ __('Projects') }}</p>
                                    <h4 class="card-title">{{ $totalProjects }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-sm-6 col-md-3">
            <a href="{{ route('agent.property_message.index', [getParam()]) }}" style="text-decoration: none !important;">
                <div class="card card-stats card-info card-round">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-5">
                                <div class="icon-big text-center">
                                    <i class="far fa-comments"></i>
                                </div>
                            </div>

                            <div class="col-7 col-stats">
                                <div class="numbers">
                                    <p class="card-category">{{ __('Property Messages') }}</p>
                                    <h4 class="card-title">{{ $propertyMessages }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-sm-6 col-md-3">
            <a href="{{ route('agent.project_management.messages', [getParam()]) }}"
                style="text-decoration: none !important;">
                <div class="card card-stats card-default card-round">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-5">
                                <div class="icon-big text-center">
                                    <i class="far fa-comments"></i>
                                </div>
                            </div>

                            <div class="col-7 col-stats">
                                <div class="numbers">
                                    <p class="card-category">{{ __('Project Messages') }}</p>
                                    <h4 class="card-title">{{ $projectMessages }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">{{ __('Monthly Properties Post') }}
                        ({{ date('Y') }})</div>
                </div>

                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="propertiesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">{{ __('Monthly Projects Post') }}
                        ({{ date('Y') }})</div>
                </div>

                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="projectsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('script')
    {{-- chart js --}}
    <script type="text/javascript" src="{{ asset('assets/tenant/js/chart.min.js') }}"></script>

    <script>
        "use strict";
        const monthArr = @json($monthArr);
        const totalPropertyArr = @json($totalPropertiesArr);
        const totalProjectsArr = @json($totalProjectsArr);
    </script>

    <script type="text/javascript" src="{{ asset('assets/tenant/js/my-chart.js') }}"></script>
@endsection
