  <div class="modal fade" id="aiGenContentModal" tabindex="-1" role="dialog" aria-labelledby="AiGeneModalCenterTitle"
      aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="AiGeneModalLongTitle">{{ __('Generated Content') }}</h5>
                  <div>
                      <button type="button" class="btn btn-danger btn-xs" data-dismiss="modal">X</button>
                  </div>
              </div>
              <div class="modal-body">

                  <div class="form-group">
                      <span class="mb-2"><strong>{{ __('Generated Content') }}:</strong></span>
                      <div id="ai-content" class="mb-2"></div>
                  </div>

                  <div class="form-group">
                      <textarea name="make_better" id="make_better" rows="4" class="form-control mb-2"
                          placeholder="{{ __('Refine the content or add specific instructions here...') }}"></textarea>
                      <small class="text-warning d-block mb-2">
                          {{ __('Tip: To get a better version, add your instructions above and click Generate again, or copy the content to your description field.') }}
                      </small>
                      <small class="text-muted d-block">
                          {{ __('Click the copy button to automatically paste this content into your description field.') }}
                      </small>
                  </div>
              </div>

              <div class="modal-footer">
                  <button class="btn btn-success btn-sm" id="copyToDescription">
                      <i class="fas fa-copy mr-1"></i>{{ __('Copy to Description') }}
                  </button>
                  <button class="btn btn-primary btn-sm" id="generate_content_again">
                      <i class="fas fa-magic mr-1"></i>{{ __('Generate Again') }}
                  </button>
              </div>
          </div>
      </div>
  </div>
