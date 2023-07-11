$('#inviteSelect').on('change', function () {
    window.location.href = window.location.origin + '/invite/' + $(this).val();
});

$('.inviteBtn').on('click', function (e) {
    e.preventDefault();
    $('.inviteErrorMsg').text('');
    let id = $(this).data('id');
    let invited = $(this).data('invited');
    let csrf_field = $(`#inviteForm_${id} input[name=csrf_field]`).val();

    $(this).hide();

    $.ajax({method: "PATCH", url: '/inviteUser', data: JSON.stringify({id, invited, csrf_field}), contentType: 'application/json', processData: false, dataType: 'json'})
        .done(function (res) {
            if (res.success)
                window.location.href = window.location.origin + '/invite/' + $('#inviteSelect').val();
            else if (res.error) {
                $(this).show();
                $(`#inviteErrorMsg_${id}`).text(res.error.errorMsg);
                updateCsrfFields(res.error.csrfField);
            }
        });
});