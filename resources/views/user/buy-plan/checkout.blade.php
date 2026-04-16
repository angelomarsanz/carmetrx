@extends('user.layout')
@includeIf('user.partials.rtl-style')
@section('content')
    @if ($message = Session::get('error'))
        <div class="alert alert-danger alert-block">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <strong>{{ $message }}</strong>
        </div>
    @endif
    @if (!empty($membership) && ($membership->package->term == 'lifetime' || $membership->is_trial == 1))
        <div class="alert bg-warning alert-warning text-white text-center">
            <h3> {{ __('If you purchase this package') }} <strong class="text-dark">({{ $package->title }})</strong>,
                {{ __('then your current package') }} <strong class="text-dark">({{ $membership->package->title }}
                    @if ($membership->is_trial == 1) <span
                            class="badge badge-secondary">{{ __('Trial') }} </span>@endif)
                </strong> {{ __('will be replaced immediately') }} </h3>
        </div>
    @endif
    <div class="row justify-content-center align-items-center mb-1">
        <div class="col-md-1 pl-md-0">
        </div>

        <div class="col-md-6 pl-md-0 pr-md-0">
            <div class="card card-pricing card-pricing-focus card-secondary">
                <form id="my-checkout-form" action="{{ route('user.plan.checkout') }}" method="post"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="package_id" value="{{ $package->id }}">
                    <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                    <input type="hidden" name="payment_method" id="payment" value="{{ old('payment_method') }}">
                    <input type="hidden" id="selected-gateway" value="{{ old('payment_method') }}">
                    <div class="card-header">
                        <h4 class="card-title">{{ $package->title }}</h4>
                        <div class="card-price">
                            <span class="price">{{ $package->price == 0 ? 'Free' : format_price($package->price) }}</span>
                            <span class="text">/{{ __($package->term) }}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <ul class="specification-list">
                            <li>
                                <span class="name-specification">{{ __('Membership') }}</span>
                                <span class="status-specification">{{ __('Yes') }}</span>
                            </li>
                            <li>
                                <span class="name-specification">{{ __('Start Date') }}</span>
                                @if (
                                    (!empty($previousPackage) && $previousPackage->term == 'lifetime') ||
                                        (!empty($membership) && $membership->is_trial == 1))
                                    <input type="hidden" name="start_date"
                                        value="{{ \Illuminate\Support\Carbon::yesterday()->format('d-m-Y') }}">
                                    <span
                                        class="status-specification">{{ \Illuminate\Support\Carbon::today()->format('d-m-Y') }}</span>
                                @else
                                    <input type="hidden" name="start_date"
                                        value="{{ \Illuminate\Support\Carbon::parse($membership->expire_date ?? \Carbon\Carbon::yesterday())->addDay()->format('d-m-Y') }}">
                                    <span
                                        class="status-specification">{{ \Illuminate\Support\Carbon::parse($membership->expire_date ?? \Carbon\Carbon::yesterday())->addDay()->format('d-m-Y') }}</span>
                                @endif
                            </li>
                            <li>
                                <span class="name-specification">{{ __('Expire Date') }}</span>
                                <span class="status-specification">
                                    @if ($package->term == 'monthly')
                                        @if (
                                            (!empty($previousPackage) && $previousPackage->term == 'lifetime') ||
                                                (!empty($membership) && $membership->is_trial == 1))
                                            {{ \Illuminate\Support\Carbon::parse(now())->addMonth()->format('d-m-Y') }}
                                            <input type="hidden" name="expire_date"
                                                value="{{ \Illuminate\Support\Carbon::parse(now())->addMonth()->format('d-m-Y') }}">
                                        @else
                                            {{ \Illuminate\Support\Carbon::parse($membership->expire_date ?? now())->addMonth()->format('d-m-Y') }}
                                            <input type="hidden" name="expire_date"
                                                value="{{ \Illuminate\Support\Carbon::parse($membership->expire_date ?? now())->addMonth()->format('d-m-Y') }}">
                                        @endif
                                    @elseif($package->term == 'lifetime')
                                        {{ __('Lifetime') }}
                                        <input type="hidden" name="expire_date"
                                            value="{{ \Illuminate\Support\Carbon::create(9999, 12, 12)->format('d-m-Y') }}">
                                    @else
                                        @if (
                                            (!empty($previousPackage) && $previousPackage->term == 'lifetime') ||
                                                (!empty($membership) && $membership->is_trial == 1))
                                            {{ \Illuminate\Support\Carbon::parse(now())->addYear()->format('d-m-Y') }}
                                            <input type="hidden" name="expire_date"
                                                value="{{ \Illuminate\Support\Carbon::parse(now())->addYear()->format('d-m-Y') }}">
                                        @else
                                            {{ \Illuminate\Support\Carbon::parse($membership->expire_date ?? now())->addYear()->format('d-m-Y') }}
                                            <input type="hidden" name="expire_date"
                                                value="{{ \Illuminate\Support\Carbon::parse($membership->expire_date ?? now())->addYear()->format('d-m-Y') }}">
                                        @endif
                                    @endif
                                </span>
                            </li>
                            <li>
                                <span class="name-specification">{{ __('Total Cost') }}</span>
                                <input type="hidden" name="price" value="{{ $package->price }}">
                                <span class="status-specification">
                                    {{ $package->price == 0 ? __('Free') : format_price($package->price) }}
                                </span>
                            </li>
                            @if ($package->price != 0)
                                <li>
                                    <div class="form-group px-0">
                                        <label class="text-white">{{ __('Payment Method') }}</label>
                                        <select name="payment_method" class="form-control input-solid" id="payment-gateway"
                                            required>
                                            <option value="" disabled selected>
                                                {{ __('Select a Payment Method') }}
                                            </option>
                                            @foreach ($payment_methods as $payment_method)
                                                <option value="{{ $payment_method->name }}"
                                                    {{ old('payment_method') == $payment_method->name ? 'selected' : '' }}>
                                                    {{ __($payment_method->name) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </li>
                            @endif
                            @php
                            $showStripeTab = $errors->has('cardNumber') || 
                                            $errors->has('cardCVC') || 
                                            $errors->has('month') || 
                                            $errors->has('year');
                                            
                            @endphp

                            <div class="gateway-details" id="tab-stripe" style="{{ $showStripeTab ? '' : 'display: none;' }}">
                                <div class="row pt-3 text-left">

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ __('Card Number') }} *</label>

                                            <input type="text" class="form-control" name="cardNumber"
                                                placeholder="{{ __('Card Number') }}" autocomplete="off"
                                                oninput="validateCard(this.value);" {{ !$showStripeTab ? 'disabled' : '' }} />

                                            @if ($errors->has('cardNumber'))
                                                <p class="text-danger mb-0">{{ $errors->first('cardNumber') }}</p>
                                            @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('CVC') }} *</label>

                                        <input type="text" class="form-control" placeholder="{{ __('CVC') }}"
                                            name="cardCVC" oninput="validateCVC(this.value);" {{ !$showStripeTab ? 'disabled' : '' }}>

                                        @if ($errors->has('cardCVC'))
                                            <p class="text-danger mb-0">{{ $errors->first('cardCVC') }}</p>
                                        @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Month') }} *</label>

                                    <input type="text" class="form-control" placeholder="{{ __('Month') }}"
                                        name="month" {{ !$showStripeTab ? 'disabled' : '' }}>

                                    @if ($errors->has('month'))
                                        <p class="text-danger mb-0">{{ $errors->first('month') }}</p>
                                    @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">

                                <label>{{ __('Year') }} *</label>

                                <input type="text" class="form-control" placeholder="{{ __('Year') }}"
                                    name="year" {{ !$showStripeTab ? 'disabled' : '' }}>

                                @if ($errors->has('year'))
                                    <p class="text-danger mb-0">{{ $errors->first('year') }}</p>
                                @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- START: Authorize.net Card Details Form --}}
            <div class="gateway-details dis-none" id="tab-anet">
                <div class="row pt-3">
                    <div class="col-lg-6">
                        <div class="form-group mb-3">
                            <input class="form-control" type="text" id="anetCardNumber"
                                placeholder="{{ __('Card Number') }}" disabled />
                        </div>
                    </div>
                    <div class="col-lg-6 mb-3">
                        <div class="form-group">
                            <input class="form-control" type="text" id="anetExpMonth"
                                placeholder="{{ __('Expire Month') }}" disabled />
                        </div>
                    </div>
                    <div class="col-lg-6 ">
                        <div class="form-group">
                            <input class="form-control" type="text" id="anetExpYear"
                                placeholder="{{ __('Expire Year') }}" disabled />
                        </div>
                    </div>
                    <div class="col-lg-6 ">
                        <div class="form-group">
                            <input class="form-control" type="text" id="anetCardCode"
                                placeholder="{{ __('Card Code') }}" disabled />
                        </div>
                    </div>
                    <input type="hidden" name="opaqueDataValue" id="opaqueDataValue" disabled />
                    <input type="hidden" name="opaqueDataDescriptor" id="opaqueDataDescriptor"
                        disabled />
                    <ul id="anetErrors" class="dis-none"></ul>
                </div>
            </div>
            {{-- END: Authorize.net Card Details Form --}}

            <div id="instructions" class="text-left"></div>
            <input type="hidden" name="is_receipt" value="0" id="is_receipt">
            <span id="receipt-error-message" data-msg="{{ $errors->first('receipt') }}"></span>
        </ul>

    </div>
    <div class="card-footer">
        <button class="btn btn-light btn-block" type="submit"><b>{{ __('Checkout Now') }}</b></button>
    </div>
</form>
</div>
</div>
<div class="col-md-1 pr-md-0"></div>
</div>
@endsection

@section('scripts')
@php
        $anetCardError = __('Please provide valid credit card number');
        $anetYearError = __('Please provide valid expiration year');
        $anetMonthError = __('Please provide valid expiration month');
        $anetExpirationDateError = __('Expiration date must be in the future');
        $anetCvvInvalidError = __('Please provide valid CVV');
@endphp
<script>
    "use strict";
    var couponRoute = "{{ route('front.membership.coupon') }}";
    var receiptTxt = "{{ __('Receipt') }}";
    var confirmBtnText = '{{ __("Confirm") }}';
    var imageExtMsg = "{{ __('Receipt image must be .jpg / .jpeg / .png') }}";
    var succesMsg = "{{ __('Coupon applied successfully') . '!' }}";
    var packageId = {{ $package->id }};
    var ogateways = @json($offline) ;
    var oinstructions = "{{ route('front.payment.instructions') }}";
</script>
<script src="{{ asset('assets/front/js/checkout.js') }}"></script>

{{-- START: Authorize.net Scripts --}}
@php
    $anet = App\Models\PaymentGateway::find(20);
    $anerInfo = $anet->convertAutoData();
    $anetTest = $anerInfo['sandbox_check'];

    if ($anetTest == 1) {
        $anetSrc = 'https://jstest.authorize.net/v1/Accept.js';
    } else {
        $anetSrc = 'https://js.authorize.net/v1/Accept.js';
    }
@endphp
<script>
    "use strict";
    var clientKey = "{{ $anerInfo['public_key'] }}";
    var loginId = "{{ $anerInfo['login_id'] }}";
    let anetCardError = "{{ $anetCardError }}";
    let anetYearError = "{{ $anetYearError }}";
    let anetMonthError = "{{ $anetMonthError }}";
    let anetExpirationDateError = "{{ $anetExpirationDateError }}";
    let anetCvvInvalidError = "{{ $anetCvvInvalidError }}";
</script>
<script type="text/javascript" src="{{ $anetSrc }}" charset="utf-8"></script>
<script src="{{ asset('assets/front/js/anet.js') }}"></script>
{{-- END: Authorize.net Scripts --}}
@endsection
