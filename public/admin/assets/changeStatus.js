$(document).ready(function () {
    $('.change-status-toggle').on('click', function (event) {

        event.preventDefault();

        Swal.fire({
            title: 'Are you sure you want to change status ?',
            showDenyButton: true,
            confirmButtonText: `Yes`,
            denyButtonText: `No`,
            padding: '10px 50px 10px 50px',
            allowOutsideClick: false
        }).then((result) => {
            if (result.isConfirmed) {
                let CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                let url = $(this).attr('href');
                $.ajax({
                    type: 'POST',
                    url: url,
                    data: {
                        _token: CSRF_TOKEN,
                    },
                    success: function (results) {
                        if (results.success === true) {
                            Swal.fire("Done!", results.message, "success");
                            location.reload();
                        } else {
                            Swal.fire("Error!", results.message, "error");
                        }
                    }
                });
            }
        });
    });
});
