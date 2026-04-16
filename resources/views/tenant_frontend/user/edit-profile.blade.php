@extends('tenant_frontend.layout')

@php $title = $keywords['Edit Profile']; @endphp

@section('pageHeading')
    {{ $title }}
@endsection

@section('content')
    @includeIf('tenant_frontend.partials.breadcrumb', [
        'breadcrumb' => $breadcrumb,
        'title' => $title,
        'subtitle' => $title,
    ])

    <div class="user-dashboard pt-100 pb-60">
        <div class="container">
            <div class="row gx-xl-5">
                @includeIf('tenant_frontend.user.side-navbar')

                <div class="col-lg-9">

                    <div class="user-profile-details mb-40">
                        <div class="account-info radius-md">
                            <div class="title">
                                <h4>{{ $keywords['Edit Your Profile'] }}</h4>
                            </div>
                            <div class="edit-info-area">
                                <form action="{{ route('frontend.user.update_profile', getParam()) }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf

                                    <div class="upload-img">
                                        <div class="file-upload-area">
                                            <div class="file-edit">
                                                <input type='file' id="imageUpload" name="image" />
                                                <label for="imageUpload"></label>
                                            </div>
                                            <div class="file-preview">
                                                <div id="imagePreview" class="lazyload bg-img"
                                                    src="{{ asset($authUser->image) }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="errorMsg"></div>

                                    <div class="row">
                                        <div class="col-lg-6 ">
                                            <div class="form-group mb-30">
                                                <label for=""
                                                    class="mb-1">{{ $keywords['First Name'] . '*' }}</label>
                                                <input type="text" class="form-control"
                                                    placeholder="{{ $keywords['First Name'] }}" name="first_name"
                                                    value="{{ old('first_name', $authUser->first_name) }}">
                                                @error('first_name')
                                                    <p class="text-danger mt-1">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-lg-6  ">
                                            <div class="form-group mb-30">
                                                <label for="" class="mb-1">{{ $keywords['Last Name'] . '*' }}
                                                </label>
                                                <input type="text" class="form-control"
                                                    placeholder="{{ $keywords['Last Name'] }}" name="last_name"
                                                    value="{{ old('last_name', $authUser->last_name) }}">
                                                @error('last_name')
                                                    <p class="text-danger mt-1">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-lg-6  ">
                                            <div class="form-group mb-30">
                                                <label for="" class="mb-1">{{ $keywords['Username'] }}
                                                </label>
                                                <input type="text" readonly class="form-control"
                                                    placeholder="{{ $keywords['Last Name'] }}"
                                                    value="{{ $authUser->username }}">

                                            </div>
                                        </div>

                                        <div class="col-lg-6 ">
                                            <div class="form-group mb-30">
                                                <label for="" class="mb-1">{{ $keywords['Email Address'] . '*' }}
                                                </label>
                                                <input type="email" class="form-control"
                                                    placeholder="{{ $keywords['Email Address'] }}"
                                                    value="{{ $authUser->email }}" readonly>
                                            </div>
                                        </div>

                                        <div class="col-lg-6 ">
                                            <div class="form-group mb-30">
                                                <label for="" class="mb-1">{{ $keywords['Phone Number'] . '*' }}
                                                </label>
                                                <input type="text" class="form-control"
                                                    placeholder="{{ $keywords['Phone Number'] }}" name="phone_number"
                                                    value="{{ old('phone_number', $authUser->contact_number) }}">
                                                @error('phone_number')
                                                    <p class="text-danger mt-1">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-lg-6 ">
                                            <div class="form-group mb-30">
                                                <label for="" class="mb-1">{{ $keywords['Address'] . '*' }}
                                                </label>
                                                <input class="form-control" type="text" name="address"
                                                    placeholder="{{ $keywords['Address'] }}"
                                                    value="{{ old('address', $authUser->address) }}">
                                                @error('address')
                                                    <p class="text-danger mt-1">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-lg-6 ">
                                            <div class="form-group mb-30">
                                                <label for="" class="mb-1">{{ $keywords['City'] . '*' }}
                                                </label>
                                                <input type="text" class="form-control"
                                                    placeholder="{{ $keywords['City'] }}" name="city"
                                                    value="{{ old('city', $authUser->city) }}">
                                                @error('city')
                                                    <p class="text-danger mt-1">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-lg-6 mb-4">
                                            <div class="form-group mb-30">
                                                <label for="" class="mb-1">{{ $keywords['State'] . '*' }}
                                                </label>
                                                <input type="text" class="form-control"
                                                    placeholder="{{ $keywords['State'] }}" name="state"
                                                    value="{{ old('state', $authUser->state) }}">
                                                @error('state')
                                                    <p class="text-danger mt-1">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-lg-6">
                                            <div class="form-group mb-30">
                                                <label for="" class="mb-1">{{ $keywords['Country'] . '*' }}
                                                </label>
                                                <input type="text" class="form-control"
                                                    placeholder="{{ $keywords['Country'] }}" name="country"
                                                    value="{{ old('country', $authUser->country) }}">
                                                @error('country')
                                                    <p class="text-danger mt-1">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-lg-12">
                                            <div class="form-button">
                                                <button class="btn btn-lg btn-primary">{{ $keywords['Submit'] }}</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        </section>
    @endsection
