<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">
                    {{ __('Edit Testimonial') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form id="ajaxEditForm" class="modal-form" action="{{ route('user.home_page.update_testimonial') }}"
                    method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" id="inid">

                    <div class="form-group">
                        <label for="">{{ __('Client Image') }} <span
                                class="text-danger">{{ '*' }}</span></label>
                        <br>
                        <div class="thumb-preview">
                            <img src="" alt="..." class="uploaded-img in_image" id="in_image">
                        </div>

                        <div class="mt-3">
                            <div role="button" class="btn btn-primary btn-sm upload-btn">
                                {{ __('Choose Image') }}
                                <input type="file" class="img-input" name="image">
                            </div>
                        </div>
                        <p id="editErr_image" class="mt-2 mb-0 text-danger em"></p>
                    </div>

                    <div class="row no-gutters">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">{{ __('Name') }} <span
                                        class="text-danger">{{ '*' }}</span></label>
                                <input type="text" class="form-control" name="name"
                                    placeholder="{{ __('Enter client name') }}" id="inname">
                                <p id="editErr_name" class="mt-2 mb-0 text-danger em"></p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">{{ __('Occupation') }} <span
                                        class="text-danger">{{ '*' }}</span></label>
                                <input type="text" class="form-control" name="occupation"
                                    placeholder="{{ __('Enter client occupation') }}" id="inoccupation">
                                <p id="editErr_occupation" class="mt-2 mb-0 text-danger em"></p>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="">{{ __('Rating (1 to 5)') }} <span
                                class="text-danger">{{ '*' }}</span></label>
                        <input type="text" class="form-control" name="rating"
                            placeholder="{{ __('Enter rating') }}" id="inrating">
                        <p id="editErr_rating" class="mt-2 mb-0 text-danger em"></p>
                    </div>
                    <div class="form-group">
                        <label for="">{{ __('Comment') }} <span
                                class="text-danger">{{ '*' }}</span></label>
                        <textarea class="form-control" name="comment" placeholder="{{ __('Enter client comment') }}" rows="4"
                            id="incomment"></textarea>
                        <p id="editErr_comment" class="mt-2 mb-0 text-danger em"></p>
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
