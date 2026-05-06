<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Read the AJAX nonce sent by the front-end and verify it against the given action.
 * Aborts the request with wp_die() on failure so callers don't have to.
 *
 * @param string $action Nonce action label.
 *
 * @return void
 */
function all_in_one_invite_codes_ajax_check_nonce( $action ) {
	$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
	if ( ! wp_verify_nonce( $nonce, $action ) ) {
		wp_send_json_error( array( 'message' => 'bad nonce' ), 403 );
	}
}

/**
 * Authorize the current user to act on a specific invite code post. Owners
 * (the post author) and admins (`manage_options`) can both manage a code.
 * Plays well with the user-facing shortcode that lets a logged-in user list
 * and revoke / resend their own codes. Aborts the AJAX request on failure.
 *
 * @param int $post_id Invite code post id.
 *
 * @return void
 */
function all_in_one_invite_codes_ajax_check_can_manage( $post_id ) {
	$post = get_post( $post_id );
	if ( ! $post || 'tk_invite_codes' !== $post->post_type ) {
		wp_send_json_error( array( 'message' => 'not found' ), 404 );
	}

	if ( current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( is_user_logged_in() && (int) $post->post_author === get_current_user_id() ) {
		return;
	}

	wp_send_json_error( array( 'message' => 'forbidden' ), 403 );
}

/**
 * Disable invite Code
 */
function all_in_one_invite_codes_disable_code() {

	if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
		wp_die();
	}

	all_in_one_invite_codes_ajax_check_nonce( 'all_in_one_invite_code_nonce' );

	$post_id = isset( $_POST['post_id'] ) ? absint( wp_unslash( $_POST['post_id'] ) ) : 0;
	if ( ! $post_id ) {
		wp_die();
	}

	all_in_one_invite_codes_ajax_check_can_manage( $post_id );

	$status = get_post_meta( $post_id, 'tk_all_in_one_invite_code_status', true );

	if ( 'Used' === $status || 'Disabled' === $status ) {
		wp_send_json( array( 'error' => esc_html__( 'Used or Disabled Invite Codes can not get changed.', 'all-in-one-invite-codes' ) ) );
	}

	update_post_meta( $post_id, 'tk_all_in_one_invite_code_status', 'disabled' );
	wp_send_json( array( 'refresh' => 'true' ) );
}

add_action( 'wp_ajax_all_in_one_invite_codes_disable_code', 'all_in_one_invite_codes_disable_code' );

/**
 * Resent the invite email
 */
function all_in_one_invite_codes_send_invite_mail() {

	if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
		wp_die();
	}

	all_in_one_invite_codes_ajax_check_nonce( 'all_in_one_invite_code_nonce' );

	$post_id = isset( $_POST['post_id'] ) ? absint( wp_unslash( $_POST['post_id'] ) ) : 0;
	if ( ! $post_id ) {
		wp_die();
	}

	all_in_one_invite_codes_ajax_check_can_manage( $post_id );

	$status = get_post_meta( $post_id, 'tk_all_in_one_invite_code_status', true );

	if ( $status ) {
		wp_send_json( array( 'error' => esc_html__( 'Used or Disabled Invite Codes can not get resent.', 'all-in-one-invite-codes' ) ) );
	}

	$all_in_one_invite_codes_options = get_post_meta( $post_id, 'all_in_one_invite_codes_options', true );

	if ( empty( $all_in_one_invite_codes_options['email'] ) ) {
		wp_send_json( array( 'error' => esc_html__( 'This invite code does not below to any email address', 'all-in-one-invite-codes' ) ) );
	}

	wp_send_json( array( 'refresh' => 'true' ) );
}

add_action( 'wp_ajax_all_in_one_invite_codes_send_invite_mail', 'all_in_one_invite_codes_send_invite_mail' );

add_action( 'wp_ajax_aioic_generate_multiple_invites', 'aioic_generate_multiple_invites' );
function aioic_generate_multiple_invites() {

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( array( 'message' => 'forbidden' ), 403 );
	}

	// The bulk-create form serialises its inputs into a single 'data' field;
	// parse_str() into a fresh array then pull values out of there.
	$form_data = array();
	if ( isset( $_POST['data'] ) ) {
		parse_str( wp_unslash( $_POST['data'] ), $form_data );
	}

	$nonce = isset( $form_data['_wpnonce'] ) ? sanitize_text_field( $form_data['_wpnonce'] ) : '';
	if ( ! wp_verify_nonce( $nonce, 'buddyforms_form_nonce' ) ) {
		wp_send_json_error( array( 'errors' => esc_html__( 'Form submit error. Please contact the site administrator.', 'all-in-one-invite-codes' ) ), 403 );
	}

	$amount             = isset( $form_data['generate_codes'] ) ? absint( $form_data['generate_codes'] ) : 0;
	$new_invites_amount = isset( $form_data['new_invites'] ) ? absint( $form_data['new_invites'] ) : 0;
	$type               = isset( $form_data['purpose'] ) ? sanitize_key( $form_data['purpose'] ) : 'any';
	$user_id            = get_current_user_id();

	for ( $i = 1; $i <= $amount; $i++ ) {
		$new_code_id = wp_insert_post( array(
			'post_type'   => 'tk_invite_codes',
			'post_author' => $user_id,
			'post_status' => 'publish',
			'post_title'  => '',
		) );

		// Create and save the new invite code as post meta.
		$code = wp_filter_post_kses( all_in_one_invite_codes_md5( $new_code_id ) );

		wp_update_post( array(
			'ID'         => $new_code_id,
			'post_title' => $code,
		) );

		update_post_meta( $new_code_id, 'tk_all_in_one_invite_code', $code );
		update_post_meta(
			$new_code_id,
			'all_in_one_invite_codes_options',
			array(
				'generate_codes' => $new_invites_amount,
				'type'           => $type,
			)
		);
		update_post_meta( $new_code_id, 'tk_all_in_one_invite_code_status', 'Active' );
	}

	wp_send_json(
		array(
			'form_remove' => 'true',
			/* translators: %d: number of invite codes generated */
			'message'     => sprintf( esc_html__( '%d Invites Codes generated successfully', 'all-in-one-invite-codes' ), $amount ),
		)
	);
}
