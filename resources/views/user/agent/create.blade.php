<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">{{ __('Add Agent') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form id="ajaxForm" class="modal-form" action="{{ route('user.agent_management.register') }}"
                    method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="">{{ __('Image') }}</label>
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
                        <p class="mt-2 mb-0 text-danger" id="err_image"></p>
                    </div>

                    <div class="row no-gutters">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="">{{ __('Username') }} <span
                                        class="text-danger">{{ '*' }}</span></label>
                                <input type="text" class="form-control" name="username"
                                    placeholder="{{ __('Enter username') }}">
                                <p id="errusername" class="mt-2 mb-0 text-danger em"></p>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="">{{ __('Email') }} <span
                                        class="text-danger">{{ '*' }}</span></label>
                                <input type="email" class="form-control" name="email"
                                    placeholder="{{ __('Enter email address') }}">
                                <p id="erremail" class="mt-2 mb-0 text-danger em"></p>
                            </div>
                        </div>
                    </div>

                    <div class="row no-gutters">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="">{{ __('Password') }} <span
                                        class="text-danger">{{ '*' }}</span></label>
                                <input type="password" class="form-control" name="password"
                                    placeholder="{{ __('Enter password') }}">
                                <p id="errpassword" class="mt-2 mb-0 text-danger em"></p>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="">{{ __('Confirm Password') }} <span
                                        class="text-danger">{{ '*' }}</span></label>
                                <input type="password" class="form-control" name="password_confirmation"
                                    placeholder=" {{ __('Re-Enter password') }} ">
                                <p id="errpassword_confirmation" class="mt-2 mb-0 text-danger em"></p>
                            </div>
                        </div>
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
