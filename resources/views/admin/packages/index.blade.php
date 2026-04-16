@extends('admin.layout')

@php
    use App\Models\Language;
    $setLang = Language::where('code', request()->input('language'))->first();
@endphp
@if (!empty($setLang) && $setLang->rtl == 1)
    @section('styles')
        <style>
            form:not(.modal-form) input,
            form:not(.modal-form) textarea,
            form:not(.modal-form) select,
            select[name='language'] {
                direction: rtl;
            }

            form:not(.modal-form) .note-editor.note-frame .note-editing-area .note-editable {
                direction: rtl;
                text-align: right;
            }
        </style>
    @endsection
@endif

@section('content')
    <div class="page-header">
        <h4 class="page-title">{{ __('Packages') }}</h4>
        <ul class="breadcrumbs">
            <li class="nav-home">
                <a href="{{ route('admin.dashboard') }}">
                    <i class="flaticon-home"></i>
                </a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="#">{{ __('Package Management') }}</a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="#">{{ __('Packages') }}</a>
            </li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="card-title d-inline-block">{{ __('Package Page') }}</div>
                        </div>
                        <div class="col-lg-4 offset-lg-4 mt-2 mt-lg-0">
                            <a href="#" class="btn btn-primary float-right btn-sm" data-toggle="modal"
                                data-target="#createModal"><i class="fas fa-plus"></i>
                                {{ __('Add Package') }}</a>
                            <button class="btn btn-danger float-right btn-sm mr-2 d-none bulk-delete"
                                data-href="{{ route('admin.package.bulk.delete') }}"><i class="flaticon-interface-5"></i>
                                {{ __('Delete') }}
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12">
                            @if (count($packages) == 0)
                                <h3 class="text-center">{{ __('NO PACKAGE FOUND YET') }}</h3>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-striped mt-3" id="basic-datatables">
                                        <thead>
                                            <tr>
                                                <th scope="col">
                                                    <input type="checkbox" class="bulk-check" data-val="all">
                                                </th>
                                                <th scope="col">{{ __('Title') }}</th>
                                                <th scope="col">{{ __('Price') }}</th>
                                                <th scope="col">{{ __('Status') }}</th>
                                                <th scope="col">{{ __('Actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($packages as $key => $package)
                                                <tr>
                                                    <td>
                                                        <input type="checkbox" class="bulk-check"
                                                            data-val="{{ $package->id }}">
                                                    </td>
                                                    <td>
                                                        {{ strlen($package->title) > 30 ? mb_substr($package->title, 0, 30, 'UTF-8') . '...' : $package->title }}
                                                        <span class="badge badge-primary">{{ $package->term }}</span>
                                                    </td>
                                                    <td>
                                                        @if ($package->price == 0)
                                                            {{ __('Free') }}
                                                        @else
                                                            {{ espanishFormatPrice($package->price, $be->base_currency_symbol) }}
                                                        @endif

                                                    </td>
                                                    <td>
                                                        @if ($package->status == 1)
                                                            <h2 class="d-inline-block">
                                                                <span
                                                                    class="badge badge-success">{{ __('Active') }}</span>
                                                            </h2>
                                                        @else
                                                            <h2 class="d-inline-block">
                                                                <span
                                                                    class="badge badge-danger">{{ __('Deactive') }}</span>
                                                            </h2>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a class="btn btn-secondary btn-sm"
                                                            href="{{ route('admin.package.edit', $package->id)  }}">
                                                            <span class="btn-label">
                                                                <i class="fas fa-edit"></i>
                                                            </span>
                                                            {{ __('Edit') }}
                                                        </a>
                                                        <form class="deleteform d-inline-block"
                                                            action="{{ route('admin.package.delete') }}" method="post">
                                                            @csrf
                                                            <input type="hidden" name="package_id"
                                                                value="{{ $package->id }}">
                                                            <button type="submit" class="btn btn-danger btn-sm deletebtn">
                                                                <span class="btn-label">
                                                                    <i class="fas fa-trash"></i>
                                                                </span>
                                                                {{ __('Delete') }}
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Create Package Modal -->
    <div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">{{ __('Add Package') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <form id="ajaxForm" enctype="multipart/form-data" class="modal-form"
                        action="{{ route('admin.package.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="">{{ __('Icon') }} <span class="text-danger">{{ '*' }}</span>
                            </label>
                            <div class="btn-group d-block">
                                <button type="button" class="btn btn-primary iconpicker-component"><i
                                        class="fa fa-fw fa-heart"></i></button>
                                <button type="button" class="icp icp-dd btn btn-primary dropdown-toggle"
                                    data-selected="fa-car" data-toggle="dropdown">
                                </button>
                                <div class="dropdown-menu"></div>
                            </div>
                            <input id="inputIcon" type="hidden" name="icon" value="fas fa-heart">
                            @if ($errors->has('icon'))
                                <p class="mb-0 text-danger">{{ $errors->first('icon') }}</p>
                            @endif
                            <div class="mt-2">
                                <small class="text-warning"> {{ __('Click on the dropdown sign to select a icon') }}
                                </small>
                            </div>
                            <p id="erricon" class="mb-0 text-danger em"></p>
                        </div>

                        <div class="form-group">
                            <label for="title">{{ __('Package title') }} <span
                                    class="text-danger">{{ '*' }}</span> </label>
                            <input id="title" type="text" class="form-control" name="title"
                                placeholder="{{ __('Enter name') }}">
                            <p id="errtitle" class="mb-0 text-danger em"></p>
                        </div>

                        <div class="form-group">
                            <label for="price">{{ __('Price') }} ({{ $bex->base_currency_text }}) <span
                                    class="text-danger">{{ '*' }}</span> </label>
                            <input id="price" type="number" class="form-control" name="price"
                                placeholder="{{ __('Enter Package price') }}">
                            <p class="text-warning mb-0">
                                <small>{{ __('If price is 0 , than it will appear as free') }}</small>
                            </p>
                            <p id="errprice" class="mb-0 text-danger em"></p>
                        </div>

                        <div class="form-group">
                            <label for="term">{{ __('Package term') }} <span
                                    class="text-danger">{{ '*' }}</span> </label>
                            <select id="term" name="term" class="form-control" required>
                                <option value="" selected disabled>{{ __('Choose a Package term') }}</option>
                                <option value="monthly">{{ __('monthly') }}</option>
                                <option value="yearly">{{ __('yearly') }}</option>
                                <option value="lifetime">{{ __('lifetime') }}</option>
                            </select>
                            <p id="errterm" class="mb-0 text-danger em"></p>
                        </div>

                        <div class="form-group">
                            <label class="form-label">{{ __('Package Features') }} <span
                                    class="text-danger">{{ '*' }}</span> </label>
                            <div class="selectgroup selectgroup-pills">
                                @foreach ($features as $key => $value)
                                    <label class="selectgroup-item">
                                        <input type="checkbox" name="features[]" id="{{ str_replace(' ', '', $value) }}"
                                            value="{{ $value }}" id="customDomain" class="selectgroup-input">
                                        <span class="selectgroup-button">{{ __($value) }}</span>
                                    </label>
                                @endforeach


                            </div>
                            <p id="errfeatures" class="mb-0 text-danger em"></p>
                        </div>

                        <div class="form-group" id="language_input">
                            <label for="languages">{{ __('Number of Additional Languages') }} <span
                                    class="text-danger">{{ '*' }}</span> </label>
                            <input id="languages" type="number" class="form-control" name="number_of_language"
                                placeholder="{{ __('Enter number of additional languages') }}">
                            <p id="errnumber_of_language" class="mb-0 text-danger em"></p>
                            <p class="text-warning mb-0">
                                <small>{{ __('Enter 999999, than it will appear as unlimited') }}</small>
                            </p>
                        </div>
                        <div class="form-group" id="agent_input">
                            <label for="agent">{{ __('Number of Agents') }} <span
                                    class="text-danger">{{ '*' }}</span> </label>
                            <input id="agent" type="number" class="form-control" name="number_of_agent"
                                placeholder="{{ __('Enter number of agents') }}">
                            <p id="errnumber_of_agent" class="mb-0 text-danger em"></p>
                            <p class="text-warning mb-0">
                                <small>{{ __('Enter 999999, than it will appear as unlimited') }}</small>
                            </p>
                        </div>

                        <div class="form-group" id="property_input">
                            <label for="property ">{{ __('Number of Properties') }} <span
                                    class="text-danger">{{ '*' }}</span> </label>
                            <input id="property " type="number" class="form-control" name="number_of_property"
                                placeholder="{{ __('Enter number of properties') }}">
                            <p id="errnumber_of_property" class="mb-0 text-danger em"></p>
                            <p class="text-warning mb-0">
                                <small>{{ __('Enter 999999, than it will appear as unlimited') }}</small>
                            </p>
                        </div>

                        <div class="form-group" id="property_featured_input">
                            <label for="property ">{{ __('Number of Featured Property') }} <span
                                    class="text-danger">{{ '*' }}</span> </label>
                            <input id="property " type="number" class="form-control" name="number_of_property_featured"
                                placeholder="{{ __('Enter number of featured property') }}">
                            <p id="errnumber_of_property_featured" class="mb-0 text-danger em"></p>
                            <p class="text-warning mb-0">
                                <small>{{ __('Enter 999999, than it will appear as unlimited') }}</small>
                            </p>
                        </div>

                        <div class="form-group" id="property_gallery_input">
                            <label class="form-label">{{ __('Number of Gallery Images (Per Property)') }}
                                <span class="text-danger">{{ '*' }}</span> </label>
                            <input type="text" name="number_of_property_gallery_images" class="form-control"
                                placeholder="{{ __('Enter how many gallery images are added under a property') }}">
                            <p id="errnumber_of_property_gallery_images" class="mb-0 text-danger em"></p>
                            <p class="text-warning mb-0">
                                <small>{{ __('Enter 999999, than it will appear as unlimited') }}</small>
                            </p>
                        </div>

                        <div class="form-group" id="property_features_input">
                            <label class="form-label">{{ __('Number of Additional Features (Per Property)') }}
                                <span class="text-danger">{{ '*' }}</span> </label>
                            <input type="text" class="form-control" name="number_of_property_additional_features"
                                placeholder="{{ __('Enter how many adittional feature are added under a property') }}">
                            <p id="errnumber_of_property_additional_features" class="mb-0 text-danger em">
                            </p>
                            <p class="text-warning mb-0">
                                <small>{{ __('Enter 999999, than it will appear as unlimited') }}</small>
                            </p>
                        </div>

                        <div class="form-group" id="project_input">
                            <label for="products ">{{ __('Number of Projects') }} <span
                                    class="text-danger">{{ '*' }}</span></label>
                            <input id="products " type="number" class="form-control" name="number_of_projects"
                                placeholder="{{ __('Enter number of projects') }}">
                            <p id="errnumber_of_projects" class="mb-0 text-danger em"></p>
                            <p class="text-warning mb-0">
                                <small>{{ __('Enter 999999 , than it will appear as unlimited') }}</small>
                            </p>
                        </div>

                        <div class="form-group" id="project_types_input">
                            <label class="form-label">{{ __('Number of Project Types (Per Project)') }}
                                <span class="text-danger">{{ '*' }}</span> </label>
                            <input type="text" class="form-control" name="number_of_project_types"
                                placeholder="{{ __('Enter how many types are added under a project') }}">
                            <p id="errnumber_of_project_types" class="mb-0 text-danger em"></p>
                            <p class="text-warning mb-0">
                                <small>{{ __('Enter 999999, than it will appear as unlimited') }}</small>
                            </p>
                        </div>

                        <div class="form-group" id="project_gallery_input">
                            <label class="form-label">{{ __('Number of Gallery Images (Per Project)') }}
                                <span class="text-danger">{{ '*' }}</span> </label>
                            <input type="text" name="number_of_project_gallery_images" class="form-control"
                                placeholder="{{ __('Enter how many gallery images are added under a project') }}">
                            <p id="errnumber_of_project_gallery_images" class="mb-0 text-danger em"></p>
                            <p class="text-warning mb-0">
                                <small>{{ __('Enter 999999, than it will appear as unlimited') }}</small>
                            </p>
                        </div>

                        <div class="form-group" id="project_additional_input">
                            <label class="form-label">{{ __('Number of Additional Features (Per Project)') }}
                                <span class="text-danger">{{ '*' }}</span> </label>
                            <input type="text" class="form-control" name="number_of_project_additional_features"
                                placeholder="{{ __('Enter how many adittional feature are added under a propject') }}">
                            <p id="errnumber_of_project_additional_features" class="mb-0 text-danger em">
                            </p>
                            <p class="text-warning mb-0">
                                <small>{{ __('Enter 999999, than it will appear as unlimited') }}</small>
                            </p>
                        </div>

                        <div class="form-group" id="blog_input">
                            <label for="languages">{{ __('Number of Blog Posts') }} <span
                                    class="text-danger">{{ '*' }}</span> </label>
                            <input id="languages" type="number" class="form-control" name="number_of_blog_post"
                                placeholder="{{ __('Enter number of blog posts') }}">
                            <p id="errnumber_of_blog_post" class="mb-0 text-danger em"></p>
                            <p class="text-warning mb-0">
                                <small>{{ __('Enter 999999, than it will appear as unlimited') }}</small>
                            </p>
                        </div>

                        <div class="form-group" id="custom_input">
                            <label for="custom">{{ __('Number of Additional Pages') }} <span
                                    class="text-danger">{{ '*' }}</span> </label>
                            <input id="custom" type="number" class="form-control" name="number_of_additional_page"
                                placeholder="{{ __('Enter number of additional pages') }}">
                            <p id="errnumber_of_additional_page" class="mb-0 text-danger em"></p>
                            <p class="text-warning mb-0">
                                <small>{{ __('Enter 999999, than it will appear as unlimited') }}</small>
                            </p>
                        </div>

                        <div class="form-group">
                            <label class="form-label">{{ __('Featured') }} <span
                                    class="text-danger">{{ '*' }}</span> </label>
                            <div class="selectgroup w-100">
                                <label class="selectgroup-item">
                                    <input type="radio" name="featured" value="1" class="selectgroup-input">
                                    <span class="selectgroup-button">{{ __('Yes') }}</span>
                                </label>
                                <label class="selectgroup-item">
                                    <input type="radio" name="featured" value="0" class="selectgroup-input"
                                        checked>
                                    <span class="selectgroup-button">{{ __('No') }}</span>
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">{{ __('Recommended') }} <span
                                    class="text-danger">{{ '*' }}</span> </label>
                            <div class="selectgroup w-100">
                                <label class="selectgroup-item">
                                    <input type="radio" name="recommended" value="1" class="selectgroup-input">
                                    <span class="selectgroup-button">{{ __('Yes') }}</span>
                                </label>
                                <label class="selectgroup-item">
                                    <input type="radio" name="recommended" value="0" class="selectgroup-input"
                                        checked>
                                    <span class="selectgroup-button">{{ __('No') }}</span>
                                </label>
                            </div>
                        </div>




                        <div class="form-group">
                            <label class="form-label">{{ __('Trial') }} <span
                                    class="text-danger">{{ '*' }}</span> </label>
                            <div class="selectgroup w-100">
                                <label class="selectgroup-item">
                                    <input type="radio" name="is_trial" value="1" class="selectgroup-input">
                                    <span class="selectgroup-button">{{ __('Yes') }}</span>
                                </label>
                                <label class="selectgroup-item">
                                    <input type="radio" name="is_trial" value="0" class="selectgroup-input"
                                        checked>
                                    <span class="selectgroup-button">{{ __('No') }}</span>
                                </label>
                            </div>
                        </div>

                        <div class="form-group dis-none" id="trial_day">
                            <label for="trial_days">{{ __('Trial days') }} <span
                                    class="text-danger">{{ '*' }}</span> </label>
                            <input id="trial_days" type="number" class="form-control" name="trial_days"
                                placeholder="{{ __('Enter trial days') }}" value="">
                            <p id="errtrial_days" class="mb-0 text-danger em"></p>
                        </div>
                        <div class="form-group">
                            <label for="status">{{ __('Status') }} <span
                                    class="text-danger">{{ '*' }}</span> </label>
                            <select id="status" class="form-control ltr" name="status">
                                <option value="" selected disabled>{{ __('Select a status') }}</option>
                                <option value="1">{{ __('Active') }}</option>
                                <option value="0">{{ __('Deactive') }}</option>
                            </select>
                            <p id="errstatus" class="mb-0 text-danger em"></p>
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                    <button id="submitBtn" type="button" class="btn btn-primary">{{ __('Submit') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/admin/js/packages.js') }}"></script>
@endsection
