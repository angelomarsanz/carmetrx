"use strict";
$(function ($) {

    // Uploaded Image Preview Start
    $('.img-input').on('change', function (event) {
        let file = event.target.files[0];
        let reader = new FileReader();

        reader.onload = function (e) {
            $('.uploaded-img').attr('src', e.target.result);
        };

        reader.readAsDataURL(file);
    });
    $('.img-input2').on('change', function (event) {
        $('.remove i').removeClass('d-none');
        let file = event.target.files[0];
        let reader = new FileReader();

        reader.onload = function (e) {
            $('.uploaded-img2').attr('src', e.target.result);
            $('<i class="fas fa-times text-danger removeBtn2"></i> ').insertAfter(".uploaded-img2");
            $(".removeBtn2").click(function (e) {
                $(this).remove();
                $('.img-input2').val('');
                $('.uploaded-img2').attr('src', mainurl + '/assets/img/noimage.jpg');

            });
        };

        reader.readAsDataURL(file);
    });
    $('.img-input3').on('change', function (event) {


        let file = event.target.files[0];
        let reader = new FileReader();

        reader.onload = function (e) {
            $('.uploaded-img3').attr('src', e.target.result);

            $('<i class="fas fa-times text-danger removeBtn3"></i> ').insertAfter(".uploaded-img3");
            $(".removeBtn3").click(function (e) {
                $(this).remove();
                $('.img-input3').val('');
                $('.uploaded-img3').attr('src', mainurl + '/assets/img/noimage.jpg');

            });
        };

        reader.readAsDataURL(file);
    });
    // Uploaded Image Preview End


    // // Uploaded counter Image Preview Start
    // $('#counter-img-input').on('change', function (event) {
    //     let file = event.target.files[0];
    //     let reader = new FileReader();

    //     reader.onload = function (e) {
    //         $('.uploaded-counter-img').attr('src', e.target.result);
    //     };

    //     reader.readAsDataURL(file);
    // });

    $('.section-img-input').on('change', function (event) {
        let file = event.target.files[0];
        let reader = new FileReader();

        // Find the closest .thumb-preview within the same parent container
        let preview = $(this).closest('.mt-3').prev('.thumb-preview').find('.uploaded-section-img');

        reader.onload = function (e) {
            preview.attr('src', e.target.result);
        };

        if (file) {
            reader.readAsDataURL(file);
        }
    });


    // Uploaded Background Image Preview Start
    $('.background-img-input').on('change', function (event) {
        let file = event.target.files[0];
        let reader = new FileReader();

        reader.onload = function (e) {
            $('.uploaded-background-img').attr('src', e.target.result);
        };

        reader.readAsDataURL(file);
    });
    // Uploaded Background Image Preview End








    /*------------------------
    Highlight Js
    -------------------------- */
    hljs.initHighlightingOnLoad();

    $("#toggle-btn").on('change', function () {
        var value = null;
        if (this.checked) {
            value = this.getAttribute('data-on');
        } else {
            value = this.getAttribute('data-off');
        }
        $.post(userStatusRoute,
            {
                value: value
            },
            function (data) {
                history.go(0);
            });
    });
});

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

function storeLesson(event, moduleId) {
    event.preventDefault();
    $('.request-loader').addClass('show');

    let lessonForm = $('#lessonForm-' + moduleId)[0];
    let fd = new FormData(lessonForm);
    let url = $('#lessonForm-' + moduleId).attr('action');
    let type = $('#lessonForm-' + moduleId).attr('method');

    $.ajax({
        url: url,
        type: type,
        data: fd,
        contentType: false,
        processData: false,
        success: function (data) {
            $('.request-loader').removeClass('show');
            $('.em').each(function () {
                $(this).html('');
            });
            if (data == 'success') {
                location.reload();
            }
        },
        error: function (error) {
            $('.em').each(function () {
                $(this).html('');
            });
            for (let x in error.responseJSON.errors) {
                $('#err_' + x + '-' + moduleId).text(error.responseJSON.errors[x][0]);
            }
            $('.request-loader').removeClass('show');
        }
    });
}
