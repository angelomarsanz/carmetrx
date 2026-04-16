<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>{{ __('Invoice') }}</title>
  <link rel="stylesheet" href="{{ asset('assets/front/css/design-pdf.css') }}">


</head>

<body>
  <div class="main">
    <div class="invoice-container">
      <div class="invoice-header">
        <table class="heading">
          <tr>
            <td>
              <img src="{{ $logoBase64 }}" height="40" class="d-inline-block">
            </td>
            <td class="text-right strong invoice-heading">{{ __('INVOICE') }}</td>
          </tr>
        </table>
      </div>

      <div class="px-15 mb-15 clearfix tm_invoice_info_table">
        <div class="bill-to w-50 text-left float-left">
          <h6>{{ __('Bill to') }}</h6>
          <table class="smtable">
            <tr>
              <td class="small gry-color">
                <span>{{ __('Username') }}</span>
                <span>: {{ $tenant->username }}</span>
              </td>
            </tr>
            <tr>
              <td class="small gry-color">
                <span>{{ __('Email') }}</span>
                <span>: {{ $tenant->email }}</span>
              </td>
            </tr>
            <tr>
              <td class="small gry-color">
                <span>{{ __('Phone') }}</span>
                <span>: {{ $tenant->phone }}</span>
              </td>
            </tr>
          </table>
        </div>

        <div class="order-details w-50 text-right float-right">
          <h6>{{ __('Order Details') }}</h6>
          <table class="text-right">
            <tr>
              <td class="gry-color small">
                <span>{{ __('Order ID') }}</span>
                <span>: #{{ $membership->transaction_id }}</span>
              </td>
            </tr>

            @if ($membership->discount > 0)
              <tr>
                <td class="gry-color small">
                  <span>{{ __('Package Price') }}</span>
                  <span>{{ $membership->package_price == 0 ? __('Free') : $membership->package_price }}</span>
                </td>
              </tr>
            @endif

            @if ($membership->discount > 0)
              <tr>
                <td class="gry-color small">
                  <span>{{ __('Discount') . ': ' }} </span>
                  <span class="text-danger">: - {{ $membership->discount }}</span>
                </td>
              </tr>
            @endif

            <tr>
              <td class="gry-color small">
                <span>{{ __('Total') }}</span>
                <span>: {{ $membership->price }}</span>
              </td>
            </tr>
            <tr>
              <td class="gry-color small">
                <span>{{ __('Payment Method') }} </span>
                <span>: {{ __($membership->payment_method) }}</span>
              </td>
            </tr>
            <tr>
              <td class="strong gry-color small">
                <span>{{ __('Payment Status') . ': ' }}</span>
                <span class="text-success">{{ __('Complete') }}</span>
              </td>
            </tr>
            <tr>
              <td class="gry-color small">
                <span>{{ __('Order Date') }}</span>
                <span class="gry-color small">: {{ now()->format('d/m/Y') }}</span>
              </td>
            </tr>
          </table>
        </div>
      </div>

      <div class="package-info">
        <table class="text-left package-info-table table-border">
          <thead>
            <tr class="info-titles">
              <th>{{ __('Package Title') }}</th>
              <th>{{ __('Start Date') }}</th>
              <th>{{ __('Expire Date') }}</th>
              <th>{{ __('Currency') }}</th>
              <th>{{ __('Total') }}</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>{{ $packageTitle }}</td>
              <td>{{ $membership->start_date }}</td>
              <td>
                @if ($membership->is_lifetime_member)
                  {{ __('Lifetime') }}
                @else
                  {{ $membership->expire_date }}
                @endif
              </td>
              <td>{{ $membership->currency }}</td>
              <td>
                {{ $membership->price == 0 ? __('Free') : $membership->price }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <table class="mt-80">
        <tr>
          <td class="text-right regards">{{ __('Thanks & Regards') . ',' }}</td>
        </tr>
        <tr>
          <td class="text-right strong regards">{{ $websiteTitle }}</td>
        </tr>
      </table>
    </div>
  </div>
</body>

</html>
