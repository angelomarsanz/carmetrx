@extends('front.layout')

@section('pagename')
    - {{ __('Listings') }}
@endsection

@section('meta-description', !empty($seo) ? $seo->profiles_meta_description : '')
@section('meta-keywords', !empty($seo) ? $seo->profiles_meta_keywords : '')



@section('content')
    @includeIf('front.partials.breadcrumb', [
        'title' => __('Listings'),
        'link' => __('Listings'),
    ])
    <section class="user-profile-area ptb-120">
        <div class="container">
            <div class="row">
                <div class="col-lg-4  mx-auto">
                    <aside class="sidebar-widget-area mb-40">
                        <div class="widget widget-search mb-50 border-0 p-0">

                            <form class="search-form" action="{{ route('front.user.view') }}" method="get">
                                <input type="search" name="search" class="search-input"
                                    placeholder="{{ __('Search with name or username') }}..."
                                    value="{{ request()->input('search') }}">
                                <button class="btn-search" type="submit">
                                    <i class="far fa-search"></i>
                                </button>
                            </form>
                        </div>
                    </aside>
                </div>
            </div>
            <div class="row">
                @if (count($users) == 0)
                    <div class=" text-center py-5 d-block w-100">
                        <h3>{{ __('NO LISTINGS FOUND') }}</h3>
                    </div>
                @else
                    @foreach ($users as $user)
                        <div class="col-lg-4 col-sm-6" data-aos="fade-up">
                            <div class="card mb-30 text-center">
                                <div class="icon">


                                    <img class="lazy" src="{{ asset($user->photo) }}" alt="user">


                                </div>
                                <div class="card-content">
                                    <h4 class="card-title">{{ $user->first_name . ' ' . $user->last_name }}</h4>
                                    <div class="social-link d-flex justify-content-center">
                                        @foreach ($user->social_media as $social)
                                            <a href="{{ $social->url }}" target="_blank"><i
                                                    class="{{ $social->icon }}"></i></a>
                                        @endforeach

                                    </div>
                                    <div class="btn-groups">
                                        @php
                                            if (!empty($user)) {
                                                $currentPackage = App\Http\Helpers\UserPermissionHelper::userPackage(
                                                    $user->id,
                                                );
                                                $preferences = App\Models\User\UserPermission::where([
                                                    ['user_id', $user->id],
                                                    ['package_id', $currentPackage->package_id],
                                                ])->first();
                                                $permissions = isset($preferences)
                                                    ? json_decode($preferences->permissions, true)
                                                    : [];
                                            }
                                        @endphp
                                        <a href="{{ detailsUrl($user) }}" class="btn btn-sm btn-outline"
                                            title="View Profile" target="_self">{{ __('View Profile') }}</a>
                                        @guest

                                            <a href="{{ route('user.follow', ['id' => $user->id]) }}"
                                                class="btn btn-sm btn-primary " title="Follow Us" target="_self">
                                                {{ __('Follow Us') }}
                                            </a>

                                        @endguest
                                        @if (Auth::guard('web')->check() && Auth::guard('web')->user()->id != $user->id)
                                            @if (App\Models\User\Follower::where('follower_id', Auth::guard('web')->user()->id)->where('following_id', $user->id)->count() > 0)
                                                <a href="{{ route('user.unfollow', $user->id) }}"
                                                    class="btn btn-sm btn-primary"> {{ __('Unfollow') }}
                                                </a>
                                            @else
                                                <a href="{{ route('user.follow', ['id' => $user->id]) }}"
                                                    class="btn btn-sm btn-primary"> {{ __('Follow Us') }}
                                            @endif
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif


            </div>
            <div class="col-12">
                {{ $users->appends(['search' => request()->input('search'), 'designation' => request()->input('designation'), 'location' => request()->input('location')])->links() }}
            </div>


            </nav>
        </div>
    </section>
@endsection
