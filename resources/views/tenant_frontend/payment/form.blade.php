@extends('tenant_frontend.layout')

@php $pageTitle = __('Pay Now'); @endphp

@section('pageHeading')
    {{ $pageTitle }}
@endsection

@section('content')
    @includeIf('tenant_frontend.partials.breadcrumb', ['breadcrumb' => $breadcrumb, 'title' => $pageTitle])

    <section class="service-checkout-area pt-120 pb-120">
        <div class="container">
            {{-- show error message for attachment (Offline) --}}
            @error('attachment')
                <div class="row mb-3">
                    <div class="col">
                        <div class="alert alert-danger alert-block">
                            <strong>{{ $message }}</strong>
                            <button type="button" class="close" data-dismiss="alert">×</button>
                        </div>
                    </div>
                </div>
            @enderror

            <form action="{{ route('frontend.pay', getParam()) }}" method="POST" enctype="multipart/form-data"
                id="payment-form">
                @csrf
                <div class="row">
                    <div class="col-12 mb-4">
                        <div class="form_group">
                            <label>{{ $keywords['pay_via'] . '*' }}</label>
                            <select class="form_control" name="gateway">
                                <option selected disabled>{{ $keywords['select_a_payment_gateway'] }}</option>

                                @if (count($onlineGateways) > 0)
                                    @foreach ($onlineGateways as $onlineGateway)
                                        <option value="{{ $onlineGateway->keyword }}" data-gateway_type="online">
                                            {{ $onlineGateway->name }}
                                        </option>
                                    @endforeach
                                @endif

                                @if (count($offlineGateways) > 0)
                                    @foreach ($offlineGateways as $offlineGateway)
                                        <option value="{{ $offlineGateway->id }}" data-gateway_type="offline"
                                            data-has_attachment="{{ $offlineGateway->is_receipt }}">
                                            {{ $offlineGateway->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                </div>

                <div style="@if (
                    $errors->has('card_number') ||
                        $errors->has('cvc_number') ||
                        $errors->has('expiry_month') ||
                        $errors->has('expiry_year')) display: block; @else display: none; @endif" id="stripe-form">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="form_group">
                                <label>{{ __('Card Number') . '*' }}</label>
                                <input type="text" class="form_control" name="card_number" autocomplete="off"
                                    oninput="checkCard(this.value)"
                                    placeholder="{{ $keywords['enter_your_card_number'] }}">
                                <p class="mt-1 text-danger" id="card-error"></p>
                                @error('card_number')
                                    <p class="mt-1 text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="form_group">
                                <label>{{ __('CVC Number') . '*' }}</label>
                                <input type="text" class="form_control" name="cvc_number" autocomplete="off"
                                    oninput="checkCVC(this.value)" placeholder="{{ $keywords['enter_CVC_number'] }}">
                                <p class="mt-1 text-danger" id="cvc-error"></p>
                                @error('cvc_number')
                                    <p class="mt-1 text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="form_group">
                                <label>{{ __('Expiry Month') . '*' }}</label>
                                <input type="text" class="form_control" name="expiry_month"
                                    placeholder="{{ $keywords['enter_expiry_month'] }}">
                                @error('expiry_month')
                                    <p class="mt-1 text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="form_group">
                                <label>{{ __('Expiry Year') . '*' }}</label>
                                <input type="text" class="form_control" name="expiry_year"
                                    placeholder="{{ $keywords['enter_expiry_year'] }}">
                                @error('expiry_year')
                                    <p class="mt-1 text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div style="display: none;" id="authorizenet-form">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="form_group">
                                <label>{{ __('Card Number') . '*' }}</label>
                                <input type="text" class="form_control" id="cardNumber" autocomplete="off"
                                    placeholder="{{ $keywords['enter_your_card_number'] }}">
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="form_group">
                                <label>{{ __('Card Code') . '*' }}</label>
                                <input type="text" class="form_control" id="cardCode" autocomplete="off"
                                    placeholder="{{ $keywords['enter_card_code'] }}">
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="form_group">
                                <label>{{ __('Expiry Month') . '*' }}</label>
                                <input type="text" class="form_control" id="expMonth"
                                    placeholder="{{ $keywords['enter_expiry_month'] }}">
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="form_group">
                                <label>{{ __('Expiry Year') . '*' }}</label>
                                <input type="text" class="form_control" id="expYear"
                                    placeholder="{{ $keywords['enter_expiry_year'] }}">
                            </div>
                        </div>

                        <input type="hidden" name="opaqueDataValue" id="opaqueDataValue">
                        <input type="hidden" name="opaqueDataDescriptor" id="opaqueDataDescriptor">

                        <ul id="anetErrors" style="display: none; margin-left: 30px;"></ul>
                    </div>
                </div>

                @if (count($offlineGateways) > 0)
                    <div class="row">
                        <div class="col-12">
                            @foreach ($offlineGateways as $offlineGateway) 
                                @if ($offlineGateway->is_receipt == 1)
                                    <div class="form_group mb-3" id="{{ 'gateway-attachment-' . $offlineGateway->id }}"
                                        style="display: none;">
                                        <label>{{ $keywords['attachment'] }}</label>
                                        <input type="file" name="attachment">
                                    </div>
                                @endif

                                @if (!is_null($offlineGateway->short_description))
                                    <div class="form_group mb-3" id="{{ 'gateway-description-' . $offlineGateway->id }}"
                                        style="display: none;">
                                        <label>{{ $keywords['description'] }}</label>
                                        <p>{{ $offlineGateway->short_description }}</p>
                                    </div>
                                @endif

                                @if (!is_null($offlineGateway->instructions))
                                    <div class="form_group mb-3" id="{{ 'gateway-instructions-' . $offlineGateway->id }}"
                                        style="display: none;">
                                        <label>{{ $keywords['instructions'] }}</label>
                                        {!! replaceBaseUrl($offlineGateway->instructions, 'summernote') !!}
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="row text-center mt-3">
                    <div class="col-12">
                        <button class="main-btn" id="payment-form-btn">
                            {{ $keywords['pay'] }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection

@section('script')
    <script type="text/javascript" src="https://js.stripe.com/v2/"></script>

    <script type="text/javascript">
        const clientKey = '{{ $anetClientKey }}';
        const loginId = '{{ $anetLoginId }}';
    </script>

    <script type="text/javascript" src="{{ $anetSource }}" charset="utf-8"></script>

    <script type="text/javascript" src="{{ asset('assets/tenant-front/js/service.js') }}"></script>
@endsection
