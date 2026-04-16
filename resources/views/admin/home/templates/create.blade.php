    <div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">{{ __('Add Theme') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="ajaxForm" action="{{ route('admin.userThemes.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <div class="col-12 mb-2">
                                <label for="image"><strong>{{ __('Image') }}<span
                                            class="text-danger">{{ '*' }}</span></strong></label>
                            </div>
                            <div class="col-md-12 showImage mb-3">
                                <img src="{{ asset('assets/admin/img/noimage.jpg') }}" alt="..."
                                    class="img-thumbnail">
                            </div>
                            <input type="file" name="image" id="image" class="form-control">
                            <p id="errimage" class="mb-0 text-danger em"></p>
                            <p class="text-warning mb-0">{{ __('Upload 900 * 570 image for best quality') }}</p>
                        </div>

                        <div class="form-group">
                            <label for="">{{ __('Name') }} <span
                                    class="text-danger">{{ '*' }}</span>
                            </label>
                            <input type="text" class="form-control" name="name"
                                placeholder="{{ __('Enter theme name') }}">
                            <p id="errname" class="mb-0 text-danger em"></p>
                        </div>
                        <div class="form-group">
                            <label for="">{{ __('URL') }} <span
                                    class="text-danger">{{ '*' }}</span> </label>
                            <input type="text" class="form-control" name="url"
                                placeholder="{{ __('Enter url') }}">

                            <p id="errurl" class="mb-0 text-danger em"></p>
                        </div>
                        <div class="form-group">
                            <label for="">{{ __('Serial Number') }} <span
                                    class="text-danger">{{ '*' }}</span></label>
                            <input type="number" class="form-control " name="serial_number" value=""
                                placeholder="{{ __('Enter Serial Number') }}">
                            <p id="errserial_number" class="mb-0 text-danger em"></p>
                            <p class="text-warning">
                                <small>{{ __('The higher the serial number is, the later will be shown') }}</small>
                            </p>
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                    <button id="submitBtn" type="button" class="btn btn-primary">{{ __('Save Changes') }}</button>
                </div>
            </div>
        </div>
    </div>
