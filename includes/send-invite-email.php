<?php

/**
 * Send the invite to the user
 *
 * @since  0.1
 * @return json
 */
function all_in_one_invite_codes_send_invite() {

	// Get the invite code
	$invite_code = get_post_meta( intval( $_POST['post_id'] ), 'tk_all_in_one_invite_code', true );

	$to          = sanitize_email( $_POST['to'] );
	$subject     = sanitize_text_field( $_POST['subject'] );
	$body        = sanitize_textarea_field( esc_html( $_POST['message_text'] ) );
	$headers     = array( 'Content-Type: text/html; charset=UTF-8' );
	$invite_code = $invite_code;

	// Replace Buddy text Shortcodes with form element values

	// Site Name
	$site_name = get_bloginfo( 'name' );
	$subject   = all_in_one_invite_codes_replace_shortcode( $subject, '[site_name]', $site_name );
	$body      = all_in_one_invite_codes_replace_shortcode( $body, '[site_name]', $site_name );

	// Invite Link
	$invite_link = '<a href="' . wp_registration_url() . '&invite_code=' . $invite_code . '">Link</a>';
	$subject     = all_in_one_invite_codes_replace_shortcode( $subject, '[invite_link]', $invite_link );
	$body        = all_in_one_invite_codes_replace_shortcode( $body, '[invite_link]', $invite_link );

	// sent the mail
	$send = wp_mail( $to, $subject, $body, $headers );


	$post_id = intval( $_POST['post_id'] );

	$all_in_one_invite_codes_options = get_post_meta( $post_id, 'all_in_one_invite_codes_options', true );

	// Assign the mail tro the code to make sure this code can only get used from the invited user.
	if ( empty( $all_in_one_invite_codes_options['email'] ) ) {
		$all_in_one_invite_codes_options['email'] = $to;
		update_post_meta( $post_id, 'all_in_one_invite_codes_options', $all_in_one_invite_codes_options );
	}

	// IF something went wrong during the sent message process
	if ( ! $send ) {
		$json['error'] = __( 'Invite could not get send. please contact the Support.', 'all-in-one-invite-code' );
		echo json_encode( $json );
		die();
	}

	// Great, all done message got sent
	$json['message'] = __( 'Invite send successfully', 'buddyforms' );;

	echo json_encode( $json );
	die();
}

add_action( 'wp_ajax_all_in_one_invite_codes_send_invite', 'all_in_one_invite_codes_send_invite' );