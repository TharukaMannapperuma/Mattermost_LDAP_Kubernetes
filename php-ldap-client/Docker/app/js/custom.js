$(document).on('click', '#input_login', function (e) {
    swal({
        title: 'Signing In',
        allowEscapeKey: false,
        allowOutsideClick: false,
        onOpen: () => {
            swal.showLoading();
        }
    })
});

$(document).on('click', '#input_accept', function (e) {
    swal({
        title: 'Authorizing',
        allowEscapeKey: false,
        allowOutsideClick: false,
        onOpen: () => {
            swal.showLoading();
        }
    })
});