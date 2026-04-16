<div class="col-12">

    @if (count($terms) > 1)
        <div class="nav-tabs-navigation text-center" data-aos="fade-up">
            <ul class="nav nav-tabs">
                @foreach ($terms as $term)
                    <li class="nav-item">
                        <button class="nav-link {{ $loop->first ? 'active' : '' }}" data-bs-toggle="tab"
                            data-bs-target="#{{ __("$term") }}" type="button">{{ __("$term") }}</button>
                    </li>
                @endforeach

            </ul>
        </div>
    @endif
    <div class="tab-content">
        @forelse ($terms as $term)
            <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="{{ __("$term") }}">
                <div class="row justify-content-center">

                    @php
                        $termPackages = $packages->where('term', strtolower($term));
                    @endphp
                    @foreach ($termPackages as $package)
                        @php
                            $pFeatures = json_decode($package->features);
                        @endphp
                        <div class="col-md-6 col-lg-4 item">
                            <div class="card mb-30 {{ $package->recommended == '1' ? 'active' : '' }}"
                                data-aos="fade-up" data-aos-delay="100">
                                <div class="d-flex align-items-center">
                                    <div class="icon"><i class="{{ $package->icon }}"></i></div>
                                    <div class="label">
                                        <h4>{{ $package->title }}</h4>

                                        @if ($package->recommended == '1')
                                            <span>{{ __('Recommended') }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="d-flex align-items-center py-3">
                                    <span class="price">
                                        {{ $package->price != 0 && $be->base_currency_symbol_position == 'left' ? $be->base_currency_symbol : '' }}
                                        {{ $package->price == 0 ? 'Free' : espanishFormatPrice($package->price) }}
                                        {{ $package->price != 0 && $be->base_currency_symbol_position == 'right' ? $be->base_currency_symbol : '' }}
                                    </span>
                                    <span class="period">/ {{ __("$package->term") }}</span>
                                </div>
                                <h5 class="mb-3 m-0">{{ __('Whats Included') }}</h5>
                                <ul class="pricing-list list-unstyled p-0">

                                    @foreach ($allPfeatures as $feature)
                                        <li
                                            class="{{ is_array($pFeatures) && in_array($feature, $pFeatures) ? '' : 'disabled' }}">
                                            @if ($feature == 'Custom Domain')
                                                <i
                                                    class="{{ is_array($pFeatures) && in_array($feature, $pFeatures) ? 'fal fa-check bg-success text-white' : 'fal fa-times  bg-danger text-white' }}"></i>
                                                {{ $package->custom_domain_limit === 999999 ? __('Unlimited') : $package->custom_domain_limit . ' ' }}
                                                {{ $package->custom_domain_limit === 1 ? __('Custom Domain') : __('Custom Domains') }}
                                            @elseif ($feature == 'Subdomain')
                                                <i
                                                    class="{{ is_array($pFeatures) && in_array($feature, $pFeatures) ? 'fal fa-check bg-success text-white' : 'fal fa-times  bg-danger text-white' }}"></i>
                                                {{ $package->subdomain_limit === 999999 ? __('Unlimited') : $package->subdomain_limit . ' ' }}
                                                {{ $package->subdomain_limit === 1 ? __('Subomain') : __('Subomains') }}
                                            @elseif ($feature == 'Additional Language')
                                                <i
                                                    class="{{ is_array($pFeatures) && in_array($feature, $pFeatures) ? 'fal fa-check bg-success text-white' : 'fal fa-times  bg-danger text-white' }}"></i>

                                                @if (is_array($pFeatures) && in_array($feature, $pFeatures))
                                                    {{ $package->number_of_language === 999999 ? __('Unlimited') : $package->number_of_language . ' ' }}
                                                    {{ $package->number_of_language === 1 ? __('Additional Language') : __('Additional Languages') }}
                                                @else
                                                    {{ __('Additional Language') }}
                                                @endif
                                            @elseif ($feature == 'Additional Page')
                                                <i
                                                    class="{{ is_array($pFeatures) && in_array($feature, $pFeatures) ? 'fal fa-check bg-success text-white' : 'fal fa-times  bg-danger text-white' }}"></i>

                                                @if (is_array($pFeatures) && in_array($feature, $pFeatures))
                                                    {{ $package->number_of_additional_page === 999999 ? __('Unlimited') : $package->number_of_additional_page . ' ' }}
                                                    {{ $package->number_of_additional_page === 1 ? __('Additional Page') : __('Additional Pages') }}
                                                @else
                                                    {{ __('Additional Page') }}
                                                @endif
                                            @elseif ($feature == 'Agent')
                                                <i
                                                    class="{{ is_array($pFeatures) && in_array($feature, $pFeatures) ? 'fal fa-check bg-success text-white' : 'fal fa-times  bg-danger text-white' }}"></i>

                                                @if (is_array($pFeatures) && in_array($feature, $pFeatures))
                                                    {{ $package->number_of_agent === 999999 ? __('Unlimited') : $package->number_of_agent . ' ' }}
                                                    {{ $package->number_of_agent === 1 ? __('Agent') : __('Agents') }}
                                                @else
                                                    {{ __('Agent') }}
                                                @endif
                                            @elseif ($feature == 'Property Management')
                                                <i
                                                    class="{{ is_array($pFeatures) && in_array($feature, $pFeatures) ? 'fal fa-check bg-success text-white' : 'fal fa-times bg-danger text-white' }}"></i>
                                                {{ __('Property Management') }}
                                                @if (is_array($pFeatures) && in_array($feature, $pFeatures))
                                                    <ul class="list-unstyled ms-4 small py-3">
                                                        <li>
                                                            <i
                                                                class="{{ is_array($pFeatures) && in_array($feature, $pFeatures) ? 'fal fa-check bg-success text-white' : 'fal fa-times bg-danger text-white' }}"></i>

                                                            {{ $package->number_of_property === 999999 ? __('Unlimited') : $package->number_of_property . ' ' }}
                                                            {{ $package->number_of_property === 1 ? __('Property') : __('Properties') }}

                                                        </li>
                                                        <li>
                                                            <i
                                                                class="{{ is_array($pFeatures) && in_array($feature, $pFeatures) ? 'fal fa-check bg-success text-white' : 'fal fa-times bg-danger text-white' }}"></i>

                                                            {{ $package->number_of_property_featured === 999999 ? __('Unlimited') : $package->number_of_property_featured . ' ' }}
                                                            {{ $package->number_of_property_featured === 1 ? __('Featured Property') : __('Featured Properties') }}
                                                        </li>
                                                        <li>
                                                            <i
                                                                class="{{ is_array($pFeatures) && in_array($feature, $pFeatures) ? 'fal fa-check bg-success text-white' : 'fal fa-times bg-danger text-white' }}"></i>
                                                            {{ $package->number_of_property_gallery_images === 999999 ? __('Unlimited') : $package->number_of_property_gallery_images . ' ' }}
                                                            {{ $package->number_of_property_gallery_images === 1 ? __('Gallery Image Per Property') : __('Gallery Images Per Property') }}
                                                        </li>
                                                        <li>
                                                            <i
                                                                class="{{ is_array($pFeatures) && in_array($feature, $pFeatures) ? 'fal fa-check bg-success text-white' : 'fal fa-times bg-danger text-white' }}"></i>
                                                            {{ $package->number_of_property_adittionl_specifications === 999999 ? __('Unlimited') : $package->number_of_property_adittionl_specifications . ' ' }}
                                                            {{ $package->number_of_property_adittionl_specifications === 1 ? __('Additional Specification Per Property') : __('Additional Specifications Per Property') }}
                                                        </li>

                                                    </ul>
                                                @endif
                                            @elseif ($feature == 'Project Management')
                                                <i
                                                    class="{{ is_array($pFeatures) && in_array($feature, $pFeatures) ? 'fal fa-check bg-success text-white' : 'fal fa-times bg-danger text-white' }}"></i>
                                                {{ __('Project Management') }}
                                                @if (is_array($pFeatures) && in_array($feature, $pFeatures))
                                                    <ul class="list-unstyled ms-4 py-3 small">
                                                        <li>
                                                            <i
                                                                class="{{ is_array($pFeatures) && in_array($feature, $pFeatures) ? 'fal fa-check bg-success text-white' : 'fal fa-times' }}"></i>

                                                            {{ $package->number_of_projects === 999999 ? __('Unlimited') : $package->number_of_projects . ' ' }}
                                                            {{ $package->number_of_projects === 1 ? __('Project') : __('Projects') }}
                                                        </li>
                                                        <li>
                                                            <i
                                                                class="{{ is_array($pFeatures) && in_array($feature, $pFeatures) ? 'fal fa-check bg-success text-white' : 'fal fa-times bg-danger text-white' }}"></i>
                                                            {{ $package->number_of_project_types === 999999 ? __('Unlimited') : $package->number_of_project_types . ' ' }}
                                                            {{ $package->number_of_project_types === 1 ? __('Project Types Per Project') : __('Project Types Per Project') }}
                                                        </li>
                                                        <li>
                                                            <i
                                                                class="{{ is_array($pFeatures) && in_array($feature, $pFeatures) ? 'fal fa-check bg-success text-white' : 'fal fa-times bg-danger text-white' }}"></i>
                                                            {{ $package->number_of_project_gallery_images === 999999 ? __('Unlimited') : $package->number_of_project_gallery_images . ' ' }}
                                                            {{ $package->number_of_project_gallery_images === 1 ? __('Gallery Image Per Project') : __('Gallery Images Per Project') }}
                                                        </li>
                                                        <li>
                                                            <i
                                                                class="{{ is_array($pFeatures) && in_array($feature, $pFeatures) ? 'fal fa-check bg-success text-white' : 'fal fa-times bg-danger text-white' }}"></i>
                                                            {{ $package->number_of_project_additionl_specifications === 999999 ? __('Unlimited') : $package->number_of_project_additionl_specifications . ' ' }}
                                                            {{ $package->number_of_project_additionl_specifications === 1 ? __('Additional Specification Per Project') : __('Additional Specifications Per Project') }}
                                                        </li>

                                                    </ul>
                                                @endif
                                            @elseif ($feature == 'Blog')
                                                <i
                                                    class="{{ is_array($pFeatures) && in_array($feature, $pFeatures) ? 'fal fa-check bg-success text-white' : 'fal fa-times  bg-danger text-white' }}"></i>
                                                @if (is_array($pFeatures) && in_array($feature, $pFeatures))
                                                    {{ $package->number_of_blog_post === 999999 ? __('Unlimited') : $package->number_of_blog_post . ' ' }}
                                                    {{ $package->number_of_blog_post === 1 ? __('Blog Post') : __('Blog Posts') }}
                                                @else
                                                    {{ __('Blog Post') }}
                                                @endif
                                            @else
                                                <i
                                                    class="{{ is_array($pFeatures) && in_array($feature, $pFeatures) ? 'fal fa-check bg-success text-white' : 'fal fa-times bg-danger text-white' }}"></i>
                                                {{ __("$feature") }}
                                            @endif
                                        </li>
                                    @endforeach

                                </ul>

                                <div class="btn-groups">

                                    @if ($package->is_trial === '1' && $package->price != 0)
                                        <a href="{{ route('front.register.view', ['status' => 'trial', 'id' => $package->id]) }}"
                                            itle="Trial" target="_self"
                                            class="btn btn-lg btn-primary no-animation">{{ __('Trial') }}</a>
                                    @endif
                                    @if ($package->price == 0)
                                        <a href="{{ route('front.register.view', ['status' => 'regular', 'id' => $package->id]) }}"
                                            class="btn btn-lg btn-primary no-animation">{{ __('Signup') }}</a>
                                    @else
                                        <a href="{{ route('front.register.view', ['status' => 'regular', 'id' => $package->id]) }}"
                                            title="Purchase" target="_self"
                                            class="btn btn-lg btn-outline no-animation">{{ __('Purchase') }}</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        @empty
            <h4 class="text-center"> {{ __('NO PACKAGE FOUND') }}</h4>
        @endforelse

    </div>
</div>
