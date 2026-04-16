<div {{ $attributes }}>

    <a href="{{ route('frontend.project.details', [getParam(), 'slug' => $project->slug]) }}">
        <div class="card mb-30 product-default">
            <div class="card-img">
                <div class="lazy-container ratio ratio-1-3">
                    <img class="lazyload"
                        data-src="{{ asset('assets/img/project/featured/' . $project->featured_image) }}">
                </div>
                <span class="label">
                    @if ($project->complete_status == 0)
                        {{ $keywords['Under Construction'] ?? __('Under Construction') }}
                    @elseif($project->complete_status == 1)
                        {{ $keywords['Complete'] ?? __('Complete') }}
                    @endif
                </span>
            </div>
            <div class="card-text text-center p-3">
                <a href="{{ route('frontend.project.details', [getParam(), 'slug' => $project->slug]) }}">
                    <h3 class="card-title product-title color-white mb-1">
                        {{ $project->title }}
                    </h3>
                </a>

                <span class="location icon-start"><i class="fal fa-map-marker-alt"></i>{{ $project->address }}</span>
                <br>
                <span class="price">
                    {{ tenantCurrencySymbol($tenant->id, $project->min_price) }}
                    {{ !empty($project->max_price) ? ' - ' . tenantCurrencySymbol($tenant->id, $project->max_price) : '' }}

                </span>


                @if ($project->agent)
                    <a class="color-medium"
                        href="{{ route('frontend.agent.details', [getParam(), 'agentusername' => $project->agent->username]) }}"
                        target="_self">
                        <div class="user rounded-pill mt-10">
                            <div class="user-img lazy-container ratio ratio-1-1 rounded-pill">
                                <img class="lazyload" data-src="{{ asset($project->agent->image) }}">
                            </div>
                            <div class="user-info">
                                <span>{{ $project->agent->username }}</span>
                            </div>
                        </div>
                    </a>
                @elseif($project->agent_id == 0)
                    <a class="color-medium" href="{{ route('frontend.tenant.details', [getParam()]) }}"
                        target="_self">
                        <div class="user rounded-pill mt-10">
                            <div class="user-img lazy-container ratio ratio-1-1 rounded-pill">
                                <img class=" lazyload" data-src="{{ asset($project->user->photo) }}">
                            </div>
                            <div class="user-info">
                                <span>{{ $project->user->username }}</span>
                            </div>
                        </div>
                    </a>
                @endif
            </div>
            @if (!empty($permissions) && in_array('User', $permissions))
                @if (Auth::guard('customer')->check())
                    @php
                        $customer_id = Auth::guard('customer')->user()->id;
                        $checkWishList = checkProjectWishList($project->id, $customer_id);
                    @endphp
                @else
                    @php
                        $checkWishList = false;
                    @endphp
                @endif
                <a href="{{ $checkWishList == false ? route('frontend.user.project.addto.wishlist', [getParam(), 'project' => $project->id]) : route('frontend.user.project.remove.wishlist', [getParam(), 'project' => $project->id]) }}"
                    class="btn-wishlist {{ $checkWishList == false ? '' : 'wishlist-active' }}" data-tooltip="tooltip"
                    data-bs-placement="top"
                    title="{{ $checkWishList == false ? $keywords['Add to Wishlist'] : $keywords['Saved'] }}">
                    <i class="fal fa-heart"></i>
                </a>
            @endif
        </div>
    </a>
</div>
