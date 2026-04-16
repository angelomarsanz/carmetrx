
@if (!is_null($tenantDefaultLang))
    @if (!empty($tenantLanguages))

        <div class="input-group mb-2">
            <div class="input-group-prepend">
                <div class="input-group-text text-secondary "><i class="fas fa-language"></i></div>
            </div>
            <select name="userLanguage" class="form-control"
                onchange="window.location='{{ url()->current() . '?language=' }}'+this.value">
                <option value="" selected disabled>{{ __('Select a Language') }}
                </option>
                @foreach ($tenantLanguages as $lang)
                    <option value="{{ $lang->code }}"
                        {{ $lang->code == request()->input('language') ? 'selected' : '' }}>
                        {{ $lang->name }}</option>
                @endforeach
            </select>
        </div>
    @endif
@endif
