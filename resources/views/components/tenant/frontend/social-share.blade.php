   {{-- share on social media modal --}}
   <div class="modal fade" id="socialMediaModal" tabindex="-1" role="dialog" aria-labelledby="socialMediaModalTitle"
       aria-hidden="true">
       <div class="modal-dialog modal-dialog-centered" role="document">
           <div class="modal-content">
               <div class="modal-header">
                   <h5 class="modal-title" id="exampleModalLongTitle"> {{ $keywords['Share On'] ?? __('Share On') }}
                   </h5>
                   <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
               </div>
               <div class="modal-body">
                   <div class="actions d-flex justify-content-around">
                       <div class="action-btn">
                           <a class="facebook btn"
                               href="https://www.facebook.com/sharer/sharer.php?u={{ url()->current() }}&src=sdkpreparse"><i
                                   class="fab fa-facebook-f"></i></a>
                           <br>
                           <span> {{ $keywords['Facebook'] ?? __('Facebook') }} </span>
                       </div>
                       <div class="action-btn">
                           <a href="http://www.linkedin.com/shareArticle?mini=true&amp;url={{ urlencode(url()->current()) }}"
                               class="linkedin btn"><i class="fab fa-linkedin-in"></i></a>
                           <br>
                           <span> {{ $keywords['Linkedin'] ?? __('Linkedin') }} </span>
                       </div>
                       <div class="action-btn">
                           <a class="twitter btn" href="https://twitter.com/intent/tweet?text={{ url()->current() }}"><i
                                   class="fab fa-twitter"></i></a>
                           <br>
                           <span> {{ $keywords['Twitter'] ?? __('Twitter') }} </span>
                       </div>
                       <div class="action-btn">
                           <a class="whatsapp btn" href="whatsapp://send?text={{ url()->current() }}"><i
                                   class="fab fa-whatsapp"></i></a>
                           <br>
                           <span> {{ $keywords['Whatsapp'] ?? __('Whatsapp') }} </span>
                       </div>
                       <div class="action-btn">
                           <a class="sms btn" href="sms:?body={{ url()->current() }}" class="sms"><i
                                   class="fas fa-sms"></i></a>
                           <br>
                           <span> {{ $keywords['SMS'] ?? __('SMS') }} </span>
                       </div>
                       <div class="action-btn">
                           <a class="mail btn" href="mailto:?{{ url()->current() }}."><i class="fas fa-at"></i></a>
                           <br>
                           <span> {{ $keywords['Mail'] ?? __('Mail') }} </span>
                       </div>
                   </div>
               </div>
           </div>
       </div>
   </div>
