@extends('tenant_frontend.layout')

@section('pageHeading')
  {{ $pageInfo->title }}
@endsection

@section('metaKeywords')
  {{ $pageInfo->meta_keywords }}
@endsection

@section('metaDescription')
  {{ $pageInfo->meta_description }}
@endsection

@section('style')
  <style>
    .custom-page-area {
      padding-top: 100px;
      padding-bottom: 90px;
    }
  </style>
@endsection

@section('content')
  @includeIf('tenant_frontend.partials.breadcrumb', ['breadcrumb' => $breadcrumb, 'title' => $pageInfo->title])

  <!--====== PAGE CONTENT PART START ======-->
  <section class="custom-page-area">
    <div class="container">
      <div class="row">
        <div class="col">
          <div class="summernote-content">
            {!! replaceBaseUrl($pageInfo->content, 'summernote') !!}
          </div>
        </div>
      </div>
    </div>
  </section>
  <!--====== PAGE CONTENT PART END ======-->
@endsection
