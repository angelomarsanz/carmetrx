<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">
                    {{ __('Add Property Amenity') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form id="ajaxForm" class="modal-form create"
                    action="{{ route('user.property_management.store_amenity') }}" method="post">
                    @csrf
                    <div class="form-group">
                        <label for="">{{ __('Icon')   }}  <span class="text-danger">{{ '*' }}</span></label>
                        <div class="btn-group d-block">
                            <button type="button" class="btn btn-primary iconpicker-component"><i
                                    class="{{ $counterInformation->icon ?? 'fa fa-fw fa-heart' }}"></i></button>
                            <button type="button" class="icp icp-dd btn btn-primary dropdown-toggle"
                                data-selected="fa-car" data-toggle="dropdown"></button>
                            <div class="dropdown-menu"></div>
                        </div>
                        <input type="hidden" id="inputIcon" name="icon">
                        <p id="erricon" class="mt-1 mb-0 text-danger em"></p>
                        <div class="text-warning mt-2">
                            <small>{{ __('Click on the dropdown icon to select a icon') }}</small>
                        </div>
                    </div>
                    @foreach ($tenantFrontLangs as $lang)
                        <div class="form-group {{ $lang->direction == 1 ? 'rtl text-right' : '' }}">
                            <label for="">{{ __('Name')   }}
                                ({{ $lang->name }})  <span class="text-danger">{{ '*' }}</span>
                                
                            </label>
                            <input type="text" class="form-control" name="{{ $lang->code }}_name"
                                placeholder="{{ __('Enter amenity name for') . ' ' . $lang->name . ' ' . __('language') }}">
                            <p id="err{{ $lang->code }}_name" class="mt-2 mb-0 text-danger em"></p>
                        </div>
                    @endforeach
                    <div class="form-group">
                        <label for="">{{ __('Status')  }}  <span class="text-danger">{{ '*' }}</span> </label>
                        <select name="status" class="form-control">
                            <option selected disabled>{{ __('Select Status') }}</option>
                            <option value="1">{{ __('Active') }}</option>
                            <option value="0">{{ __('Deactive') }}</option>
                        </select>
                        <p id="errstatus" class="mt-2 mb-0 text-danger em"></p>
                    </div>

                    <div class="form-group">
                        <label for="">{{ __('Serial Number')  }}  <span class="text-danger">{{ '*' }}</span> </label>
                        <input type="number" class="form-control " name="serial_number"
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
