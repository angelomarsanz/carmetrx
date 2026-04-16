  <div class="modal fade" id="AiGeneModal" tabindex="-1" role="dialog" aria-labelledby="AiGeneModalCenterTitle"
      aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="AiGeneModalLongTitle">{{ __('Generated Prompt') }}</h5>
                  <div>
                      <button type="button" class="btn btn-danger btn-xs" data-dismiss="modal">X</button>
                  </div>
              </div>
              <div class="modal-body">
                  <textarea id="promt" class="form-control mb-3" rows="20"
                      placeholder="{{ __('Start typing or paste AI content here...') }}"></textarea>
                  <p class="text-warning d-block mt-2">
                      <strong>
                          {{ __('Tip: You can personalize the text or highlight unique features to attract buyers, or leave it as is and click the Generate button.') }}
                      </strong>
                  </p>
              </div>

              <div class="modal-footer">
                  <button class="btn btn-primary btn-sm w-30" id="generate_content_now">
                      <i class="fas fa-magic mr-1"></i>{{ __('Generate') }}
                  </button>
              </div>
          </div>
      </div>
  </div>
