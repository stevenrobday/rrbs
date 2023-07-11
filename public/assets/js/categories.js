$('#categorySelect').on('change', function () {
    window.location.href = window.location.origin + '/categories/' + $(this).val();
});

$('.categoryDelete').on('click', function (e) {
    e.preventDefault();
    $('.deleteErrorMsg').text('');
    $('.scheduleErrorMsg').text('');
    let id = $(this).data('id');
    $(this).hide();
    let csrf_field = $(`#categoryVideoForm_${id} input[name=csrf_field]`).val();

    $.ajax({method: "PATCH", url: '/video/deleteVideo', data: JSON.stringify({id, csrf_field}), contentType: 'application/json', processData: false, dataType: 'json'})
        .done(function (res) {
            if (res.success)
                window.location.href = window.location.origin + '/categories/' + $('#categorySelect').val();
            else if (res.error) {
                $(this).show();
                $(`#deleteErrorMsg_${id}`).text(res.error.errorMsg);
                updateCsrfFields(res.error.csrfField);
            }
        });
});

$('.categoryScheduleSelect').on('change', function (e) {
    e.preventDefault();
    $('.deleteErrorMsg').text('');
    $('.scheduleErrorMsg').text('');
    let videoId = $(this).data('id');
    let scheduleId = $(this).val();
    let csrf_field = $(`#categoryVideoForm_${id} input[name=csrf_field]`).val();

    $.ajax({method: 'PATCH', url: '/video/updateSchedule', data: JSON.stringify({videoId, scheduleId, csrf_field}), contentType: 'application/json', processData: false, dataType: 'json'})
        .done(function (res) {
            if (res.success)
                window.location.href = window.location.origin + '/categories/' + $('#categorySelect').val();
            else if (res.error) {
                $(`#scheduleErrorMsg_${videoId}`).text(res.error.errorMsg);
                updateCsrfFields(res.error.csrfField);
            }
        });
});