$('.videoLog').on('click', function () {
    window.location.href = window.location.origin + '/videoLog/' + Intl.DateTimeFormat().resolvedOptions().timeZone.replace('/', '-');
});

function updateCsrfFields(value) {
    let allForms = document.forms;
    for(e of allForms) {
        const field = e.querySelector('input[name=csrf_field]');
        if (field !== null)
            field.value = value;
    }
}