@extends('front.layout')

@section('pagename')
    - {{ __('Blog') }}
@endsection

@section('meta-description', !empty($seo) ? $seo->blogs_meta_description : '')
@section('meta-keywords', !empty($seo) ? $seo->blogs_meta_keywords : '')

@section('content')
    @includeIf('front.partials.breadcrumb', [
        'title' => __('Blog'),
        'link' => __('Blog'),
    ])

    <section class="blog-area ptb-120">
        <div class="container">
            <div class="row justify-content-center">
                @forelse ($blogs as $blog)
                    <div class="col-md-6 col-lg-4">
                        <article class="card mb-30" data-aos="fade-up" data-aos-delay="100">
                            <div class="card-image">
                                <a href="{{ route('front.blogdetails', ['id' => $blog->id, 'slug' => $blog->slug]) }}"
                                    class="lazy-container ratio-16-9">
                                    <img class="lazyload lazy-image"
                                        data-src="{{ asset('assets/front/img/blogs/' . $blog->main_image) }}"
                                        alt="Blog Image">
                                </a>
                                <ul class="info-list">
                                    <li><i class="fal fa-user"></i>{{ __('Admin') }}</li>
                                    <li><i class="fal fa-calendar"></i>
                                        {{ \Carbon\Carbon::parse($blog->created_at)->locale(app()->getLocale())->translatedFormat('d F, Y') }}
                                    </li>
                                    <li><i class="fal fa-tag"></i>{{ $blog->bcategory->name }}</li>
                                </ul>
                            </div>
                            <div class="content">
                                <h5 class="card-title lc-2">
                                    <a href="{{ route('front.blogdetails', ['id' => $blog->id, 'slug' => $blog->slug]) }}">

                                        {{ $blog->title }}
                                    </a>
                                </h5>
                                <p class="card-text lc-2">
                                    {!! substr(strip_tags($blog->content), 0, 150) !!}
                                </p>
                                <a href="{{ route('front.blogdetails', ['id' => $blog->id, 'slug' => $blog->slug]) }}"
                                    class="card-btn">{{ __('Read More') }}</a>
                            </div>
                        </article>
                    </div>
                @empty
                    <h4 class="text-center"> {{ __('NO BLOG POST FOUND') }}</h4>
                @endforeLSE

            </div>
            {{ $blogs->appends(['category' => request()->input('category')])->links() }}


        </div>
    </section>
@endsection
