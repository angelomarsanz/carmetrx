<!-- Header Start -->
<header class="header-area header-1 @if (!request()->routeIs('frontend.user.index')) header-static @endif" data-aos="slide-down">

    <div class="mobile-menu">
        <div class="container">
            <div class="mobile-menu-wrapper"></div>
        </div>
    </div>

    <div class="main-responsive-nav">
        <div class="container">
            <div class="logo">

                @if (!empty($basicInfo->logo))
                    <a href="{{ route('frontend.user.index', getParam()) }}">
                        <img src="{{ asset(  $basicInfo->logo) }}">
                    </a>
                @endif
            </div>
            <button class="menu-toggler" type="button">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </div>
    <div class="main-navbar">
        <div class="container">
            <nav class="navbar navbar-expand-lg">
      
                @if (!empty($basicInfo->logo))
                    <a href="{{ route('frontend.user.index', getParam()) }}" class="navbar-brand">
                        <img src="{{ asset(  $basicInfo->logo) }}">
                    </a>
                @endif
                <div class="collapse navbar-collapse">
                    @php $menuDatas =  $menuInfos; @endphp

                    <ul id="mainMenu" class="navbar-nav mobile-item mx-auto">

                        @foreach ($menuDatas as $menuData)
                            @php $href = getUserHref($menuData); @endphp
                            @if (!property_exists($menuData, 'children'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ $href }}">{{ $menuData->text }}</a>
                                </li>
                            @else
                                <li class="nav-item">
                                    <a class="nav-link toggle" href="{{ $href }}">{{ $menuData->text }}<i
                                            class="fal fa-plus"></i></a>
                                    <ul class="menu-dropdown">
                                        @php $childMenuDatas = $menuData->children; @endphp
                                        @foreach ($childMenuDatas as $childMenuData)
                                            @php $child_href = getUserHref($childMenuData); @endphp
                                            <li class="nav-item">
                                                <a class="nav-link"
                                                    href="{{ $child_href }}">{{ $childMenuData->text }}</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </li>
                            @endif
                        @endforeach


                    </ul>
                </div>
                <div class="more-option mobile-item">
                    @if (!empty($permissions) && in_array('Additional Language', $permissions) )
                        <div class="item">
                            <div class="language">
                                <form action="{{ route('frontend.change_language', getParam()) }}" method="GET">
                                    <select class="nice-select" name="lang_code" onchange="this.form.submit()">
                                        @foreach ($allLanguageInfos as $languageInfo)
                                            <option value="{{ $languageInfo->code }}"
                                                {{ $languageInfo->code == $currentLanguageInfo->code ? 'selected' : '' }}>
                                                {{ $languageInfo->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>


                            </div>
                        </div>
                    @endif
                    @if (!empty($permissions) && in_array('User', $permissions))
                        <div class="item">
                            <div class="dropdown">
                                <button class="btn btn-primary btn-sm dropdown-toggle" type="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    @if (!Auth::guard('customer')->check())
                                        {{ $keywords['Customer'] ?? __('Customer') }}
                                    @else
                                        {{ Auth::guard('customer')->user()->username }}
                                    @endif
                                </button>
                                <ul class="dropdown-menu radius-0">
                                    @if (!Auth::guard('customer')->check())
                                        <li><a class="dropdown-item"
                                                href="{{ route('frontend.user.login', getParam()) }}">{{ $keywords['Login'] ?? __('Login') }}</a>
                                        </li>
                                        <li><a class="dropdown-item"
                                                href="{{ route('frontend.user.signup', getParam()) }}">{{ $keywords['Signup'] ?? __('Signup') }}</a>
                                        </li>
                                    @else
                                        <li><a class="dropdown-item"
                                                href="{{ route('frontend.user.dashboard', getParam()) }}">{{ $keywords['Dashboard'] ?? __('Dashboard') }}</a>
                                        </li>
                                        <li><a class="dropdown-item"
                                                href="{{ route('frontend.user.logout', getParam()) }}">{{ $keywords['Logout'] ?? __('Logout') }}</a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>
            </nav>
        </div>
    </div>
</header>
<!-- Header End -->
