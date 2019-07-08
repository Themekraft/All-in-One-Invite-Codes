<?php

/**
 * Disable invite Code
 */
function all_in_one_invite_codes_disable_code() {


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

	$status = get_post_meta( $post_id, 'tk_all_in_one_invite_code_status', true );

	if ( $status == 'Used' || $status == 'Disabled' ) {
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

	if (! (is_array($_POST) && defined('DOING_AJAX') && DOING_AJAX)) {
		wp_die();
	}

	if ( ! isset($_POST['action']) || wp_verify_nonce($_POST['nonce'], 'all_in_one_invite_code_nonce') === false ) {
		wp_die();
	}

	if ( ! $_POST['post_id'] ) {
		die();
	}

	$post_id = intval( $_POST['post_id'] );

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