@if ($subsc)
    <h4>{{ __('Hello Subscriber') }}, </h4>
@endif


<p> {{ replaceBaseUrl($text) }}</p>

@if ($subsc)
    <p class="mb-0">{{ __('Best Regards') }},</p>
    <p>{{ $bs->website_title }}</p>
@endif
