jQuery(document).ready(function (jQuery) {

    jQuery(document.body).on('click', '#all_in_one_disable_invite_code', function () {

        var post_id = jQuery(this).attr('data-post_id');

        jQuery.ajax({
            type: 'POST',
            dataType: "json",
            url: ajaxurl,
            data: {
                "action": "all_in_one_invite_codes_disable_code",
                "nonce": allInOneInviteCodesAdminJs.nonce,
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
                "nonce": allInOneInviteCodesAdminJs.nonce,
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

    jQuery('#bulk-invite-aioic').submit(function(event){

        var currentForm = jQuery("#bulk-invite-aioic");
        var formMessage = jQuery('#form_message_aioic');
        formMessage.removeClass();
        if (jQuery.validator && !currentForm.valid()) {
            return false;
        }
        jQuery("#aioic_form_hero .form_wrapper form").LoadingOverlay("show");
        var FormData = currentForm.serialize();

        jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            dataType: 'json',
            data: {
                "action": "aioic_generate_multiple_invites",
                "data": FormData
            },
            error: function (xhr, status, error) {
                formMessage.addClass('bf-alert error');
                formMessage.html(xhr.responseText);
            },
            success: function (response) {
                jQuery.each(response, function (i, val) {

                    switch (i) {
                        case 'error':
                            formMessage.addClass('bf-alert error');
                            formMessage.html(val);
                            break;
                        case 'message':
                            formMessage.addClass('bf-alert success');
                            formMessage.html(val);
                            break;
                        case 'form_remove':
                            jQuery("#aioic_form_hero .form_wrapper").fadeOut("normal", function () {
                                jQuery("#aioic_form_hero .form_wrapper").remove();
                            });
                            break;
                    };
                    // formMessage.addClass('bf-alert success');
                    // var message =
                    //     formMessage.html(response['message']);
                });



            },
            complete: function () {

                var scrollElement = jQuery('#aioic_form_hero');
                if (scrollElement.length > 0) {
                    jQuery('html, body').animate({scrollTop: scrollElement.offset().top - 100}, {
                        duration: 500, complete: function () {
                            jQuery('html, body').on("click", function () {
                                jQuery('html, body').stop()
                            });
                        }
                    }).one("click", function () {
                        jQuery('html, body').stop()
                    });
                }
                jQuery("#aioic_form_hero .form_wrapper form").LoadingOverlay("hide");

            }



        });
        return false;
    });

});
