
$(window).on('load', function () {
  // scroll to bottom
  if ($('.messages-container').length > 0) {
    $('.messages-container')[0].scrollTop = $('.messages-container')[0].scrollHeight;
  }
});

$(document).ready(function () {
  'use strict';

  // post form
  $('#postForm').on('submit', function (e) {
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

        if (data.status == 'success') {
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

        $('#postErrors ul').html(errors);
        $('#postErrors').show();

        $('.request-loader').removeClass('show');

        $('html, body').animate({
          scrollTop: $('#postErrors').offset().top - 100
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

        if (data.status == 'success') {
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

        $('#pageErrors ul').html(errors);
        $('#pageErrors').show();

        $('.request-loader').removeClass('show');

        $('html, body').animate({
          scrollTop: $('#pageErrors').offset().top - 100
        }, 1000);
      }
    });
  });


  // show or hide input field according to selected ad type
  $('.ad-type').on('change', function () {
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


  // show different input field according to input type for digital product
  $('select[name="input_type"]').on('change', function () {
    let optionVal = $(this).val();

    if (optionVal == 'upload') {
      $('#file-input').removeClass('d-none');

      if (!$('#link-input').hasClass('d-none')) {
        $('#link-input').addClass('d-none');
      }
    } else if (optionVal == 'link') {
      $('#link-input').removeClass('d-none');

      if (!$('#file-input').hasClass('d-none')) {
        $('#file-input').addClass('d-none');
      }
    }
  });

  // show uploaded zip file name
  $('.zip-file-input').on('change', function (e) {
    let fileName = e.target.files[0].name;
    $('.zip-file-info').text(fileName);
  });






  // uploaded file progress bar and file name preview
  $('.custom-file-input').on('change', function (e) {
    let file = e.target.files[0];
    let fileName = e.target.files[0].name;

    let fd = new FormData();
    fd.append('attachment', file);

    $.ajax({
      xhr: function () {
        let xhr = new window.XMLHttpRequest();

        xhr.upload.addEventListener('progress', function (ele) {
          if (ele.lengthComputable) {
            let percentage = ((ele.loaded / ele.total) * 100);
            $('.progress').removeClass('d-none');
            $('.progress-bar').css('width', percentage + '%');
            $('.progress-bar').html(Math.round(percentage) + '%');

            if (Math.round(percentage) === 100) {
              $('.progress-bar').addClass('bg-success');
              $('#attachment-info').text(fileName);
            }
          }
        }, false);

        return xhr;
      },
      url: $(this).data('url'),
      method: 'POST',
      data: fd,
      contentType: false,
      processData: false,
      success: function (res) {
       
      }
    });
  });

  // close ticket using swal start
  $('.closeBtn').on('click', function (e) {
    e.preventDefault();
    $('.request-loader').addClass('show');

    swal({
      title: are_you_sure,
      text: "You want to close this ticket!",
      type: 'warning',
      buttons: {
        confirm: {
          text: 'Yes, close it',
          className: 'btn btn-success'
        },
        cancel: {
          visible: true,
          className: 'btn btn-danger'
        }
      }
    }).then((Delete) => {
      if (Delete) {
        $(this).parent('.ticketForm').submit();
      } else {
        swal.close();

        $('.request-loader').removeClass('show');
      }
    });
  });
  // close ticket using swal end




  $('thead').on('click', '.addRow', function (e) {
    e.preventDefault();
    var tr = `<tr>
                <td>
                  ${labels}
                </td>
                <td>
                  ${values}
                </td>
                <td>
                  <a href="javascript:void(0)" class="btn btn-danger  btn-sm deleteRow">
                    <i class="fas fa-minus"></i></a>
                </td>
              </tr>`;
    $('#tbody').append(tr);
  });

  $('tbody').on('click', '.deleteRow', function () {
    $(this).parent().parent().remove();
  });




  // Form Submit with AJAX Request Start
  $("#propertySubmit").on('click', function (e) {

    $(e.target).attr('disabled', true);
    $(".request-loader").addClass("show");

    if ($(".iconpicker-component").length > 0) {
      $("#inputIcon").val($(".iconpicker-component").find('i').attr('class'));
    }

    let propertyForm = document.getElementById('propertyForm');
    let fd = new FormData(propertyForm);
    let url = $("#propertyForm").attr('action');
    let method = $("#propertyForm").attr('method');

    //if summernote has then get summernote content
    $('.form-control').each(function (i) {
      let index = i;

      let $toInput = $('.form-control').eq(index);

      if ($(this).hasClass('summernote')) {
        let tmcId = $toInput.attr('id');
        let content = tinyMCE.get(tmcId).getContent();
        fd.delete($(this).attr('name'));
        fd.append($(this).attr('name'), content);
      }
    });

    $.ajax({
      url: url,
      method: method,
      data: fd,
      contentType: false,
      processData: false,
      success: function (data) {
        $(e.target).attr('disabled', false);
        $('.request-loader').removeClass('show');

        $('.em').each(function () {
          $(this).html('');
        });

        if (data.status == 'success') {
          location.reload();
        }

        if (data == "downgrade") {
          packageDowngradeNotify()

        }


      },
      error: function (error) {

        if (error.responseJSON.deactive) {

          deactive(error)
          $('.request-loader').removeClass('show');
          return
        }
        let errors = ``;

        for (let x in error.responseJSON.errors) {
          errors += `<li>
                <p class="text-danger mb-0">${error.responseJSON.errors[x][0]}</p>
              </li>`;
        }

        $('#propertyErrors ul').html(errors);
        $('#propertyErrors').show();

        $('.request-loader').removeClass('show');

        $('html, body').animate({
          scrollTop: $('#propertyErrors').offset().top - 100
        }, 1000);
      }

    });
    $(e.target).attr('disabled', false);
  });

  $("#propertySubmit2").on('click', function (e) {

    $(e.target).attr('disabled', true);
    $(".request-loader").addClass("show");

    if ($(".iconpicker-component").length > 0) {
      $("#inputIcon").val($(".iconpicker-component").find('i').attr('class'));
    }

    let carForm = document.getElementById('propertyForm2');
    let fd = new FormData(carForm);
    let url = $("#propertyForm2").attr('action');
    let method = $("#propertyForm2").attr('method');

    //if summernote has then get summernote content
    $('.form-control').each(function (i) {
      let index = i;

      let $toInput = $('.form-control').eq(index);

      if ($(this).hasClass('summernote')) {
        let tmcId = $toInput.attr('id');
        let content = tinyMCE.get(tmcId).getContent();
        fd.delete($(this).attr('name'));
        fd.append($(this).attr('name'), content);
      }
    });

    $.ajax({
      url: url,
      method: method,
      data: fd,
      contentType: false,
      processData: false,
      success: function (data) {
        $(e.target).attr('disabled', false);
        $('.request-loader').removeClass('show');

        $('.em').each(function () {
          $(this).html('');
        });

        if (data.status == 'success') {
          location.reload();
        }

        if (data == "downgrade") {
          packageDowngradeNotify();
          // $('.modal').modal('hide');

          // "use strict";
          // var content = {};

          // content.message = downgradeMsg
          // content.title = warning;
          // content.icon = 'fa fa-bell';

          // $.notify(content, {
          //   type: 'warning',
          //   placement: {
          //     from: 'top',
          //     align: position
          //   },
          //   showProgressbar: true,
          //   time: 1000,
          //   delay: 4000,
          // });
          // $("#allLimits").modal('show');
        }
      },
      error: function (error) {
        if (error.responseJSON.deactive) {

          deactive(error)
          $('.request-loader').removeClass('show');
          return
        }
        let errors = ``;

        for (let x in error.responseJSON.errors) {
          errors += `<li>
                <p class="text-danger mb-0">${error.responseJSON.errors[x][0]}</p>
              </li>`;
        }

        $('#propertyErrors2 ul').html(errors);
        $('#propertyErrors2').show();

        $('.request-loader').removeClass('show');

        $('html, body').animate({
          scrollTop: $('#propertyErrors2').offset().top - 100
        }, 1000);
      }

    });
    $(e.target).attr('disabled', false);
  });

  // spacification delete
  $('tbody').on('click', '.deleteSpecification', function () {
    let spacification = $(this).data('specification');
    $('.request-loader').addClass('show');
    $(this).parent().parent().remove();
    $.ajax({
      url: specificationRmvUrl,
      method: 'POST',
      data: {
        spacificationId: spacification,
      },

      success: function (data) {

        if (data.status == 'success') {

          $('.request-loader').removeClass('show');

          var content = {};
          content.message = dltSucesMsg;
          content.title = success;
          content.icon = 'fa fa-bell';

          $.notify(content, {
            type: 'success',
            placement: {
              from: 'top',
              align: position
            },
            showProgressbar: true,
            time: 1000,
            delay: 4000,
          });

        }

      },
      error: function (error) {
        if (data.status == 'success') {

          var content = {};

          content.message = wentWrgMsg;
          content.title = warning;
          content.icon = 'fa fa-bell';

          $.notify(content, {
            type: 'warning',
            placement: {
              from: 'top',
              align: position
            },
            showProgressbar: true,
            time: 1000,
            delay: 4000,
          });

        }

      }
    });
  });

});

function searchFormSubmit(e) {
  if (e.keyCode == 13) {
    $('#searchForm').submit();
  }
}
