<div class="widget widget-form radius-md mb-30">
    <form action="{{ route('frontend.agents', getParam()) }}" method="GET">
        <h3 class="title mb-20">{{ $keywords['Find Team Member'] ?? __('Find Team Member') }}</h3>
        <div class="form-group mb-20">

            <input type="text" name="name" value="{{ request()->input('name') }}" class="form-control "
                placeholder="{{ $keywords['Team member name/username'] ?? __('Agent member name/username') }}">
        </div>
        <div class="form-group mb-20">
            <select class="nice-select" aria-label="#" id="type" name="type">
                <option value="" selected>{{ $keywords['Select Property Type'] ??__('Select Property Type') }}</option>

                <option value="residential" {{ request()->input('type') == 'residential' ? 'selected' : '' }}>
                    {{ $keywords['Residential'] ?? __('Residential') }} </option>

                <option value="commercial" {{ request()->input('type') == 'commercial' ? 'selected' : '' }}>
                    {{ $keywords['Commercial'] ?? __('Commercial') }} </option>


            </select>
        </div>
        <div class="form-group mb-20">

            <input type="text" name="location" class="form-control  " value="{{ request()->input('location') }}"
                placeholder="{{ $keywords['Enter location'] ?? __('Enter location') }}">
        </div>
        <button type="submit" class="btn btn-md btn-primary w-100">{{ $keywords['Search'] ?? __('Search') }}</button>
    </form>
</div>
