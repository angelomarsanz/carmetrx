@php

    $admin = Auth::guard('admin')->user();
    if (!empty($admin->role)) {
        $permissions = $admin->role->permissions;
        $permissions = json_decode($permissions, true);
    }
@endphp

<div class="sidebar sidebar-style-2" @if (request()->cookie('admin-theme') == 'dark') data-background-color="dark2" @endif>
    <div class="sidebar-wrapper scrollbar scrollbar-inner">
        <div class="sidebar-content">
            <div class="user">
                <div class="avatar-sm float-left mr-2">
                    @if (!empty(Auth::guard('admin')->user()->image))
                        <img src="{{ asset('assets/admin/img/propics/' . Auth::guard('admin')->user()->image) }}"
                            alt="..." class="avatar-img rounded">
                    @else
                        <img src="{{ asset('assets/admin/img/propics/blank_user.jpg') }}" alt="..."
                            class="avatar-img rounded">
                    @endif
                </div>
                <div class="info">
                    <a data-toggle="collapse" href="#collapseExample" aria-expanded="true">
                        <span>
                            {{ Auth::guard('admin')->user()->first_name }}
                            <span class="user-level">{{ __('Admin') }}</span>
                            <span class="caret"></span>
                        </span>
                    </a>
                    <div class="clearfix"></div>

                    <div class="collapse in" id="collapseExample">
                        <ul class="nav">
                            <li>
                                <a href="{{ route('admin.editProfile') }}">
                                    <span class="link-collapse">{{ __('Edit Profile') }}</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.changePass') }}">
                                    <span class="link-collapse">{{ __('Change Password') }}</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.logout') }}">
                                    <span class="link-collapse">{{ __('Logout') }}</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <ul class="nav nav-primary">

                <div class="row mb-2">
                    <div class="col-12">
                        <form action="">
                            <div class="form-group py-0">
                                <input name="term" type="text" class="form-control sidebar-search " value=""
                                    placeholder="{{ __('Search Menu Here') }}...">
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Dashboard --}}
                <li class="nav-item @if (request()->path() == 'admin/dashboard') active @endif">
                    <a href="{{ route('admin.dashboard') }}">
                        <i class="far fa-tachometer-alt"></i>
                        <p>{{ __('Dashboard') }}</p>
                    </a>
                </li>

                {{-- Package --}}
                @if (empty($admin->role) || (!empty($permissions) && in_array('Packages', $permissions)))
                    <li
                        class="nav-item
                    @if (request()->path() == 'admin/package/settings') active
                    @elseif(request()->routeIs('admin.package.index')) active
                    @elseif(request()->path() == 'admin/package/features') active
                    @elseif(request()->is('admin/package/*/edit')) active
                    @elseif(request()->path() == 'admin/coupon') active
                    @elseif(request()->routeIs('admin.coupon.edit')) active @endif">
                        <a data-toggle="collapse" href="#packageManagement">
                            <i class="far fa-receipt"></i>
                            <p>{{ __('Package Management') }}</p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse
                        @if (request()->path() == 'admin/package/settings') show
                         @elseif(request()->routeIs('admin.package.index')) show
                        @elseif(request()->path() == 'admin/package/features') show
                        @elseif(request()->is('admin/package/*/edit')) show
                        @elseif(request()->path() == 'admin/coupon') show
                        @elseif(request()->routeIs('admin.coupon.edit')) show @endif"
                            id="packageManagement">
                            <ul class="nav nav-collapse">
                                <li class="@if (request()->path() == 'admin/package/settings') active @endif">
                                    <a href="{{ route('admin.package.settings') }}">
                                        <span class="sub-item">{{ __('Settings') }}</span>
                                    </a>
                                </li>
                                <li
                                    class="@if (request()->path() == 'admin/coupon') active
                                @elseif(request()->routeIs('admin.coupon.edit')) active @endif">
                                    <a href="{{ route('admin.coupon.index') }}">
                                        <span class="sub-item">{{ __('Coupons') }}</span>
                                    </a>
                                </li>
                                <li class="@if (request()->path() == 'admin/package/features') active @endif">
                                    <a href="{{ route('admin.package.features') }}">
                                        <span class="sub-item">{{ __('Package Features') }}</span>
                                    </a>
                                </li>
                                <li
                                    class=" @if (request()->routeIs('admin.package.index')) active
                                @elseif(request()->is('admin/package/*/edit')) active @endif">
                                    <a href="{{ route('admin.package.index') . '?language=' . $currentLang->code }}">
                                        <span class="sub-item">{{ __('Packages') }}</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endif


                @if (empty($admin->role) || (!empty($permissions) && in_array('Payment Log', $permissions)))
                    <li class="nav-item
                    @if (request()->path() == 'admin/payment-log') active @endif">
                        <a href="{{ route('admin.payment-log.index') }}">
                            <i class="far fa-file-invoice-dollar"></i>
                            <p>{{ __('Payment Log') }}</p>
                        </a>
                    </li>
                @endif

                @if (empty($admin->role) || (!empty($permissions) && in_array('Custom Domains', $permissions)))
                    <li
                        class="nav-item
                        @if (request()->path() == 'admin/domains') active
                        @elseif(request()->path() == 'admin/domain/texts') active @endif">
                        <a data-toggle="collapse" href="#customDomains">
                            <i class="far fa-link"></i>
                            <p>{{ __('Custom Domains') }}</p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse
                            @if (request()->path() == 'admin/domains') show
                            @elseif(request()->path() == 'admin/domain/texts') show @endif"
                            id="customDomains">
                            <ul class="nav nav-collapse">
                                <li class="@if (request()->path() == 'admin/domain/texts') active @endif">
                                    <a href="{{ route('admin.custom-domain.texts') }}">
                                        <span class="sub-item">{{ __('Request Page Texts') }}</span>
                                    </a>
                                </li>
                                <li class="@if (request()->path() == 'admin/domains' && empty(request()->input('type'))) active @endif">
                                    <a href="{{ route('admin.custom-domain.index') }}">
                                        <span class="sub-item">{{ __('All Requests') }}</span>
                                    </a>
                                </li>
                                <li class="@if (request()->path() == 'admin/domains' && request()->input('type') == 'pending') active @endif">
                                    <a href="{{ route('admin.custom-domain.index', ['type' => 'pending']) }}">
                                        <span class="sub-item">{{ __('Pending Requests') }}</span>
                                    </a>
                                </li>
                                <li class="@if (request()->path() == 'admin/domains' && request()->input('type') == 'connected') active @endif">
                                    <a href="{{ route('admin.custom-domain.index', ['type' => 'connected']) }}">
                                        <span class="sub-item">{{ __('Connected Requests') }}</span>
                                    </a>
                                </li>
                                <li class="@if (request()->path() == 'admin/domains' && request()->input('type') == 'rejected') active @endif">
                                    <a href="{{ route('admin.custom-domain.index', ['type' => 'rejected']) }}">
                                        <span class="sub-item">{{ __('Rejected Requests') }}</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endif

                @if (empty($admin->role) || (!empty($permissions) && in_array('Subdomains', $permissions)))
                    <li class="nav-item
                        @if (request()->path() == 'admin/subdomains') active @endif">
                        <a data-toggle="collapse" href="#subDomains">
                            <i class="far fa-link"></i>
                            <p>{{ __('Subdomains') }}</p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse
                            @if (request()->path() == 'admin/subdomains') show @endif"
                            id="subDomains">
                            <ul class="nav nav-collapse">
                                <li class="@if (request()->path() == 'admin/subdomains' && empty(request()->input('type'))) active @endif">
                                    <a href="{{ route('admin.subdomain.index') }}">
                                        <span class="sub-item">{{ __('All Subdomains') }}</span>
                                    </a>
                                </li>
                                <li class="@if (request()->path() == 'admin/subdomains' && request()->input('type') == 'pending') active @endif">
                                    <a href="{{ route('admin.subdomain.index', ['type' => 'pending']) }}">
                                        <span class="sub-item">{{ __('Pending Subdomains') }}</span>
                                    </a>
                                </li>
                                <li class="@if (request()->path() == 'admin/subdomains' && request()->input('type') == 'connected') active @endif">
                                    <a href="{{ route('admin.subdomain.index', ['type' => 'connected']) }}">
                                        <span class="sub-item">{{ __('Connected Subdomains') }}</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endif


                {{-- Registered Users --}}
                @if (empty($admin->role) || (!empty($permissions) && in_array('Registered Users', $permissions)))



                    <li
                        class="nav-item
                        @if (request()->routeIs('admin.register.user')) active
                        @elseif (request()->routeIs('register.user.view')) active
                        @elseif (request()->routeIs('admin.register.user')) active
                        @elseif (request()->routeIs('admin.mailsubscriber')) active
                        @elseif(request()->routeIs('admin.subscriber.index')) active
                        @elseif (request()->routeIs('register.user.changePass')) active @endif">
                        <a data-toggle="collapse" href="#regUser">
                            <i class="far fa-users"></i>

                            <p>{{ __('User Management') }}</p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse
                            @if (request()->routeIs('admin.register.user')) show
                            @elseif (request()->routeIs('register.user.view')) show
                            @elseif (request()->routeIs('admin.mailsubscriber')) show
                            @elseif(request()->routeIs('admin.subscriber.index')) show
                            @elseif (request()->routeIs('register.user.changePass')) show @endif"
                            id="regUser">
                            <ul class="nav nav-collapse">
                                <li class="@if (request()->routeIs('admin.register.user') && empty(request()->input('type'))) active @endif">
                                    <a href="{{ route('admin.register.user') }}">

                                        <span class="sub-item">{{ __('Registered Users') }}</span>
                                    </a>
                                </li>

                                @if (empty($admin->role) || (!empty($permissions) && in_array('Subscribers', $permissions)))
                                    {{-- Subscribers --}}
                                    <li
                                        class="submenu
         @if (request()->routeIs('admin.mailsubscriber')) selected
            @elseif(request()->routeIs('admin.subscriber.index')) selected @endif">
                                        <a data-toggle="collapse" href="#subscribers">
                                            <span class="sub-item">{{ __('Subscribers') }}</span>
                                            <span class="caret"></span>
                                        </a>
                                        <div class="collapse
            @if (request()->routeIs('admin.mailsubscriber')) show
            @elseif(request()->routeIs('admin.subscriber.index')) show @endif"
                                            id="subscribers">
                                            <ul class="nav nav-collapse subnav">
                                                <li class="@if (request()->routeIs('admin.subscriber.index')) active @endif">
                                                    <a href="{{ route('admin.subscriber.index') }}">
                                                        <span class="sub-item">{{ __('Subscribers') }}</span>
                                                    </a>
                                                </li>
                                                <li class="@if (request()->routeIs('admin.mailsubscriber')) active @endif">
                                                    <a href="{{ route('admin.mailsubscriber') }}">
                                                        <span class="sub-item">{{ __('Mail to Subscribers') }}</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </li>
                @endif


                @if (empty($admin->role) || (!empty($permissions) && in_array('Property Specifications', $permissions)))
                    @php
                        $allowProSep =
                            request()->routeIs('admin.property_management.settings') ||
                            request()->routeIs('admin.property_management.categories') ||
                            request()->routeIs('admin.property_management.amenities') ||
                            request()->routeIs('admin.property_management.countries') ||
                            request()->routeIs('admin.property_management.states') ||
                            request()->routeIs('admin.property_management.cities') ||
                            request()->routeIs('admin.property_management.property_types')
                                ? true
                                : false;
                    @endphp
                    <li class="nav-item
                        @if ($allowProSep == true) active @endif">
                        <a data-toggle="collapse" href="#propertySpecifications">
                            <i class="far fa-home"></i>
                            <p>{{ __('Property Specifications') }}</p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse
                            @if ($allowProSep == true) show @endif"
                            id="propertySpecifications">
                            <ul class="nav nav-collapse">
                                <li
                                    class="{{ request()->routeIs('admin.property_management.settings') ? 'active' : '' }}">
                                    <a
                                        href="{{ route('admin.property_management.settings', ['language' => $currentLang->code]) }}">
                                        <span class="sub-item">{{ __('Settings') }}</span>
                                    </a>
                                </li>

                                <li class="@if (request()->routeIs('admin.property_management.property_types')) active @endif">
                                    <a href="{{ route('admin.property_management.property_types') }}">
                                        <span class="sub-item">{{ __('Property Types') }}</span>
                                    </a>
                                </li>
                                <li class="@if (request()->routeIs('admin.property_management.categories')) active @endif">
                                    <a
                                        href="{{ route('admin.property_management.categories', ['language' => $currentLang->code]) }}">
                                        <span class="sub-item">{{ __('Categories') }}</span>
                                    </a>
                                </li>
                                <li class="@if (request()->routeIs('admin.property_management.amenities')) active @endif">
                                    <a
                                        href="{{ route('admin.property_management.amenities', ['language' => $currentLang->code]) }}">
                                        <span class="sub-item">{{ __('Amenities') }}</span>
                                    </a>
                                </li>
                                @if ($bs->property_country_status == 1)
                                    <li
                                        class="{{ request()->routeIs('admin.property_management.countries') ? 'active' : '' }}">
                                        <a
                                            href="{{ route('admin.property_management.countries', ['language' => $currentLang->code]) }}">
                                            <span class="sub-item">{{ __('States') }}</span>
                                        </a>
                                    </li>
                                @endif
                                @if ($bs->property_state_status == 1)
                                    <li
                                        class="{{ request()->routeIs('admin.property_management.states') ? 'active' : '' }}">
                                        <a
                                            href="{{ route('admin.property_management.states', ['language' => $currentLang->code]) }}">
                                            <span class="sub-item">{{ __('Cities') }}</span>
                                        </a>
                                    </li>
                                @endif
                                <li
                                    class="{{ request()->routeIs('admin.property_management.cities') ? 'active' : '' }}">
                                    <a
                                        href="{{ route('admin.property_management.cities', ['language' => $currentLang->code]) }}">
                                        <span class="sub-item">{{ __('Areas') }}</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endif

                <li
                    class="nav-item
                    @if (request()->routeIs('admin.page.create') == 'admin/page/create') active
                    @elseif(request()->routeIs('admin.page.index')) active
                    @elseif(request()->routeIs('admin.page.edit')) active
                    @elseif(request()->routeIs('admin.bcategory.index')) active
                    @elseif (request()->path() == 'admin/features') active
                    @elseif(request()->path() == 'admin/introsection') active
                    @elseif(request()->routeIs('admin.herosection.imgtext')) active
                    @elseif(request()->is('admin/feature/*/edit')) active
                    @elseif(request()->is('admin/process')) active
                    @elseif(request()->is('admin/process/*/edit')) active
                    @elseif(request()->path() == 'admin/testimonials') active
                    @elseif(request()->is('admin/testimonial/*/edit')) active
                    @elseif(request()->path() == 'admin/menu/section') active
                    @elseif(request()->path() == 'admin/special/section') active
                    @elseif(request()->path() == 'admin/herosection/video') active
                    @elseif(request()->path() == 'admin/home-page-text-section') active
                    @elseif(request()->path() == 'admin/partners') active
                    @elseif(request()->is('admin/partner/*/edit')) active
                    @elseif(request()->path() == 'admin/sections') active
                    @elseif (request()->path() == 'admin/faqs') active
                    @elseif (request()->path() == 'admin/bcategorys') active
                    @elseif (request()->path() == 'admin/blogs') active
                    @elseif (request()->routeIs('admin.blog.edit')) active
                    @elseif (request()->path() == 'admin/contact') active
                    @elseif (request()->path() == 'admin/menu-builder') active
                    @elseif (request()->path() == 'admin/footers') active
                    @elseif(request()->path() == 'admin/ulinks') active
                    {{-- @elseif (request()->path() == 'admin/breadcrumb') active  --}}
                    @elseif (request()->routeIs('admin.additional_sections')) active
                    @elseif (request()->routeIs('admin.additional_section.create')) active
                    @elseif (request()->routeIs('admin.additional_section.edit')) active
                    @elseif (request()->routeIs('admin.about_us.additional_sections')) active
                    @elseif (request()->routeIs('admin.about_us.section.hide_show')) active
                    @elseif (request()->routeIs('admin.about_us.additional_section.edit')) active
                    @elseif(request()->routeIs('admin.about_us.additional_section.create')) active
                                                @elseif(request()->routeIs('admin.userThemes')) active
                    @elseif (request()->path() == 'admin/seo') active @endif">
                    <a data-toggle="collapse" href="#pages">
                        <i class="far fa-file-alt"></i>
                        <p>{{ __('Pages') }}</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse
                        @if (request()->routeIs('admin.page.index') == 'admin/page/create') show
                        @elseif(request()->routeIs('admin.page.create') == 'admin/pages') show
                        @elseif(request()->routeIs('admin.page.edit')) show
                        @elseif(request()->routeIs('admin.bcategory.index')) show
                        @elseif (request()->path() == 'admin/features') show
                        @elseif(request()->path() == 'admin/introsection') show
                        @elseif(request()->routeIs('admin.herosection.imgtext')) show
                        @elseif(request()->is('admin/feature/*/edit')) show
                        @elseif(request()->is('admin/process')) show
                        @elseif(request()->is('admin/process/*/edit')) show
                        @elseif(request()->path() == 'admin/testimonials') show
                        @elseif(request()->is('admin/testimonial/*/edit')) show
                        @elseif(request()->path() == 'admin/menu/section') show
                        @elseif(request()->path() == 'admin/special/section') show
                        @elseif(request()->path() == 'admin/herosection/video') show
                        @elseif(request()->path() == 'admin/home-page-text-section') show
                        @elseif(request()->path() == 'admin/partners') show
                        @elseif(request()->is('admin/partner/*/edit')) show
                        @elseif(request()->path() == 'admin/sections') show
                        @elseif (request()->path() == 'admin/faqs') show
                        @elseif (request()->path() == 'admin/bcategorys') show
                        @elseif (request()->path() == 'admin/blogs') show
                        @elseif (request()->routeIs('admin.blog.edit')) show
                        @elseif (request()->path() == 'admin/contact') show
                        @elseif (request()->path() == 'admin/menu-builder') show
                        @elseif (request()->path() == 'admin/footers') show
                        @elseif(request()->path() == 'admin/ulinks') show
                        @elseif (request()->routeIs('admin.additional_sections')) show
                        @elseif (request()->routeIs('admin.additional_section.create')) show
                        @elseif (request()->routeIs('admin.additional_section.edit')) show
                        @elseif (request()->routeIs('admin.about_us.additional_sections')) show
                    @elseif (request()->routeIs('admin.about_us.section.hide_show')) show
                    @elseif (request()->routeIs('admin.about_us.additional_section.edit')) show
                    @elseif(request()->routeIs('admin.about_us.additional_section.create')) show
                                                @elseif(request()->routeIs('admin.userThemes')) show
                        @elseif (request()->path() == 'admin/seo') show @endif"
                        id="pages">
                        <ul class="nav nav-collapse">

                            @if (empty($admin->role) || (!empty($permissions) && in_array('Home Page', $permissions)))
                                {{-- Home Page --}}
                                <li
                                    class="submenu
                                        @if (request()->path() == 'admin/features') selected
                                        @elseif(request()->path() == 'admin/introsection') selected
                                        @elseif(request()->routeIs('admin.herosection.imgtext')) selected
                                        @elseif(request()->is('admin/feature/*/edit')) selected
                                        @elseif(request()->is('admin/process')) selected
                                        @elseif(request()->is('admin/process/*/edit')) selected
                                        @elseif(request()->path() == 'admin/testimonials') selected
                                        @elseif(request()->is('admin/testimonial/*/edit')) selected
                                        @elseif(request()->path() == 'admin/menu/section') selected
                                        @elseif(request()->path() == 'admin/special/section') selected
                                        @elseif(request()->path() == 'admin/herosection/video') selected
                                        @elseif(request()->path() == 'admin/home-page-text-section') selected
                                        @elseif(request()->path() == 'admin/partners') selected
                                        @elseif(request()->is('admin/partner/*/edit')) selected
                                                                    @elseif(request()->routeIs('admin.userThemes')) selected
                                         @elseif (request()->routeIs('admin.additional_sections')) selected
                                                @elseif (request()->routeIs('admin.additional_section.create')) selected
                                                @elseif (request()->routeIs('admin.additional_section.edit')) selected
                                        @elseif(request()->path() == 'admin/sections') selected @endif">
                                    <a data-toggle="collapse" href="#home">
                                        <span class="sub-item">{{ __('Home Page') }}</span>
                                        <span class="caret"></span>

                                    </a>
                                    <div class="collapse
                                            @if (request()->path() == 'admin/features') show
                                            @elseif (request()->routeIs('admin.additional_sections')) show
                                                @elseif (request()->routeIs('admin.additional_section.create')) show
                                                @elseif (request()->routeIs('admin.additional_section.edit')) show
                                            @elseif(request()->path() == 'admin/introsection') show
                                            @elseif(request()->routeIs('admin.herosection.imgtext')) show
                                            @elseif(request()->is('admin/feature/*/edit')) show
                                            @elseif(request()->is('admin/process')) show
                                            @elseif(request()->is('admin/process/*/edit')) show
                                            @elseif(request()->path() == 'admin/testimonials') show
                                            @elseif(request()->is('admin/testimonial/*/edit')) show
                                            @elseif(request()->path() == 'admin/special/section') show
                                            @elseif(request()->path() == 'admin/home-page-text-section') show
                                            @elseif(request()->path() == 'admin/partners') show
                                                                        @elseif(request()->routeIs('admin.userThemes')) show
                                            @elseif(request()->is('admin/partner/*/edit')) show
                                            @elseif(request()->path() == 'admin/sections') show @endif"
                                        id="home">
                                        <ul class="nav nav-collapse subnav">

                                            <li class="@if (request()->routeIs('admin.herosection.imgtext')) active @endif">
                                                <a
                                                    href="{{ route('admin.herosection.imgtext') . '?language=' . $currentLang->code }}">
                                                    <span class="sub-item">{{ __('Hero Section') }}</span>
                                                </a>
                                            </li>

                                            <li class="@if (request()->path() == 'admin/introsection') active @endif">
                                                <a
                                                    href="{{ route('admin.introsection.index') . '?language=' . $currentLang->code }}">
                                                    <span class="sub-item">{{ __('Intro Section') }}</span>
                                                </a>
                                            </li>

                                            <li
                                                class="
                                                    @if (request()->path() == 'admin/features') active
                                                    @elseif(request()->is('admin/feature/*/edit')) active @endif">
                                                <a
                                                    href="{{ route('admin.feature.index') . '?language=' . $currentLang->code }}">
                                                    <span class="sub-item">{{ __('Features') }}</span>
                                                </a>
                                            </li>

                                            <li
                                                class="
                                                    @if (request()->path() == 'admin/process') active
                                                    @elseif(request()->is('admin/process/*/edit')) active @endif">
                                                <a
                                                    href="{{ route('admin.process.index') . '?language=' . $currentLang->code }}">
                                                    <span class="sub-item">{{ __('Work Process') }}</span>
                                                </a>
                                            </li>

                                            <li class="@if (request()->routeIs('admin.userThemes')) active @endif">
                                                <a href="{{ route('admin.userThemes') }}">
                                                    <span class="sub-item">{{ __('Preview Templates') }}</span>
                                                </a>
                                            </li>

                                            <li
                                                class="
                                                    @if (request()->path() == 'admin/testimonials') active
                                                    @elseif(request()->is('admin/testimonial/*/edit')) active @endif">
                                                <a
                                                    href="{{ route('admin.testimonial.index') . '?language=' . $currentLang->code }}">
                                                    <span class="sub-item">{{ __('Testimonials') }}</span>
                                                </a>
                                            </li>
                                            <li
                                                class="
                                                    @if (request()->path() == 'admin/partners') active
                                                    @elseif(request()->is('admin/partner/*/edit')) active @endif">
                                                <a
                                                    href="{{ route('admin.partner.index') . '?language=' . $currentLang->code }}">
                                                    <span class="sub-item">{{ __('Partners') }}</span>
                                                </a>
                                            </li>
                                            <li class="@if (request()->path() == 'admin/home-page-text-section') active @endif">
                                                <a
                                                    href="{{ route('admin.home.page.text.index') . '?language=' . $currentLang->code }}">
                                                    <span class="sub-item">{{ __('Section Titles') }}</span>
                                                </a>
                                            </li>

                                            <li
                                                class="@if (request()->routeIs('admin.additional_sections')) active
                                                @elseif (request()->routeIs('admin.additional_section.create')) active
                                                @elseif (request()->routeIs('admin.additional_section.edit')) active @endif">
                                                <a
                                                    href="{{ route('admin.additional_sections') . '?language=' . $currentLang->code }}">
                                                    <span class="sub-item">{{ __('Additional Sections') }}</span>
                                                </a>
                                            </li>
                                            <li class=" @if (request()->path() == 'admin/sections') active @endif">
                                                <a href="{{ route('admin.sections.index') }}">
                                                    <span class="sub-item">{{ __('Section Hide / Show') }}</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                            @endif
                            <li
                                class="subnav
                @if (request()->routeIs('admin.about_us.additional_sections')) selected
                 @elseif (request()->routeIs('admin.about_us.section.hide_show')) selected
                 @elseif (request()->routeIs('admin.about_us.additional_section.edit')) selected
                @elseif(request()->routeIs('admin.about_us.additional_section.create')) selected @endif
                ">
                                <a data-toggle="collapse" href="#gatewaysa">

                                    <span class="sub-item">{{ __('About Page') }}</span>
                                    <span class="caret"></span>
                                </a>
                                <div class="collapse
                    @if (request()->routeIs('admin.about_us.additional_sections')) show
                    @elseif (request()->routeIs('admin.about_us.section.hide_show')) show
                    @elseif (request()->routeIs('admin.about_us.additional_section.edit')) show
                    @elseif(request()->routeIs('admin.about_us.additional_section.create')) show @endif
                    "
                                    id="gatewaysa">
                                    <ul class="nav nav-collapse">
                                        <li
                                            class="
                                                    @if (request()->routeIs('admin.about_us.additional_sections')) active @elseif(request()->routeIs('admin.about_us.additional_section.edit')) active @endif
                                                    ">
                                            <a href="{{ route('admin.about_us.additional_sections') }}">
                                                <span class="sub-item">{{ __('Additional Section') }}</span>
                                            </a>
                                        </li>
                                        <li
                                            class=" @if (request()->routeIs('admin.about_us.section.hide_show')) active @endif
                                                    ">
                                            <a href="{{ route('admin.about_us.section.hide_show') }}">
                                                <span class="sub-item">{{ __('Section Show/Hide') }}</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>

                            @if (empty($admin->role) || (!empty($permissions) && in_array('FAQ Management', $permissions)))
                                {{-- FAQ Management --}}
                                <li class="  @if (request()->path() == 'admin/faqs') active @endif">
                                    <a href="{{ route('admin.faq.index') . '?language=' . $currentLang->code }}">
                                        <span class="sub-item">{{ __('FAQs') }}</span>
                                    </a>
                                </li>
                            @endif

                            @if (empty($admin->role) || (!empty($permissions) && in_array('Blogs', $permissions)))
                                {{-- Blogs --}}
                                <li
                                    class="submenu
                                        @if (request()->routeIs('admin.bcategory.index')) selected
                                        @elseif(request()->path() == 'admin/blogs') selected
                                        @elseif(request()->is('admin/blog/*/edit')) selected @endif">
                                    <a data-toggle="collapse" href="#blog">

                                        <span class="sub-item">{{ __('Blog') }}</span>
                                        <span class="caret"></span>
                                    </a>
                                    <div class="collapse
                                            @if (request()->routeIs('admin.bcategory.index')) show
                                            @elseif(request()->path() == 'admin/blogs') show
                                            @elseif(request()->is('admin/blog/*/edit')) show @endif"
                                        id="blog">
                                        <ul class="nav nav-collapse subnav">
                                            <li class="@if (request()->routeIs('admin.bcategory.index')) active @endif">
                                                <a
                                                    href="{{ route('admin.bcategory.index') . '?language=' . $currentLang->code }}">
                                                    <span class="sub-item">{{ __('Category') }}</span>
                                                </a>
                                            </li>
                                            <li
                                                class="
                                                    @if (request()->path() == 'admin/blogs') active
                                                    @elseif(request()->is('admin/blog/*/edit')) active @endif">
                                                <a
                                                    href="{{ route('admin.blog.index') . '?language=' . $currentLang->code }}">
                                                    <span class="sub-item">{{ __('Post') }}</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                            @endif

                            @if (empty($admin->role) || (!empty($permissions) && in_array('Contact Page', $permissions)))
                                {{-- Contact Page --}}
                                <li class="   @if (request()->path() == 'admin/contact') active @endif">
                                    <a href="{{ route('admin.contact.index') . '?language=' . $currentLang->code }}">

                                        <span class="sub-item">{{ __('Contact Page') }}</span>
                                    </a>
                                </li>
                            @endif

                            @if (empty($admin->role) || (!empty($permissions) && in_array('Pages', $permissions)))
                                {{-- Dynamic Pages --}}
                                <li
                                    class="submenu
                                    @if (request()->routeIs('admin.page.edit')) selected
                                    @elseif(request()->routeIs('admin.page.create')) selected
                                    @elseif(request()->routeIs('admin.page.index')) selected @endif">
                                    <a data-toggle="collapse" href="#additionalPages">

                                        <span class="sub-item">{{ __('Additional Pages') }}</span>
                                        <span class="caret"></span>
                                    </a>
                                    <div class="collapse
                                        @if (request()->routeIs('admin.page.create')) show
                                        @elseif(request()->routeIs('admin.page.index')) show
                                        @elseif(request()->routeIs('admin.page.edit')) show @endif"
                                        id="additionalPages">
                                        <ul class="nav nav-collapse subnav">
                                            <li class="@if (request()->routeIs('admin.page.create')) active @endif">
                                                <a href="{{ route('admin.page.create') }}">
                                                    <span class="sub-item">{{ __('Create Page') }}</span>
                                                </a>
                                            </li>
                                            <li
                                                class="
                                                @if (request()->routeIs('admin.page.index')) active
                                                @elseif(request()->routeIs('admin.page.edit')) active @endif">
                                                <a
                                                    href="{{ route('admin.page.index') . '?language=' . $currentLang->code }}">
                                                    <span class="sub-item">{{ __('Pages') }}</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                            @endif

                            @if (empty($admin->role) || (!empty($permissions) && in_array('Menu Builder', $permissions)))
                                {{-- Menu Builder --}}
                                <li class=" @if (request()->path() == 'admin/menu-builder') active @endif">
                                    <a
                                        href="{{ route('admin.menu_builder.index') . '?language=' . $currentLang->code }}">

                                        <span class="sub-item">{{ __('Menu Builder') }}</span>
                                    </a>
                                </li>
                            @endif

                            @if (empty($admin->role) || (!empty($permissions) && in_array('Footer', $permissions)))
                                {{-- Footer --}}
                                <li
                                    class="submenu
                                        @if (request()->path() == 'admin/footers') selected
                                        @elseif(request()->path() == 'admin/ulinks') selected @endif">
                                    <a data-toggle="collapse" href="#footer">

                                        <span class="sub-item">{{ __('Footer') }}</span>
                                        <span class="caret"></span>
                                    </a>
                                    <div class="collapse
                                            @if (request()->path() == 'admin/footers') show
                                            @elseif(request()->path() == 'admin/ulinks') show @endif"
                                        id="footer">
                                        <ul class="nav nav-collapse subnav">
                                            <li class="@if (request()->path() == 'admin/footers') active @endif">
                                                <a
                                                    href="{{ route('admin.footer.index') . '?language=' . $currentLang->code }}">
                                                    <span class="sub-item">{{ __('Image & Text') }}</span>
                                                </a>
                                            </li>
                                            <li class="@if (request()->path() == 'admin/ulinks') active @endif">
                                                <a
                                                    href="{{ route('admin.ulink.index') . '?language=' . $currentLang->code }}">
                                                    <span class="sub-item">{{ __('Useful Links') }}</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                            @endif


                            <li class="@if (request()->path() == 'admin/seo') active @endif">
                                <a href="{{ route('admin.seo', ['language' => $currentLang->code]) }}">
                                    <span class="sub-item">{{ __('SEO Information') }}</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>


                <li
                    class="nav-item @if (request()->routeIs('admin.support_tickets.settings')) active
                    @elseif (request()->routeIs('admin.support_tickets')) active
                    @elseif (request()->routeIs('admin.support_ticket.conversation')) active @endif">
                    <a data-toggle="collapse" href="#support_tickets">
                        <i class="far fa-ticket-alt"></i>
                        <p>{{ __('Support Tickets') }}</p>
                        <span class="caret"></span>
                    </a>

                    <div id="support_tickets"
                        class="collapse
                            @if (request()->routeIs('admin.support_tickets.settings')) show
                            @elseif (request()->routeIs('admin.support_tickets')) show
                            @elseif (request()->routeIs('admin.support_ticket.conversation')) show @endif  ">
                        <ul class="nav nav-collapse">
                            <li class="{{ request()->routeIs('admin.support_tickets.settings') ? 'active' : '' }}">
                                <a href="{{ route('admin.support_tickets.settings') }}">
                                    <span class="sub-item">{{ __('Settings') }}</span>
                                </a>
                            </li>

                            <li
                                class="{{ request()->routeIs('admin.support_tickets') && empty(request()->input('ticket_status')) ? 'active' : '' }}">
                                <a href="{{ route('admin.support_tickets') }}">
                                    <span class="sub-item">{{ __('All Tickets') }}</span>
                                </a>
                            </li>

                            <li
                                class="{{ request()->routeIs('admin.support_tickets') && request()->input('ticket_status') == 'pending' ? 'active' : '' }}">
                                <a href="{{ route('admin.support_tickets', ['ticket_status' => 'pending']) }}">
                                    <span class="sub-item">{{ __('Pending Tickets') }}</span>
                                </a>
                            </li>

                            <li
                                class="{{ request()->routeIs('admin.support_tickets') && request()->input('ticket_status') == 'open' ? 'active' : '' }}">
                                <a href="{{ route('admin.support_tickets', ['ticket_status' => 'open']) }}">
                                    <span class="sub-item">{{ __('Open Tickets') }}</span>
                                </a>
                            </li>

                            <li
                                class="{{ request()->routeIs('admin.support_tickets') && request()->input('ticket_status') == 'closed' ? 'active' : '' }}">
                                <a href="{{ route('admin.support_tickets', ['ticket_status' => 'closed']) }}">
                                    <span class="sub-item">{{ __('Closed Tickets') }}</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                {{-- Announcement Popup --}}
                @if (empty($admin->role) || (!empty($permissions) && in_array('Announcement Popup', $permissions)))
                    <li
                        class="nav-item
                    @if (request()->path() == 'admin/popup/create') active
                    @elseif(request()->path() == 'admin/popup/types') active
                    @elseif(request()->is('admin/popup/*/edit')) active
                    @elseif(request()->path() == 'admin/popups') active @endif">
                        <a data-toggle="collapse" href="#announcementPopup">
                            <i class="far fa-bullhorn"></i>
                            <p>{{ __('Announcement Popup') }}</p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse
                        @if (request()->path() == 'admin/popup/create') show
                        @elseif(request()->path() == 'admin/popup/types') show
                        @elseif(request()->path() == 'admin/popups') show
                        @elseif(request()->is('admin/popup/*/edit')) show @endif"
                            id="announcementPopup">
                            <ul class="nav nav-collapse">
                                <li
                                    class="@if (request()->path() == 'admin/popup/types') active
                                @elseif(request()->path() == 'admin/popup/create') active @endif">
                                    <a href="{{ route('admin.popup.types') }}">
                                        <span class="sub-item">{{ __('Add Popup') }}</span>
                                    </a>
                                </li>
                                <li
                                    class="@if (request()->path() == 'admin/popups') active
                                @elseif(request()->is('admin/popup/*/edit')) active @endif">
                                    <a href="{{ route('admin.popup.index') . '?language=' . $currentLang->code }}">
                                        <span class="sub-item">{{ __('Popups') }}</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endif







                @if (empty($admin->role) || (!empty($permissions) && in_array('Settings', $permissions)))
                    {{-- Basic Settings --}}
                    <li
                        class="nav-item
                            @if (request()->path() == 'admin/favicon') active
                            @elseif(request()->path() == 'admin/logo') active
                            @elseif(request()->path() == 'admin/preloader') active
                            @elseif(request()->path() == 'admin/basicinfo') active
                            @elseif(request()->path() == 'admin/social') active
                            @elseif(request()->is('admin/social/*')) active
                            @elseif(request()->path() == 'admin/heading') active
                            @elseif(request()->path() == 'admin/script') active
                            @elseif(request()->path() == 'admin/maintainance') active
                            @elseif(request()->path() == 'admin/cookie-alert') active
                            @elseif(request()->path() == 'admin/mail-from-admin') active
                            @elseif(request()->path() == 'admin/mail-to-admin') active
                            @elseif(request()->path() == 'admin/email-templates') active
                            @elseif(request()->routeIs('admin.edit_mail_template')) active
                            @elseif(request()->routeIs('admin.mail_templates')) active
                            @elseif (request()->path() == 'admin/gateways') active
                            @elseif(request()->path() == 'admin/offline/gateways') active
                            @elseif (request()->path() == 'admin/languages') active
                            @elseif(request()->is('admin/language/*/edit')) active
                            @elseif(request()->routeIs('admin.language.edit_admin_front_keyword')) active
                            @elseif(request()->routeIs('admin.language.edit_admin_dashboard_keyword')) active
                            @elseif(request()->routeIs('admin.language.edit_user_dashboard_keyword')) active
                            @elseif(request()->routeIs('admin.language.edit_user_frontend_keyword')) active
                            @elseif (request()->path() == 'admin/sitemap') active @endif">
                        <a data-toggle="collapse" href="#basic">
                            <i class="far fa-cog"></i>
                            <p>{{ __('Settings') }}</p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse
                            @if (request()->path() == 'admin/favicon') show
                            @elseif(request()->path() == 'admin/logo') show
                            @elseif(request()->path() == 'admin/preloader') show
                            @elseif(request()->path() == 'admin/basicinfo') show
                            @elseif(request()->path() == 'admin/social') show
                            @elseif(request()->is('admin/social/*')) show
                            @elseif(request()->path() == 'admin/heading') show
                            @elseif(request()->path() == 'admin/script') show
                            @elseif(request()->path() == 'admin/maintainance') show
                            @elseif(request()->path() == 'admin/cookie-alert') show
                            @elseif(request()->path() == 'admin/mail-from-admin') show
                            @elseif(request()->path() == 'admin/mail-to-admin') show
                            @elseif(request()->path() == 'admin/email-templates') show
                            @elseif(request()->routeIs('admin.product.tags')) show
                            @elseif(request()->routeIs('admin.edit_mail_template')) show
                            @elseif(request()->routeIs('admin.mail_templates')) show
                            @elseif (request()->path() == 'admin/gateways') show
                            @elseif(request()->path() == 'admin/offline/gateways') show
                            @elseif (request()->path() == 'admin/languages') show
                            @elseif(request()->is('admin/language/*/edit')) show
                            @elseif(request()->routeIs('admin.language.edit_admin_front_keyword')) show
                            @elseif(request()->routeIs('admin.language.edit_admin_dashboard_keyword')) show
                            @elseif(request()->routeIs('admin.language.edit_user_dashboard_keyword')) show
                            @elseif(request()->routeIs('admin.language.edit_user_frontend_keyword')) show
                            @elseif (request()->path() == 'admin/sitemap') show @endif"
                            id="basic">
                            <ul class="nav nav-collapse">
                                <li class="@if (request()->path() == 'admin/basicinfo') active @endif">
                                    <a href="{{ route('admin.basicinfo') }}">
                                        <span class="sub-item">{{ __('General Settings') }}</span>
                                    </a>
                                </li>

                                <li
                                    class="submenu
                                @if (request()->routeIs('admin.mail_from_admin')) selected
                                @elseif (request()->routeIs('admin.mail_to_admin')) selected
                                @elseif (request()->routeIs('admin.mail_templates')) selected
                                @elseif (request()->routeIs('admin.edit_mail_template')) selected @endif">
                                    <a data-toggle="collapse" href="#emailset"
                                        aria-expanded="{{ request()->path() == 'admin/mail-from-admin' || request()->path() == 'admin/mail-to-admin' || request()->routeIs('admin.mail_templates') || request()->routeIs('admin.edit_mail_template') ? 'true' : 'false' }}">
                                        <span class="sub-item">{{ __('Email Settings') }}</span>
                                        <span class="caret"></span>
                                    </a>
                                    <div class="collapse {{ request()->path() == 'admin/mail-from-admin' || request()->path() == 'admin/mail-to-admin' || request()->routeIs('admin.mail_templates') || request()->routeIs('admin.edit_mail_template') ? 'show' : '' }}"
                                        id="emailset">
                                        <ul class="nav nav-collapse subnav">
                                            <li class="@if (request()->path() == 'admin/mail-from-admin') active @endif">
                                                <a href="{{ route('admin.mailFromAdmin') }}">
                                                    <span class="sub-item">{{ __('Mail from Admin') }}</span>
                                                </a>
                                            </li>
                                            <li class="@if (request()->path() == 'admin/mail-to-admin') active @endif">
                                                <a href="{{ route('admin.mailToAdmin') }}">
                                                    <span class="sub-item">{{ __('Mail to Admin') }}</span>
                                                </a>
                                            </li>
                                            <li
                                                class="
                                            @if (request()->routeIs('admin.mail_templates')) active
                                            @elseif (request()->routeIs('admin.edit_mail_template')) active @endif">
                                                <a href="{{ route('admin.mail_templates') }}">
                                                    <span class="sub-item">{{ __('Mail Templates') }}</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>

                                @if (empty($admin->role) || (!empty($permissions) && in_array('Payment Gateways', $permissions)))
                                    {{-- Payment Gateways --}}
                                    <li
                                        class="submenu
                                        @if (request()->path() == 'admin/gateways') selected
                                        @elseif(request()->path() == 'admin/offline/gateways') selected @endif">
                                        <a data-toggle="collapse" href="#gateways">

                                            <span class="sub-item">{{ __('Payment Gateways') }}</span>
                                            <span class="caret"></span>
                                        </a>
                                        <div class="collapse
                                            @if (request()->path() == 'admin/gateways') show
                                            @elseif(request()->path() == 'admin/offline/gateways') show @endif"
                                            id="gateways">
                                            <ul class="nav nav-collapse subnav">
                                                <li class="@if (request()->path() == 'admin/gateways') active @endif">
                                                    <a href="{{ route('admin.gateway.index') }}">
                                                        <span class="sub-item">{{ __('Online Gateways') }}</span>
                                                    </a>
                                                </li>
                                                <li class="@if (request()->path() == 'admin/offline/gateways') active @endif">
                                                    <a
                                                        href="{{ route('admin.gateway.offline') . '?language=' . $currentLang->code }}">
                                                        <span class="sub-item">{{ __('Offline Gateways') }}</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>
                                @endif


                                @if (empty($admin->role) || (!empty($permissions) && in_array('Language Management', $permissions)))
                                    {{-- Language Management Page --}}
                                    <li
                                        class="
                                        @if (request()->path() == 'admin/languages') active
                                        @elseif(request()->is('admin/language/*/edit')) active
                                        @elseif(request()->routeIs('admin.language.edit_admin_front_keyword')) active
                                        @elseif(request()->routeIs('admin.language.edit_admin_dashboard_keyword')) active
                                        @elseif(request()->routeIs('admin.language.edit_user_dashboard_keyword')) active
                                        @elseif(request()->routeIs('admin.language.edit_user_frontend_keyword')) active @endif">
                                        <a href="{{ route('admin.language.index') }}">
                                            <span class="sub-item">{{ __('Languages') }}</span>
                                        </a>
                                    </li>
                                @endif




                                <li
                                    class="@if (request()->path() == 'admin/social') active
                                @elseif(request()->is('admin/social/*')) active @endif">
                                    <a href="{{ route('admin.social.index') }}">
                                        <span class="sub-item">{{ __('Social Links') }}</span>
                                    </a>
                                </li>

                                <li class="@if (request()->path() == 'admin/script') active @endif">
                                    <a href="{{ route('admin.script') }}">
                                        <span class="sub-item">{{ __('Plugins') }}</span>
                                    </a>
                                </li>

                                <li class="@if (request()->path() == 'admin/maintainance') active @endif">
                                    <a href="{{ route('admin.maintainance') }}">
                                        <span class="sub-item">{{ __('Maintainance Mode') }}</span>
                                    </a>
                                </li>
                                <li class="@if (request()->path() == 'admin/cookie-alert') active @endif">
                                    <a href="{{ route('admin.cookie.alert') . '?language=' . $currentLang->code }}">
                                        <span class="sub-item">{{ __('Cookie Alert') }}</span>
                                    </a>
                                </li>


                                @if (empty($admin->role) || (!empty($permissions) && in_array('Sitemap', $permissions)))
                                    {{-- Sitemap --}}
                                    <li class="
            @if (request()->path() == 'admin/sitemap') active @endif">
                                        <a
                                            href="{{ route('admin.sitemap.index') . '?language=' . $currentLang->code }}">
                                            <span class="sub-item">{{ __('Sitemap') }}</span>
                                        </a>
                                    </li>
                                @endif


                                {{-- Cache Clear --}}
                                <li class="">
                                    <a href="{{ route('admin.cache.clear') }}">
                                        <span class="sub-item">{{ __('Clear Cache') }}</span>
                                    </a>
                                </li>

                            </ul>
                        </div>
                    </li>
                @endif



                @if (empty($admin->role) || (!empty($permissions) && in_array('Role Management', $permissions)))
                    {{-- Role Management Page --}}
                    <li
                        class="nav-item
          @if (request()->path() == 'admin/roles') active
          @elseif(request()->is('admin/role/*/permissions/manage')) active @endif">
                        <a href="{{ route('admin.role.index') }}">
                            <i class="far fa-toggle-on"></i>
                            <p>{{ __('Role Management') }}</p>
                        </a>
                    </li>
                @endif



                @if (empty($admin->role) || (!empty($permissions) && in_array('Admins Management', $permissions)))
                    {{-- Admins Management Page --}}
                    <li
                        class="nav-item
           @if (request()->path() == 'admin/users') active
           @elseif(request()->is('admin/user/*/edit')) active @endif">
                        <a href="{{ route('admin.user.index') }}">
                            <i class="far fa-user-cog"></i>
                            <p>{{ __('Admins Management') }}</p>
                        </a>
                    </li>
                @endif




            </ul>
        </div>
    </div>
</div>
