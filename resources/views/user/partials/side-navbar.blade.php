@php
    use App\Http\Helpers\UserPermissionHelper;
    $package = UserPermissionHelper::currentPackage($tenant->id);
    if (!empty($tenant)) {
        $permissions = UserPermissionHelper::packagePermission($tenant->id);
        $permissions = json_decode($permissions, true);
    }
@endphp

<div class="sidebar sidebar-style-2" @if (request()->cookie('user-theme') == 'dark') data-background-color="dark2" @endif>
    <div class="sidebar-wrapper scrollbar scrollbar-inner">
        <div class="sidebar-content">
            <div class="user">
                <div class="avatar-sm float-left mr-2">

                    @if ($tenant->photo != null)
                        <img src="{{ asset($tenant->photo) }}" alt="..." class="avatar-img rounded">
                    @endif
                </div>
                <div class="info">
                    <a data-toggle="collapse" href="#collapseExample" aria-expanded="true">
                        <span>
                            {{ auth()->user()->first_name . ' ' . auth()->user()->last_name }}
                            <span class="user-level">{{ auth()->user()->username }}</span>
                            <span class="caret"></span>
                        </span>
                    </a>
                    <div class="clearfix"></div>
                    <div class="collapse in" id="collapseExample">
                        <ul class="nav">
                            @if (!is_null($package))
                                <li>
                                    <a href="{{ route('user-profile-update') }}">
                                        <span class="link-collapse">{{ __('Edit Profile') }}</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('user.changePass') }}">
                                        <span class="link-collapse">{{ __('Change Password') }}</span>
                                    </a>
                                </li>
                            @endif
                            <li>
                                <a href="{{ route('user-logout') }}">
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
                <li class="nav-item
            @if (request()->path() == 'user/dashboard') active @endif">
                    <a href="{{ route('user-dashboard') }}">
                        <i class="la flaticon-paint-palette"></i>
                        <p>{{ __('Dashboard') }}</p>
                    </a>
                </li>


                {{-- Start property Management --}}
                @if (!empty($package) && !empty($permissions) && in_array('Property Management', $permissions))

                    <li
                        class="nav-item @if (request()->routeIs('user.property_management.categories')) active
                        @elseif (request()->routeIs('user.property_management.countries')) active
              @elseif (request()->routeIs('user.property_management.settings')) active
              @elseif (request()->routeIs('user.property_management.states')) active
              @elseif (request()->routeIs('user.property_management.cities')) active
             @elseif (request()->routeIs('user.property_management.create_property')) active
                     @elseif (request()->routeIs('user.property_management.properties')) active
                     @elseif (request()->routeIs('user.property_management.messages')) active
                     @elseif (request()->routeIs('user.property_management.edit')) active
                      @elseif(request()->routeIs('user.property_management.type')) active @endif">
                        <a data-toggle="collapse" href="#propertySpecification">
                            <i class="fas fa-car"></i>
                            <p>{{ __('Car Management') }}</p>
                            <span class="caret"></span>
                        </a>

                        <div id="propertySpecification"
                            class="collapse
              @if (request()->routeIs('user.property_management.categories')) show
              @elseif (request()->routeIs('user.property_management.settings')) show
              @elseif (request()->routeIs('user.property_management.countries')) show
              @elseif (request()->routeIs('user.property_management.states')) show
              @elseif (request()->routeIs('user.property_management.amenities'))  show
              @elseif (request()->routeIs('user.property_management.cities'))  show
              @elseif (request()->routeIs('user.property_management.create_property')) show
              @elseif (request()->routeIs('user.property_management.type')) show
              @elseif (request()->routeIs('user.property_management.properties')) show
              @elseif (request()->routeIs('user.property_management.messages')) show
              @elseif (request()->routeIs('user.property_management.edit')) show @endif">
                            <ul class="nav nav-collapse">
                                {{-- <li
                                    class="{{ request()->routeIs('user.property_management.settings') ? 'active' : '' }}">
                                    <a
                                        href="{{ route('user.property_management.settings', ['language' => $tenantDefaultLang->code]) }}">
                                        <span class="sub-item">{{ __('Settings') }}</span>
                                    </a>
                                </li> --}}

                                {{-- <li
                                    class="submenu
                                @if (request()->routeIs('user.property_management.categories')) selected
                                        @elseif (request()->routeIs('user.property_management.countries')) selected
                                        @elseif (request()->routeIs('user.property_management.states')) selected
                                        @elseif (request()->routeIs('user.property_management.amenities'))  selected
                                        @elseif (request()->routeIs('user.property_management.cities')) selected @endif
                                ">
                                    <a data-toggle="collapse" href="#Specifications">
                                        <span class="sub-item">{{ __('Specifications') }}</span>
                                        <span class="caret"></span>
                                    </a>

                                    <div id="Specifications"
                                        class="collapse

                                        @if (request()->routeIs('user.property_management.categories')) show
                                        @elseif (request()->routeIs('user.property_management.countries')) show
                                        @elseif (request()->routeIs('user.property_management.states')) show
                                        @elseif (request()->routeIs('user.property_management.amenities'))  show
                                        @elseif (request()->routeIs('user.property_management.cities')) show @endif">
                                        <ul class="nav nav-collapse subnav">
                                            <li
                                                class="{{ request()->routeIs('user.property_management.categories') ? 'active' : '' }}">
                                                <a
                                                    href="{{ route('user.property_management.categories', ['language' => $tenantDefaultLang->code]) }}">
                                                    <span class="sub-item">{{ __('Categories') }}</span>
                                                </a>
                                            </li>
                                            <li
                                                class="{{ request()->routeIs('user.property_management.amenities') ? 'active' : '' }}">
                                                <a
                                                    href="{{ route('user.property_management.amenities', ['language' => $tenantDefaultLang->code]) }}">
                                                    <span class="sub-item">{{ __('Amenities') }}</span>
                                                </a>
                                            </li>
                                            @if ($userBs->property_country_status == 1)
                                                <li
                                                    class="{{ request()->routeIs('user.property_management.countries') ? 'active' : '' }}">
                                                    <a
                                                        href="{{ route('user.property_management.countries', ['language' => $tenantDefaultLang->code]) }}">
                                                        <span class="sub-item">{{ __('Country') }}</span>
                                                    </a>
                                                </li>
                                            @endif
                                            @if ($userBs->property_state_status == 1)
                                                <li
                                                    class="{{ request()->routeIs('user.property_management.states') ? 'active' : '' }}">
                                                    <a
                                                        href="{{ route('user.property_management.states', ['language' => $tenantDefaultLang->code]) }}">
                                                        <span class="sub-item">{{ __('States') }}</span>
                                                    </a>
                                                </li>
                                            @endif
                                            <li
                                                class="{{ request()->routeIs('user.property_management.cities') ? 'active' : '' }}">
                                                <a
                                                    href="{{ route('user.property_management.cities', ['language' => $tenantDefaultLang->code]) }}">
                                                    <span class="sub-item">{{ __('Cities') }}</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </li> --}}

                                <li
                                    class="{{ request()->routeIs('user.property_management.properties') ||
                                    request()->routeIs('user.property_management.edit')
                                        ? 'active'
                                        : '' }}">
                                    <a
                                        href="{{ route('user.property_management.properties', ['language' => $tenantDefaultLang->code]) }}">
                                        <span class="sub-item">{{ __('Car Management') }}</span>
                                    </a>
                                </li>

                                <li
                                    class="{{ request()->routeIs('user.property_management.create_property') || request()->routeIs('user.property_management.type') ? 'active' : '' }}">
                                    <a
                                        href="{{ route('user.property_management.type', ['language' => $tenantDefaultLang->code]) }}">
                                        <span class="sub-item">{{ __('Add Property') }}</span>
                                    </a>
                                </li>


                                <li
                                    class="{{ request()->routeIs('user.property_management.messages') ? 'active' : '' }}">
                                    <a href="{{ route('user.property_management.messages') }}">
                                        <span class="sub-item">{{ __('Messages') }}</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endif
                {{-- End property specification  --}}

                {{-- Project management  start --}}
                @if (!empty($package) && !empty($permissions) && in_array('Project Management', $permissions))
                    <li
                        class="nav-item
                            @if (request()->routeIs('user.project_management.projects')) active
                            @elseif (request()->routeIs('user.project_management.create_project')) active
                            @elseif (request()->routeIs('user.project_management.project_types')) active
                            @elseif (request()->routeIs('user.project_management.settings')) active
                            @elseif (request()->routeIs('user.project_management.categories')) active
                            @elseif (request()->routeIs('user.project_management.countries')) active
                            @elseif (request()->routeIs('user.project_management.states')) active
                            @elseif (request()->routeIs('user.project_management.cities')) active
                            @elseif (request()->routeIs('user.project_management.messages')) active
                            @elseif (request()->routeIs('user.project_management.edit')) active @endif">
                        <a data-toggle="collapse" href="#projectManagement">
                            <i class="fas fa-building"></i>
                            <p>{{ __('Motorcycle Management') }}</p>
                            <span class="caret"></span>
                        </a>

                        <div id="projectManagement"
                            class="collapse
                                @if (request()->routeIs('user.project_management.create_project')) show
                                @elseif (request()->routeIs('user.project_management.projects')) show
                                @elseif (request()->routeIs('user.project_management.settings')) show
                                @elseif (request()->routeIs('user.project_management.edit')) show
                                @elseif (request()->routeIs('user.project_management.categories')) show
                                @elseif (request()->routeIs('user.project_management.countries')) show
                                @elseif (request()->routeIs('user.project_management.states')) show
                                @elseif (request()->routeIs('user.project_management.cities')) show
                                @elseif (request()->routeIs('user.project_management.messages')) show
                                @elseif (request()->routeIs('user.project_management.project_types')) show @endif ">
                            <ul class="nav nav-collapse">

                                <li
                                    class="{{ request()->routeIs('user.project_management.settings') ? 'active' : '' }}">
                                    <a href="{{ route('user.project_management.settings') }}">
                                        <span class="sub-item">{{ __('Settings') }}</span>
                                    </a>
                                </li>

                                <li
                                    class="submenu
                                @if (request()->routeIs('user.project_management.categories')) selected
                                        @elseif (request()->routeIs('user.project_management.countries')) selected
                                        @elseif (request()->routeIs('user.project_management.states')) selected
                                        @elseif (request()->routeIs('user.project_management.cities')) selected @endif
                                ">
                                    <a data-toggle="collapse" href="#projetSpecifications">
                                        <span class="sub-item">{{ __('Specifications') }}</span>
                                        <span class="caret"></span>
                                    </a>

                                    <div id="projetSpecifications"
                                        class="collapse

                                        @if (request()->routeIs('user.project_management.categories')) show
                                        @elseif (request()->routeIs('user.project_management.countries')) show
                                        @elseif (request()->routeIs('user.project_management.states')) show
                                        @elseif (request()->routeIs('user.project_management.cities')) show @endif">
                                        <ul class="nav nav-collapse subnav">
                                            <li
                                                class="{{ request()->routeIs('user.project_management.categories') ? 'active' : '' }}">
                                                <a
                                                    href="{{ route('user.project_management.categories', ['language' => $tenantDefaultLang->code]) }}">
                                                    <span class="sub-item">{{ __('Categories') }}</span>
                                                </a>
                                            </li>

                                            @if ($userBs->project_country_status == 1)
                                                <li
                                                    class="{{ request()->routeIs('user.project_management.countries') ? 'active' : '' }}">
                                                    <a
                                                        href="{{ route('user.project_management.countries', ['language' => $tenantDefaultLang->code]) }}">
                                                        <span class="sub-item">{{ __('Country') }}</span>
                                                    </a>
                                                </li>
                                            @endif
                                            @if ($userBs->project_state_status == 1)
                                                <li
                                                    class="{{ request()->routeIs('user.project_management.states') ? 'active' : '' }}">
                                                    <a
                                                        href="{{ route('user.project_management.states', ['language' => $tenantDefaultLang->code]) }}">
                                                        <span class="sub-item">{{ __('States') }}</span>
                                                    </a>
                                                </li>
                                            @endif
                                            <li
                                                class="{{ request()->routeIs('user.project_management.cities') ? 'active' : '' }}">
                                                <a
                                                    href="{{ route('user.project_management.cities', ['language' => $tenantDefaultLang->code]) }}">
                                                    <span class="sub-item">{{ __('Cities') }}</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>

                                <li
                                    class="{{ request()->routeIs('user.project_management.projects') ||
                                    request()->routeIs('user.project_management.edit') ||
                                    request()->routeIs('user.project_management.project_types')
                                        ? 'active'
                                        : '' }}">
                                    <a
                                        href="{{ route('user.project_management.projects', ['language' => $tenantDefaultLang->code]) }}">
                                        <span class="sub-item">{{ __('Manage Projects') }}</span>
                                    </a>

                                </li>
                                <li
                                    class="{{ request()->routeIs('user.project_management.create_project') ? 'active' : '' }}">
                                    <a href="{{ route('user.project_management.create_project') }}">
                                        <span class="sub-item">{{ __('Add Project') }}</span>
                                    </a>
                                </li>


                                <li
                                    class="{{ request()->routeIs('user.project_management.messages') ? 'active' : '' }}">
                                    <a href="{{ route('user.project_management.messages') }}">
                                        <span class="sub-item">{{ __('Messages') }}</span>
                                    </a>
                                </li>

                            </ul>
                        </div>
                    </li>
                @endif
                {{-- Project Management end  --}}

                {{-- Start Agent Management --}}
                @if (!empty($package) && !empty($permissions) && in_array('Agent', $permissions))
                    <li class="nav-item  @if (request()->routeIs('user.agent_management.index')) active @endif">
                        <a
                            href="{{ route('user.agent_management.index', ['language' => $tenantDefaultLang->code]) }}">
                            <i class="fas fa-users-cog"></i>
                            <p>{{ __('Agents') }}</p>
                        </a>
                    </li>
                @endif
                {{-- End Agent Management --}}

                <li
                    class="nav-item
                       @if (request()->path() == 'user/registered-users') active
                        @elseif(request()->routeIs('user.user_details')) active
                        @elseif(request()->routeIs('user.user.change_password')) active
                        @elseif  (request()->path() == 'user/subscribers') active
                        @elseif(request()->path() == 'user/mailsubscriber') active @endif">
                    <a data-toggle="collapse" href="#regUser">
                        <i class="far fa-users"></i>

                        <p>{{ __('User Management') }}</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse
                            @if (request()->path() == 'user/registered-users') show
                            @elseif(request()->routeIs('user.user_details')) show
                            @elseif(request()->routeIs('user.user.change_password')) show
                            @elseif  (request()->path() == 'user/subscribers') show
                            @elseif(request()->path() == 'user/mailsubscriber') show @endif"
                        id="regUser">
                        <ul class="nav nav-collapse">
                            <li class="@if (request()->routeIs('user.registered_users') || request()->routeIs('user.user.change_password')) active @endif">
                                <a href="{{ route('user.registered_users') }}">

                                    <span class="sub-item">{{ __('Registered Users') }}</span>
                                </a>
                            </li>

                            @if (empty($admin->role) || (!empty($permissions) && in_array('Subscribers', $permissions)))
                                {{-- Subscribers --}}
                                <li
                                    class="submenu
         @if (request()->path() == 'user/subscribers') selected
                @elseif(request()->path() == 'user/mailsubscriber')  selected @endif">
                                    <a data-toggle="collapse" href="#subscribers">

                                        <span class="sub-item">{{ __('Subscribers') }}</span>
                                        <span class="caret"></span>
                                    </a>
                                    <div class="collapse
            @if (request()->path() == 'user/subscribers') show
                @elseif(request()->path() == 'user/mailsubscriber')   show @endif"
                                        id="subscribers">
                                        <ul class="nav nav-collapse subnav">
                                            <li class="@if (request()->routeIs('user.subscriber.index')) active @endif">
                                                <a href="{{ route('user.subscriber.index') }}">
                                                    <span class="sub-item">{{ __('Subscribers') }}</span>
                                                </a>
                                            </li>
                                            <li class="@if (request()->routeIs('user.mailsubscriber')) active @endif">
                                                <a href="{{ route('user.mailsubscriber') }}">
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

                <li
                    class="nav-item
                    @if (request()->routeIs('user.custom_pages')) active
                        @elseif (request()->routeIs('user.faq_management')) active
                        @elseif (request()->routeIs('user.home_page.images_&_texts')) active
                        @elseif (request()->routeIs('user.custom_pages.create_page')) active
                        @elseif (request()->routeIs('user.custom_pages.edit_page')) active
                        @elseif (request()->routeIs('user.home_page.hero_section')) active
                        @elseif (request()->routeIs('user.home_page.section_titles')) active
                        @elseif (request()->routeIs('user.home_page.about_section')) active
                          @elseif (request()->routeIs('user.home_page.why_choose_us_section')) active
                        @elseif (request()->routeIs('user.home_page.testimonials_section')) active
                        @elseif (request()->routeIs('user.home_page.newsletter_section')) active
                        @elseif (request()->routeIs('user.home_page.partners_section')) active
                        @elseif (request()->routeIs('user.home_page.work_process_section')) active
                         @elseif (request()->routeIs('user.additional_sections')) active
                        @elseif (request()->routeIs('user.additional_section.create')) active
                        @elseif (request()->routeIs('user.additional_section.edit')) active
                        @elseif (request()->routeIs('user.about.additional_sections')) active
                        @elseif (request()->routeIs('user.about.sections.index')) active
                        @elseif(request()->routeIs('user.about.additional_section.create')) active
                        @elseif(request()->routeIs('user.about.additional_section.edit')) active
                        @elseif (request()->routeIs('user.home_page.counter_section')) active
                        @elseif (request()->routeIs('user.home_page.section_customization')) active
                        @elseif (request()->routeIs('user.blog_management.categories')) active
                        @elseif (request()->routeIs('user.blog_management.blogs')) active
                        @elseif (request()->routeIs('user.blog_management.create_blog')) active
                        @elseif (request()->routeIs('user.blog_management.edit_blog')) active
                        @elseif (request()->routeIs('user.footer.content')) active
                        @elseif(request()->routeIs('user.footer.logo'))active
                        @elseif(request()->routeIs('user.basic_settings.seo'))active
                        @elseif(request()->routeIs('user.page_headings'))active
                        @elseif (request()->routeIs('user.footer.quick_links')) active
                        @elseif (request()->path() == 'user/breadcrumb') active
                        @elseif (request()->path() == 'user/contact-form') active
                        @elseif (request()->path() == 'user/menu-builder') active @endif">
                    <a data-toggle="collapse" href="#pages">
                        <i class="far fa-file-alt"></i>
                        <p>{{ __('Pages') }}</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse
                        @if (request()->routeIs('user.custom_pages')) show
                        @elseif (request()->routeIs('user.faq_management')) show
                        @elseif (request()->routeIs('user.home_page.images_&_texts')) show
                        @elseif (request()->routeIs('user.custom_pages.create_page')) show
                        @elseif (request()->routeIs('user.custom_pages.edit_page')) show
                        @elseif (request()->routeIs('user.home_page.hero_section')) show
                        @elseif (request()->routeIs('user.home_page.section_titles')) show
                        @elseif (request()->routeIs('user.home_page.about_section')) show
                          @elseif (request()->routeIs('user.home_page.why_choose_us_section')) show
                        @elseif (request()->routeIs('user.home_page.testimonials_section')) show
                        @elseif (request()->routeIs('user.home_page.newsletter_section')) show
                        @elseif (request()->routeIs('user.home_page.partners_section')) show
                        @elseif (request()->routeIs('user.home_page.work_process_section')) show
                         @elseif (request()->routeIs('user.additional_sections')) show
                                        @elseif (request()->routeIs('user.additional_section.create')) show
                                        @elseif (request()->routeIs('user.additional_section.edit')) show
                        @elseif (request()->routeIs('user.about.additional_sections')) show
                 @elseif (request()->routeIs('user.about.sections.index')) show
                @elseif(request()->routeIs('user.about.additional_section.create')) show
                @elseif(request()->routeIs('user.about.additional_section.edit')) show
                        @elseif (request()->routeIs('user.home_page.counter_section')) show
                        @elseif (request()->routeIs('user.home_page.section_customization')) show
                        @elseif (request()->routeIs('user.blog_management.categories')) show
                        @elseif (request()->routeIs('user.blog_management.blogs')) show
                        @elseif (request()->routeIs('user.blog_management.create_blog')) show
                        @elseif (request()->routeIs('user.blog_management.edit_blog')) show
                        @elseif (request()->routeIs('user.footer.content')) show
                        @elseif(request()->routeIs('user.footer.logo'))  show
                        @elseif(request()->routeIs('user.basic_settings.seo'))  show
                        @elseif(request()->routeIs('user.page_headings'))  show
                        @elseif (request()->routeIs('user.footer.quick_links')) show
                        @elseif (request()->path() == 'user/breadcrumb') show
                        @elseif (request()->path() == 'user/contact-form') show
                        @elseif (request()->path() == 'user/menu-builder') show @endif"
                        id="pages">
                        <ul class="nav nav-collapse">

                            {{-- start home page --}}
                            <li
                                class=" @if (request()->routeIs('user.home_page.hero_section')) selected
                                    @elseif (request()->routeIs('user.home_page.images_&_texts')) selected
                                    @elseif (request()->routeIs('user.home_page.section_titles')) selected
                                    @elseif (request()->routeIs('user.home_page.about_section')) selected
                                      @elseif (request()->routeIs('user.home_page.why_choose_us_section')) selected
                                    @elseif (request()->routeIs('user.home_page.testimonials_section')) selected
                                    @elseif (request()->routeIs('user.home_page.newsletter_section')) selected
                                    @elseif (request()->routeIs('user.home_page.partners_section')) selected
                                    @elseif (request()->routeIs('user.home_page.work_process_section')) selected
                                 @elseif (request()->routeIs('user.additional_sections')) selected
                                        @elseif (request()->routeIs('user.additional_section.create')) selected
                                        @elseif (request()->routeIs('user.additional_section.edit')) selected
                                    @elseif (request()->routeIs('user.home_page.counter_section')) selected
                                    @elseif (request()->routeIs('user.home_page.section_customization')) selected @endif">
                                <a data-toggle="collapse" href="#home_page">
                                    <span class="sub-item">{{ __('Home Page') }}</span>
                                    <span class="caret"></span>
                                </a>

                                <div id="home_page"
                                    class="collapse
                                        @if (request()->routeIs('user.home_page.hero_section')) show
                                        @elseif (request()->routeIs('user.home_page.images_&_texts')) show
                                        @elseif (request()->routeIs('user.home_page.section_titles')) show
                                        @elseif (request()->routeIs('user.home_page.about_section')) show
                                          @elseif (request()->routeIs('user.home_page.why_choose_us_section')) show
                                        @elseif (request()->routeIs('user.home_page.testimonials_section')) show
                                        @elseif (request()->routeIs('user.home_page.newsletter_section')) show
                                        @elseif (request()->routeIs('user.home_page.partners_section')) show
                                        @elseif (request()->routeIs('user.home_page.work_process_section')) show

                                        @elseif (request()->routeIs('user.additional_sections')) show
                                        @elseif (request()->routeIs('user.additional_section.create')) show
                                        @elseif (request()->routeIs('user.additional_section.edit')) show
                                        @elseif (request()->routeIs('user.home_page.counter_section')) show
                                        @elseif (request()->routeIs('user.home_page.section_customization')) show @endif">
                                    <ul class="nav nav-collapse subnav">
                                        <li
                                            class="{{ request()->routeIs('user.home_page.hero_section') ? 'active' : '' }}">
                                            <a
                                                href="{{ route('user.home_page.hero_section', ['language' => $tenantDefaultLang->code]) }}">
                                                <span class="sub-item">{{ __('Hero Section') }}</span>
                                            </a>
                                        </li>


                                        <li
                                            class="{{ request()->routeIs('user.home_page.images_&_texts') ? 'active' : '' }}">
                                            <a
                                                href="{{ route('user.home_page.images_&_texts', ['language' => $tenantDefaultLang->code]) }}">
                                                <span class="sub-item">{{ __('Images & Texts') }}</span>
                                            </a>
                                        </li>

                                        <li
                                            class="{{ request()->routeIs('user.home_page.about_section') ? 'active' : '' }}">
                                            <a
                                                href="{{ route('user.home_page.about_section', ['language' => $tenantDefaultLang->code]) }}">
                                                <span class="sub-item">{{ __('About Section') }}</span>
                                            </a>
                                        </li>

                                        <li
                                            class="{{ request()->routeIs('user.home_page.why_choose_us_section') ? 'active' : '' }}">
                                            <a
                                                href="{{ route('user.home_page.why_choose_us_section', ['language' => $tenantDefaultLang->code]) }}">
                                                <span class="sub-item">{{ __('Why Choose Us') }}</span>
                                            </a>
                                        </li>

                                        @if ($userBs->theme_version != 2)
                                            <li
                                                class="{{ request()->routeIs('user.home_page.counter_section') ? 'active' : '' }}">
                                                <a
                                                    href="{{ route('user.home_page.counter_section', ['language' => $tenantDefaultLang->code]) }}">
                                                    <span class="sub-item">{{ __('Counters') }}</span>
                                                </a>
                                            </li>
                                        @endif

                                        <li
                                            class="{{ request()->routeIs('user.home_page.work_process_section') ? 'active' : '' }}">
                                            <a
                                                href="{{ route('user.home_page.work_process_section', ['language' => $tenantDefaultLang->code]) }}">
                                                <span class="sub-item">{{ __('Work Steps') }}</span>
                                            </a>
                                        </li>

                                        <li
                                            class="{{ request()->routeIs('user.home_page.testimonials_section') ? 'active' : '' }}">
                                            <a
                                                href="{{ route('user.home_page.testimonials_section', ['language' => $tenantDefaultLang->code]) }}">
                                                <span class="sub-item">{{ __('Testimonials') }}</span>
                                            </a>
                                        </li>


                                        @if ($userBs->theme_version != 1)
                                            <li
                                                class="{{ request()->routeIs('user.home_page.partners_section') ? 'active' : '' }}">
                                                <a href="{{ route('user.home_page.partners_section') }}">
                                                    <span class="sub-item">{{ __('Partners') }}</span>
                                                </a>
                                            </li>
                                        @endif

                                        <li
                                            class="
                                                @if (request()->routeIs('user.additional_sections')) active
                                                @elseif (request()->routeIs('user.additional_section.create'))
                                                active
                                                @elseif (request()->routeIs('user.additional_section.edit'))
                                                active @endif ">
                                            <a
                                                href="{{ route('user.additional_sections', ['language' => $tenantDefaultLang->code]) }}">
                                                <span class="sub-item">{{ __('Additional Sections') }}</span>
                                            </a>
                                        </li>

                                        <li
                                            class="{{ request()->routeIs('user.home_page.section_customization') ? 'active' : '' }}">
                                            <a href="{{ route('user.home_page.section_customization') }}">
                                                <span class="sub-item">{{ __('Section Show/Hide') }}</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            {{-- end home page  --}}
                            <li
                                class="subnav
                @if (request()->routeIs('user.about.additional_sections')) selected
                 @elseif (request()->routeIs('user.about.sections.index')) selected
                 @elseif (request()->routeIs('user.about.additional_section.edit')) selected
                @elseif(request()->routeIs('user.about.additional_section.create')) selected @endif
                ">
                                <a data-toggle="collapse" href="#gateways">

                                    <span class="sub-item">{{ __('About Page') }}</span>
                                    <span class="caret"></span>
                                </a>
                                <div class="collapse
                    @if (request()->routeIs('user.about.additional_sections')) show
                    @elseif (request()->routeIs('user.about.sections.index')) show
                    @elseif (request()->routeIs('user.about.additional_section.edit')) show
                    @elseif(request()->routeIs('user.about.additional_section.create')) show @endif
                    "
                                    id="gateways">
                                    <ul class="nav nav-collapse">
                                        <li
                                            class="
                                                    @if (request()->routeIs('user.about.additional_sections')) active
                                                    @elseif(request()->routeIs('user.about.additional_section.create')) active
                                                    @elseif (request()->routeIs('user.about.additional_section.edit')) active @endif
                                                    ">
                                            <a href="{{ route('user.about.additional_sections') }}">
                                                <span class="sub-item">{{ __('Additional Sections') }}</span>
                                            </a>
                                        </li>
                                        <li
                                            class=" @if (request()->routeIs('user.about.sections.index')) active @endif
                                                    ">
                                            <a href="{{ route('user.about.sections.index') }}">
                                                <span class="sub-item">{{ __('Section Show/Hide') }}</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>


                            {{-- faq --}}
                            <li class=" {{ request()->routeIs('user.faq_management') ? 'selected' : '' }}">
                                <a
                                    href="{{ route('user.faq_management', ['language' => $tenantDefaultLang->code]) }}">

                                    <span class="sub-item">{{ __('FAQ') }}</span>
                                </a>
                            </li>
                            {{-- blog Management --}}
                            @if (!empty($permissions) && in_array('Blog', $permissions))
                                <li
                                    class="submenu @if (request()->routeIs('user.blog_management.categories')) selected
                                    @elseif (request()->routeIs('user.blog_management.blogs')) selected
                                    @elseif (request()->routeIs('user.blog_management.create_blog')) selected
                                    @elseif (request()->routeIs('user.blog_management.edit_blog')) selected @endif">
                                    <a data-toggle="collapse" href="#blog">
                                        <span class="sub-item">{{ __('Blog') }}</span>
                                        <span class="caret"></span>
                                    </a>
                                    <div id="blog"
                                        class="collapse
                                        @if (request()->routeIs('user.blog_management.categories')) show
                                        @elseif (request()->routeIs('user.blog_management.blogs')) show
                                        @elseif (request()->routeIs('user.blog_management.create_blog')) show
                                        @elseif (request()->routeIs('user.blog_management.edit_blog')) show @endif">
                                        <ul class="nav nav-collapse subnav">
                                            <li
                                                class="{{ request()->routeIs('user.blog_management.categories') ? 'active' : '' }}">
                                                <a
                                                    href="{{ route('user.blog_management.categories', ['language' => $tenantDefaultLang->code]) }}">
                                                    <span class="sub-item">{{ __('Categories') }}</span>
                                                </a>
                                            </li>
                                            <li
                                                class="@if (request()->routeIs('user.blog_management.blogs')) active
                                                @elseif (request()->routeIs('user.blog_management.create_blog')) active
                                                @elseif (request()->routeIs('user.blog_management.edit_blog')) active @endif">
                                                <a
                                                    href="{{ route('user.blog_management.blogs', ['language' => $tenantDefaultLang->code]) }}">
                                                    <span class="sub-item">{{ __('Posts') }}</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                            @endif

                            <li class="@if (request()->path() == 'user/contact-form') active @endif">
                                <a href="{{ route('user.contact_form') }}">
                                    <span class="sub-item">{{ __('Contact Page') }}</span>
                                </a>
                            </li>

                            {{-- Additional Page --}}
                            @if (!empty($permissions) && in_array('Additional Page', $permissions))
                                <li
                                    class=" @if (request()->routeIs('user.custom_pages')) selected
                                        @elseif (request()->routeIs('user.custom_pages.create_page')) selected
                                        @elseif (request()->routeIs('user.custom_pages.edit_page')) selected @endif">
                                    <a
                                        href="{{ route('user.custom_pages', ['language' => $tenantDefaultLang->code]) }}">

                                        <span class="sub-item">{{ __('Additional Pages') }}</span>
                                    </a>
                                </li>
                            @endif
                            {{-- Menu Builder --}}
                            <li
                                class="
                                @if (request()->path() == 'user/menu-builder') active @endif">
                                <a
                                    href="{{ route('user.menu_builder.index', ['language' => $tenantDefaultLang->code]) }}">

                                    <span class="sub-item">{{ __('Menu Builder') }}</span>
                                </a>
                            </li>

                            {{-- footer --}}
                            <li
                                class="submenu
                                @if (request()->routeIs('user.footer.content')) selected
                                @elseif(request()->routeIs('user.footer.logo'))  selected
                                @elseif (request()->routeIs('user.footer.quick_links')) selected @endif">
                                <a data-toggle="collapse" href="#footer">

                                    <span class="sub-item">{{ __('Footer') }}</span>
                                    <span class="caret"></span>
                                </a>
                                <div id="footer"
                                    class="collapse
                                    @if (request()->routeIs('user.footer.content')) show
                                    @elseif(request()->routeIs('user.footer.logo')) show
                                    @elseif (request()->routeIs('user.footer.quick_links')) show @endif">
                                    <ul class="nav nav-collapse subnav">
                                        <li class="{{ request()->routeIs('user.footer.logo') ? 'active' : '' }}">
                                            <a href="{{ route('user.footer.logo') }}">
                                                <span class="sub-item">{{ __('Logo & Image') }}</span>
                                            </a>
                                        </li>
                                        <li class="{{ request()->routeIs('user.footer.content') ? 'active' : '' }}">
                                            <a
                                                href="{{ route('user.footer.content', ['language' => $tenantDefaultLang->code]) }}">
                                                <span class="sub-item">{{ __('Content') }}</span>
                                            </a>
                                        </li>
                                        <li
                                            class="{{ request()->routeIs('user.footer.quick_links') ? 'active' : '' }}">
                                            <a
                                                href="{{ route('user.footer.quick_links', ['language' => $tenantDefaultLang->code]) }}">
                                                <span class="sub-item">{{ __('Quick Links') }}</span>
                                            </a>
                                        </li>


                                    </ul>
                                </div>
                            </li>

                            <li
                                class="submenu
                                @if (request()->routeIs('user.breadcrumb')) selected
                                @elseif (request()->routeIs('user.page_headings')) selected @endif">
                                <a data-toggle="collapse" href="#Breadcrumb">

                                    <span class="sub-item">{{ __('Breadcrumb') }}</span>
                                    <span class="caret"></span>
                                </a>
                                <div id="Breadcrumb"
                                    class="collapse
                                    @if (request()->routeIs('user.breadcrumb')) show
                                    @elseif (request()->routeIs('user.page_headings')) show @endif">
                                    <ul class="nav nav-collapse subnav">
                                        <li class="{{ request()->routeIs('user.breadcrumb') ? 'active' : '' }}">
                                            <a href="{{ route('user.breadcrumb') }}">
                                                <span class="sub-item">{{ __('Image') }}</span>
                                            </a>
                                        </li>

                                        <li class="{{ request()->routeIs('user.page_headings') ? 'active' : '' }}">
                                            <a
                                                href="{{ route('user.page_headings', ['language' => $tenantDefaultLang->code]) }}">
                                                <span class="sub-item">{{ __('Page Headings') }}</span>
                                            </a>
                                        </li>

                                    </ul>
                                </div>
                            </li>

                            <li class="@if (request()->path() == 'user/basic_settings/seo') active @endif">
                                <a
                                    href="{{ route('user.basic_settings.seo', ['language' => $tenantDefaultLang->code]) }}">
                                    <span class="sub-item">{{ __('SEO Information') }}</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                @if (!empty($permissions) && in_array('Support Ticket', $permissions))
                    <li
                        class="nav-item @if (request()->routeIs('admin_user.support_tickets')) active
                        @elseif (request()->routeIs('admin_user.support_ticket.conversation')) active
                        @elseif (request()->routeIs('admin_user.support_tickets.create')) active @endif">
                        <a data-toggle="collapse" href="#support_ticketsaa">
                            <i class="fal fa-ticket-alt"></i>
                            <p>{{ __('Support Tickets') }}</p>
                            <span class="caret"></span>
                        </a>

                        <div id="support_ticketsaa"
                            class="collapse
                            @if (request()->routeIs('admin_user.support_tickets')) show
                            @elseif (request()->routeIs('admin_user.support_ticket.conversation')) show
                            @elseif (request()->routeIs('admin_user.support_tickets.create')) show @endif">
                            <ul class="nav nav-collapse">

                                <li class="{{ request()->routeIs('admin_user.support_tickets') ? 'active' : '' }}">
                                    <a href="{{ route('admin_user.support_tickets') }}">
                                        <span class="sub-item">{{ __('All Tickets') }}</span>
                                    </a>
                                </li>

                                <li
                                    class="{{ request()->routeIs('admin_user.support_tickets.create') ? 'active' : '' }}">
                                    <a href="{{ route('admin_user.support_tickets.create') }}">
                                        <span class="sub-item">{{ __('Add Tickets') }}</span>
                                    </a>
                                </li>


                            </ul>
                        </div>
                    </li>
                @endif
                {{-- advertise --}}
                @if (!empty($permissions) && in_array('Advertisement', $permissions))
                    <li
                        class="nav-item
                @if (request()->routeIs('user.advertise.settings')) active
                @elseif (request()->routeIs('user.advertisements')) active @endif">
                        <a data-toggle="collapse" href="#ad">
                            <i class="fab fa-buysellads"></i>
                            <p>{{ __('Advertisements') }}</p>
                            <span class="caret"></span>
                        </a>
                        <div id="ad"
                            class="collapse
                    @if (request()->routeIs('user.advertise.settings')) show
                    @elseif (request()->routeIs('user.advertisements')) show @endif">
                            <ul class="nav nav-collapse">
                                <li class="{{ request()->routeIs('user.advertise.settings') ? 'active' : '' }}">
                                    <a href="{{ route('user.advertise.settings') }}">
                                        <span class="sub-item">{{ __('Settings') }}</span>
                                    </a>
                                </li>
                                <li class="{{ request()->routeIs('user.advertisements') ? 'active' : '' }}">
                                    <a href="{{ route('user.advertisements') }}">
                                        <span class="sub-item">{{ __('Advertisements') }}</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endif

                @if (!is_null($package))
                    {{-- announcement popup --}}
                    <li
                        class="nav-item @if (request()->routeIs('user.announcement_popups')) active
                @elseif (request()->routeIs('user.announcement_popups.select_popup_type')) active
                @elseif (request()->routeIs('user.announcement_popups.create_popup')) active
                @elseif (request()->routeIs('user.announcement_popups.edit_popup')) active @endif">
                        <a href="{{ route('user.announcement_popups', ['language' => $tenantDefaultLang->code]) }}">
                            <i class="fas fa-bullhorn"></i>
                            <p>{{ __('Announcement Popups') }}</p>
                        </a>
                    </li>
                @endif



                @if (!is_null($package))
                    {{-- Start Basic Settings --}}
                    <li
                        class="nav-item
                        @if (request()->path() == 'user/favicon') active
                        @elseif(request()->path() == 'user/logo') active
                        @elseif(request()->path() == 'user/footer-logo') active
                        @elseif(request()->path() == 'user/currency') active
                        @elseif(request()->path() == 'user/appearance') active
                        @elseif(request()->path() == 'user/social') active
                        @elseif(request()->is('user/social/*')) active
                        @elseif(request()->is('user/information')) active
                        @elseif(request()->is('user/plugins')) active
                        @elseif(request()->is('user/maintenance-mode')) active
                        @elseif(request()->is('user/cookie-alert')) active
                        @elseif (request()->routeIs('user.mail_templates')) active
                        @elseif (request()->routeIs('user.preloader')) active
                        @elseif (request()->routeIs('user.edit_mail_template')) active
                        @elseif (request()->routeIs('user.mail.info')) active
                        @elseif (request()->path() == 'user/domains') active
                        @elseif(request()->path() == 'user/subdomain') active
                        @elseif (request()->path() == 'user/languages') active
                                        @elseif(request()->is('user/language/*/edit')) active
                                        @elseif(request()->is('user/language/*/edit/keyword')) active @endif">
                        <a data-toggle="collapse" href="#basic">
                            <i class="la flaticon-settings"></i>
                            <p>{{ __('Settings') }}</p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse
                            @if (request()->path() == 'user/favicon') show
                            @elseif(request()->path() == 'user/logo') show
                            @elseif(request()->path() == 'user/footer-logo') show
                            @elseif(request()->path() == 'user/currency') show
                            @elseif(request()->path() == 'user/appearance') show
                            @elseif(request()->path() == 'user/social') show
                            @elseif(request()->is('user/social/*')) show
                            @elseif(request()->is('user/information')) show
                            @elseif(request()->is('user/plugins')) show
                            @elseif(request()->is('user/maintenance-mode')) show
                            @elseif(request()->is('user/cookie-alert')) show
                            @elseif (request()->routeIs('user.mail_templates')) show
                            @elseif (request()->routeIs('user.edit_mail_template')) show
                            @elseif (request()->routeIs('user.preloader')) show
                            @elseif (request()->routeIs('user.smtp.info')) show
                            @elseif (request()->routeIs('user.mail.info')) show
                            @elseif (request()->path() == 'user/domains') show
                            @elseif(request()->path() == 'user/subdomain') show
                            @elseif (request()->path() == 'user/languages') show
                                        @elseif(request()->is('user/language/*/edit')) show
                                        @elseif(request()->is('user/language/*/edit/keyword')) show @endif"
                            id="basic">
                            <ul class="nav nav-collapse">


                                <li class="@if (request()->path() == 'user/information') active @endif">
                                    <a href="{{ route('user.basic_settings.information') }}">
                                        <span class="sub-item">{{ __('General Settings') }}</span>
                                    </a>
                                </li>



                                @if (
                                    !empty($package) &&
                                        !empty($permissions) &&
                                        (in_array('Custom Domain', $permissions) || in_array('Subdomain', $permissions)))
                                    <li
                                        class="submenu
                                        @if (request()->path() == 'user/domains') selected
                                        @elseif(request()->path() == 'user/subdomain') selected @endif">
                                        <a data-toggle="collapse" href="#domains">

                                            <span class="sub-item">{{ __('Domains & URLs') }}</span>
                                            <span class="caret"></span>
                                        </a>
                                        <div class="collapse
                                            @if (request()->path() == 'user/domains') show
                                            @elseif(request()->path() == 'user/subdomain') show @endif"
                                            id="domains">
                                            <ul class="nav nav-collapse subnav">
                                                @if (!empty($permissions) && in_array('Custom Domain', $permissions))
                                                    <li class="  @if (request()->path() == 'user/domains') active @endif">
                                                        <a href="{{ route('user-domains') }}">
                                                            <span class="sub-item">{{ __('Custom Domain') }}</span>
                                                        </a>
                                                    </li>
                                                @endif
                                                @if (!empty($permissions) && in_array('Subdomain', $permissions))
                                                    <li class=" @if (request()->path() == 'user/subdomain') active @endif">
                                                        <a href="{{ route('user-subdomain') }}">
                                                            <span
                                                                class="sub-item">{{ __('Subdomain & Path URL') }}</span>
                                                        </a>
                                                    </li>
                                                {{-- @else
                                                    <li class=" @if (request()->path() == 'user/subdomain') active @endif">
                                                        <a href="{{ route('user-subdomain') }}">
                                                            <span class="sub-item">{{ __('Path-Based URL') }}</span>
                                                        </a>
                                                    </li> --}}
                                                @endif
                                            </ul>
                                        </div>
                                    </li>


                                @endif


                                <li
                                    class="submenu
                            @if (request()->routeIs('user.mail_templates')) selected
                            @elseif (request()->routeIs('user.edit_mail_template')) selected
                            @elseif (request()->routeIs('user.mail.info')) selected @endif">
                                    <a data-toggle="collapse" href="#mail_settings">
                                        <span class="sub-item">{{ __('Email Settings') }}</span>
                                        <span class="caret"></span>
                                    </a>
                                    <div id="mail_settings"
                                        class="collapse
                                @if (request()->routeIs('user.mail_templates')) show
                                @elseif (request()->routeIs('user.mail.info')) show
                                @elseif (request()->routeIs('user.edit_mail_template')) show @endif">
                                        <ul class="nav nav-collapse subnav">

                                            <li class="@if (request()->routeIs('user.mail.info')) active @endif">
                                                <a href="{{ route('user.mail.info') }}">
                                                    <span class="sub-item">{{ __('Mail Information') }}</span>
                                                </a>
                                            </li>
                                            <li
                                                class="@if (request()->routeIs('user.mail_templates')) active
                                        @elseif (request()->routeIs('user.edit_mail_template')) active @endif">
                                                <a href="{{ route('user.mail_templates') }}">
                                                    <span class="sub-item">{{ __('Mail Templates') }}</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>

                                {{-- Language Management Page --}}

                                    <li
                                        class="
                                        @if (request()->path() == 'user/languages') active
                                        @elseif(request()->is('user/language/*/edit')) active
                                        @elseif(request()->is('user/language/*/edit/keyword')) active @endif">
                                        <a href="{{ route('user.language.index') }}">

                                            <span class="sub-item">{{ __('Language') }}</span>
                                        </a>
                                    </li>

                                <li class="{{ request()->routeIs('user.plugins') ? 'active' : '' }}">
                                    <a href="{{ route('user.plugins') }}">
                                        <span class="sub-item">{{ __('Plugins') }}</span>
                                    </a>
                                </li>

                                <li class="{{ request()->routeIs('user.maintenance_mode') ? 'active' : '' }}">
                                    <a href="{{ route('user.maintenance_mode') }}">
                                        <span class="sub-item">{{ __('Maintenance Mode') }}</span>
                                    </a>
                                </li>
                                <li class="{{ request()->routeIs('user.cookie_alert') ? 'active' : '' }}">
                                    <a
                                        href="{{ route('user.cookie_alert', ['language' => $tenantDefaultLang->code]) }}">
                                        <span class="sub-item">{{ __('Cookie Alert') }}</span>
                                    </a>
                                </li>

                                <li
                                    class="@if (request()->path() == 'user/social') active
                            @elseif(request()->is('user/social/*')) active @endif">
                                    <a href="{{ route('user.social.index') }}">
                                        <span class="sub-item">{{ __('Social Medias') }}</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endif
                {{-- End Basic Setting --}}

                <li
                    class="nav-item
                    @if (request()->path() == 'user/package-list') active
                    @elseif(request()->is('user/package/checkout/*')) active
                    @elseif (request()->path() == 'user/payment-log') active @endif">
                    <a data-toggle="collapse" href="#membership">
                        <i class="fas fa-file-invoice-dollar"></i>
                        <p>{{ __('Membership') }}</p>
                        <span class="caret"></span>
                    </a>

                    <div class="collapse
               @if (request()->path() == 'user/package-list') show
                    @elseif(request()->is('user/package/checkout/*')) show
                    @elseif (request()->path() == 'user/payment-log')  show @endif"
                        id="membership">
                        <ul class="nav nav-collapse">


                            <li
                                class="
                                @if (request()->path() == 'user/payment-log') active @endif">
                                <a href="{{ route('user.payment-log.index') }}">

                                    <span class="sub-item">{{ __('Payment Logs') }}</span>
                                </a>
                            </li>

                            <li
                                class="
                                @if (request()->path() == 'user/package-list') active
                                @elseif(request()->is('user/package/checkout/*')) active @endif">
                                <a href="{{ route('user.plan.extend.index') }}">

                                    <span class="sub-item">{{ __('Buy Plan') }}</span>
                                </a>
                            </li>

                        </ul>
                    </div>
                </li>




            </ul>
        </div>
    </div>
</div>
