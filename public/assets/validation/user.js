$(document).ready(function () {
    $('#user_submit').validate({
        rules: {
            name: {
                required: true,
            },
            dob: {
                required: true,
                date: true,
            },
            email: {
                required: true,
                email: true,
            },
            mobile: {
                required: true,
                digits: true,
            },
            address: {
                required: true,
            },
            shift_id: {
                required: true,
            },
            department_id: {
                required: true,
            },
            monthly_salary: {
                number: true,
                min: 0,
            },
        },
        messages: {
            name: {
                required: "Please Enter Name"
            },
            dob: {
                required: "Please Select Date of Birth",
                date: "Please enter a valid date"
            },
            email: {
                required: "Please Enter Email",
                email: "Please enter a valid email address"
            },
            mobile: {
                required: "Please Enter Mobile Number",
                digits: "Please enter only digits"
            },
            address: {
                required: "Please Enter Address"
            },
            shift_id: {
                required: "Please Select Shift"
            },
            department_id: {
                required: "Please Select Department"
            },
            monthly_salary: {
                number: "Please enter a valid number",
                min: "Salary must be positive"
            },
        },
        errorElement: 'span',
        errorPlacement: function (error, element) {
            error.addClass('invalid-feedback');
            element.closest('div').append(error);
        },
        highlight: function (element, errorClass, validClass) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).removeClass('is-invalid');
        }
    });
});
