<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">{{ __('Add FAQ') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form id="ajaxForm" class="modal-form create"
                    action="{{ route('user.faq_management.store_faq', ['language' => request()->input('language')]) }}"
                    method="post">
                    @csrf

                    <div class="form-group">
                        <label for="">{{ __('Question') }} <span class="text-danger">{{ '*' }}</span></label>
                        <input type="text" class="form-control" name="question"
                            placeholder="{{ __('Enter question') }}">
                        <p id="errquestion" class="mt-1 mb-0 text-danger em"></p>
                    </div>

                    <div class="form-group">
                        <label for="">{{ __('Answer') }} <span class="text-danger">{{ '*' }}</span> </label>
                        <textarea class="form-control" name="answer" rows="5" cols="80" placeholder="{{ __('Enter answer') }}"></textarea>
                        <p id="erranswer" class="mt-1 mb-0 text-danger em"></p>
                    </div>

                    <div class="form-group">
                        <label for="">{{ __('Serial Number') }} <span class="text-danger">{{ '*' }}</span> </label>
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
