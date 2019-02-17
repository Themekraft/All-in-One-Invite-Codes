<?php

add_action( 'wp_ajax_all_in_one_invite_codes_change_code_status', 'all_in_one_invite_codes_change_code_status' );
function all_in_one_invite_codes_change_code_status() {

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

	echo $post_id;
	die();

}

add_action( 'wp_ajax_all_in_one_invite_codes_send_invite_mail', 'all_in_one_invite_codes_send_invite_mail' );
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
	$json['refresh'] = 'true';
	update_post_meta( $post_id, 'tk_all_in_one_invite_code_status', 'disabled' );

	echo $post_id;
	die();

}