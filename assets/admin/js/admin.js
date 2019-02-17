//
// Lets do some stuff after the document is loaded
//
jQuery(document).ready(function (jQuery) {


    jQuery(document.body).on('click', '#all_in_one_disable_invite_code', function () {
        alert('binda 1');

        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                "action": "all_in_one_invite_codes_change_code_status",
                "code_id": '22'
            },
            success: function (data) {
                console.log(data);
            },
            error: function (error) {
                console.log(error);
            }
        });

    });

    jQuery(document.body).on('click', '#all_in_one_resent_invite_code', function () {
        alert('binda 2');
    });


});