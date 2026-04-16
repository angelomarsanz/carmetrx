<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">
                    {{ __('Edit Blog Category') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form id="ajaxEditForm" class="modal-form" action="{{ route('user.blog_management.update_category') }}"
                    method="post">

                    @csrf
                    @method('PUT')
                    <input type="hidden" id="inid" name="id">

                   

                    @foreach ($tenantFrontLangs as $lan)
                        <div class="form-group {{ $lan->direction == 1 ? 'rtl text-right' : '' }}">
                            <label for="">{{ __('Category Name') }}
                                ({{ $lan->name }})
                                <span class="text-danger">{{ '*' }}</span>
                            </label>
                            <input type="text" id="in{{ $lan->code }}_name" class="form-control"
                                name="{{ $lan->code }}_name"
                                placeholder="{{ __('Enter category name for') . ' ' . $lan->name . ' ' . __('language') }}">
                            <p id="editErr_{{ $lan->code }}_name" class="mt-2 mb-0 text-danger em"></p>
                        </div>
                    @endforeach

                    <div class="form-group">
                        <label for="">{{ __('Category Status') }} <span
                                class="text-danger">{{ '*' }}</span> </label>
                        <select name="status" id="instatus" class="form-control">
                            <option disabled>{{ __('Select Status') }}</option>
                            <option value="1">{{ __('Active') }}</option>
                            <option value="0">{{ __('Deactive') }}</option>
                        </select>
                        <p id="editErr_status" class="mt-1 mb-0 text-danger em"></p>
                    </div>

                    <div class="form-group">
                        <label for="">{{ __('Serial Number') }} <span
                                class="text-danger">{{ '*' }}</span> </label>
                        <input type="number" id="inserial_number" class="form-control " name="serial_number"
                            placeholder="{{ __('Enter serial number') }}">
                        <p id="editErr_serial_number" class="mt-1 mb-0 text-danger em"></p>
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
                <button id="updateBtn" type="button" class="btn btn-primary btn-sm">
                    {{ __('Update') }}
                </button>
            </div>
        </div>
    </div>
</div>
