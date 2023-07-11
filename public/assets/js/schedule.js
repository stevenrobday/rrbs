$('#timezoneSelect').on('change', function (e) {
    e.preventDefault();
    clearMsgSchedule();
    let csrf_field = $("#timezoneForm input[name=csrf_field]").val();
    $.post({url: '/schedule/updateTimezone', data: {timezoneId: $(this).val(), csrf_field}, dataType: 'json'})
        .done(function (res) {
            if (res.success) {
                $("#timezoneMsg").text('Timezone updated');
                updateCsrfFields(res.success.csrfField);
            }
            else if (res.error) {
                $("#timezoneErrorMsg").text(res.error.errorMsg);
                updateCsrfFields(res.error.csrfField);
            }
        });
});

$('#addScheduleForm').on('submit', function (e) {
    e.preventDefault();
    clearMsgSchedule();
    let btn = $('#addSchedule');
    btn.hide();
    let status = $('#status').prop('checked') ? 1 : 0;
    let regular_rotation = $('#regularRotation').prop('checked') ? 1 : 0;
    let category = $('#category').val().trim();
    let start_time = $('#startTime').val();
    let end_time = $('#endTime').val();
    let msg = $('#msg');

    if (validateSchedule(btn, category, start_time, end_time, msg)) {
        let csrf_field = $("#addScheduleForm input[name=csrf_field]").val();

        let params = {
            category,
            start_time,
            end_time,
            status,
            regular_rotation,
            csrf_field
        };

        $.post({url: '/schedule/addUpdateSchedule/add', data: params, dataType: 'json'})
            .done(function (res) {
                if (res.error) {
                    btn.show();
                    msg.text(res.error.errorMsg);
                    updateCsrfFields(res.error.csrfField);
                } else if (res.success)
                    window.location.href = window.location.origin + '/schedule'
            });
    }
});

$('.deleteSchedule').on('click', function (e) {
    e.preventDefault();
    clearMsgSchedule();
    let id = $(this).data('id');
    let csrf_field = $(`#editScheduleForm_${id} input[name=csrf_field]`).val();
    let btn = $(this);
    btn.hide();

    $.ajax({method: "DELETE", url: '/schedule/deleteSchedule', data: JSON.stringify({id, csrf_field}), contentType: 'application/json', processData: false, dataType: 'json'})
        .done(function (res) {
            if (res.success)
                window.location.href = window.location.origin + '/schedule';
            else if (res.error) {
                btn.show();
                $(`#deleteErrorMsg_${id}`).text(res.error.errorMsg);
                updateCsrfFields(res.error.csrfField);
            }
        });
});

$('.editSchedule').on('click', function (e) {
    e.preventDefault();
    clearMsgSchedule();
    let btn = $(this);
    btn.hide();
    let id = $(this).data('id');

    console.log(id);

    let status = $(`#status_${id}`).prop('checked') ? 1 : 0;
    let category = $(`#category_${id}`).val().trim();
    let start_time = $(`#startTime_${id}`).val();
    let end_time = $(`#endTime_${id}`).val();
    let msg = $(`#msg_${id}`);

    if (validateSchedule(btn, category, start_time, end_time, msg)) {
        let csrf_field = $(`#editScheduleForm_${id} input[name=csrf_field]`).val();
        let params = {
            category,
            start_time,
            end_time,
            status,
            id,
            csrf_field
        };

        $.post({url: '/schedule/addUpdateSchedule/update', data: params, dataType: 'json'})
            .done(function (res) {
                if (res.error) {
                    btn.show();
                    msg.text(res.error.errorMsg);
                    updateCsrfFields(res.error.csrfField);
                } else if (res.success)
                    window.location.href = window.location.origin + '/schedule'
            });
    }
});

$('.regularRotation').on('change', function (e) {
    e.preventDefault();
    clearMsgSchedule();
    let scheduleId = $(this).data('id');
    let regular_rotation = $(this).prop('checked') ? 1 : 0;
    let csrf_field = $(`#editScheduleForm_${scheduleId} input[name=csrf_field]`).val();

    $.ajax({method: "PATCH", url: '/schedule/updateRotation', data: JSON.stringify({scheduleId, regular_rotation, csrf_field}), contentType: 'application/json', processData: false, dataType: 'json'})
        .done(function (res) {
            if (res.success) {
                $(`#regularRotationMsg_${scheduleId}`).text('Schedule updated');
                updateCsrfFields(res.success.csrfField);
            }
            else if (res.error) {
                $(`#regularRotationErrorMsg_${scheduleId}`).text(res.error);
                updateCsrfFields(res.error.csrfField);
            }
        });
});

function clearMsgSchedule() {
    $("#timezoneMsg").text('');
    $("#timezoneErrorMsg").text('');
    $('#msg').text('');
    $('.regularRotationMsg').text('');
    $('.regularRotationErrorMsg').text('');
    $('.deleteErrorMsg').text('');
    $('.scheduleSaveErrorMsg').text('');
}

function validateSchedule(btn, category, startTime, endTime, msg) {
    msg.val('');

    if (!category.length) {
        btn.show();
        msg.text('Please fill out the category.');
        return false;
    }

    if (startTime === 'null') {
        btn.show();
        msg.text('Please select a start time.');
        return false;
    }

    if (endTime === 'null') {
        btn.show();
        msg.text('Please select an end time.');
        return false;
    }

    return true;
}