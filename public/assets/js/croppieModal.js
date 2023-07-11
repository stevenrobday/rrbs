let croppie = $('#croppie').croppie({
    enableExif: true,
    viewport: {
        width: 200,
        height: 200,
        type: 'circle'
    },
    boundary: {
        width: 300,
        height: 300
    }
});

$('#browseImg').on('click', function() {
    $('#croppieFile').click();
});

$('#croppieFile').on('change', function() {
    let reader = new FileReader();
    reader.onload = function (event) {
        $('#uploadPrompt').hide();
        $('#croppie').show();
        $('#saveImg').show();
        croppie.croppie('bind', {
            url: event.target.result
        });
    }
    reader.readAsDataURL(this.files[0]);
});

$('#saveImg').on('click', function(e) {
    e.preventDefault();
    clearMsg();
    let csrf_field = $(`#imgForm input[name=csrf_field]`).val();
    croppie.croppie('result', {
        type: 'canvas',
        size: 'viewport'
    }).then(function (response) {
        $.post({
            url: '/user/addImage',
            data: {image: response, csrf_field},
            dataType: 'json'
        })
            .done(function (res) {
                if (res.error) {
                    $("#uploadErrorMsg").text(res.error.errorMsg);
                    updateCsrfFields(res.error.csrfField);
                }
                else if (res.success) {
                    $("#uploadSuccessMsg").text('Image successfully uploaded');
                    updateCsrfFields(res.success.csrfField);

                    const profileImg = $("#profileImg");

                    if (profileImg.hasClass('fa-stack') && profileImg.hasClass('fa-lg')) {
                        profileImg.removeClass('fa-stack');
                        profileImg.removeClass('fa-lg');
                    }

                    profileImg.html(`<img alt="profile_picture" id="profilePic" src="/assets/img/profile/${res.success.img}">`);
                }
            });
    });
});

const textarea = $("#aboutTextarea");
const chars = $("#charCount");

textarea.on('input', function(e){
    chars.text(textarea.val().length);
});

$('#saveAbout').on('click', function(){
    clearMsg();
    let csrf_field = $(`#aboutForm input[name=csrf_field]`).val();

    $.post({
        url: '/user/addAbout',
        data: {about: textarea.val(), csrf_field},
        dataType: 'json'
    })
        .done(function (res) {
            if (res.error) {
                $("#aboutErrorMsg").text(res.error.errorMsg);
                updateCsrfFields(res.error.csrfField);
            }
            else if (res.success) {
                $("#aboutSuccessMsg").text('Profile updated');
                updateCsrfFields(res.success.csrfField);
            }
        });
});

function clearMsg() {
    $("#uploadErrorMsg").text('');
    $("#uploadSuccessMsg").text('');
    $("#aboutErrorMsg").text('');
    $("#aboutSuccessMsg").text('');
}