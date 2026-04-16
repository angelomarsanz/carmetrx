'use strict'
$(document).ready(function () {


    $('.js-example-basic-single3').select2();
    $('.js-example-basic-single2').select2({
        placeholder: selectAmenLocal,
    });

    $(document).on('click', '.rmvflrImg', function () {
        let indb = $(this).data('indb');
        $(".request-loader").addClass("show");
        $.ajax({
            url: flrimgrmv,
            type: 'POST',
            data: {
                fileid: indb
            },
            success: function (data) {
                $(".request-loader").removeClass("show");
                var content = {};
                if (data == 'false') {
                    content.message = wentWrgMsg;
                    content.title = warning;
                    var type = 'warning';
                } else {
                    $('.rmvflrImg').remove();
                    content.message = dltSucesMsg;
                    content.title = success;
                    var type = 'success';
                    $('.uploaded-img2').attr('src', mainurl + '/assets/img/noimage.jpg');

                }

                content.icon = 'fa fa-bell';

                $.notify(content, {
                    type: type,
                    placement: {
                        from: 'top',
                        align: 'right'
                    },
                    showProgressbar: true,
                    time: 1000,
                    delay: 4000
                });
            }
        });
    });
    $(document).on('click', '.rmvvdoImg', function () {
        let indb = $(this).data('indb');
        $(".request-loader").addClass("show");
        $.ajax({
            url: vdoimgrmv,
            type: 'POST',
            data: {
                fileid: indb
            },
            success: function (data) {
                $(".request-loader").removeClass("show");
                var content = {};
                if (data == 'false') {
                    content.message = wentWrgMsg;
                    content.title = warning;
                    var type = 'warning';
                } else {
                    $('.rmvvdoImg').remove();
                    content.message = dltSucesMsg;
                    content.title = success;
                    var type = 'success';
                    $('.uploaded-img3').attr('src', mainurl + '/assets/img/noimage.jpg');

                }

                content.icon = 'fa fa-bell';

                $.notify(content, {
                    type: type,
                    placement: {
                        from: 'top',
                        align: 'right'
                    },
                    showProgressbar: true,
                    time: 1000,
                    delay: 4000
                });
            }
        });
    });

    $(".country").on('change', function (e) {
        $('.request-loader').addClass('show');
        let addedState = "state_id";
        let addedCity = "city_id";
        let id = $(this).val();

        $.ajax({
            type: 'GET',
            url: stateUrl,
            data: { id: id },
            success: function (data) {

                // Always reset dropdowns first
                $('.' + addedState).empty();
                $('.' + addedCity).empty();

                if (data.states.length > 0) {

                    $('.state').show();
                    $('.city').hide();

                    // 🔹 Add default option for states
                    $('.' + addedState).append(
                        $('<option selected disabled></option>').val('').html('Selecionar Ciudad')
                    );

                    $.each(data.states, function (key, value) {
                        $('.' + addedState).append(
                            $('<option></option>').val(value.id).html(value.name)
                        );
                    });

                    let firstStateId = data.states[0].id;

                    // Auto-load cities of first state
                    $.ajax({
                        type: 'GET',
                        url: cityUrl,
                        data: { state_id: firstStateId },
                        success: function (data) {

                            $('.' + addedCity).empty();

                            if (data.cities.length > 0) {
                                $('.city').show();

                                // 🔹 Add default option for cities
                                $('.' + addedCity).append(
                                    $('<option selected disabled></option>').val('').html('Seleccionar Zona')
                                );

                                $.each(data.cities, function (key, value) {
                                    $('.' + addedCity).append(
                                        $('<option></option>').val(value.id).html(value.name)
                                    );
                                });
                            }

                            $('.request-loader').removeClass('show');
                        }
                    });

                }
                // When only cities exist (country → city direct)
                else if (data.cities.length > 0) {

                    $('.state').hide();
                    $('.city').show();

                    // 🔹 Add default option
                    $('.' + addedCity).append(
                        $('<option selected disabled></option>').val('').html('Seleccionar Zona')
                    );

                    $.each(data.cities, function (key, value) {
                        $('.' + addedCity).append(
                            $('<option></option>').val(value.id).html(value.name)
                        );
                    });
                }

                $('.request-loader').removeClass('show');
            }
        });
    });


});

function getCities(e) {

    let $this = e.target;
    $('.request-loader').addClass('show');
    let addedCity = "city_id";
    let id = $($this).val();
    $.ajax({
        type: 'GET',
        url: cityUrl,
        data: {
            state_id: id,
        },
        success: function (data) {
            $('.' + addedCity).empty();
            if (data.cities.length > 0) {
                $('.' + addedCity).append(
                    $('<option selected disabled></option>').val('').html('Seleccionar Zona')
                );

                $('.city').show();

                $.each(data.cities, function (key, value) {
                    $('.' + addedCity).append(
                        $('<option></option>').val(value.id).html(value.name)
                    );
                });
            } else {
                $('.' + addedCity).find('option').remove().end().append(
                    $(
                        `<option selected ></option>`).val('').html('No City Found'));

            }
            $('.request-loader').removeClass('show');
        }
    });
}
