@extends('tenant_frontend.layout')

@section('pageHeading')
    {{ !empty($pageHeading) ? $pageHeading->blog_page_title : $keywords['Blog'] ?? __('Blog') }}
@endsection

@section('metaKeywords')
    @if (!empty($seoInfo))
        {{ $seoInfo->meta_keyword_blog }}
    @endif
@endsection

@section('metaDescription')
    @if (!empty($seoInfo))
        {{ $seoInfo->meta_description_blog }}
    @endif
@endsection

@section('content')
    @includeIf('tenant_frontend.partials.breadcrumb', [
        'breadcrumb' => $breadcrumb,
    
        'title' => !empty($pageHeading) ? $pageHeading->blog_page_title : $keywords['Blog'] ?? __('Blog'),
        'subtitle' => $keywords['Blog'] ?? __('Blog'),
    ])

    <div class="blog-area pt-100 pb-70">
        <div class="container">
            <div class="row justify-content-center gx-xl-5">
                <div class="col-lg-9">
                    @if (count($posts) == 0)
                        <h3 class="text-center mt-3">{{ $keywords['No Post Found'] ?? __('No Post Found') }}</h3>
                    @else
                        <div class="row">
                            @foreach ($posts as $blog)
                                <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="100">
                                    <article class="card mb-30">
                                        <div class="card-image">
                                            <a href="{{ route('frontend.blog.post_details', [getParam(), 'slug' => $blog->slug]) }}"
                                                class="lazy-container ratio ratio-16-9">
                                                <img class="lazyload"
                                                    src="{{ asset('assets/front/images/placeholder.png') }}"
                                                    data-src="{{ asset(\App\Constants\Constant::WEBSITE_BLOG_IMAGE . '/' . $blog->image) }}">
                                            </a>
                                            <a
                                                href="{{ route('frontend.blog', [getParam(), 'category' => $blog->category_slug]) }}">
                                                <span class="tag">{{ $blog->category_name }}</span></a>
                                        </div>
                                        <div class="content">
                                            <ul class="info-list justify-content-around">
                                                <li><i class="fal fa-user"></i>{{ $blog->author }}</li>
                                                <li><i class="fal fa-calendar-alt"></i>
                                                    {{ \Carbon\Carbon::parse($blog->created_at)->locale($currentLanguageInfo?->code)->translatedFormat('d F, Y') }}
                                                </li>

                                            </ul>
                                            <h3 class="card-title">
                                                <a
                                                    href="{{ route('frontend.blog.post_details', [getParam(), 'slug' => $blog->slug]) }}">
                                                    {{ @$blog->title }}
                                                </a>
                                            </h3>
                                            <p class="card-text">
                                                {{ strlen(strip_tags($blog->content)) > 90 ? mb_substr(strip_tags($blog->content), 0, 90, 'UTF-8') . '...' : strip_tags($blog->content) }}
                                            </p>
                                            <a href="{{ route('frontend.blog.post_details', [getParam(), 'slug' => $blog->slug]) }}"
                                                class="card-btn">{{ $keywords['Read More'] ?? __('Read More') }}</a>
                                        </div>
                                    </article>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{ $posts->links() }}
                    @if (!empty(showAd(3)))
                        <div class="text-center mt-4">
                            {!! showAd(3) !!}
                        </div>
                    @endif
                </div>

                <div class="col-lg-3">
                    <aside class="sidebar-widget-area">
                        <div class="widget widget-search radius-md mb-30">

                            <h4 class="title mb-15">{{ $keywords['Search Posts'] ?? __('Search Posts') }}</h4>
                            <form class="search-form radius-md" action="{{ route('frontend.blog', getParam()) }}"
                                method="GET">
                                <input type="search"
                                    class="search-input"placeholder="{{ $keywords['Search By Title'] ?? __('Search By Title') }}"
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
                        @if (!empty(showAd(1)))
                            <div class="text-center mb-40">
                                {!! showAd(1) !!}
                            </div>
                        @endif
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

                    </aside>
                </div>
            </div>

        </div>
    </div>
@endsection
