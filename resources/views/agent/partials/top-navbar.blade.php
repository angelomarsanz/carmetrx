
<div class="main-header">
    <div class="logo-header"
        data-background-color="{{ Session::get('agent_theme_version') == 'light' ? 'white' : 'dark2' }}">

        @if (!empty($settings->logo))
            <a href="{{ route('frontend.user.index', getParam()) }}" class="logo" target="_blank">
                <img src="{{ asset($settings->logo) }}" alt="logo" class="navbar-brand" width="120">
            </a>
        @endif

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


    <nav class="navbar navbar-header navbar-expand-lg"
        data-background-color="{{ Session::get('agent_theme_version') == 'light' ? 'white2' : 'dark' }}">
        <div class="container-fluid">
            <ul class="navbar-nav topbar-nav ml-md-auto align-items-center">
                <li>
                    @if (!empty($adminLangs))
                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <div class="input-group-text text-secondary "><i class="fas fa-language"></i></div>
                            </div>

                            <select name="adminLanguage" class="form-control language-select"
                                onchange="window.location='{{ route('agent.change_language', getParam()) . '?language=' }}'+this.value">

                                <option disabled> {{ __('Select a Language') }}
                                </option>
                                @foreach ($adminLangs as $lang)
                                    <option value="{{ $lang->code }}"
                                        {{ $lang->code == $adminCurrentLang->code ? 'selected' : '' }}>
                                        {{ $lang->name }}
                                    </option>
                                @endforeach
                            </select>

                        </div>
                    @endif
                </li>

                <li>
                    <a class="btn btn-primary btn-sm btn-round ml-3" target="_blank"
                        href="{{ route('frontend.agent.details', [getParam(), 'agentusername' => Auth::guard('agent')->user()->username]) }}"
                        title="View Profile">
                        <i class="fas fa-eye"></i>
                    </a>
                </li>
                <form action="{{ route('agent.change_theme', getParam()) }}" class="form-inline mr-3" method="POST">

                    @csrf
                    <div class="form-group">
                        <div class="selectgroup selectgroup-secondary selectgroup-pills">
                            <label class="selectgroup-item">
                                <input type="radio" name="agent_theme_version" value="light"
                                    class="selectgroup-input"
                                    {{ Session::get('agent_theme_version') == 'light' ? 'checked' : '' }}
                                    onchange="this.form.submit()">
                                <span class="selectgroup-button selectgroup-button-icon"><i
                                        class="fa fa-sun"></i></span>
                            </label>

                            <label class="selectgroup-item">
                                <input type="radio" name="agent_theme_version" value="dark"
                                    class="selectgroup-input"
                                    {{ Session::get('agent_theme_version') == 'dark' ? 'checked' : '' }}
                                    onchange="this.form.submit()">
                                <span class="selectgroup-button selectgroup-button-icon"><i
                                        class="fa fa-moon"></i></span>
                            </label>
                        </div>
                    </div>
                </form>


                <li class="nav-item dropdown hidden-caret">
                    <a class="dropdown-toggle profile-pic" data-toggle="dropdown" href="#" aria-expanded="false">
                        <div class="avatar-sm">
                            @if (Auth::guard('agent')->user()->image != null)
                                <img src="{{ asset(Auth::guard('agent')->user()->image) }}" alt="Agent Image"
                                    class="avatar-img rounded-circle">
                            @endif
                        </div>
                    </a>

                    <ul class="dropdown-menu dropdown-user animated fadeIn">
                        <div class="dropdown-user-scroll scrollbar-outer">
                            <li>
                                <div class="user-box">
                                    <div class="avatar-lg">
                                        @if (Auth::guard('agent')->user()->image != null)
                                            <img src="{{ asset(Auth::guard('agent')->user()->image) }}"
                                                alt="Agent Image" class="avatar-img rounded-circle">
                                        @endif
                                    </div>

                                    <div class="u-text">
                                        <h4>
                                            {{ Auth::guard('agent')->user()->username }}
                                        </h4>
                                        <p class="text-muted">{{ Auth::guard('agent')->user()->email }}</p>
                                    </div>
                                </div>
                            </li>

                            <li>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{ route('agent.edit.profile', getParam()) }}">
                                    {{ __('Edit Profile') }}
                                </a>

                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{ route('agent.change_password', getParam()) }}">
                                    {{ __('Change Password') }}
                                </a>

                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{ route('agent.logout', getParam()) }}">
                                    {{ __('Logout') }}
                                </a>
                            </li>
                        </div>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</div>
