@extends('user.layout')

@section('content')
    <div class="page-header">
        <h4 class="page-title">{{ __('Edit Blog') }}</h4>
        <ul class="breadcrumbs">
            <li class="nav-home">
                <a href="{{ route('user-dashboard') }}">
                    <i class="flaticon-home"></i>
                </a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="#">{{ __('Blog Management') }}</a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="#">{{ __('Blog') }}</a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="#">{{ __('Edit Blog') }}</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title d-inline-block">{{ __('Edit Blog') }}</div>
                    <a class="btn btn-info btn-sm float-right d-inline-block"
                        href="{{ route('user.blog_management.blogs', ['language' => request()->input('language')]) }}">
                        <span class="btn-label">
                            <i class="fas fa-backward"></i>
                        </span>
                        {{ __('Back') }}
                    </a>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-8 offset-lg-2">
                            <div class="alert alert-danger pb-1 dis-none" id="blogErrors">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                <ul></ul>
                            </div>

                            <form id="blogForm"
                                action="{{ route('user.blog_management.update_blog', ['id' => $blog->id]) }}" method="POST"
                                enctype="multipart/form-data">

                                @csrf
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <div class="col-12 mb-2">
                                                <label for="image"><strong>{{ __('Image') }}</strong> <span
                                                        class="text-danger">{{ '*' }}</span> </label>
                                            </div>
                                            <div class="col-md-12 showImage mb-3">
                                                <img src="{{ isset($blog->image) ? asset(Constant::WEBSITE_BLOG_IMAGE . '/' . $blog->image) : asset('assets/tenant/image/default.jpg') }}"
                                                    alt="..." class="img-thumbnail">
                                            </div>
                                            <input type="file" name="image" id="image" class="form-control">
                                            @error('image')
                                                <p class="mt-2 mb-0 text-danger">{{ $message }}</p>
                                            @enderror
                                            <p class="text-warning mb-0">
                                                {{ __('Upload 370 * 280 image for best quality') }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div
                                            class="form-group  {{ $defaultLang->direction == 1 ? 'rtl text-right' : '' }}">


                                            <label for="">{{ __('Category') }} <span
                                                    class="text-danger">{{ '*' }}</span> </label>
                                            <select name="category_id" class="form-control">
                                                <option selected disabled>
                                                    {{ __('Select Category') }}
                                                </option>

                                                @foreach ($categories as $category)
                                                    <option {{ $category->id == $blog->category_id ? 'selected' : '' }}
                                                        value="{{ $category->id }}">
                                                        {{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label>{{ __('Serial Number') }} <span
                                                    class="text-danger">{{ '*' }}</span></label>
                                            <input class="form-control" type="number" name="serial_number"
                                                placeholder=" {{ __('Enter serial number') }}"
                                                value="{{ $blog->serial_number }}">
                                            <p class="text-warning mt-2 mb-0">
                                                <small>{{ __('The higher the serial number will be shown') }}</small>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div id="accordion" class="mt-3">
                                    @foreach ($tenantLangs as $language)
                                        @php
                                            $blogData = $language
                                                ->blogInformations()
                                                ->where('blog_id', $blog->id)
                                                ->first();
                                        @endphp

                                        <div class="version">
                                            <div class="version-header" id="heading{{ $language->id }}">
                                                <h5 class="mb-0">
                                                    <button type="button" class="btn btn-link" data-toggle="collapse"
                                                        data-target="#collapse{{ $language->id }}"
                                                        aria-expanded="{{ $language->is_default == 1 ? 'true' : 'false' }}"
                                                        aria-controls="collapse{{ $language->id }}">
                                                        {{ $language->name . ' ' . __('language') }}
                                                        {{ $language->is_default == 1 ? '(Default)' : '' }}
                                                    </button>
                                                </h5>
                                            </div>

                                            <div id="collapse{{ $language->id }}"
                                                class="collapse {{ $language->is_default == 1 ? 'show' : '' }}"
                                                aria-labelledby="heading{{ $language->id }}" data-parent="#accordion">
                                                <div class="version-body">
                                                    <div class="row">
                                                        <div class="col-lg-6">
                                                            <div
                                                                class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                                                <label>{{ __('Title') }} <span
                                                                        class="text-danger">{{ '*' }}</span>
                                                                </label>
                                                                <input type="text" class="form-control"
                                                                    name="{{ $language->code }}_title"
                                                                    placeholder="{{ __('Enter Title') }}"
                                                                    value="{{ is_null($blogData) ? '' : $blogData->title }}">
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-6">
                                                            <div
                                                                class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                                                <label>{{ __('Author') }} <span
                                                                        class="text-danger">{{ '*' }}</span>
                                                                </label>
                                                                <input type="text" class="form-control"
                                                                    name="{{ $language->code }}_author"
                                                                    placeholder="{{ __('Enter author name') }}"
                                                                    value="{{ is_null($blogData) ? '' : $blogData->author }}">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col">
                                                            <div
                                                                class="form-group {{ $language->direction == 1 ? 'rtl text-right' : '' }}">
                                                                <label>{{ __('Content') }} <span
                                                                        class="text-danger">{{ '*' }}</span>
                                                                </label>
                                                                <textarea class="form-control summernote" name="{{ $language->code }}_content"
                                                                    placeholder="{{ __('Enter content') }}" data-height="300">{{ is_null($blogData) ? '' : replaceBaseUrl($blogData->content, 'summernote') }}</textarea>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col">
                                                            <div
                                                                class="form-group">
                                                                <label>{{ __('Meta Keywords') }}</label>
                                                                <input class="form-control"
                                                                    name="{{ $language->code }}_meta_keywords"
                                                                    placeholder="{{ __('Enter Meta Keywords') }}"
                                                                    data-role="tagsinput"
                                                                    value="{{ is_null($blogData) ? '' : $blogData->meta_keywords }}">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col">
                                                            <div
                                                                class="form-group ">
                                                                <label>{{ __('Meta Description') }}</label>
                                                                <textarea class="form-control" name="{{ $language->code }}_meta_description" rows="5"
                                                                    placeholder="{{ __('Enter Meta Description') }}">{{ is_null($blogData) ? '' : $blogData->meta_description }}</textarea>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col">
                                                            @php $currLang = $language; @endphp

                                                            @foreach ($tenantLangs as $language)
                                                                @continue($language->id == $currLang->id)

                                                                <div class="form-check py-0">
                                                                    <label class="form-check-label">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            onchange="cloneInput('collapse{{ $currLang->id }}', 'collapse{{ $language->id }}', event)">
                                                                        <span
                                                                            class="form-check-sign">{{ __('Clone for') }}
                                                                            <strong
                                                                                class="text-capitalize text-secondary">{{ $language->name }}</strong>
                                                                            {{ __('language') }}</span>
                                                                    </label>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="row">
                        <div class="col-12 text-center">
                            <button type="submit" form="blogForm" class="btn btn-success">
                                {{ __('Update') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript" src="{{ asset('assets/tenant/js/partial.js') }}"></script>
@endsection
