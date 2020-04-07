<?php

add_action( 'wp_ajax_all_in_one_invite_codes_create_code', 'all_in_one_invite_codes_create_code' );
function all_in_one_invite_codes_create_code() {

	if ( empty( $_POST['tk_invite_code'] ) ) {
		return;
	}

	$tk_invite_code = sanitize_key( trim( $_POST['tk_invite_code'] ) );


	$user_id = get_current_user_id();
	$args    = array(
		'post_type'   => 'tk_invite_codes',
		'post_author' => $user_id,
		'post_status' => 'publish',
		'post_title'  => $tk_invite_code,
	);

	$email                                                 = isset( $_POST['email'] ) ? $_POST['email'] : '';
	$generate_codes                                        = isset( $_POST['generate_codes'] ) ? $_POST['generate_codes'] : '';
	$type                                                  = isset( $_POST['type'] ) ? $_POST['type'] : 'any';
	$all_in_one_invite_codes_new_options                   = array();
	$all_in_one_invite_codes_new_options['email']          = sanitize_email( $email );
	$all_in_one_invite_codes_new_options['generate_codes'] = wp_filter_post_kses( $generate_codes );
	$all_in_one_invite_codes_new_options['$type']          = sanitize_text_field( $type );

	$new_code_id = wp_insert_post( $args );

	update_post_meta( $new_code_id, 'tk_all_in_one_invite_code', $tk_invite_code );
	update_post_meta( $new_code_id, 'tk_all_in_one_invite_code_status', 'Active' );
	update_post_meta( $new_code_id, 'all_in_one_invite_codes_options', $all_in_one_invite_codes_new_options );

	if ( ! empty( $email ) ) {

		$subject     = __( "You've Been Invited!", "all-in-one-invite-code" );
		$site_name   = get_bloginfo( 'name' );
		$invite_link = '<a href="' . wp_registration_url() . '?invite_code=' . $tk_invite_code . '">Link</a>';
		$body        = __( sprintf( "You got an invite from the %s. Please use this link to register with your invite code: %s", $site_name, $invite_link ), "all-in-one-invite-code" );

		// sent the mail
		$headers = array( 'Content-Type: text/html; charset=UTF-8' );
		$send    = wp_mail( $email, $subject, $body, $headers );
		// IF something went wrong during the sent message process
		if ( ! $send ) {
			$json['error'] = __( 'Invite could not get send. Please contact the Support.', 'all-in-one-invite-code' );
			echo json_encode( $json );
			die();
		}
	}
	$json['message'] = __( 'Invite send out successfully', 'all-in-one-invite-code' );;
	echo json_encode( $json );
	die();

}
