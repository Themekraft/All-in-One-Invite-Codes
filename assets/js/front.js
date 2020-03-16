jQuery(document).ready(function (jQuery) {
    jQuery(document.body).on('click', '#tk_all_in_one_invite_code_send_invite_submit', function () {

        var code_id = jQuery(this).attr('data-send_code_id');
        var to = jQuery('#tk_all_in_one_invite_code_send_invite_to').val();
        var subject = jQuery('#tk_all_in_one_invite_code_send_invite_subject').val();
        var message_text = jQuery('#tk_all_in_one_invite_code_send_invite_message_text').val();

        if (!isEmail(to)) {
            jQuery('#tk_all_in_one_invite_code_send_invite_to').css({
                "border-color": "red",
                "border-width": "1px",
                "border-style": "solid"
            });
            jQuery("#tk_all_in_one_invite_code_send_invite_to").focus();
        }

        jQuery.ajax({
            type: 'POST',
            dataType: "json",
            url: ajaxurl,
            data: {
                "action": "all_in_one_invite_codes_send_invite",
                "post_id": code_id,
                "to": to,
                "subject": subject,
                "message_text": message_text,
                "nonce": allInOneInviteCodesFrontJs.nonce,
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

        return false;

    });

    jQuery(document.body).on('click', '#tk_all_in_one_invite_code_open_invite_form', function () {
        var code_id = jQuery(this).attr('data-code_id');

        jQuery("#tk_all_in_one_invite_code_send_invite_form").appendTo("#tk_all_in_one_invite_code_open_invite_form_id_" + code_id);
        jQuery("#tk_all_in_one_invite_code_send_invite_form").show();

        jQuery("#tk_all_in_one_invite_code_send_invite_submit").attr('data-send_code_id', code_id);
    });

    function isEmail(email) {
        var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        return regex.test(email);
    }


    jQuery("#tk_all_in_one_invite_code_send_invite_to").keyup(function () {
        var email = jQuery("#tk_all_in_one_invite_code_send_invite_to").val();
        var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        if (!filter.test(email)) {
            //alert('Please provide a valid email address');
            jQuery("#tk_all_in_one_invite_code_send_invite_to_error").text(email + " is not a valid email");
            email.focus;
            //return false;
        } else {
            jQuery("#tk_all_in_one_invite_code_send_invite_to_error").text("");
        }
    });

    jQuery(document.body).on('click','#tk_all_in_one_invite_code_buddypress_create',function () {
        var code_id = jQuery('#tk_all_in_one_invite_code_modal').val();
        var email = jQuery('#all_in_one_invite_codes_options_email').val();
        var generate_codes = jQuery('#all_in_one_invite_codes_options_generate_codes').val();
        var type = jQuery('#all_in_one_invite_codes_options_type').val();
        jQuery.ajax({
            type: 'POST',
            dataType: "json",
            url: ajaxurl,
            data: {
                "action": "all_in_one_invite_codes_create_code",
                "tk_invite_code": code_id,
                "email" : email,
                "generate_codes" : generate_codes,
                "type" : type,
                "nonce": allInOneInviteCodesFrontJs.nonce,
            },
            beforeSend: function() {
                jQuery('#loader').show();
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
            },
            complete: function(){
                jQuery('#loader').hide();
            },
        });

        return false;

    })


});