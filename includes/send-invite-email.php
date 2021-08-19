<?php

/**
 * Send the invite to the user
 *
 * @since  0.1
 * @return json
 */
function all_in_one_invite_codes_send_invite() {

	if (! (is_array($_POST) && defined('DOING_AJAX') && DOING_AJAX)) {
		wp_die();
	}

	if ( ! isset($_POST['action']) || wp_verify_nonce($_POST['nonce'], 'all_in_one_invite_code_nonce') === false ) {
		wp_die();
	}

	if ( ! $_POST['post_id'] ) {
		wp_die();
	}

	$post_id = intval( $_POST['post_id'] );

	// Get the invite code
	$invite_code = get_post_meta( $post_id, 'tk_all_in_one_invite_code', true );

	$to          = sanitize_email( $_POST['to'] );
	$subject     = sanitize_text_field( $_POST['subject'] );
	$body        = sanitize_textarea_field( esc_html( $_POST['message_text'] ) );
	$headers     = array( 'Content-Type: text/html; charset=UTF-8' );
	if ( ! empty( $to ) ) {

		// Replace Buddy text Shortcodes with form element values

		// Site Name
		$site_name = get_bloginfo( 'name' );
		$subject   = all_in_one_invite_codes_replace_shortcode( $subject, '[site_name]', $site_name );
		$subject   = all_in_one_invite_codes_replace_shortcode( $subject, '[invite_code]', $invite_code );
		$body      = all_in_one_invite_codes_replace_shortcode( $body, '[site_name]', $site_name );
		$body      = all_in_one_invite_codes_replace_shortcode( $body, '[invite_code]', $invite_code );

		// Invite Link
		$buddypress_active = false;
		if(function_exists('bp_is_active')){
			$buddypress_active = true;
		}
		if ($buddypress_active || !all_in_one_invite_codes_is_default_registration() ){
			$invite_link = '<a href="' . wp_registration_url() . '?invite_code=' . $invite_code . '">Link</a>';
		}
		else{
			$invite_link = '<a href="' . wp_registration_url() . '&invite_code=' . $invite_code . '">Link</a>';
		}
		$subject     = all_in_one_invite_codes_replace_shortcode( $subject, '[invite_link]', $invite_link );
		$body        = all_in_one_invite_codes_replace_shortcode( $body, '[invite_link]', $invite_link );

		// sent the mail
		$email_param= array("to"=>$to,"subject"=>$subject,"body"=>$body,"headers"=>$headers);
		$email_param = apply_filters("all_in_one_invite_code_custom_email",$email_param);

		$send = wp_mail( $email_param["to"], $email_param["subject"] ,$email_param["body"]  ,$email_param["headers"]  );

		$all_in_one_invite_codes_options = get_post_meta( $post_id, 'all_in_one_invite_codes_options', true );

		// Assign the mail tro the code to make sure this code can only get used from the invited user.
		if ( empty( $all_in_one_invite_codes_options['email'] ) ) {
			$all_in_one_invite_codes_options['email'] = $to;
			update_post_meta( $post_id, 'all_in_one_invite_codes_options', $all_in_one_invite_codes_options );
		}

		// IF something went wrong during the sent message process
		if ( ! $send ) {
			$json['error'] = __( 'Invite could not get send. Please contact the Support.', 'all-in-one-invite-code' );
			echo json_encode( $json );
			die();
		}
		else{
			$json['message'] = __( 'Invite send out successfully', 'all-in-one-invite-code' );
			echo json_encode( $json );
			die();

		}

	}
	else{
		$json['error'] = __( 'Invite could not get send. destination email is empty.', 'all-in-one-invite-code' );
		echo json_encode( $json );
		die();

	}




}

add_action( 'wp_ajax_all_in_one_invite_codes_send_invite', 'all_in_one_invite_codes_send_invite' );
