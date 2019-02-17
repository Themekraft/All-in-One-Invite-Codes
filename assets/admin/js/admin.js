//
// Lets do some stuff after the document is loaded
//
jQuery(document).ready(function (jQuery) {


    jQuery(document.body).on('click', '#all_in_one_disable_invite_code', function () {
        alert('binda 1');
    });

    jQuery(document.body).on('click', '#all_in_one_resent_invite_code', function () {
        alert('binda 2');
    });


});