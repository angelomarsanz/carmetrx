(function ($) {
    "use strict";
    $('input[name="is_trial"]').on('change', function () {
        if ($(this).val() == 1) {
            $('#trial_day').show();
        } else {
            $('#trial_day').hide();
        }
        $('#trial_days').val(null);
    });


    // // for USer 

    // $("#user_input").hide();


    // $(document).on('click', '#User', function () {
    //     const isChecked = $(this).is(':checked');
    //     if (isChecked) {
    //         $("#user_input").show();
    //     } else {
    //         $("#user_input").hide();
    //     }
    // });

    // for Language 

    $("#language_input").hide();


    $(document).on('click', '#AdditionalLanguage', function () {
        const isChecked = $(this).is(':checked');
        if (isChecked) {
            $("#language_input").show();
        } else {
            $("#language_input").hide();
        }
    });

    // for custom page 

    $("#custom_input").hide();


    $(document).on('click', '#AdditionalPage', function () {
        const isChecked = $(this).is(':checked');
        if (isChecked) {
            $("#custom_input").show();
        } else {
            $("#custom_input").hide();
        }
    });

    // for blog 

    $("#blog_input").hide();


    $(document).on('click', '#Blog', function () {
        const isChecked = $(this).is(':checked');
        if (isChecked) {
            $("#blog_input").show();
        } else {
            $("#blog_input").hide();
        }
    });

    // for Agent 

    $("#agent_input").hide();


    $(document).on('click', '#Agent', function () {
        const isChecked = $(this).is(':checked');
        if (isChecked) {
            $("#agent_input").show();
        } else {
            $("#agent_input").hide();
        }
    });

    // for property managemnt

    $("#property_input").hide();
    $("#property_featured_input").hide();
    $("#property_gallery_input").hide();
    $("#property_features_input").hide();

    $(document).on('click', '#PropertyManagement', function () {
        const isChecked = $(this).is(':checked');
        if (isChecked) {
            $("#property_input").show();
            $("#property_featured_input").show();
            $("#property_gallery_input").show();
            $("#property_features_input").show();
        } else {
            $("#property_input").hide();
            $("#property_featured_input").hide();
            $("#property_gallery_input").hide();
            $("#property_features_input").hide();
        }
    });

    // for project managemnt

    $("#project_input").hide();
    $("#project_types_input").hide();
    $("#project_gallery_input").hide();
    $("#project_additional_input").hide();

    $(document).on('click', '#ProjectManagement', function () {
        const isChecked = $(this).is(':checked');
        if (isChecked) {
            $("#project_input").show();
            $("#project_types_input").show();
            $("#project_gallery_input").show();
            $("#project_additional_input").show();
        } else {
            $("#project_input").hide();
            $("#project_types_input").hide();
            $("#project_gallery_input").hide();
            $("#project_additional_input").hide();
        }
    });

})(jQuery);
