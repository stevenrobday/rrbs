var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
})

$('#suggestVideoForm').on('submit', function (e) {
    e.preventDefault();
    const msg = $('#suggestMsg');
    const btn = $('#addSuggestVideo');
    let link = $('#suggestLink');
    let linkVal = link.val().trim();

    msg.text('');
    let arr2;
    if (linkVal.indexOf("youtube.com") >= 0) {
        let arr = linkVal.split('watch?v=');
        if (arr.length < 2) {
            msg.text("Invalid link. Try Again.");
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

    let start_time = $('#suggest_start_time').val();
    let end_time = $('#suggest_end_time').val();

    if (!start_time.length) start_time = 0;
    if (!end_time.length) end_time = 0;

    let csrf_field = $("#suggestVideoForm input[name=csrf_field]").val();
    let submitToken = btn.data('submitToken');
    btn.hide();

    $.post({
        url: '/video/addVideo/user',
        data: {videoId: arr2[0], start_time, end_time, submitToken, csrf_field},
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
                window.location.href = window.location.origin + '/suggestVideos';
            }
        });
});

$('#searchBtnUser').on('click', function () {
    searchVideosUser();
});

let userSearch = $('#searchVideosUser');
let searchedVideosContainer = $('#searchedVideosContainer');
let header = $('#searchedVideosHeader');

$('#clearBtnUser').on('click', function () {
    header.css('display', 'none');
    searchedVideosContainer.html('');
    userSearch.val('');
});

userSearch.on('keyup', function (e) {
    if (e.key === 'Enter') searchVideosUser();
});


function searchVideosUser() {
    let search = userSearch.val().trim();
    $.get({url: `/video/searchVideosUser/${search}`, contentType : 'application/json', dataType: 'json'})
        .done(function (res) {
            if (res.success) {
                header.css('display', 'flex');
                searchedVideosContainer.html('');
                res.success.forEach(i => {
                    searchedVideosContainer.append(
                        $('<div>').prop({
                            innerHTML: `${i.title}`,
                            className: 'col-8 text-light text-center'
                        }),
                        [
                            $('<div>').prop({
                                innerHTML: `${i.start_time}`,
                                className: 'col-2 text-light text-center'
                            }),
                            $('<div>').prop({
                                innerHTML: `${i.end_time}`,
                                className: 'col-2 text-light text-center'
                            })
                        ]
                    )
                });
            }
        });
}