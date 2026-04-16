"use strict";

$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function applyCoupon() {
        let fd = new FormData();
        let coupon = $("input[name='coupon']").val();
        fd.append('coupon', coupon);
        fd.append('package_id', packageId);

        $.ajax({
            url: couponRoute,
            type: 'POST',
            data: fd,
            contentType: false,
            processData: false,
            success: function (data) {
                if (data == 'success') {
                    $("#couponReload").load(location.href + " #couponReload", function () {
                        $('select').niceSelect();
                    });
                    toastr['success'](succesMsg);
                } else {
                    toastr['warning'](data);
                }
            }
        });
    }

    $("input[name='coupon']").on('keypress', function (e) {
        if (e.which === 13) {
            e.preventDefault();
            applyCoupon();
        }
    });

    $(".coupon-apply").on('click', function () {
        applyCoupon();
    });

    $(document).on('change', "#payment-gateway", function () {

        let offline = ogateways;
        let data = [];
        offline.map(({ id, name }) => {
            data.push(name);
        });

        let paymentMethod = $("#payment-gateway").val();
        $("input[name='payment_method']").val(paymentMethod);

        $(".gateway-details").hide();
        $(".gateway-details input").attr('disabled', true);

        // Clear Stripe-specific elements
        if (paymentMethod !== 'Stripe') {
            // Clear Stripe input values
            $("#tab-stripe input").val('');

            // Clear Stripe validation errors
            $("#tab-stripe .text-danger").text('');

            // If using Stripe Elements, you might need to:
            if (typeof cardElement !== 'undefined') {
                cardElement.clear(); // Clear the Stripe Elements card input
            }
        }

        if (paymentMethod !== 'Authorize.net') {
            $("#anetErrors").empty().addClass('dis-none');
            $("#tab-anet input").val('');
        }
        if (paymentMethod === 'Stripe') {
            $("#tab-stripe").show();
            $("#tab-stripe input").removeAttr('disabled');
        }
        if (paymentMethod === 'Iyzico') {
            $("#IdentityNumber").removeClass("d-none");

        }else{
            $("#IdentityNumber").addClass("d-none");
        }

        if (paymentMethod === 'Authorize.net') {
            $("#tab-anet").show();
            $("#tab-anet input").removeAttr('disabled');
        }

        if (data.indexOf(paymentMethod) !== -1) {
            let formData = new FormData();
            formData.append('name', paymentMethod);
            $.ajax({
                url: oinstructions,
                type: 'POST',
                contentType: false,
                processData: false,
                cache: false,
                data: formData,
                success: function (data) {
                    let instruction = $("#instructions");
                    let instructions = `<div class="gateway-desc">${data.instructions}</div>`;
                    let description = data.description ? `<div class="gateway-desc"><p>${data.description}</p></div>` : `<div></div>`;

                    // Get receipt error message from hidden span
                    let receiptErrorMsg = $("#receipt-error-message").data('msg') || '';
                    let receiptError = receiptErrorMsg
                        ? `<span class="error"><strong>${receiptErrorMsg}</strong></span>`
                        : '';

                    let receipt = `<div class="form-element mb-2">
                            <label>${receiptTxt}<span>*</span></label><br>
                            <input type="file" name="receipt" class="file-input" required>
                            <p class="mb-0 text-warning">** ${imageExtMsg}</p>
                             ${receiptError}
                          </div>`;

                    if (data.is_receipt == 1) {
                        $("#is_receipt").val(1);
                        instruction.html(instructions + description + receipt);
                    } else {
                        $("#is_receipt").val(0);
                        instruction.html(instructions + description);
                        $("input[name='receipt']").remove();
                    }
                    $('#instructions').fadeIn();
                },
                error: function (data) { }
            });
        } else {
            $('#instructions').fadeOut();
            $("#is_receipt").val(0);
            $("input[name='receipt']").remove();
        }
    });

    /** ========== STRIPE SETUP ========== */
    let stripe, cardElement;
    if (typeof stripe_key !== 'undefined' && stripe_key) {
        stripe = Stripe(stripe_key);
        const elements = stripe.elements();
        cardElement = elements.create('card', {
            style: {
                base: {
                    iconColor: '#454545',
                    color: '#454545',
                    fontWeight: '500',
                    fontSmoothing: 'antialiased',
                    fontSize: '16px',
                    '::placeholder': {
                        color: '#aab7c4'
                    }
                }
            }
        });
        cardElement.mount('#stripe-element');
    }

    /** ========== FORM SUBMIT HANDLER ========== */
    $('#payment-form').on('submit', function (e) {
        let selected = $('#payment-gateway').val();
        if (selected === 'Stripe') {
            e.preventDefault();

            stripe.createToken(cardElement).then(function (result) {
                if (result.error) {
                    $('#stripe-errors').text(result.error.message);
                } else {
                    const tokenInput = $('<input type="hidden" name="stripeToken">').val(result.token.id);
                    $('#payment-form').append(tokenInput);
                    $('#payment-form')[0].submit();
                }
            });
        } else if (selected === 'Authorize.net') {
            e.preventDefault();
            sendPaymentDataToAnet();
        }
    });

});

$(document).ready(function () {
    let gateway = $("#selected-gateway").val();
    if (gateway) {
        $("#payment-gateway").val(gateway).trigger('change');
    }
});
