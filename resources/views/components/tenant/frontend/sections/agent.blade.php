  <section class="agent-area pb-70">
      <div class="container">
          <div class="row">
              <div class="col-12">
                  <div class="section-title title-center mb-40" data-aos="fade-up">
                      <span class="subtitle">{{ $agentInfo?->agent_section_title }}</span>
                      <h2 class="title">{{ $agentInfo?->agent_section_subtitle }}</h2>
                  </div>
              </div>
              <div class="col-12" data-aos="fade-up">
                  <div class="swiper agent-slider">
                      <div class="swiper-wrapper">
                          @forelse ($agents as $agent)
                              <div class="swiper-slide">
                                  <x-tenant.frontend.agent :$agent />

                              </div>
                          @empty
                              <div class="p-3 text-center mb-30 w-100">
                                  <h3 class="mb-0">
                                      {{ $keywords['No Team Member Found'] ?? __('No Team Member Found') }}</h3>
                              </div>
                          @endforelse
                      </div>
                  </div>
              </div>
              @if (count($agents) > 3)
                  <div class="text-center">
                      <a href="{{ route('frontend.agents', getParam()) }}"
                          class="btn btn-lg btn-primary bg-secondary mb-30">{{ $agentInfo->btn_name ?? ($keywords['All Agent'] ?? __('All Agent')) }}</a>
                  </div>
              @endif
          </div>
      </div>
  </section>
