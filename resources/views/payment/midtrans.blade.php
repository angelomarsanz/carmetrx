<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ __('Event Booking via Midtrans') }}</title>
</head>

<body>
    <button class="btn btn-primary" id="pay-button" style="display: none">{{ __('Pay Now') }}</button>

    <script src="{{ asset('assets/admin/js/core/jquery-3.7.1.min.js') }}"></script>
    @if ($is_production == 0)
        <script src="https://app.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
    @else
        <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}">
        </script>
    @endif

    <script>
        var baseUrl = "{{ route('front.index') }}";
        let notifyUrl = "{{ route('membership.midtrans.success') }}";
        let cancleUrl = "{{ route('membership.midtrans.payment.cancel') }}";
        $(document).ready(function() {
            $('#pay-button').trigger('click');
        })
        const payButton = document.querySelector('#pay-button');
        payButton.addEventListener('click', function(e) {
            e.preventDefault();

            snap.pay('{{ $snapToken }}', {
                // Optional
                onSuccess: function(result) {
                    /* You may add your own js here, this is just example */
                    // document.getElementById('result-json').innerHTML += JSON.stringify(result, null, 2);
                    let orderId = result.order_id;
                    window.location.href = notifyUrl + '/' + orderId;
                },
                // Optional
                onPending: function(result) {
                    /* You may add your own js here, this is just example */
                    // document.getElementById('result-json').innerHTML += JSON.stringify(result, null, 2);
                    window.location.href = cancleUrl;
                },
                // Optional
                onError: function(result) {
                    /* You may add your own js here, this is just example */
                    // document.getElementById('result-json').innerHTML += JSON.stringify(result, null, 2);
                    window.location.href = cancleUrl;
                }
            });
        });
    </script>
</body>

</html>
