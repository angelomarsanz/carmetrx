<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">{{ __('Add City') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form id="ajaxForm" class="modal-form create"
                    action="{{ route('user.property_management.store_city') }}" method="post"
                    enctype="multipart/form-data">
                    @csrf

                    @if ($userBs->property_country_status == 1)

                        <div class="form-group">
                            <label for="">{{ __('Country') }} <span
                                    class="text-danger">{{ '*' }}</span></label>
                            <select name="country" class="form-control" id="country">
                                <option selected disabled>{{ __('Select a Country') }}
                                </option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                                @endforeach
                            </select>
                            <p id="errcountry" class="mt-2 mb-0 text-danger em"></p>
                        </div>
                    @endif
                    @if ($userBs->property_country_status == 1 && $userBs->property_state_status == 1)
                        <div class="form-group" id="state">
                            <label for="">{{ __('State') }} <span
                                    class="text-danger">{{ '*' }}</span></label>
                            <select name="state" class="form-control">
                                <option selected disabled>{{ __('Select a State') }}
                                </option>

                            </select>
                            <p id="errstate" class="mt-2 mb-0 text-danger em"></p>
                        </div>
                    @elseif($userBs->property_country_status == 0 && $userBs->property_state_status == 1)
                        <div class="form-group">
                            <label for="">{{ __('State') }} <span
                                    class="text-danger">{{ '*' }}</span></label>
                            <select name="state" class="form-control">
                                <option selected disabled>{{ __('Select a State') }}
                                </option>
                                @foreach ($states as $state)
                                    <option value="{{ $state->id }}">{{ $state->name }}</option>
                                @endforeach
                            </select>
                            <p id="errstate" class="mt-2 mb-0 text-danger em"></p>
                        </div>
                    @endif
                    <div class="form-group">
                        <label for="">{{ __('Image') }} <span
                                class="text-danger">{{ '*' }}</span></label>
                        <br>
                        <div class="thumb-preview">
                            <img src="{{ asset('assets/img/noimage.jpg') }}" alt="..." class="uploaded-img">
                        </div>

                        <div class="mt-3">
                            <div role="button" class="btn btn-primary btn-sm upload-btn">
                                {{ __('Choose Image') }}
                                <input type="file" class="img-input" name="image">
                            </div>
                        </div>

                        <p id="errimage" class=" mb-0 text-danger em"></p>
                    </div>
                    @foreach ($tenantFrontLangs as $lang)
                        <div class="form-group {{ $lang->direction == 1 ? 'rtl text-right' : '' }}">
                            <label for="">{{ __('Name') }}
                                ({{ $lang->name }})
                                <span class="text-danger">{{ '*' }}</span>
                            </label>
                            <input type="text" class="form-control" name="{{ $lang->code }}_name"
                                placeholder="{{ __('Enter city name for') . ' ' . $lang->name . ' ' . __('language') }}">
                            <p id="err{{ $lang->code }}_name" class="mt-2 mb-0 text-danger em"></p>
                        </div>
                    @endforeach
                    <div class="form-group">
                        <label for="">{{ __('Status') }} <span
                                class="text-danger">{{ '*' }}</span></label>
                        <select name="status" class="form-control">
                            <option value="" selected disabled>
                                {{ __('Select Status') }}</option>
                            <option value="1">{{ __('Active') }}</option>
                            <option value="0">{{ __('Deactive') }}</option>
                        </select>
                        <p id="errstatus" class="mt-2 mb-0 text-danger em"></p>
                    </div>

                    <div class="form-group">
                        <label for="">{{ __('Serial Number') }} <span
                                class="text-danger">{{ '*' }}</span> </label>
                        <input type="number" class="form-control" name="serial_number"
                            placeholder="{{ __('Enter serial number') }}">
                        <p id="errserial_number" class="mt-2 mb-0 text-danger em"></p>
                        <p class="text-warning mt-2 mb-0">
                            <small>{{ __('The higher the serial number will be shown') }}</small>
                        </p>
                    </div>
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
