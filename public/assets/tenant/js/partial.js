'use strict';

function cloneInput(fromId, toId, event) {
    let $target = $(event.target);
    if ($target.is(':checked')) {
        $('#' + fromId + ' .form-control').each(function (i) {
            let index = i;
            let val = $(this).val();
            let $toInput = $('#' + toId + ' .form-control').eq(index);
            if ($toInput.hasClass('summernote')) {
                let sourceId = $(this).attr('id');
                let val = tinyMCE.get(sourceId).getContent();
                let tmcId = $toInput.attr('id');
                tinyMCE.get(tmcId).setContent(val);
            } else if ($(this).data('role') == 'tagsinput') {
                if (val.length > 0) {
                    let tags = val.split(',');
                    tags.forEach(tag => {
                        $toInput.tagsinput('add', tag);
                    });
                } else {
                    $toInput.tagsinput('removeAll');
                }
            } else if ($(this).data('role') == 'checkbox') {
                if ($(this).is(':checked')) {
                    $toInput.prop('checked', true);
                }
            } else {
                $toInput.val(val);
            }
        });
    } else {
        $('#' + toId + ' .form-control').each(function (i) {
            let $toInput = $('#' + toId + ' .form-control').eq(i);
            if ($(this).hasClass('summernote')) {
                let tmcId = $toInput.attr('id');
                tinyMCE.get(tmcId).setContent('');
            } else if ($(this).data('role') == 'tagsinput') {
                $toInput.tagsinput('removeAll');
            } else {
                $toInput.val('');
            }
        });
    }
}

$(document).ready(function () {

    // course thumbnail image
    $('#thumb-img-input').on('change', function (event) {
        let file = event.target.files[0];
        let reader = new FileReader();
        reader.onload = function (e) {
            $('#uploaded-thumb-img').attr('src', e.target.result);
        };
        reader.readAsDataURL(file);
    });

    // course cover image
    $('#cover-img-input').on('change', function (event) {
        let file = event.target.files[0];
        let reader = new FileReader();
        reader.onload = function (e) {
            $('#uploaded-cover-img').attr('src', e.target.result);
        };
        reader.readAsDataURL(file);
    });

    // course price type
    $('input:radio[name="pricing_type"]').on('change', function () {
        let radioBtnVal = $('input:radio[name="pricing_type"]:checked').val();
        if (radioBtnVal == 'premium') {
            $('#price-input').removeClass('d-none');
        } else {
            $('#price-input').addClass('d-none');
        }
    });

    // course form
    $('#courseForm').on('submit', function (e) {
        $('.request-loader').addClass('show');
        e.preventDefault();
        let action = $(this).attr('action');
        let fd = new FormData($(this)[0]);

        $.ajax({
            url: action,
            method: 'POST',
            data: fd,
            contentType: false,
            processData: false,
            success: function (data) {
                $('.request-loader').removeClass('show');
                if (data == 'success') {
                    location.reload();
                }
            },
            error: function (error) {
                let errors = ``;
                for (let x in error.responseJSON.errors) {
                    errors += `<li>
                                  <p class="text-danger mb-0">${error.responseJSON.errors[x][0]}</p>
                              </li>`;
                }

                if (error?.responseJSON?.exception) {
                    errors += `<li>
                                  <p class="text-danger mb-0">${error?.responseJSON?.exception}</p>
                              </li>`;
                }

                $('#courseErrors ul').html(errors);
                $('#courseErrors').show();
                $('.request-loader').removeClass('show');
                $('html, body').animate({
                    scrollTop: $('#courseErrors').offset().top - 100
                }, 1000);
            }
        });
    });

    // course's thanks page form
    $('#thanksPageForm').on('submit', function (e) {
        $('.request-loader').addClass('show');
        e.preventDefault();
        let action = $(this).attr('action');
        let fd = new FormData($(this)[0]);

        $.ajax({
            url: action,
            method: 'POST',
            data: fd,
            contentType: false,
            processData: false,
            success: function (data) {
                $('.request-loader').removeClass('show');

                if (data == 'success') {
                    location.reload();
                }
            },
            error: function (error) {
                let errors = ``;

                for (let x in error.responseJSON.errors) {
                    errors += `<li>
                            <p class="text-danger mb-0">${error.responseJSON.errors[x][0]}</p>
                            </li>`;
                }

                $('#thanksPageErrors ul').html(errors);
                $('#thanksPageErrors').show();

                $('.request-loader').removeClass('show');

                $('html, body').animate({
                    scrollTop: $('#thanksPageErrors').offset().top - 100
                }, 1000);
            }
        });
    });

    // blog form
    $('#blogForm').on('submit', function (e) {
        $('.request-loader').addClass('show');
        e.preventDefault();

        let action = $(this).attr('action');
        let fd = new FormData($(this)[0]);

        $.ajax({
            url: action,
            method: 'POST',
            data: fd,
            contentType: false,
            processData: false,
            success: function (data) {
                $('.request-loader').removeClass('show');

                if (data == 'success') {
                    location.reload();
                }
                if (data == "downgrade") {
                    packageDowngradeNotify()
                }
            },
            error: function (error) {
                let errors = ``;

                for (let x in error.responseJSON.errors) {
                    errors += `<li>
                                <p class="text-danger mb-0">${error.responseJSON.errors[x][0]}</p>
                              </li>`;
                }

                if (error?.responseJSON?.exception) {
                    errors += `<li>
                                  <p class="text-danger mb-0">${error?.responseJSON?.exception}</p>
                              </li>`;
                }

                $('#blogErrors ul').html(errors);
                $('#blogErrors').show();

                $('.request-loader').removeClass('show');

                $('html, body').animate({
                    scrollTop: $('#blogErrors').offset().top - 100
                }, 1000);
            }
        });
    });

    // custom page form
    $('#pageForm').on('submit', function (e) {
        $('.request-loader').addClass('show');
        e.preventDefault();

        let action = $(this).attr('action');
        let fd = new FormData($(this)[0]);

        $.ajax({
            url: action,
            method: 'POST',
            data: fd,
            contentType: false,
            processData: false,
            success: function (data) {
                $('.request-loader').removeClass('show');

                if (data == 'success') {
                    location.reload();
                }
                if (data == "downgrade") {
                    packageDowngradeNotify()
                }
            },
            error: function (error) {
                let errors = ``;

                for (let x in error.responseJSON.errors) {
                    errors += `<li>
                <p class="text-danger mb-0">${error.responseJSON.errors[x][0]}</p>
              </li>`;
                }

                $('#pageErrors ul').html(errors);
                $('#pageErrors').show();

                $('.request-loader').removeClass('show');

                $('html, body').animate({
                    scrollTop: $('#pageErrors').offset().top - 100
                }, 1000);
            }
        });
    });

    // sort course lesson contents
    $('#sort-content').sortable({
        stop: function (event, ui) {
            let sortRoute = '';

            if (sortContentUrl) {
                sortRoute = sortContentUrl;
            }

            $('.request-loader').addClass('show');

            let fd = new FormData();

            $('.ui-state-default').each(function (index) {
                fd.append('ids[]', $(this).data('id'));

                let orderNo = parseInt(index) + 1;
                fd.append('orders[]', orderNo);
            });

            $.ajax({
                url: sortRoute,
                type: 'POST',
                data: fd,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function (data) {
                    $('.request-loader').removeClass('show');
                }
            });
        }
    });

    // change course certificate status (enable/disable)
    $('input:radio[name="certificate_status"]').on('change', function () {
        let radioBtnVal = $('input:radio[name="certificate_status"]:checked').val();
        if (radioBtnVal == 1) {
            $('#certificate-settings').show();
        } else {
            $('#certificate-settings').hide();
        }
    });

    // show or hide input field according to selected ad type
    $('#ad-type').on('change', function () {
        let adType = $(this).val();
        if (adType == 'banner') {
            if (!$('#slot-input').hasClass('d-none')) {
                $('#slot-input').addClass('d-none');
            }

            $('#image-input').removeClass('d-none');
            $('#url-input').removeClass('d-none');
        } else {
            if (!$('#image-input').hasClass('d-none') && !$('#url-input').hasClass('d-none')) {
                $('#image-input').addClass('d-none');
                $('#url-input').addClass('d-none');
            }

            $('#slot-input').removeClass('d-none');
        }
    });

    $('.edit-ad-type').on('change', function () {
        let adType = $(this).val();

        if (adType == 'banner') {
            if (!$('#edit-slot-input').hasClass('d-none')) {
                $('#edit-slot-input').addClass('d-none');
            }
            $('#edit-image-input').removeClass('d-none');
            $('#edit-url-input').removeClass('d-none');
        } else {
            if (!$('#edit-image-input').hasClass('d-none') && !$('#edit-url-input').hasClass('d-none')) {
                $('#edit-image-input').addClass('d-none');
                $('#edit-url-input').addClass('d-none');
            }
            $('#edit-slot-input').removeClass('d-none');
        }
    });

    if ($("input[name='quiz_completion']").length > 0) {
        function loadQuizScore() {
            if ($("input[name='quiz_completion']:checked").val() == 1) {
                $("#minScore").show();
            } else {
                $("#minScore").hide();
            }
        }
        loadQuizScore();
        $("input[name='quiz_completion']").on('change', function () {
            loadQuizScore();
        });
    }
});
