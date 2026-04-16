@extends('user.layout')

@php
    use App\Http\Helpers\UserPermissionHelper;
    use App\Models\User\Language;
    use Illuminate\Support\Facades\Auth;
    $default = Language::where('is_default', 1)->first();
    $user = Auth::guard('web')->user();
    $package = UserPermissionHelper::currentPackage($user->id);
    if (!empty($user)) {
        $permissions = UserPermissionHelper::packagePermission($user->id);
        $permissions = json_decode($permissions, true);
    }
@endphp

@section('content')
    <div class="mt-2 mb-4">
        <h2 class="pb-2">{{ __('Welcome back') }}, {{ Auth::guard('web')->user()->first_name }}
            {{ Auth::guard('web')->user()->last_name }}!</h2>
    </div>

    @if (is_null($package))
        @php
            $pendingMemb = \App\Models\Membership::query()
                ->where([['user_id', '=', Auth::id()], ['status', 0]])
                ->whereYear('start_date', '<>', '9999')
                ->orderBy('id', 'DESC')
                ->first();
            $pendingPackage = isset($pendingMemb)
                ? \App\Models\Package::query()->findOrFail($pendingMemb->package_id)
                : null;
        @endphp

        @if ($pendingPackage)
            <div class="alert alert-warning">
                {{__('You have requested a package which needs an action (Approval / Rejection) by Admin') . '. ' . __('You will be notified via mail once an action is taken') . '.' }}
            </div>
            <div class="alert alert-warning">
                <strong>{{ __('Pending Package') }}: </strong> {{ $pendingPackage->title }}
                <span class="badge badge-secondary">{{ __($pendingPackage->term) }}</span>
                <span class="badge badge-warning">{{ __('Decision Pending') }}</span>
            </div>
        @else
            <div class="alert alert-warning">
                {{ __('Your membership is expired') . '. ' . __('Please purchase a new package / extend the current package') . '.' }}
            </div>
        @endif
    @else
        <div class="row justify-content-center align-items-center mb-1">
            <div class="col-12">
                <div class="alert border-left border-primary text-dark">
                    @if ($package_count >= 2)
                        @if ($next_membership->status == 0)
                            <strong
                                class="text-danger">{{__('You have requested a package which needs an action (Approval / Rejection) by Admin') . '. ' .  __('You will be notified via mail once an action is taken') . '.' }}</strong><br>
                        @elseif ($next_membership->status == 1)
                            <strong
                                class="text-danger">{{__('You have another package to activate after the current package expires') . '. ' . __('You cannot purchase / extend any package, until the next package is activated') . '.' }}</strong><br>
                        @endif
                    @endif

                    <strong>{{ $keywords['Current Package'] ?? __('Current Package') }}: </strong>
                    {{ $current_package->title }}
                    <span class="badge badge-secondary">{{ __($current_package->term)}}</span>
                    @if ($current_membership->is_trial == 1)
                        ({{ $keywords['Expire Date'] ?? __('Expire Date') }}:
                        {{ Carbon\Carbon::parse($current_membership->expire_date)->format('M-d-Y') }})
                        <span class="badge badge-primary">{{ $keywords['Trial'] ?? __('Trial') }}</span>
                    @else
                        ({{ $keywords['Expire Date'] ?? __('Expire Date') }}:
                        {{ $current_package->term === 'lifetime' ? __('Lifetime') : Carbon\Carbon::parse($current_membership->expire_date)->format('M-d-Y') }})
                    @endif

                    @if ($package_count >= 2)
                        <div>
                            <strong>{{ $keywords['Next Package To Activate'] ?? __('Next Package To Activate') }}:
                            </strong> {{ $next_package->title }} <span
                                class="badge badge-secondary">{{ __($next_package->term) }}</span>
                            @if ($current_package->term != 'lifetime' && $current_membership->is_trial != 1)
                                (
                                {{ $keywords['Activation Date'] ?? __('Activation Date') }}:
                                {{ Carbon\Carbon::parse($next_membership->start_date)->format('M-d-Y') }},
                                {{ __('Expire Date') }}:
                                {{ $next_package->term === 'lifetime' ? __('Lifetime') : Carbon\Carbon::parse($next_membership->expire_date)->format('M-d-Y') }})
                            @endif
                            @if ($next_membership->status == 0)
                                <span
                                    class="badge badge-warning">{{ $keywords['Decision Pending'] ?? __('Decision Pending') }}</span>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
    <div class="row">
        <div class="col-sm-6 col-md-3">
            <a class="card card-stats card-round card-info" href="{{ route('user.property_management.properties') }}">
                <div class="card-body">
                    <div class="row">
                        <div class="col-5">
                            <div class="icon-big text-center">
                                <i class="fas fa-car"></i>
                            </div>
                        </div>
                        <div class="col-7 col-stats">
                            <div class="numbers">
                                <p class="card-category">{{ __('Cars') }}</p>
                                <h4 class="card-title">
                                    {{ $totalProperties }}
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-sm-6 col-md-3">
            <a class="card card-stats card-round card-secondary" href="{{ route('user.project_management.projects') }}">
                <div class="card-body">
                    <div class="row">
                        <div class="col-5">
                            <div class="icon-big text-center">
                                <i class="fas fa-motorcycle"></i>
                            </div>
                        </div>
                        <div class="col-7 col-stats">
                            <div class="numbers">
                                <p class="card-category">{{ __('Motorcycle') }}</p>
                                <h4 class="card-title">
                                    {{ $totalProjects }}

                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-sm-6 col-md-3">
            <a class="card card-stats card-round card-success" href="{{ route('user.agent_management.index') }}">
                <div class="card-body">
                    <div class="row">
                        <div class="col-5">
                            <div class="icon-big text-center">
                                <i class="fal fa-users"></i>
                            </div>
                        </div>
                        <div class="col-7 col-stats">
                            <div class="numbers">
                                <p class="card-category">{{ __('Agents') }}</p>
                                <h4 class="card-title">
                                    {{ $agents }}
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-sm-6 col-md-3">
            <a class="card card-stats card-round card-orchid " href="{{ route('user.registered_users') }}">
                <div class="card-body">
                    <div class="row">
                        <div class="col-5">
                            <div class="icon-big text-center">
                                <i class="la flaticon-users"></i>
                            </div>
                        </div>
                        <div class="col-7 col-stats">
                            <div class="numbers">
                                <p class="card-category">{{ $keywords['Registered Users'] ?? __('Registered Users') }}</p>
                                <h4 class="card-title">{{ $customers }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-sm-6 col-md-3">
            <a class="card card-stats card-default card-round" href="{{ route('user.follower.list') }}">
                <div class="card-body">
                    <div class="row">
                        <div class="col-5">
                            <div class="icon-big text-center">
                                <i class="fas fa-poll-people"></i>
                            </div>
                        </div>
                        <div class="col-7 col-stats">
                            <div class="numbers">
                                <p class="card-category">{{ $keywords['Followers'] ?? __('Followers') }}</p>
                                <h4 class="card-title">{{ $followers }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-sm-6 col-md-3">
            <a class="card card-stats card-primary card-round" href="{{ route('user.following.list') }}">
                <div class="card-body">
                    <div class="row">
                        <div class="col-5">
                            <div class="icon-big text-center">
                                <i class="fas fa-people-carry"></i>
                            </div>
                        </div>
                        <div class="col-7 col-stats">
                            <div class="numbers">
                                <p class="card-category">{{ $keywords['Followings'] ?? __('Followings') }}</p>
                                <h4 class="card-title">{{ $followings }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-sm-6 col-md-3">
            <a class="card card-stats card-round card-danger" href="{{ route('user.property_management.messages') }}">
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
                                <h4 class="card-title">
                                    {{ $propertyMessages }}
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-sm-6 col-md-3">
            <a class="card card-stats card-round card-primary" href="{{ route('user.project_management.messages') }}">
                <div class="card-body">
                    <div class="row">
                        <div class="col-5">
                            <div class="icon-big text-center">
                                <i class="fal fa-comments"></i>
                            </div>
                        </div>
                        <div class="col-7 col-stats">
                            <div class="numbers">
                                <p class="card-category">{{ __('Project Messages') }}</p>
                                <h4 class="card-title">
                                    {{ $projectMessages }}

                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">{{ __('Monthly Properties Post') }} ({{ date('Y') }}) </div>
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
                    <div class="card-title">{{ __('Monthly Projects Post') }} ({{ date('Y') }})</div>
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
@section('scripts')
    <script type="text/javascript" src="{{ asset('assets/tenant/js/chart.min.js') }}"></script>

    <script>
        "use strict";
        let propertiesPost = "{{ __('Monthly Properties Post') }}";
        let projectPost = "{{ __('Monthly Projects Post') }}";
        const monthArr = @json($monthArr);
        const totalPropertyArr = @json($totalPropertiesArr);
        const totalProjectsArr = @json($totalProjectsArr);
    </script>

    <script type="text/javascript" src="{{ asset('assets/tenant/js/my-chart.js') }}"></script>
@endsection
