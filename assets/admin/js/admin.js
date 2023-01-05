jQuery(document).ready(function (jQuery) {

    jQuery(document).on('click','#all_in_one_invite_codes_options_multiple_use',this,function(event){


        if(this.checked){
            jQuery("#label_single_use").hide();
            jQuery("#label_multiple_use").show();
        }
        else{
            jQuery("#label_single_use").show();
            jQuery("#label_multiple_use").hide();
        }



    })

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

    jQuery('a.aioic-go-pro').css('color', '#fca300' );
    jQuery('a.aioic-go-pro').parent().insertAfter('#menu-posts-tk_invite_codes > ul > li:last-child');

    jQuery( ".bundle-list-see-more" ).click(function() {
        jQuery(".show-more").animate({
            height: "1100"
        });
        jQuery(".tk-bundle-2").height(620);
        jQuery(".separator, .bundle-list-see-more").hide();
    });

    jQuery('#purchase-2').on('click', function (e) {

        var handler = FS.Checkout.configure({
            plugin_id:  '8013',
            plan_id:    '13146',
            public_key: 'pk_b8b8e319fd537d6d44d73a448f64e',
            image:      '//s3-us-west-2.amazonaws.com/freemius/plugins/8013/icons/c505ef3d7561464423bc8498e8878783.png'
        });
        
        handler.open({
            name     : 'ThemeKraft Bundle',
            licenses : jQuery('#licenses-2').val(),
            purchaseCompleted  : function (response) {
            },
            success  : function (response) {
            }
        });
        e.preventDefault();
    });

    jQuery('#purchase-3').on('click', function (e) {

        var handler = FS.Checkout.configure({
            plugin_id:  '2046',
            plan_id:    '4316',
            public_key: 'pk_ee958df753d34648b465568a836aa',
            image:      '//s3-us-west-2.amazonaws.com/freemius/plugins/2046/icons/2921156b0159ff6ef809b152449e6aa9.jpg'
        });
        
        handler.open({
            name     : 'ThemeKraft Bundle',
            licenses : jQuery('#licenses-3').val(),
            purchaseCompleted  : function (response) {
            },
            success  : function (response) {
            }
        });
        e.preventDefault();
    });

    jQuery("select#licenses-2").change(function () {
        var selectedLicense = jQuery(this).children("option:selected").val();
        if( selectedLicense == '1'){
            jQuery('.fs-bundle-price-2').text('39.99');
            jQuery('#savings-price-2').text('119.97');
        }
        if( selectedLicense == '5'){
            jQuery('.fs-bundle-price-2').text('69.99');
            jQuery('#savings-price-2').text('199.84');
        }
        if( selectedLicense == 'unlimited'){
            jQuery('.fs-bundle-price-2').text('99.99');
            jQuery('#savings-price-2').text('219.99');
        }
    });

    jQuery("select#licenses-3").change(function () {
        var selectedLicense = jQuery(this).children("option:selected").val();
        if( selectedLicense == '1'){
            jQuery('.fs-bundle-price-3').text('99.99');
            jQuery('#savings-price-3').text('602.75');
        }
        if( selectedLicense == '5'){
            jQuery('.fs-bundle-price-3').text('119.99');
            jQuery('#savings-price-3').text('965.75');
        }
        if( selectedLicense == 'unlimited'){
            jQuery('.fs-bundle-price-3').text('129.99');
            jQuery('#savings-price-3').text('1168.76');
        }
    });

});
