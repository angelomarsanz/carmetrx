@extends('user.layout')
@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/buy_plan.css') }}">
@endsection
@includeIf('user.partials.rtl-style')

@php
    $package = \App\Http\Helpers\UserPermissionHelper::currentPackage($tenant->id);
@endphp

@section('content')


    @if (!empty($currentLang) && $currentLang->rtl == 1)
        <style>
            .card-pricing2 .pricing-content {
                transform: translate3d(-0px, 0, 0) !important;
            }

            .card-pricing2 .pricing-content {
                text-align: right;
            }

            .card-pricing2 .pricing-content li.disable:before,
            .card-pricing2 .pricing-content li:before {
                left: unset;
                right: -50px;
            }
        </style>
    @endif

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
                {{ __('You have requested a package which needs an action (Approval or Rejection) by Admin. You will be notified via mail once an action is taken') }}

            </div>
            <div class="alert alert-warning">
                <strong>{{ __('Pending Package') }}: </strong> {{ $pendingPackage->title }}
                <span class="badge badge-secondary">{{ __($pendingPackage->term) }}</span>
                <span class="badge badge-warning">{{ __('Decision Pending') }}</span>
            </div>
        @else
            <div class="alert alert-warning">
                {{ __('Your membership is expired. Please purchase a new package or extend the current package') }}
            </div>
        @endif
    @else
        <div class="row justify-content-center align-items-center mb-1">
            <div class="col-12">
                <div class="alert border-left border-primary text-dark">
                    @if ($package_count >= 2)
                        @if ($next_membership->status == 0)
                            <strong class="text-danger">
                                {{ __('You have requested a package which needs an action (Approval or Rejection) by Admin. You will be notified via mail once an action is taken') }}
                            </strong><br>
                        @elseif ($next_membership->status == 1)
                            <strong class="text-danger">
                                {{__('You have another package to activate after the current package expires') . '. ' .  __('You cannot purchase or extend any package, until the next package is activated') }}
                            </strong><br>
                        @endif
                    @endif

                    <strong>{{ __('Current Package') }}: </strong>
                    {{ $current_package->title }}
                    <span class="badge badge-secondary">{{ __($current_package->term) }}</span>
                    @if ($current_membership->is_trial == 1)
                        ({{ __('Expire Date') }}:
                        {{ Carbon\Carbon::parse($current_membership->expire_date)->format('M-d-Y') }})
                        <span class="badge badge-primary"> {{ __('Trial') }}</span>
                    @else
                        ({{ __('Expire Date') }}:
                        {{ $current_package->term === 'lifetime' ? __('Lifetime') : Carbon\Carbon::parse($current_membership->expire_date)->format('M-d-Y') }})
                    @endif

                    @if ($package_count >= 2)
                        <div>
                            <strong>{{ __('Next Package To Activate') }}:
                            </strong> {{ $next_package->title }} <span
                                class="badge badge-secondary">{{ __($next_package->term) }}</span>
                            @if ($current_package->term != 'lifetime' && $current_membership->is_trial != 1)
                                (
                                {{ __('Next Package To Activate') }}:
                                {{ Carbon\Carbon::parse($next_membership->start_date)->format('M-d-Y') }},
                                {{ __('Expire Date') }}:
                                {{ $next_package->term === 'lifetime' ? __('Lifetime') : Carbon\Carbon::parse($next_membership->expire_date)->format('M-d-Y') }})
                            @endif
                            @if ($next_membership->status == 0)
                                <span class="badge badge-warning">
                                    {{ __('Decision Pending') }} </span>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
    <div class="row mb-5 justify-content-center">
        @foreach ($packages as $key => $package)
            <div class="col-md-3 pr-md-0 mb-5">
                <div class="card-pricing2 @if (isset($current_package->id) && $current_package->id === $package->id) card-success @else card-primary @endif">
                    <div class="pricing-header">
                        <h3 class="fw-bold d-inline-block">
                            {{ $package->title }}
                        </h3>
                        @if (isset($current_package->id) && $current_package->id === $package->id)
                            <h3 class="badge badge-danger d-inline-block float-right ml-2">
                                {{ __('Current') }} </h3>
                        @endif
                        @if ($package_count >= 2 && $next_package->id == $package->id)
                            @if ($next_membership->status == 1)
                                <h3 class="badge badge-warning d-inline-block float-right ml-2">
                                    {{ __('Next') }} </h3>
                            @endif
                        @endif
                        <span class="sub-title"></span>
                    </div>
                    <div class="price-value">
                        <div class="value">
                            <span
                                class="amount">{{ $package->price == 0 ? __('Free') : format_price($package->price) }}</span>
                            <span class="month">/{{ __($package->term) }}</span>
                        </div>
                    </div>


                    <ul class="pricing-content">
                        @php
                            $pFeatures = json_decode($package->features, true);
                            $allPfeatures = json_decode($be->package_features, true);
                        @endphp

                        @foreach ($allPfeatures as $feature)
                            <li class="{{ is_array($pFeatures) && in_array($feature, $pFeatures) ? '' : 'disabled' }}">
                                @if ($feature == 'Custom Domain')
                                    <i
                                        class="{{ is_array($pFeatures) && in_array($feature, $pFeatures) ? 'fal fa-check' : 'fal fa-times' }}"></i>

                                    {{ $package->custom_domain_limit === 1 ? __('Custom Domain') : __('Custom Domains') }}
                                    @if ($package->custom_domain_limit > 0)
                                        (<span>{{ $package->custom_domain_limit === 999999 ? __('Unlimited') : $package->custom_domain_limit }}</span>)
                                    @endif
                                @elseif ($feature == 'Subdomain')
                                    <i
                                        class="{{ is_array($pFeatures) && in_array($feature, $pFeatures) ? 'fal fa-check' : 'fal fa-times' }}"></i>

                                    {{ $package->subdomain_limit === 1 ? __('Subomain') : __('Subomains') }}
                                    @if ($package->subdomain_limit > 0)
                                        (<span>{{ $package->subdomain_limit === 999999 ? __('Unlimited') : $package->subdomain_limit }}</span>)
                                    @endif
                                @elseif ($feature == 'Additional Language')
                                    <i
                                        class="{{ is_array($pFeatures) && in_array($feature, $pFeatures) ? 'fal fa-check' : 'fal fa-times  ' }}"></i>

                                    @if (is_array($pFeatures) && in_array($feature, $pFeatures))
                                        {{ $package->number_of_language === 999999 ? __('Unlimited') : $package->number_of_language . ' ' }}
                                        {{ $package->number_of_language === 1 ? __('Additional Language') : __('Additional Languages') }}
                                    @else
                                        {{ __('Additional Language') }}
                                    @endif
                                @elseif ($feature == 'Additional Page')
                                    <i
                                        class="{{ is_array($pFeatures) && in_array($feature, $pFeatures) ? 'fal fa-check' : 'fal fa-times  ' }}"></i>

                                    @if (is_array($pFeatures) && in_array($feature, $pFeatures))
                                        {{ $package->number_of_additional_page === 999999 ? __('Unlimited') : $package->number_of_additional_page . ' ' }}
                                        {{ $package->number_of_additional_page === 1 ? __('Additional Page') : __('Additional Pages') }}
                                    @else
                                        {{ __('Additional Page') }}
                                    @endif
                                @elseif ($feature == 'Agent')
                                    <i
                                        class="{{ is_array($pFeatures) && in_array($feature, $pFeatures) ? 'fal fa-check' : 'fal fa-times' }}"></i>

                                    @if (is_array($pFeatures) && in_array($feature, $pFeatures))
                                        {{ $package->number_of_agent === 999999 ? __('Unlimited') : $package->number_of_agent . ' ' }}
                                        {{ $package->number_of_agent === 1 ? __('Agent') : __('Agents') }}
                                    @else
                                        {{ __('Agent') }}
                                    @endif
                                @elseif ($feature == 'Property Management')
                                    <i
                                        class="{{ is_array($pFeatures) && in_array($feature, $pFeatures) ? 'fal fa-check ' : 'fal fa-times' }}"></i>
                                    {{ __('Property Management') }}
                                    @if (is_array($pFeatures) && in_array($feature, $pFeatures))
                                        <ul class="list-unstyled ms-5 py-3">
                                            <li>
                                                <i
                                                    class="{{ is_array($pFeatures) && in_array($feature, $pFeatures) ? 'fal fa-check' : 'fal fa-times' }}"></i>

                                                {{ $package->number_of_property === 999999 ? __('Unlimited') : $package->number_of_property . ' ' }}
                                                {{ $package->number_of_property === 1 ? __('Property') : __('Properties') }}

                                            </li>
                                            <li>
                                                <i
                                                    class="{{ is_array($pFeatures) && in_array($feature, $pFeatures) ? 'fal fa-check' : 'fal fa-times' }}"></i>

                                                {{ $package->number_of_property_featured === 999999 ? __('Unlimited') : $package->number_of_property_featured . ' ' }}
                                                {{ $package->number_of_property_featured === 1 ? __('Featured Property') : __('Featured Properties') }}
                                            </li>
                                            <li>
                                                <i
                                                    class="{{ is_array($pFeatures) && in_array($feature, $pFeatures) ? 'fal fa-check' : 'fal fa-times' }}"></i>
                                                {{ $package->number_of_property_gallery_images === 999999 ? __('Unlimited') : $package->number_of_property_gallery_images . ' ' }}
                                                {{ $package->number_of_property_gallery_images === 1 ? __('Gallery Image Per Property') : __('Gallery Images Per Property') }}
                                            </li>
                                            <li>
                                                <i
                                                    class="{{ is_array($pFeatures) && in_array($feature, $pFeatures) ? 'fal fa-check' : 'fal fa-times' }}"></i>
                                                {{ $package->number_of_property_adittionl_specifications === 999999 ? __('Unlimited') : $package->number_of_property_adittionl_specifications . ' ' }}
                                                {{ $package->number_of_property_adittionl_specifications === 1 ? __('Additional Specification Per Property') : __('Additional Specifications Per Property') }}
                                            </li>

                                        </ul>
                                    @endif
                                @elseif ($feature == 'Project Management')
                                    <i
                                        class="{{ is_array($pFeatures) && in_array($feature, $pFeatures) ? 'fal fa-check' : 'fal fa-times' }}"></i>
                                    {{ __('Project Management') }}
                                    @if (is_array($pFeatures) && in_array($feature, $pFeatures))
                                        <ul class="list-unstyled ms-5 py-3">
                                            <li>
                                                <i
                                                    class="{{ is_array($pFeatures) && in_array($feature, $pFeatures) ? 'fal fa-check' : 'fal fa-times' }}"></i>

                                                {{ $package->number_of_projects === 999999 ? __('Unlimited') : $package->number_of_projects . ' ' }}
                                                {{ $package->number_of_projects === 1 ? __('Project') : __('Projects') }}
                                            </li>
                                            <li>
                                                <i
                                                    class="{{ is_array($pFeatures) && in_array($feature, $pFeatures) ? 'fal fa-check' : 'fal fa-times' }}"></i>
                                                {{ $package->number_of_project_types === 999999 ? __('Unlimited') : $package->number_of_project_types . ' ' }}
                                                {{ $package->number_of_project_types === 1 ? __('Project Types Per Project') : __('Project Types Per Project') }}
                                            </li>
                                            <li>
                                                <i
                                                    class="{{ is_array($pFeatures) && in_array($feature, $pFeatures) ? 'fal fa-check' : 'fal fa-times' }}"></i>
                                                {{ $package->number_of_project_gallery_images === 999999 ? __('Unlimited') : $package->number_of_project_gallery_images . ' ' }}
                                                {{ $package->number_of_project_gallery_images === 1 ? __('Gallery Image Per Project') : __('Gallery Images Per Project') }}
                                            </li>
                                            <li>
                                                <i
                                                    class="{{ is_array($pFeatures) && in_array($feature, $pFeatures) ? 'fal fa-check' : 'fal fa-times' }}"></i>
                                                {{ $package->number_of_project_additionl_specifications === 999999 ? __('Unlimited') : $package->number_of_project_additionl_specifications . ' ' }}
                                                {{ $package->number_of_project_additionl_specifications === 1 ? __('Additional Specification Per Project') : __('Additional Specifications Per Project') }}
                                            </li>

                                        </ul>
                                    @endif
                                @elseif ($feature == 'Blog')
                                    <i
                                        class="{{ is_array($pFeatures) && in_array($feature, $pFeatures) ? 'fal fa-check' : 'fal fa-times' }}"></i>

                                    @if (is_array($pFeatures) && in_array($feature, $pFeatures))
                                        {{ $package->number_of_blog_post === 999999 ? __('Unlimited') : $package->number_of_blog_post . ' ' }}
                                        {{ $package->number_of_blog_post === 1 ? __('Blog Post') : __('Blog Posts') }}
                                    @else
                                        {{ __('Blog Post') }}
                                    @endif
                                    
                                @else
                                    <i
                                        class="{{ is_array($pFeatures) && in_array($feature, $pFeatures) ? 'fal fa-check' : 'fal fa-times' }}"></i>
                                    {{ __("$feature") }}
                                @endif
                            </li>
                        @endforeach

                    </ul>

                    @php
                        $hasPendingMemb = \App\Http\Helpers\UserPermissionHelper::hasPendingMembership(Auth::id());
                    @endphp
                    @if ($package_count < 2 && !$hasPendingMemb)
                        <div class="px-4">
                            @if (isset($current_package->id) && $current_package->id === $package->id)
                                @if ($package->term != 'lifetime' || $current_membership->is_trial == 1)
                                    <a href="{{ route('user.plan.extend.checkout', $package->id) }}"
                                        class="btn btn-success btn-lg w-75 fw-bold mb-3">
                                        {{ __('Extend') }} </a>
                                @endif
                            @else
                                <a href="{{ route('user.plan.extend.checkout', $package->id) }}"
                                    class="btn btn-primary btn-block btn-lg fw-bold mb-3">
                                    {{ __('Buy Now') }} </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@endsection
