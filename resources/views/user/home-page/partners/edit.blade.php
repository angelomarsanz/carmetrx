<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">{{ __('Edit Partner') }}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form id="ajaxEditForm" class="modal-form" action="{{ route('user.home_page.update_partner') }}"
                    method="POST">
                    @csrf
                    <input type="hidden" id="inid" name="id">

                    <div class="form-group">
                        <label for="">{{ __('Image')   }} <span class="text-danger">{{ '*' }}</span> </label>
                        <br>
                        <div class="thumb-preview">
                            <img src="" alt="client" class="inimage uploaded-img" id="in_image">
                        </div>

                        <div class="mt-3">
                            <div role="button" class="btn btn-primary btn-sm upload-btn">
                                {{ __('Choose Image') }}
                                <input type="file" class="img-input" name="image">
                            </div>
                        </div>
                        <p id="editErr_image" class="mt-2 mb-0 text-danger em"></p>
                    </div>

                    <div class="form-group">
                        <label for="">{{ __('Partner URL')   }} <span class="text-danger">{{ '*' }}</span></label>
                        <input type="url" id="inurl" class="form-control" name="url"
                            placeholder="{{ __('Enter url') }}">
                        <p id="editErr_url" class="mt-2 mb-0 text-danger em"></p>
                    </div>

                    <div class="form-group">
                        <label for="">{{ __('Serial Number')   }} <span class="text-danger">{{ '*' }}</span> </label>
                        <input type="number" id="inserial_number" class="form-control" name="serial_number"
                            placeholder="{{ __('Enter serial number') }}">
                        <p id="editErr_serial_number" class="mt-2 mb-0 text-danger em"></p>
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
