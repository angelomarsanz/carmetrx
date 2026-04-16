@php
    use App\Services\Tenant\PackageDowngradeService;

    $packageDowngradeService = new PackageDowngradeService();
    $dowgradedService = $packageDowngradeService->checkDowngradeStatus($tenant->id);

@endphp
<div class="main-header">
    <!-- Logo Header -->

    <div class="logo-header" @if (request()->cookie('user-theme') == 'dark') data-background-color="dark2" @endif>
        <a href="{{ route('frontend.user.index', $tenant->username) }}" class="logo" target="_blank">
            <img src="{{ !is_null($userBs->logo) ? asset($userBs->logo) : asset('assets/admin/img/propics/blank_user.jpg') }}"
                alt="navbar brand" class="navbar-brand">
        </a>
        <button class="navbar-toggler sidenav-toggler ml-auto" type="button" data-toggle="collapse"
            data-target="collapse" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon">
                <i class="icon-menu"></i>
            </span>
        </button>
        <button class="topbar-toggler more"><i class="icon-options-vertical"></i></button>
        <div class="nav-toggle">
            <button class="btn btn-toggle toggle-sidebar">
                <i class="icon-menu"></i>
            </button>
        </div>
    </div>
    <!-- End Logo Header -->
    <!-- Navbar Header -->
    <nav class="navbar navbar-header navbar-expand-lg"
        @if (request()->cookie('user-theme') == 'dark') data-background-color="dark" @endif>
        <div class="container-fluid">
            <ul class="navbar-nav topbar-nav ml-md-auto align-items-center">
                <li class="mx-2">
                    
                </li>

                <li>
                    <div class="selectgroup-pills">

                        <button type="button" class="border-0  btn-round btn btn-sm btn-secondary mr-2"
                            data-toggle="modal" data-target="#allLimits">

                            @if (!is_null($userCurrentPackage))

                                @if (
                                    $dowgradedService['proGalImgDown'] ||
                                        $dowgradedService['projectTypeDown'] ||
                                        $dowgradedService['proSpeciDown'] ||
                                        $dowgradedService['projectGalImgDown'] ||
                                        $dowgradedService['projectSpeciDown'] ||
                                        $dowgradedService['agentDown'] ||
                                        $dowgradedService['propertyDown'] ||
                                        $dowgradedService['profeaturedDown'] ||
                                        $dowgradedService['projectDown'] ||
                                        $dowgradedService['blogDown'] ||
                                        $dowgradedService['languageDown']
                                )
                                    <i class="fas fa-exclamation-triangle text-danger"></i>
                                @endif
                            @endif

                            {{ __('All Limit') }}

                        </button>

                    </div>
                </li>

                <li class="mr-1">
                    <a class="btn btn-primary btn-sm btn-round" target="_blank"
                        href="{{ route('frontend.user.index', Auth::user()->username) }}" title="View Website">
                        <i class="fas fa-eye"></i>
                    </a>
                </li>

                <form action="{{ route('user.theme.change') }}" class="mr-2 form-inline" id="adminThemeForm">
                    <div class="form-group">
                        <div class="selectgroup selectgroup-secondary selectgroup-pills">
                            <label class="selectgroup-item">
                                <input type="radio" name="theme" value="light" class="selectgroup-input"
                                    {{ empty(request()->cookie('user-theme')) || request()->cookie('user-theme') == 'light' ? 'checked' : '' }}
                                    onchange="document.getElementById('adminThemeForm').submit();">
                                <span class="selectgroup-button selectgroup-button-icon"><i
                                        class="fa fa-sun"></i></span>
                            </label>
                            <label class="selectgroup-item">
                                <input type="radio" name="theme" value="dark" class="selectgroup-input"
                                    {{ request()->cookie('user-theme') == 'dark' ? 'checked' : '' }}
                                    onchange="document.getElementById('adminThemeForm').submit();">
                                <span class="selectgroup-button selectgroup-button-icon"><i
                                        class="fa fa-moon"></i></span>
                            </label>
                        </div>
                    </div>
                </form>

                <li class="d-flex mr-4">
                    <label class="switch">
                        <input type="checkbox" name="online_status" id="toggle-btn" data-toggle="toggle" data-on="1"
                            data-off="0" @if (Auth::user()->online_status == 1) checked @endif>
                        <span class="slider round"></span>
                    </label>
                    @if (Auth::user()->online_status == 1)
                        <h5 class="mt-2 ml-2 @if (request()->cookie('user-theme') == 'dark') text-white @endif">
                            {{ __('Active') }}
                        </h5>
                    @else
                        <h5 class="mt-2 ml-2 @if (request()->cookie('user-theme') == 'dark') text-white @endif">
                            {{ __('Deactive') }}
                        </h5>
                    @endif
                </li>
                <li class="nav-item dropdown hidden-caret">
                    <a class="dropdown-toggle profile-pic" data-toggle="dropdown" href="#" aria-expanded="false">
                        <div class="avatar-sm">
                            @if (!empty($tenant->photo))
                                <img src="{{ asset($tenant->photo) }}" alt="..."
                                    class="avatar-img rounded-circle">
                            @endif
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-user animated fadeIn">
                        <div class="dropdown-user-scroll scrollbar-outer">
                            <li>
                                <div class="user-box">
                                    <div class="avatar-lg">
                                        @if (!empty($tenant->photo))
                                            <img src="{{ asset($tenant->photo) }}" alt="..."
                                                class="avatar-img rounded">
                                        @endif
                                    </div>
                                    <div class="u-text">
                                        <h4>{{ $tenant->first_name }} {{ $tenant->last_name }}</h4>
                                        <p class="text-muted">{{ Auth::user()->email }}</p>
                                        <a href="{{ route('user-profile-update') }}"
                                            class="btn btn-xs btn-secondary btn-sm">{{ __('Edit Profile') }}</a>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item"
                                    href="{{ route('user-profile-update') }}">{{ __('Edit Profile') }}</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item"
                                    href="{{ route('user.changePass') }}">{{ __('Change Password') }}</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{ route('user-logout') }}">{{ __('Logout') }}</a>
                            </li>
                        </div>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
    <!-- End Navbar -->
</div>
<!-- Modal -->

@if (!is_null($userCurrentPackage))
    <div class="modal fade" id="allLimits" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="exampleModalLabel">
                        {{ __('All Limit') }}
                    </h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    @if (
                        $dowgradedService['agentLeft'] < 0 ||
                            $dowgradedService['propertiesLeft'] < 0 ||
                            $dowgradedService['featuredPropertiesLeft'] < 0 ||
                            $dowgradedService['proGalImgDown'] ||
                            $dowgradedService['proSpeciDown'] ||
                            $dowgradedService['projectLeft'] < 0 ||
                            $dowgradedService['projectGalImgDown'] ||
                            $dowgradedService['projectTypeDown'] ||
                            $dowgradedService['projectSpeciDown']
                    )
                        <div class="alert alert-danger">
                            <span
                                class="text-danger">{{ __("Your package feature's limit exceeds, you can not add or edit any other feature") }}</span>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <span
                                class="">{{ __('If any listing limit of your package is exceeded, you cannot add or edit any other feature') }}
                            </span>
                        </div>
                    @endif
                    <ul class="list-group">



                        <li class="list-group-item">
                            <div class="d-flex  justify-content-between">
                                <span class="text-focus">
                                    @if ($dowgradedService['agentLeft'] < 0)
                                        <i class="fas fa-exclamation-circle text-danger"></i>
                                    @endif
                                    {{ __('Agents Left') }} :
                                </span>

                                <span class="badge badge-primary badge-sm">
                                    {{ $userCurrentPackage->number_of_agent == 999999 ? __('Unlimited') : ($userCurrentPackage->number_of_agent - $userFeaturesCount['agents'] < 0 ? 0 : $userCurrentPackage->number_of_agent - $userFeaturesCount['agents']) }}
                                </span>
                            </div>

                            @if ($dowgradedService['agentLeft'] == 0)
                                <p class="text-warning m-0">
                                    {{ __('Your agent limit has been reached. You cannot add more agents.') }}
                                </p>
                            @endif
                            @if ($dowgradedService['agentLeft'] < 0)
                                <p class="text-warning m-0">
                                    {{ __('Limit has been crossed, you have to delete') }}


                                    {{ abs($userCurrentPackage->number_of_agent - $userFeaturesCount['agents']) }}
                                    {{ abs($userCurrentPackage->number_of_agent - $userFeaturesCount['agents']) == 1 ? __('agent') : __('agents') }}

                                </p>
                            @endif
                        </li>

                        <li class="list-group-item">
                            <div class="d-flex  justify-content-between">
                                <span class="text-focus">
                                    @if ($dowgradedService['propertiesLeft'] < 0)
                                        <i class="fas fa-exclamation-circle text-danger"></i>
                                    @endif
                                    {{ __('Properties Left') }} :
                                </span>

                                <span class="badge badge-primary badge-sm">
                                    {{ $userCurrentPackage->number_of_property == 999999 ? __('Unlimited') : ($userCurrentPackage->number_of_property - $userFeaturesCount['properties'] < 0 ? 0 : $userCurrentPackage->number_of_property - $userFeaturesCount['properties']) }}
                                </span>
                            </div>
                            @if ($dowgradedService['propertiesLeft'] == 0)
                                <p class="text-warning m-0">
                                    {{ __('Your property limit has been reached. You cannot add more properties') }}
                                </p>
                            @endif
                            @if ($userFeaturesCount['properties'] > $userCurrentPackage->number_of_property)
                                <p class="text-warning m-0">
                                    {{ __('Limit has been crossed, you have to delete') }}


                                    {{ abs($userCurrentPackage->number_of_property - $userFeaturesCount['properties']) }}
                                    {{ abs($userCurrentPackage->number_of_property - $userFeaturesCount['properties']) == 1 ? __('property') : __('properties') }}

                                </p>
                            @endif

                        </li>


                        <li class="list-group-item">
                            <div class="d-flex  justify-content-between">
                                <span class="text-focus">
                                    @if ($dowgradedService['featuredPropertiesLeft'] < 0)
                                        <i class="fas fa-exclamation-circle text-danger"></i>
                                    @endif
                                    {{ __('Properties Available to Feature') }} :
                                </span>

                                <span class="badge badge-primary badge-sm">
                                    {{ $userCurrentPackage->number_of_property_featured == 999999 ? 'Unlimited' : ($userCurrentPackage->number_of_property_featured - $userFeaturesCount['featuredProperties'] < 0 ? 0 : $userCurrentPackage->number_of_property_featured - $userFeaturesCount['featuredProperties']) }}
                                </span>
                            </div>
                            @if ($dowgradedService['featuredPropertiesLeft'] == 0)
                                <p class="text-warning m-0">
                                    {{ __('Your property feature limit has been reached. You cannot set any more properties as featured') }}
                                </p>
                            @endif
                            @if ($userFeaturesCount['featuredProperties'] > $userCurrentPackage->number_of_property_featured)
                                <p class="text-warning m-0">
                                    {{ __('Limit has been crossed, you have to delete') }}


                                    {{ abs($userCurrentPackage->number_of_property_featured - $userFeaturesCount['featuredProperties']) }}
                                    {{ abs($userCurrentPackage->number_of_property_featured - $userFeaturesCount['featuredProperties']) == 1 ? __('property') : __('properties') }}

                                </p>
                            @endif

                        </li>

                        <li class="list-group-item ">
                            <div class="d-flex  justify-content-between">
                                <span class="text-focus">
                                    @if ($dowgradedService['proGalImgDown'])
                                        <i class="fas fa-exclamation-circle text-danger"></i>
                                    @endif
                                    {{ __('Gallery Images Limit (Per Property)') }}
                                    :
                                </span>

                                <span class="badge badge-primary badge-sm">
                                    {{ $userCurrentPackage->number_of_property_gallery_images == 999999 ? __('Unlimited') : $userCurrentPackage->number_of_property_gallery_images }}
                                </span>
                            </div>

                            @if ($dowgradedService['proGalImgDown'])
                                <p class="text-warning m-0">
                                    {{ __("Please remove some 'gallery images' from properties & make sure each property has maximum") }}
                                    {{ abs($userCurrentPackage->number_of_property_gallery_images) }}
                                    {{ __('gallery images') }}
                                </p>
                            @endif
                        </li>


                        <li class="list-group-item ">
                            <div class="d-flex  justify-content-between">
                                <span class="text-focus">
                                    @if ($dowgradedService['proSpeciDown'])
                                        <i class="fas fa-exclamation-circle text-danger"></i>
                                    @endif
                                    {{ __('Additional Features Limit (Per Property)') }}
                                    :
                                </span>
                                <span class="badge badge-primary badge-sm">
                                    {{ $userCurrentPackage->number_of_property_adittionl_specifications == 999999 ? __('Unlimited') : $userCurrentPackage->number_of_property_adittionl_specifications }}
                                </span>
                            </div>


                            @if ($dowgradedService['proSpeciCount'] > $userCurrentPackage->number_of_property_adittionl_specifications)
                                <p class="text-warning m-0">
                                    {{ __("Please remove some 'additional features' from properties & make sure each property has maximum") }}
                                    {{ abs($userCurrentPackage->number_of_property_adittionl_specifications) }}
                                    {{ __('additional features') }}
                                </p>
                            @endif
                        </li>


                        <li class="list-group-item">
                            <div class="d-flex  justify-content-between">
                                <span class="text-focus">
                                    @if ($dowgradedService['projectLeft'] < 0)
                                        <i class="fas fa-exclamation-circle text-danger"></i>
                                    @endif
                                    {{ __('Projects Left') }} :
                                </span>

                                <span class="badge badge-primary badge-sm">
                                    {{ $userCurrentPackage->number_of_projects == 999999 ? 'Unlimited' : ($userCurrentPackage->number_of_projects - $userFeaturesCount['projects'] < 0 ? 0 : $userCurrentPackage->number_of_projects - $userFeaturesCount['projects']) }}
                                </span>
                            </div>
                            @if ($dowgradedService['projectLeft'] == 0)
                                <p class="text-warning m-0">
                                    {{ __('Your project limit has been reached. You cannot add more projects') }}
                                </p>
                            @endif

                            @if ($userFeaturesCount['projects'] > $userCurrentPackage->number_of_projects)
                                <p class="text-warning m-0">
                                    {{ __('Limit has been crossed, you have to delete') }}

                                    {{ abs($userCurrentPackage->number_of_projects - $userFeaturesCount['projects']) }}
                                    {{ abs($userCurrentPackage->number_of_projects - $userFeaturesCount['projects']) == 1 ? 'project' : 'projects' }}

                                </p>
                            @endif
                        </li>


                        <li class="list-group-item ">
                            <div class="d-flex  justify-content-between">
                                <span class="text-focus">
                                    @if ($dowgradedService['projectGalImgDown'])
                                        <i class="fas fa-exclamation-circle text-danger"></i>
                                    @endif
                                    {{ __('Gallery Images Limit (Per Project)') }}
                                    :
                                </span>
                                <span class="badge badge-primary badge-sm">
                                    {{ $userCurrentPackage->number_of_project_gallery_images == 999999 ? __('Unlimited') : $userCurrentPackage->number_of_project_gallery_images }}
                                </span>
                            </div>


                            @if ($dowgradedService['projectGalImgDown'])
                                <p class="text-warning m-0">
                                    {{ __("Please remove some 'gallery images' from projects & make sure each project has maximum") }}
                                    {{ abs($userCurrentPackage->number_of_project_gallery_images) }}
                                    {{ __('gallery images') }}
                                </p>
                            @endif
                        </li>
                        <li class="list-group-item ">
                            <div class="d-flex  justify-content-between">
                                <span class="text-focus">
                                    @if ($dowgradedService['projectTypeDown'])
                                        <i class="fas fa-exclamation-circle text-danger"></i>
                                    @endif
                                    {{ __('Project Types Limit (Per Project)') }}
                                    :
                                </span>
                                <span class="badge badge-primary badge-sm">
                                    {{ $userCurrentPackage->number_of_project_types == 999999 ? __('Unlimited') : $userCurrentPackage->number_of_project_types }}
                                </span>

                            </div>


                            @if ($dowgradedService['projectTypeCount'] > $userCurrentPackage->number_of_project_types)
                                <p class="text-warning m-0">
                                    {{ __("Please remove some 'project type' from projects & make sure each project has maximum") }}
                                    {{ abs($userCurrentPackage->number_of_project_types) }}
                                    {{ __('project types') }}
                                </p>
                            @endif
                        </li>

                        <li class="list-group-item">
                            <div class="d-flex  justify-content-between ">
                                <span class="text-focus">
                                    @if ($dowgradedService['projectSpeciDown'])
                                        <i class="fas fa-exclamation-circle text-danger"></i>
                                    @endif
                                    {{ __('Additional Features Limit (Per Project)') }}
                                    :
                                </span>
                                <span class="badge badge-primary badge-sm">
                                    {{ $userCurrentPackage->number_of_project_additionl_specifications == 999999 ? __('Unlimited') : $userCurrentPackage->number_of_project_additionl_specifications }}
                                </span>
                            </div>

                            @if ($dowgradedService['projectSpeciCount'] > $userCurrentPackage->number_of_project_additionl_specifications)
                                <p class="text-warning m-0">
                                    {{ __("Please remove some 'additional features' from projects & make sure each project has maximum") }}
                                    {{ abs($userCurrentPackage->number_of_project_additionl_specifications) }}
                                    {{ __('additional features') }}
                                </p>
                            @endif
                        </li>

                        <li class="list-group-item">
                            <div class="d-flex  justify-content-between">
                                <span class="text-focus">
                                    @if ($dowgradedService['languageLeft'] < 0)
                                        <i class="fas fa-exclamation-circle text-danger"></i>
                                    @endif
                                    {{ __('Additional Languages Left') }} :
                                </span>

                                <span class="badge badge-primary badge-sm">
                                    {{ $userCurrentPackage->number_of_language == 999999 ? 'Unlimited' : ($userCurrentPackage->number_of_language - $userFeaturesCount['languages'] < 0 ? 0 : $userCurrentPackage->number_of_language - $userFeaturesCount['languages']) }}
                                </span>
                            </div>
                            @if ($dowgradedService['languageLeft'] == 0)
                                <p class="text-warning m-0">
                                    {{ __('Your additional language limit has been reached. You cannot add more languages') }}
                                </p>
                            @endif

                            @if ($userFeaturesCount['languages'] > $userCurrentPackage->number_of_language)
                                <p class="text-warning m-0">
                                    {{ __('Limit has been crossed, you have to delete') }}

                                    {{ abs($userCurrentPackage->number_of_language - $userFeaturesCount['languages']) }}
                                    {{ abs($userCurrentPackage->number_of_language - $userFeaturesCount['languages']) == 1 ? 'language' : 'languages' }}

                                </p>
                            @endif
                        </li>

                        <li class="list-group-item">
                            <div class="d-flex  justify-content-between">
                                <span class="text-focus">
                                    @if ($dowgradedService['blogLeft'] < 0)
                                        <i class="fas fa-exclamation-circle text-danger"></i>
                                    @endif
                                    {{ __('Blog Post Left') }} :
                                </span>

                                <span class="badge badge-primary badge-sm">
                                    {{ $userCurrentPackage->number_of_blog_post == 999999 ? 'Unlimited' : ($userCurrentPackage->number_of_blog_post - $userFeaturesCount['blogs'] < 0 ? 0 : $userCurrentPackage->number_of_blog_post - $userFeaturesCount['blogs']) }}
                                </span>
                            </div>
                            @if ($dowgradedService['blogLeft'] == 0)
                                <p class="text-warning m-0">
                                    {{ __('Your blog post limit has been reached. You cannot add more blog posts') }}
                                </p>
                            @endif

                            @if ($userFeaturesCount['blogs'] > $userCurrentPackage->number_of_blog_post)
                                <p class="text-warning m-0">
                                    {{ __('Limit has been crossed, you have to delete') }}

                                    {{ abs($userCurrentPackage->number_of_blog_post - $userFeaturesCount['blogs']) }}
                                    {{ abs($userCurrentPackage->number_of_blog_post - $userFeaturesCount['blogs']) == 1 ? 'blog post' : 'blog posts' }}

                                </p>
                            @endif
                        </li>

                        <li class="list-group-item  border-bottom-0">
                            <div class="d-flex  justify-content-between">
                                <span class="text-focus">
                                    @if ($dowgradedService['customPageLeft'] < 0)
                                        <i class="fas fa-exclamation-circle text-danger"></i>
                                    @endif
                                    {{ __('Additional Pages Left') }} :
                                </span>

                                <span class="badge badge-primary badge-sm">
                                    {{ $userCurrentPackage->number_of_additional_page == 999999 ? 'Unlimited' : ($userCurrentPackage->number_of_additional_page - $userFeaturesCount['customPages'] < 0 ? 0 : $userCurrentPackage->number_of_additional_page - $userFeaturesCount['customPages']) }}
                                </span>
                            </div>
                            @if ($dowgradedService['customPageLeft'] == 0)
                                <p class="text-warning m-0">
                                    {{ __('Your additional page limit has been reached. You cannot add more additional pages') }}
                                </p>
                            @endif

                            @if ($userFeaturesCount['customPages'] > $userCurrentPackage->number_of_additional_page)
                                <p class="text-warning m-0">
                                    {{ __('Limit has been crossed, you have to delete') }}
                                    {{ abs($userCurrentPackage->number_of_additional_page - $userFeaturesCount['customPages']) }}
                                    {{ abs($userCurrentPackage->number_of_additional_page - $userFeaturesCount['customPages']) == 1 ? 'additional page' : 'additional pages' }}

                                </p>
                            @endif
                        </li>

                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-dismiss="modal">{{ __('Close') }}</button>
                </div>
            </div>
        </div>
    </div>
@endif
