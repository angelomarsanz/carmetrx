@extends('tenant_frontend.layout')

@section('pageHeading')
    {{ $keywords['payment_success'] }}
@endsection

@section('content')
    @includeIf('tenant_frontend.partials.breadcrumb', ['breadcrumb' => $breadcrumb, 'title' => $keywords['success']])

    <!-- Start Purchase Success Section -->
    <div class="purchase-message">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="purchase-success">
                        <div class="icon text-success"><i class="far fa-check-circle"></i></div>
                        <h2>{{ $keywords['success'] }}</h2>

                        @if ($payVia == 'online')
                            <p>{{ $keywords['your_transaction_was_successful']}}</p>
                            <p>{{ $keywords['We_have_sent_you_a_mail_with_an_invoice'] }}</p>
                        @elseif ($payVia == 'offline')
                            <p>{{ __('Your transaction request was received and sent for review') . '.' }}</p>
                            <p>{{ __('We answer every request as quickly as we can, usually within 24–48 hours') . '.' }}
                            </p>
                        @else
                            <p>{{ __('Thank you for writing to us') . '.' }}</p>
                            <p>{{ __('We have received your order and, will get back to you as soon as possible') . '.' }}
                            </p>
                        @endif

                        <p class="mt-4">{{ $keywords['thank_you'] }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Purchase Success Section -->
@endsection

@if (url()->current() == route('frontend.shop.purchase_product.complete', getParam()))
    @section('script')
        <script type="text/javascript">
            sessionStorage.removeItem('calculatedTax');
            sessionStorage.removeItem('grandTotal');
            sessionStorage.removeItem('newSubtotal');
            sessionStorage.removeItem('discount');
        </script>
    @endsection
@endif
