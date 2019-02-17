//
// Lets do some stuff after the document is loaded
//
jQuery(document).ready(function (jQuery) {


    jQuery(document.body).on('click', '#all_in_one_disable_invite_code', function () {

        var post_id = jQuery(this).attr('data-post_id' );

        jQuery.ajax({
            type: 'POST',
            dataType: "json",
            url: ajaxurl,
            data: {
                "action": "all_in_one_invite_codes_change_code_status",
                "post_id": post_id
            },
            success: function (data) {
                console.log(data);

                if (data['error']) {
                    alert(data['error']);
                } else {
                    location.reload();
                }

            },
            error: function (error) {
                console.log(error);
            }
        });

    });

    jQuery(document.body).on('click', '#all_in_one_resent_invite_code', function () {

        var post_id = jQuery(this).attr('data-post_id');

        jQuery.ajax({
            type: 'POST',
            dataType: "json",
            url: ajaxurl,
            data: {
                "action": "all_in_one_invite_codes_send_invite_mail",
                "post_id": post_id
            },
            success: function (data) {
                console.log(data);

                if (data['error']) {
                    alert(data['error']);
                } else {
                    // location.reload();
                }

            },
            error: function (error) {
                console.log(error);
            }
        });
    });


});