@extends('front.layout')

@section('pagename')
    - {{ __('Blog Details') }}
@endsection

@section('meta-description', !empty($blog) ? $blog->meta_keywords : '')
@section('meta-keywords', !empty($blog) ? $blog->meta_description : '')

@section('og-meta')
    <meta property="og:image" content="{{ asset('assets/front/img/blogs/' . $blog->main_image) }}">
    <meta property="og:image:type" content="image/png">
    <meta property="og:image:width" content="1024">
    <meta property="og:image:height" content="1024">
@endsection



@section('content')
    @includeIf('front.partials.breadcrumb', [
        'title' => $blog->title,
        'link' => __('Blog Details'),
    ])

    <div class="blog-details-area pt-120 pb-90">
        <div class="container">
            <div class="row justify-content-center gx-xl-5">
                <div class="col-lg-8">

                    <div class="blog-description mb-50">
                        <article class="item-single">
                            <div class="image">
                                <div class="lazy-container ratio-16-9">
                                    <img class="lazyload lazy-image"
                                        src="{{ asset('assets/front/img/blogs/' . $blog->main_image) }}"
                                        data-src="{{ asset('assets/front/img/blogs/' . $blog->main_image) }}"
                                        alt="Blog Image">
                                </div>
                                {{-- <a href="//twitter.com/intent/tweet?text=my share text&amp;url={{ urlencode(url()->current()) }}"
                                    class="btn btn-lg btn-primary"><i class="fas fa-share-alt"></i>{{ __('Share') }}</a> --}}
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#socialMediaModal"
                                        class="btn btn-lg btn-primary"><i class="fas fa-share-alt"></i>{{ __('Share') }}</a>
                                        
                            </div>
                            <div class="content">
                                <ul class="info-list">
                                    <li><i class="fal fa-user"></i>{{ __('Admin') }}</li>
                                    <li> <i class="fal fa-calendar"></i>
                                        {{ \Carbon\Carbon::parse($blog->created_at)->locale(app()->getLocale())->translatedFormat('d F, Y') }}
                                    </li>
                                    <li><i class="fal fa-tag"></i>
                                        {{ $blog->bcategory->name }}
                                    </li>
                                </ul>
                                <h4 class="title">
                                    {{ $blog->title }}
                                </h4>

                                <div class="summernote-content">{!! replaceBaseUrl($blog->content, 'summernote') !!}</div>
                            </div>
                        </article>
                    </div>

                </div>
                <div class="col-lg-4">
                    @includeIf('front.partials.blog-sidebar')
                </div>
            </div>
        </div>
    </div>

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
    
    {{-- <x-tenant.frontend.social-share /> --}}
@endsection

@if ($bs->is_disqus == 1)
    @section('scripts')
        <script>
            "use strict";
            (function() {
                var d = document,
                    s = d.createElement('script');
                s.src = '//{{ $bs->disqus_shortname }}.disqus.com/embed.js';
                s.setAttribute('data-timestamp', +new Date());
                (d. || d.body).appendChild(s);
            })();
        </script>
    @endsection
@endif
