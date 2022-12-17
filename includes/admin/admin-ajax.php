<?php

/**
 * Disable invite Code
 */
function all_in_one_invite_codes_disable_code() {

	if ( ! ( is_array( $_POST ) && defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
		wp_die();
	}

	if ( ! isset( $_POST['action'] ) || wp_verify_nonce( $_POST['nonce'], 'all_in_one_invite_code_nonce' ) === false ) {
		wp_die();
	}

	if ( ! isset( $_POST['post_id'] ) ) {
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

	if ( ! ( is_array( $_POST ) && defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
		wp_die();
	}

	if ( ! isset( $_POST['action'] ) || wp_verify_nonce( $_POST['nonce'], 'all_in_one_invite_code_nonce' ) === false ) {
		wp_die();
	}

	if ( ! isset( $_POST['post_id'] ) ) {
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

add_action( 'wp_ajax_aioic_generate_multiple_invites', 'aioic_generate_multiple_invites' );
function aioic_generate_multiple_invites() {
	$form_data = array();
	if ( isset( $_POST['data'] ) ) {
		parse_str( filter_var ( $_POST['data'], FILTER_SANITIZE_STRING), $form_data );
		$_POST = $form_data;
	}
	$json_array = array();

	// Check nonce
	$buddyforms_form_nonce_value = $_POST['_wpnonce'];

	$nonce_result = wp_verify_nonce( $buddyforms_form_nonce_value, 'buddyforms_form_nonce' );

	if ( ! $nonce_result ) {

		header( 'Content-type: application/json' );

		echo wp_json_encode( array( 'errors' => __( 'Form submit error. Please contact the site administrator.', 'buddyforms' ) ) );
		die;
	}
	$invite_count = 0;
	$amount       = isset( $form_data['generate_codes'] ) ? $form_data['generate_codes'] : 0;
	$type         = isset( $form_data['purpose'] ) ? $form_data['purpose'] : 'any';
	$user_id      = get_current_user_id();
	for ( $i = 1; $i <= $amount; $i ++ ) {

		$args        = array(
			'post_type'   => 'tk_invite_codes',
			'post_author' => $user_id,

			'post_status' => 'publish',
			'post_title'  => '',
		);
		$new_code_id = wp_insert_post( $args );

		// Create and save the new invite code as post meta
		$code = all_in_one_invite_codes_md5( $new_code_id );
		$code = wp_filter_post_kses( $code );

		$args = array(
			'ID'         => $new_code_id,
			'post_title' => $code,
		);
		wp_update_post( $args );

		update_post_meta( $new_code_id, 'tk_all_in_one_invite_code', $code );

		$all_in_one_invite_codes_new_options['generate_codes'] = 0;
		$all_in_one_invite_codes_new_options['type']           = $type;
		update_post_meta( $new_code_id, 'all_in_one_invite_codes_options', $all_in_one_invite_codes_new_options );

		update_post_meta( $new_code_id, 'tk_all_in_one_invite_code_status', 'Active' );
	}

	$json['form_remove'] = 'true';

	$json['message'] = __( $amount . ' Invites Codes  generated successfully', 'all-in-one-invite-code' );

	echo json_encode( $json );
	die();

}
