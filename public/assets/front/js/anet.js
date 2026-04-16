"use strict";

function sendPaymentDataToAnet() {
    // Set up authorisation to access the gateway.
    var authData = {};
    authData.clientKey = clientKey;
    authData.apiLoginID = loginId;

    var cardData = {};
    cardData.cardNumber = document.getElementById("anetCardNumber").value;
    cardData.month = document.getElementById("anetExpMonth").value;
    cardData.year = document.getElementById("anetExpYear").value;
    cardData.cardCode = document.getElementById("anetCardCode").value;

    // Now send the card data to the gateway for tokenisation.
    // The responseHandler function will handle the response.
    var secureData = {};
    secureData.authData = authData;
    secureData.cardData = cardData;
    Accept.dispatchData(secureData, responseHandler);
}

// this code written by rakib for translate error message
function translateMessage(message) {
    const translations = {
        "Please provide valid credit card number.": anetCardError,
        "Please provide valid expiration year.": anetYearError,
        "Please provide valid expiration month.": anetMonthError,
        "Expiration date must be in the future.": anetExpirationDateError,
        "Please provide valid CVV.": anetCvvInvalidError,
    };

    return translations[message] || message;
}

function responseHandler(response) {

    const confirmBtn = document.getElementById('confirmBtn');
    const originalText = confirmBtnText;
    if (response.messages.resultCode === "Error") {
        var i = 0;
        let errorLists = ``;
        while (i < response.messages.message.length) {

            const errorMessage = response.messages.message[i].text;
            const translatedMessage = translateMessage(errorMessage);
            errorLists += `<li class="text-danger">${translatedMessage}</li>`;

            i = i + 1;
        }
        $("#anetErrors").show();
        $("#anetErrors").html(errorLists);
        // Reset button on Authorize.net errors
        confirmBtn.innerHTML = originalText;
        confirmBtn.disabled = false;
    } else {
        paymentFormUpdate(response.opaqueData);
    }
}

function paymentFormUpdate(opaqueData) {
    document.getElementById("opaqueDataDescriptor").value = opaqueData.dataDescriptor;
    document.getElementById("opaqueDataValue").value = opaqueData.dataValue;
    document.getElementById("my-checkout-form").submit();
}

$(document).ready(function() {
    $("#my-checkout-form").on('submit', function(e) {
        e.preventDefault();
        let val = $("#payment-gateway").val();
        if (val == 'Authorize.net') {
            sendPaymentDataToAnet();
        } else {
            $(this).unbind(' ').submit();
        }
    });
});
