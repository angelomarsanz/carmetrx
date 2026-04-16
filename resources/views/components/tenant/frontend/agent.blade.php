<div class="agent-box radius-md mb-30">
    <div class="agent-img">
        <figure>
            <a href="#" class="lazy-container ratio ratio-1-2">
                <img class="lazyload"
                    data-src="{{ asset($agent?->image) }}">
            </a>
        </figure>
        

    </div>
    <div class="agent-details text-center">

        <span class="color-primary font-sm">
            {{ $agent->propertyCount() }}
            {{ $keywords['Properties'] ??  __('Properties') }}</span> |

        <span class="color-primary font-sm">
            {{ $agent->projectCount() }}
            {{ $keywords['Projects'] ??  __('Projects') }}</span>


        <h4 class="agent-title"><a
                href="{{ route('frontend.agent.details', [getParam(), 'agentusername' => $agent->username]) }}">
                {{ $agent->agentInfo?->full_name }}
            </a>
        </h4>
        <ul class="agent-info list-unstyled p-0">

            @if ($agent->show_phone_number == 1)
                @if (!is_null($agent->phone))
                    <li class="icon-start ">
                        <a href="tel:{{ $agent->phone }}"> <i class="fal fa-phone-plus"></i>
                            {{ $agent?->phone }}</a>
                    </li>
                @endif
            @endif

            @if ($agent->show_email_addresss == 1)
                <li class="icon-start font-sm">
                    <a href="mailto:{{ $agent?->email }}"> <i class="fal fa-envelope"></i>
                        {{ $agent?->email }}</a>
                </li>
            @endif
        </ul>
        <a href="{{ route('frontend.agent.details', [getParam(), 'agentusername' => $agent->username]) }}"
            class="btn-text">{{ $keywords['View Profile'] ??  __('View Profile') }}</a>
    </div>
</div>
