<div class="col-lg-3">
    <div class="sidebar-widget-area mb-40">
        <div class="widget radius-md">
            <ul class="links">
                <li>
                    <a href="{{ route('frontend.user.dashboard', getParam()) }}"
                        class="{{ request()->routeIs('frontend.user.dashboard', getParam()) ? 'active' : '' }}">
                        {{ $keywords['dashboard'] }}
                    </a>
                </li>

                <li>
                    <a class=" d-block  @if (request()->routeIs('frontend.user.property.wishlist', getParam()) ||
                            request()->routeIs('frontend.user.project.wishlist', getParam())) active @endif" data-bs-toggle="collapse"
                        href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
                        {{ $keywords['Wishlists'] ?? __('Wishlists') }}
                        <span class="d-inline-block ms-1"><i class="fas fa-caret-down"></i></span>
                    </a>
                </li>
                <div class="collapse @if (request()->routeIs('frontend.user.property.wishlist', getParam()) ||
                        request()->routeIs('frontend.user.project.wishlist', getParam())) show @endif" id="collapseExample">
                    <div class="card card-body py-0 mb-4">
                        <li>
                            <a href="{{ route('frontend.user.property.wishlist', getParam()) }}"
                                class="{{ request()->routeIs('frontend.user.property.wishlist', getParam()) ? 'active' : '' }}">
                                {{ $keywords['Properties'] ?? __('Properties') }}
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('frontend.user.project.wishlist', getParam()) }}"
                                class="{{ request()->routeIs('frontend.user.project.wishlist', getParam()) ? 'active' : '' }}">
                                {{ $keywords['Projects'] ?? __('Projects') }}
                            </a>
                        </li>
                    </div>
                </div>


                <li>
                    <a href="{{ route('frontend.user.edit_profile', getParam()) }}"
                        class="{{ request()->routeIs('frontend.user.edit_profile', getParam()) ? 'active' : '' }}">
                        {{ $keywords['Edit Profile'] }}
                    </a>
                </li>

                @php $authUser = Auth::guard('customer')->user(); @endphp

                @if (!is_null($authUser->password))
                    <li>
                        <a href="{{ route('frontend.user.change_password', getParam()) }}"
                            class="{{ request()->routeIs('frontend.user.change_password', getParam()) ? 'active' : '' }}">
                            {{ $keywords['Change Password'] }}
                        </a>
                    </li>
                @endif

                <li>
                    <a href="{{ route('frontend.user.logout', getParam()) }}">
                        {{ $keywords['Logout'] }}
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
