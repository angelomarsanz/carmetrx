<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">
                    {{ __('Add Blog Category') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form id="ajaxForm" class="modal-form create"
                    action="{{ route('user.blog_management.store_category', ['language' => request()->input('language')]) }}"
                    method="post">
                    @csrf


                    @foreach ($tenantFrontLangs as $lang)
                        <div class="form-group {{ $lang->direction == 1 ? 'rtl text-right' : '' }}">
                            <label for="">{{ __('Category Name') }}
                                ({{ $lang->name }})
                                <span class="text-danger">{{ '*' }}</span>
                            </label>
                            <input type="text" class="form-control" name="{{ $lang->code }}_name"
                                placeholder="{{ __('Enter category name for') . ' ' . $lang->name . ' ' . __('language') }}">
                            <p id="err{{ $lang->code }}_name" class="mt-2 mb-0 text-danger em"></p>
                        </div>
                    @endforeach

                    <div class="form-group">
                        <label for="">{{ __('Category Status') }} <span
                                class="text-danger">{{ '*' }}</span></label>
                        <select name="status" class="form-control">
                            <option selected disabled>{{ __('Select Status') }}
                            </option>
                            <option value="1">{{ __('Active') }}</option>
                            <option value="0">{{ __('Deactive') }}</option>
                        </select>
                        <p id="errstatus" class="mt-1 mb-0 text-danger em"></p>
                    </div>

                    <div class="form-group">
                        <label for="">{{ __('Serial Number') }} <span
                                class="text-danger">{{ '*' }}</span> </label>
                        <input type="number" class="form-control " name="serial_number"
                            placeholder="{{ __('Enter serial number') }}">
                        <p id="errserial_number" class="mt-1 mb-0 text-danger em"></p>
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
