@extends('user.layout')
@php
    use App\Http\Helpers\UserPermissionHelper;
    $package = UserPermissionHelper::currentPackage($tenantId);
    if (!empty($tenantId)) {
        $permissions = UserPermissionHelper::packagePermission($tenantId);
        $permissions = json_decode($permissions, true);
    }
@endphp

@section('content')
    <div class="page-header">
        <h4 class="page-title">{{ __('Languages') }}</h4>
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
                <a href="#">{{ __('Settings') }}</a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="#">{{ __('Language') }}</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">

            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="card-title d-inline-block">{{ __('Languages') }}</div>
                        </div>
                        <div class="col-lg-3">

                        </div>
                        <div class="col-lg-4 offset-lg-1 mt-2 mt-lg-0">
                            @if (!empty($permissions) && in_array('Additional Language', $permissions))
                            <a class="btn btn-primary float-right text-white" data-toggle="modal"
                                data-target="#createModal">
                                <i class="fas fa-plus"></i>
                                {{ __('Add Language') }}
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12">
                            @if (count($languages) == 0)
                                <h3 class="text-center">{{ __('NO LANGUAGE FOUND') }}</h3>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-striped mt-3">
                                        <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">{{ __('Name') }}</th>
                                                <th scope="col">{{ __('Code') }}</th>
                                                <th scope="col">
                                                    {{ __('Appearance in Website') }}
                                                </th>
                                                <th scope="col">{{ __('Actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($languages as $key => $language)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $language->name }}</td>
                                                    <td>{{ $language->code }}</td>
                                                    <td>
                                                        @if ($language->is_default == 1)
                                                            <strong
                                                                class="badge badge-success">{{ __('Default') }}</strong>
                                                        @else
                                                            <form class="d-inline-block"
                                                                action="{{ route('user.language.default', $language->id) }}"
                                                                method="post">
                                                                @csrf
                                                                <button class="btn btn-primary btn-sm" type="submit"
                                                                    name="button">{{ __('Make Default') }}</button>
                                                            </form>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a class="btn btn-secondary btn-sm"
                                                            href="{{ route('user.language.editKeyword', $language->id) }}">
                                                            <span class="btn-label">
                                                                <i class="fas fa-edit"></i>
                                                            </span>
                                                            {{ __('Edit Keywords') }}
                                                        </a>
                                                        {{-- @if ($language->is_admin != 1) --}}
                                                        @if (!empty($permissions) && in_array('Additional Language', $permissions))
                                                            <a class="btn btn-secondary btn-sm"
                                                                href="{{ route('user.language.edit', $language->id) }}">
                                                                <span class="btn-label">
                                                                    <i class="fas fa-edit"></i>
                                                                </span>
                                                                {{ __('Edit') }}
                                                            </a>
                                                            @endif
                                                            <form class="deleteform d-inline-block"
                                                                action="{{ route('user.language.delete', $language->id) }}"
                                                                method="post">
                                                                @csrf
                                                                <button type="submit"
                                                                    class="btn btn-danger btn-sm deletebtn">
                                                                    <span class="btn-label">
                                                                        <i class="fas fa-trash"></i>
                                                                    </span>
                                                                    {{ __('Delete') }}
                                                                </button>
                                                            </form>
                                                        {{-- @endif --}}
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

    <!-- Create Language Modal -->
    @includeif('user.language.create')
@endsection
