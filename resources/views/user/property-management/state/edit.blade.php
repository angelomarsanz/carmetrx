<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">{{ __('Edit State') }}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form id="ajaxEditForm" class="modal-form" action="{{ route('user.property_management.update_state') }}"
                    method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="in_id" name="id">

                    @foreach ($tenantFrontLangs as $lan)
                        <div class="form-group {{ $lan->direction == 1 ? 'rtl text-right' : '' }}">
                            <label for="">{{ __('Name') }}
                                ({{ $lan->name }})
                                <span class="text-danger">{{ '*' }}</span>
                            </label>
                            <input type="text" id="in_{{ $lan->code }}_name" class="form-control"
                                name="{{ $lan->code }}_name"
                                placeholder="{{ __('Enter state name for') . ' ' . $lan->name . ' ' . __('language') }}">
                            <p id="editErr_{{ $lan->code }}_name" class="mt-2 mb-0 text-danger em"></p>
                        </div>
                    @endforeach

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
