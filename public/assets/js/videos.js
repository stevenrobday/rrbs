var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
})

$('#addVideoForm').on('submit', function (e) {
    e.preventDefault();
    clearMsg();
    const msg = $('#msg');
    const btn = $('#addVideo');
    let link = $('#link');
    let linkVal = link.val().trim();

    let arr2;
    if (linkVal.indexOf("youtube.com") >= 0) {
        let arr = linkVal.split('watch?v=');
        if (arr.length < 2) {
            msg.text("Invalid link.");
            link.val('');
            return;
        }
        arr2 = arr[1].split('&');
    } else if (linkVal.indexOf("youtu.be") >= 0) {
        let videoId = linkVal.split('/').pop();
        arr2 = videoId.split('&');
    } else {
        msg.text("Invalid link. Try Again.");
        link.val('');
        return;
    }

    let start_time = $('#start_time').val();
    let end_time = $('#end_time').val();

    if (!start_time.length) start_time = 0;
    if (!end_time.length) end_time = 0;

    let csrf_field = $("#addVideoForm input[name=csrf_field]").val();
    let submitToken = btn.data('submitToken');
    btn.hide();

    $.post({
        url: '/video/addVideo/admin',
        data: {videoId: arr2[0], scheduleId: $('#floatingSelect').val(), start_time, end_time, submitToken, csrf_field},
        dataType: 'json'
    })
        .done(function (res) {
            if (res.submitToken) {
                btn.show();
                btn.data('submitToken', 1);
                msg.text("Your video is over five minutes long. If it contains more than one song, add the start and/or end times for the song you want. If it's just one long song, resubmit the link and we'll add it to the database.");
                updateCsrfFields(res.submitToken.csrfField);
            } else if (res.error) {
                btn.show();
                msg.text(res.error.errorMsg);
                updateCsrfFields(res.error.csrfField);
            } else if (res.success) {
                searchVideos();
            }
        });
});

$('#searchBtn').on('click', function () {
    searchVideos();
});

$('#searchVideos').on('keyup', function (e) {
    if (e.key === 'Enter') searchVideos();
});

$('.delete').on('click', function (e) {
    e.preventDefault();
    clearMsg();
    let btn = $(this);
    let id = btn.data('id');
    btn.hide();
    let csrf_field = $(`#videoForm_${id} input[name=csrf_field]`).val();

    $.ajax({method: "PATCH", url: '/video/deleteVideo', data: JSON.stringify({id, csrf_field}), contentType: 'application/json', processData: false, dataType: 'json'})
        .done(function (res) {
            if (res.success)
                searchVideos();
            else if (res.error) {
                btn.show();
                $(`#deleteErrorMsg_${id}`).text(res.error.errorMsg);
                updateCsrfFields(res.error.csrfField);
            }
        });
});

$('.scheduleSelect').on('change', function (e) {
    e.preventDefault();
    clearMsg();
    let videoId = $(this).data('id');
    let scheduleId = $(this).val();
    let csrf_field = $(`#videoForm_${videoId} input[name=csrf_field]`).val();
    $.ajax({method: 'PATCH', url: '/video/updateSchedule', data: JSON.stringify({videoId, scheduleId, csrf_field}), contentType: 'application/json', processData: false, dataType: 'json'})
        .done(function (res) {
            if (res.success) {
                $(`#scheduleMsg_${videoId}`).text('Video updated');
                updateCsrfFields(res.success.csrfField);
            }
            else if (res.error) {
                $(`#scheduleErrorMsg_${videoId}`).text(res.error.errorMsg);
                updateCsrfFields(res.error.csrfField);
            }
        });
});

$('.saveTimes').on('click', function (e) {
    e.preventDefault();
    clearMsg();
    let btn = $(this);
    let videoId = btn.data('id');
    let csrf_field = $(`#videoForm_${videoId} input[name=csrf_field]`).val();
    btn.hide();
    let start_time = $(`#start_time_${videoId}`).val();
    let end_time = $(`#end_time_${videoId}`).val();

    if (!start_time.length) start_time = 0;
    if (!end_time.length) end_time = 0;

    const errorMsg = $(`#saveTimesErrorMsg_${videoId}`);

    $.ajax({method: "PATCH", url: '/video/updateTimes', data: JSON.stringify({videoId, start_time, end_time, csrf_field}), contentType: 'application/json', processData: false, dataType: 'json'})
        .done(function (res) {
            btn.show();
            if (res.success) {
                $(`#saveTimesMsg_${videoId}`).text('Video updated');
                updateCsrfFields(res.success.csrfField);
            }
            else if (res.error) {
                errorMsg.text(res.error.errorMsg);
                updateCsrfFields(res.error.csrfField);
            }
        });
});


function clearMsg() {
    $('.scheduleMsg').text('');
    $('.scheduleErrorMsg').text('');
    $('.saveTimesMsg').text('');
    $('.saveTimesErrorMsg').text('');
    $('.deleteErrorMsg').text('');
    $('#msg').text('');
}

function searchVideos() {
    let searchVal = decodeHtml($('#searchVideos').val().trim());
    if (searchVal.length) window.location.href = window.location.origin + '/videos/' + searchVal;
    else window.location.href = window.location.origin + '/videos';
}

function decodeHtml(str)
{
    let map =
        {
            '&amp;': '&',
            '&lt;': '<',
            '&gt;': '>',
            '&quot;': '"',
            '&#039;': "'"
        };
    return str.replace(/&amp;|&lt;|&gt;|&quot;|&#039;/g, function(m) {return map[m];});
}