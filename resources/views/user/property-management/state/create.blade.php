<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">{{ __('Add State') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form id="ajaxForm" class="modal-form create"
                    action="{{ route('user.property_management.store_state') }}" method="post"
                    enctype="multipart/form-data">
                    @csrf
                    @if ($userBs->property_country_status == 1)
                        <div class="form-group">
                            <label for="">{{ __('Country') }} <span class="text-danger">{{ '*' }}</span> </label>
                            <select name="country" class="form-control">
                                <option selected disabled>{{ __('Select a Country') }}
                                </option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}">{{ $country?->name }}
                                    </option>
                                @endforeach
                            </select>
                            <p id="errcountry" class="mt-2 mb-0 text-danger em"></p>
                        </div>
                    @endif

                    @foreach ($tenantFrontLangs as $lang)
                        <div class="form-group {{ $lang->direction == 1 ? 'rtl text-right' : '' }}">
                            <label for="">{{ __('State Name')  }}
                                ({{ $lang->name }}) <span class="text-danger">{{ '*' }}</span>
                            </label>
                            <input type="text" class="form-control" name="{{ $lang->code }}_name"
                                placeholder="{{ __('Enter state name for') . ' ' . $lang->name . ' ' . __('language') }}">
                            <p id="err{{ $lang->code }}_name" class="mt-2 mb-0 text-danger em"></p>
                        </div>
                    @endforeach

                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                    {{ __('Close') }}
                </button>
                <button id="submitBtn" type="button" class="btn btn-primary btn-sm">
                    {{ __('Save') }}
                </button>
            </div>
        </div>
    </div>
</div>
