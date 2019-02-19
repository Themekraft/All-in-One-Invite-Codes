<?php

/**
 * Disable invite Code
 */
function all_in_one_invite_codes_disable_code() {

	if ( ! $_POST['post_id'] ) {
		die();
	}

	$post_id = $_POST['post_id'];

	$status = get_post_meta( $post_id, 'tk_all_in_one_invite_code_status', true );

	if ( $status ) {
		$json['error'] = __( 'Used or Disabled Invite Codes can not get changed.', 'all-in-one-invite-code' );
		echo json_encode( $json );
		die();
	}
	$json['refresh'] = 'true';
	update_post_meta( $post_id, 'tk_all_in_one_invite_code_status', 'disabled' );

	echo json_encode( $json );
	die();

}

add_action( 'wp_ajax_all_in_one_invite_codes_disable_code', 'all_in_one_invite_codes_disable_code' );

/**
 * Resent the invite email
 */
function all_in_one_invite_codes_send_invite_mail() {

	if ( ! $_POST['post_id'] ) {
		die();
	}

	$post_id = $_POST['post_id'];

	$status = get_post_meta( $post_id, 'tk_all_in_one_invite_code_status', true );

	if ( $status ) {
		$json['error'] = __( 'Used or Disabled Invite Codes can not get resent.', 'all-in-one-invite-code' );
		echo json_encode( $json );
		die();
	}
	$all_in_one_invite_codes_options = get_post_meta( $post_id, 'all_in_one_invite_codes_options', true );

	if ( ! isset( $all_in_one_invite_codes_options['email'] ) || empty( $all_in_one_invite_codes_options['email'] ) ) {
		$json['error'] = __( 'This invite code does not below to any email address', 'all-in-one-invite-code' );
		echo json_encode( $json );
		die();
	}

	$json['refresh'] = 'true';
	echo json_encode( $json );
	die();
}

add_action( 'wp_ajax_all_in_one_invite_codes_send_invite_mail', 'all_in_one_invite_codes_send_invite_mail' );