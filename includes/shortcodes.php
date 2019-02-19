<?php

/**
 * Create the list of codes for the user with a option to sent invites to new users.
 *
 * @since  0.1
 * @return html
 */
function all_in_one_invite_codes_list_codes( $atts ) {

	// If the user is not logged in display a login form
	if ( ! is_user_logged_in() ) {
		echo '<p>' . __( 'Please login to manage your invite codes.', 'all-in-one-invite-codes' ) . '</p>';
		wp_login_form();

		return;
	}


	// Add the js in the shortcode so we can use this more easy as Block in a later process. ?>
    <script>
        jQuery(document).ready(function (jQuery) {
			<?php echo 'var ajaxurl = "' . admin_url( 'admin-ajax.php' ) . '";' ?>
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


        });

    </script>

	<?php

	// Get the user invite codes
	$args = array(
		'author'         => get_current_user_id(),
		'posts_per_page' => - 1,
		'post_type'      => 'tk_invite_codes', //you can use also 'any'
	);

	$the_query = new WP_Query( $args );

	if ( $the_query->have_posts() ) :
		echo '<ul>';
		while ( $the_query->have_posts() ) : $the_query->the_post();
			$all_in_one_invite_codes_options = get_post_meta( get_the_ID(), 'all_in_one_invite_codes_options', true );
			$email                           = empty( $all_in_one_invite_codes_options['email'] ) ? '' : $all_in_one_invite_codes_options['email'];

			echo '<li>';
			echo 'Code: ';
			echo get_post_meta( get_the_ID(), 'tk_all_in_one_invite_code', true );
			echo '<br>Status: ';
			echo $status = all_in_one_invite_codes_get_status( get_the_ID() );
			echo '<br>';

			if ( empty( $email ) && $status == 'Active' ) {
				echo '<p><a data-code_id="' . get_the_ID() . '" id="tk_all_in_one_invite_code_open_invite_form" href="#">Invite a Friend Now</a></p><div id="tk_all_in_one_invite_code_open_invite_form_id_' . get_the_ID() . '"></div>';
			} else {
				echo '<p>' . __( 'Invite was sent to: ', 'all_in_one_invite_codes' ) . $email . '<p>';
			}

			echo '</li>';
		endwhile;
		echo '</ul>';

		$all_in_one_invite_codes_mail_templates = get_option( 'all_in_one_invite_codes_mail_templates' )

		?>

        <div style="display: none" id="tk_all_in_one_invite_code_send_invite_form">
            <p>To: <input type="email" id="tk_all_in_one_invite_code_send_invite_to" value=""><span
                        id="tk_all_in_one_invite_code_send_invite_to_error"></span></p>
            <p>Subject: <input type="text" id="tk_all_in_one_invite_code_send_invite_subject"
                               value="<?php echo empty( $all_in_one_invite_codes_mail_templates['subject'] ) ? '' : $all_in_one_invite_codes_mail_templates['subject']; ?>">
            </p>
            <p>Message Text:<textarea cols="70" rows="5"
                                      id="tk_all_in_one_invite_code_send_invite_message_text"><?php echo empty( $all_in_one_invite_codes_mail_templates['message_text'] ) ? '' : $all_in_one_invite_codes_mail_templates['message_text']; ?></textarea>
            </p>
            <a href="#" data-send_code_id="0" id="tk_all_in_one_invite_code_send_invite_submit" class="button">Send</a>
        </div>

	<?php

	endif;

	wp_reset_postdata();

}

add_shortcode( 'all_in_one_invite_codes_list_codes_by_user', 'all_in_one_invite_codes_list_codes' );