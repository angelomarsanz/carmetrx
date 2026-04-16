@extends('front.layout')

@section('pagename')
    - {{ $page->name }}
@endsection

@section('meta-description', !empty($page) ? $page->meta_keywords : '')
@section('meta-keywords', !empty($page) ? $page->meta_description : '')



@section('content')
    @includeIf('front.partials.breadcrumb', [
        'title' => $page->name,
        'link' => $page->name,
    ])

    <section class="terms-condition-area pt-120 pb-90">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12  ">

                    <div class="item-singles mb-30" data-aos="fade-up">
                        {!! replaceBaseUrl($page->body) !!}
                    </div>

                </div>
            </div>
        </div>
    </section>

@endsection
