@extends('user.layout')

@section('content')
    <div class="page-header">
        <h4 class="page-title">{{ __('Change Password') }}</h4>
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
                <a href="#">{{ __('User Management') }}</a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="#">{{ __('Registered Users') }}</a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>

            <li class="nav-item">
                <a href="#">{{ __('Change Password') }}</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card-title">{{ __('Change Password') }}</div>
                        </div>
                    </div>
                </div>

                <div class="card-body py-5">
                    <div class="row">
                        <div class="col-lg-6 offset-lg-3">
                            <form id="ajaxEditForm"
                                action="{{ route('user.user.update_password', ['id' => $userInfo->id]) }}" method="post">
                                @csrf
                                <div class="form-group">
                                    <label>{{ __('New Password') }}<span class="text-danger">{{ '*' }}</span></label>
                                    <input type="password" placeholder="{{ __('Enter your new password') }}"
                                        class="form-control" name="new_password">
                                    <p id="editErr_new_password" class="mt-1 mb-0 text-danger em"></p>
                                </div>

                                <div class="form-group">
                                    <label>{{ __('Confirm New Password') }}<span class="text-danger">{{ '*' }}</span></label>
                                    <input type="password" placeholder="{{ __('Enter confirm password') }}"
                                        class="form-control" name="new_password_confirmation">
                                    <p id="editErr_new_password_confirmation" class="mt-1 mb-0 text-danger em"></p>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="row">
                        <div class="col-12 text-center">
                            <button type="submit" id="updateBtn" class="btn btn-success">
                                {{ __('Update') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
