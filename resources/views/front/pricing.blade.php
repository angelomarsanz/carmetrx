@extends('front.layout')

@section('pagename')
    - {{ __('Pricing') }}
@endsection

@section('meta-description', !empty($seo) ? $seo->pricing_meta_description : '')
@section('meta-keywords', !empty($seo) ? $seo->pricing_meta_keywords : '')


@section('content')

    @includeIf('front.partials.breadcrumb', [
        'title' => __('Pricing'),
        'link' => __('Pricing'),
    ])

    <section class="pricing-area pt-120 pb-90">
        <div class="container">

            <x-front.sections.pricing />
        </div>
    </section>

@endsection
