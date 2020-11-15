$(document).ready(function () {
    var editForm = $("#edit_form");
    var validator = editForm.validate({
        rules: {
            title: {
                required: true
            },
            description: {
                required: true
            }
        },
        messages: {
            title: {
                required: "This field is required"
            },
            description: {
                required: "This field is required"
            }
        }
    });
});
