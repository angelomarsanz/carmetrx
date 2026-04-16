@extends('user.layout')

@section('content')
    <div class="page-header">
        <h4 class="page-title">{{ __('Registered Users') }}</h4>
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
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-3">
                            <div class="card-title">{{ __('All Users') }}</div>

                        </div>
                        <div class="col-lg-3 offset-lg-2">
                        </div>
                        <div class="col-lg-4 ">
                            <button class="btn btn-danger btn-sm float-right d-none bulk-delete mr-2 ml-3 mt-1"
                                data-href="{{ route('user.bulk_delete_user') }}">
                                <i class="flaticon-interface-5"></i> {{ __('Delete') }}
                            </button>

                            <form class="float-right" action="{{ route('user.registered_users') }}" method="GET">
                                <input name="info" type="text" class="form-control "
                                    placeholder="{{ __('Search by username or email id') }}"
                                    value="{{ !empty(request()->input('info')) ? request()->input('info') : '' }}">
                            </form>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12">
                            @if (count($users) == 0)
                                <h3 class="text-center">{{ __('NO USER FOUND') }}</h3>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-striped mt-3">
                                        <thead>
                                            <tr>
                                                <th scope="col">
                                                    <input type="checkbox" class="bulk-check" data-val="all">
                                                </th>
                                                <th scope="col">{{ __('Username') }}</th>
                                                <th scope="col">{{ __('Email ID') }}</th>
                                                <th scope="col">{{ __('Email Status') }}
                                                </th>
                                                <th scope="col">{{ __('Phone') }}</th>
                                                <th scope="col">
                                                    {{ __('Account Status') }}</th>
                                                <th scope="col">{{ __('Actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($users as $user)
                                                <tr>
                                                    <td>
                                                        <input type="checkbox" class="bulk-check"
                                                            data-val="{{ $user->id }}">
                                                    </td>
                                                    <td>{{ $user->username }}</td>
                                                    <td>{{ $user->email }}</td>
                                                    <td>
                                                        <form id="emailForm{{ $user->id }}" class="d-inline-block"
                                                            action="{{ route('user.email') }}" method="post">
                                                            @csrf
                                                            <select
                                                                class="form-control form-control-sm {{ !empty($user->email_verified_at) ? 'bg-success' : 'bg-danger' }}"
                                                                name="email_verified"
                                                                onchange="document.getElementById('emailForm{{ $user->id }}').submit();">
                                                                <option value="1"
                                                                    {{ !empty($user->email_verified_at) ? 'selected' : '' }}>
                                                                    {{ __('Verified') }}</option>
                                                                <option value="0"
                                                                    {{ empty($user->email_verified_at) ? 'selected' : '' }}>
                                                                    {{ __('Unverified') }}
                                                                </option>
                                                            </select>
                                                            <input type="hidden" name="user_id"
                                                                value="{{ $user->id }}">
                                                        </form>
                                                    </td>
                                                    <td>{{ empty($user->contact_number) ? '-' : $user->contact_number }}
                                                    </td>
                                                    <td>
                                                        <form id="accountStatusForm-{{ $user->id }}"
                                                            class="d-inline-block"
                                                            action="{{ route('user.user.update_account_status', ['id' => $user->id]) }}"
                                                            method="post">
                                                            @csrf
                                                            <select
                                                                class="form-control form-control-sm {{ $user->status == 1 ? 'bg-success' : 'bg-danger' }}"
                                                                name="account_status"
                                                                onchange="document.getElementById('accountStatusForm-{{ $user->id }}').submit()">
                                                                <option value="1"
                                                                    {{ $user->status == 1 ? 'selected' : '' }}>
                                                                    {{ __('Active') }}
                                                                </option>
                                                                <option value="2"
                                                                    {{ $user->status == 0 ? 'selected' : '' }}>
                                                                    {{ __('Deactive') }}
                                                                </option>
                                                            </select>
                                                        </form>
                                                    </td>
                                                    <td>
                                                        <div class="dropdown">
                                                            <button class="btn btn-secondary dropdown-toggle btn-sm"
                                                                type="button" id="dropdownMenuButton"
                                                                data-toggle="dropdown" aria-haspopup="true"
                                                                aria-expanded="false">
                                                                {{ __('Select') }}
                                                            </button>

                                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                                <a href="{{ route('user.user_details', ['id' => $user->id]) }}"
                                                                    class="dropdown-item">
                                                                    {{ __('Details') }}
                                                                </a>

                                                                <a href="{{ route('user.user.change_password', ['id' => $user->id]) }}"
                                                                    class="dropdown-item">
                                                                    {{ __('Change Password') }}
                                                                </a>


                                                                <form class="deleteform d-block" target="_blank"
                                                                    action="{{ route('user.secretLogin') }}"
                                                                    method="post">
                                                                    @csrf
                                                                    <input type="hidden" name="user_id"
                                                                        value="{{ $user->id }}">
                                                                    <button type="submit" class="dropdown-item">
                                                                        {{ __('Secret Login') }}
                                                                    </button>
                                                                </form>

                                                                <form class="deleteform d-block"
                                                                    action="{{ route('user.user.delete', ['id' => $user->id]) }}"
                                                                    method="post">
                                                                    @csrf
                                                                    <button type="submit" class="deletebtn dropdown-item">
                                                                        {{ __('Delete') }}
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </div>
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

                <div class="card-footer">
                    <div class="row">
                        <div class="d-inline-block mx-auto">
                            {{ $users->appends(['info' => request()->input('info')])->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
