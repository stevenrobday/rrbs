var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
})

$('#statusSelect').on('change', function () {
    window.location.href = window.location.origin + '/suggestedVideos/' + $(this).val();
});

$('.deny, .approve').on('click', function () {
    let id = $(this).data('id');
    let status = $(this).data('status');
    let statusId = $(this).data('status_id');
    let scheduleId = $('#suggestedSchedule_' + id).val();
    let comments = $('#comments_' + id).val();
    let start = $('#suggested_start_time_' + id).val();
    let end = $('#suggested_end_time_' + id).val();
    let csrf_field = $(`#suggestedForm_${id} input[name=csrf_field]`).val();

    if (!start.length) start = 0;
    if (!end.length) end = 0;

    if (!start.length) start = 0;
    if (!end.length) end = 0;

    const errorMsg = $(`#suggestedErrorMsg_${id}`);

    let data = {
        id,
        status,
        statusId,
        scheduleId,
        comments,
        start,
        end,
        csrf_field
    };
    $('.approve').hide();
    $('.deny').hide();

    $.post({url: '/updateSuggestedVideo', data, dataType: 'json'})
        .done(function (res) {
            if (res.success)
                window.location.href = window.location.origin + '/suggestedVideos/' + $('#statusSelect').val();
            else if (res.error) {
                $('.approve').show();
                $('.deny').show();
                errorMsg.text(res.error.errorMsg);
                updateCsrfFields(res.error.csrfField);
            }
        });
});