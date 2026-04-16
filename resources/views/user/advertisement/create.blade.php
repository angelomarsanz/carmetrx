<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">
                    {{ __('Add Advertisement') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form id="ajaxForm" class="modal-form" action="{{ route('user.advertise.store_advertisement') }}"
                    method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="">{{ __('Advertisement Type') }} <span
                                class="text-danger">{{ '*' }}</span></label>
                        <select name="ad_type" class="form-control" id="ad-type">
                            <option selected disabled>{{ __('Select a Type') }}</option>
                            <option value="banner">{{ __('Banner') }}</option>
                            <option value="adsense">{{ __('Google AdSense') }}</option>
                        </select>
                        <p id="errad_type" class="mt-2 mb-0 text-danger em"></p>
                    </div>

                    <div class="form-group">
                        <label for="">{{ __('Advertisement Resolution') }} <span
                                class="text-danger">{{ '*' }}</span></label>
                        <select name="resolution_type" class="form-control">
                            <option selected disabled>
                                {{ __('Select a Resolution') }}</option>
                            <option value="1">{{ __('300 x 250') }}</option>
                            <option value="2">{{ __('300 x 600') }}</option>
                            <option value="3">{{ __('728 x 90') }}</option>
                        </select>
                        <p id="errresolution_type" class="mt-2 mb-0 text-danger em"></p>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group d-none" id="image-input">
                                <div class="col-12 mb-2">
                                    <label for="image"><strong>{{ __('Image') }}</strong> <span
                                            class="text-danger">{{ '*' }}</span></label>
                                </div>
                                <div class="col-md-12 showImage mb-3">
                                    <img src="{{ asset('assets/tenant/image/default.jpg') }}" alt="..."
                                        class="img-thumbnail">
                                </div>
                                <input type="file" name="image" id="image" class="form-control">
                                <p class="mt-2 mb-0 text-danger em" id="errimage"></p>
                            </div>
                        </div>
                    </div>

                    <div class="form-group d-none" id="url-input">
                        <label for="">{{ __('Redirect URL') }} <span
                                class="text-danger">{{ '*' }}</span></label>
                        <input type="url" class="form-control" name="url"
                            placeholder="{{ __('Enter redirect url') }}">
                        <p id="errurl" class="mt-2 mb-0 text-danger em"></p>
                    </div>

                    <div class="form-group d-none" id="slot-input">
                        <label for="">{{ __('Ad Slot') }} <span
                                class="text-danger">{{ '*' }}</span></label>
                        <input type="text" class="form-control" name="slot"
                            placeholder="{{ __('Enter ad slot') }}">
                        <p id="errslot" class="mt-2 mb-0 text-danger em"></p>
                        <p class="mt-2 mb-0">
                            <a href="//prnt.sc/1uwa420" target="_blank"
                                class="redirect-link">{{ __('Click Here to see where you can find the ad slot') }}</a>

                        </p>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                    {{ $keywords['Close'] ?? __('Close') }}
                </button>
                <button id="submitBtn" type="button" class="btn btn-primary btn-sm">
                    {{ $keywords['Save'] ?? __('Save') }}
                </button>
            </div>
        </div>
    </div>
</div>
