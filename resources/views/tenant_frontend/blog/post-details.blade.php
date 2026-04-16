@extends('tenant_frontend.layout')

@section('pageHeading')
    {{ $details->title }}
@endsection

@section('metaKeywords')
    {{ $details->meta_keywords }}
@endsection

@section('metaDescription')
    {{ $details->meta_description }}
@endsection
@section('og:tag')
    <meta property="og:title" content="{{ $details->title }}">
    <meta property="og:image" content="{{ asset(\App\Constants\Constant::WEBSITE_BLOG_IMAGE . '/' . $details->image) }}">
    <meta property="og:url" content="{{ route('frontend.blog.post_details', [getParam(), 'slug' => $details->slug]) }}">
@endsection

@section('content')
    @includeIf('tenant_frontend.partials.breadcrumb', [
        'breadcrumb' => $breadcrumb,
        'title' =>  $details->title,
        'subtitle' => $keywords['Post Deatils'] ?? __('Post Deatils'),
    ])


    <div class="blog-details-area pt-100 pb-70">
        <div class="container">
            <div class="row justify-content-center gx-xl-5">
                <div class="col-lg-9">
                    <div class="blog-description mb-40">
                        <article class="item-single">
                            <div class="image radius-md">
                                <div class="lazy-container ratio ratio-16-9">
                                    <img class="lazyload" src="{{ asset('assets/front/images/placeholder.png') }}"
                                        data-src="{{ asset(\App\Constants\Constant::WEBSITE_BLOG_IMAGE . '/' . $details->image) }}">
                                </div>

                            </div>
                            <div class="content">
                                <ul class="info-list d-flex">
                                    <li><i class="fal fa-user"></i>{{ $details->author }} </li>
                                    <li><i class="fal fa-calendar-alt"></i>
                                        {{ \Carbon\Carbon::parse($details->created_at)->locale($currentLanguageInfo?->code)->translatedFormat('d F, Y') }}
                                    </li>
                                    <li><a
                                            href="{{ route('frontend.blog', [getParam(), 'category' => $details->categorySlug]) }}">
                                            <i class="fal fa-list"></i>
                                            {{ $details->categoryName }}</a></li>
                                    <li><a href="#" data-bs-toggle="modal" data-bs-target="#socialMediaModal"><i
                                                class="far fa-share-alt"></i> {{ $keywords['Share'] ?? __('Share') }}</a>
                                    </li>
                                </ul>
                                <h3 class="title">
                                    {{ $details->title }}
                                </h3>
                                <div class="summernote-content">{!! replaceBaseUrl($details->content, 'summernote') !!}</div>
                            </div>
                        </article>
                        @if (!empty(showAd(3)))
                            <div class="text-center mt-4">
                                {!! showAd(3) !!}
                            </div>
                        @endif
                    </div>
                    <div class="comments mb-30">
                        @if ($disqusInfo->disqus_status == 1)
                            <h4 class="mb-20">{{ $keywords['Comments'] ?? __('Comments') }}</h4>
                            <div id="disqus_thread"></div>
                        @endif
                    </div>
                </div>
                <div class="col-lg-3">
                    <aside class="sidebar-widget-area">
                        <div class="widget widget-search radius-md mb-30">

                            <h4 class="title mb-15">{{ $keywords['Search Posts'] ?? __('Search Posts') }}</h4>
                            <form class="search-form radius-md" action="{{ route('frontend.blog', getParam()) }}"
                                method="GET">
                                <input type="search" class="search-input"placeholder="{{ __('Search By Title') }}"
                                    name="title"
                                    value="{{ !empty(request()->input('title')) ? request()->input('title') : '' }}">

                                @if (!empty(request()->input('category')))
                                    <input type="hidden" name="category" value="{{ request()->input('category') }}">
                                @endif
                                <button class="btn-search" type="submit">
                                    <i class="far fa-search"></i>
                                </button>
                            </form>
                        </div>
                        <div class="widget widget-blog-categories radius-md mb-30">
                            <h3 class="title mb-15">{{ $keywords['Categories'] ?? __('Categories') }}</h3>
                            <ul class="list-unstyled m-0">

                                @foreach ($categories as $category)
                                    <li class="d-flex align-items-center justify-content-between">
                                        <a
                                            href="{{ route('frontend.blog', [getParam(), 'category' => $category->slug]) }}"><i
                                                class="fal fa-folder"></i>
                                            {{ $category->name }}</a>
                                        <span class="tqy">({{ $category->postCount }})</span>
                                    </li>
                                @endforeach

                            </ul>
                        </div>
                        <div class="widget widget-post radius-md mb-30">
                            <h3 class="title mb-15">{{ $keywords['Recent Posts'] ?? __('Recent Posts') }}</h3>
                            @foreach ($recent_blogs as $blog)
                                <article class="article-item mb-30">
                                    <div class="image">
                                        <a href="{{ route('frontend.blog.post_details', [getParam(), 'slug' => $blog->slug]) }}"
                                            class="lazy-container ratio ratio-1-1">

                                            <img class="lazyload" src="{{ asset('assets/front/images/placeholder.png') }}"
                                                data-src="{{ asset(\App\Constants\Constant::WEBSITE_BLOG_IMAGE . '/' . $blog->image) }}">
                                        </a>
                                    </div>
                                    <div class="content">
                                        <ul class="info-list">
                                            <li><i class="fal fa-user"></i>{{ $blog->author }}</li>
                                            <li><i class="fal fa-calendar-alt"></i>
                                                {{ \Carbon\Carbon::parse($blog->created_at)->locale($currentLanguageInfo?->code)->translatedFormat('d F, Y') }}
                                            </li>
                                        </ul>
                                        <h6>
                                            <a
                                                href="{{ route('frontend.blog.post_details', [getParam(), 'slug' => $blog->slug]) }}">
                                                {{ strlen($blog->title) > 40 ? mb_substr($blog->title, 0, 40, 'UTF-8') . '...' : $blog->title }}
                                            </a>
                                        </h6>
                                    </div>
                                </article>
                            @endforeach

                        </div>

                        @if (!empty(showAd(2)))
                            <div class="text-center mb-40">
                                {!! showAd(2) !!}
                            </div>
                        @endif
                    </aside>
                </div>
            </div>
        </div>
    </div>
    <x-tenant.frontend.social-share />
@endsection


@if (!empty($permissions) && in_array('Disqus', $permissions) && $basicInfo->disqus_status == 1)

    @section('script')
        <script>
            "use strict";

            (function() {
                var d = document,
                    s = d.createElement('script');
                s.src = '//{{ $disqusInfo->disqus_short_name }}.disqus.com/embed.js';
                s.setAttribute('data-timestamp', +new Date());
                (d.head || d.body).appendChild(s);
            })();
        </script>
    @endsection
@endif
