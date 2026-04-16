'use strict';
$('body').on('click', '.generateDescriptionBtn', function () {
    $('.request-loader').addClass('show');

    let langName = $(this).data('lang');
    let langCode = $(this).data('langcode');

    let summary = $('#' + langCode + '_summary').val();
    let title = $('#' + langCode + '_title').val();

    // Unique features collect (duplicate remove)
    let featuresMap = new Map();
    $('#tbody tr').each(function () {
        let label = $(this).find('input[name="' + langCode + '_label[]"]').val();
        let value = $(this).find('input[name="' + langCode + '_value[]"]').val();

        if (label && label.trim() !== '') {
            let featureText = label.trim();
            if (value && value.trim() !== '') {
                featureText = label.trim() + ': ' + value.trim();
            }

            // lowercase key for case-insensitive duplicate check
            let key = featureText.toLowerCase();
            if (!featuresMap.has(key)) {
                featuresMap.set(key, featureText);
            }
        }
    });

    let features = Array.from(featuresMap.values());

    let selectedAmenities = [];
    $('.amenities-values option:selected').each(function () {
        selectedAmenities.push($(this).data('value'));
    });

    $.ajax({
        type: 'POST',
        url: genPromptUrl,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            langName: langName,
            summary: summary,
            title: title,
            features: features,
            amenities: selectedAmenities
        },
        success: function (response) {
            $('.request-loader').removeClass('show');
            $('#AiGeneModal').modal('hide');
            $('#aiGenContentModal').modal('show');

            const parsedContent = marked.parse(response);
            $('#ai-content').html(parsedContent);
            $('#copyToDescription').data('langcode', langCode);
        },
        error: function (xhr) {
            $('.request-loader').removeClass('show');
            alert('Error: ' + xhr.responseText);
        }
    });
});


/**
 * Copy AI Content to TinyMCE editor
 */
$('body').on('click', '#copyToDescription', function () {
    $('.request-loader').addClass('show');
    let content = $('#ai-content').html();
    let langcode = $(this).data('langcode');


    let editorId = langcode + '_description';
    let editor = tinymce.get(editorId);

    if (editor) {
        editor.setContent(content); // Sets content inside TinyMCE
        $('#aiGenContentModal').modal('hide');
    } else {
        console.error('TinyMCE editor not found or not initialized yet.');
    }
    $('.request-loader').removeClass('show');
});

/**
 * Generate AI Content Again
 */
$('body').on('click', '#generate_content_again', function () {
    $('.request-loader').addClass('show');

    let content = $('#ai-content').html();
    let make_better = $('#make_better').val();
    let langCode = $('#copyToDescription').data('langcode');

    if (!make_better.trim()) {
        $('.request-loader').removeClass('show');
        alert('Please enter a prompt or instructions.');
        return false;
    }

    $.ajax({
        type: 'GET',
        url: genContentUrl,
        data: {
            prev_content: content,
            new_prompt: make_better,
            type: 'make_better',
            lang: langCode
        },
        success: function (response) {
            $('.request-loader').removeClass('show');

            const parsedContent = marked.parse(response);
            $('#ai-content').html(parsedContent);
            $('#make_better').val('');
        },
        error: function (xhr) {
            $('.request-loader').removeClass('show');
            alert('Error: ' + xhr.responseText);
        }
    });
});
