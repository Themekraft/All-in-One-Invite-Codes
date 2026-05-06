<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Send the invite to the user.
 *
 * Called from the frontend "Invite a Friend Now" flow on the
 * `all_in_one_invite_codes_list_codes_by_user` shortcode. The frontend can
 * be a logged-in member sending one of their own codes, so authorisation
 * is by post-ownership rather than `manage_options` (admins are also
 * allowed via `all_in_one_invite_codes_ajax_check_can_manage`).
 *
 * @since 0.1
 *
 * @return void
 */
function all_in_one_invite_codes_send_invite() {

	if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
		wp_die();
	}

	all_in_one_invite_codes_ajax_check_nonce( 'all_in_one_invite_code_nonce' );

	$post_id = isset( $_POST['post_id'] ) ? absint( wp_unslash( $_POST['post_id'] ) ) : 0;
	if ( ! $post_id ) {
		wp_die();
	}

	all_in_one_invite_codes_ajax_check_can_manage( $post_id );

	$to      = isset( $_POST['to'] ) ? sanitize_email( wp_unslash( $_POST['to'] ) ) : '';
	$subject = isset( $_POST['subject'] ) ? sanitize_text_field( wp_unslash( $_POST['subject'] ) ) : '';
	$body    = isset( $_POST['message_text'] ) ? wp_kses_post( wp_unslash( $_POST['message_text'] ) ) : '';
	$headers = array( 'Content-Type: text/html; charset=UTF-8' );

	if ( empty( $to ) ) {
		wp_send_json( array( 'error' => esc_html__( 'Invite could not be sent: the destination email is empty.', 'all-in-one-invite-codes' ) ) );
	}

	$invite_code = get_post_meta( $post_id, 'tk_all_in_one_invite_code', true );

	// Replace shortcodes with the actual values.
	$site_name = get_bloginfo( 'name' );
	$subject   = all_in_one_invite_codes_replace_shortcode( $subject, '[site_name]', $site_name );
	$subject   = all_in_one_invite_codes_replace_shortcode( $subject, '[invite_code]', $invite_code );
	$body      = all_in_one_invite_codes_replace_shortcode( $body, '[site_name]', $site_name );
	$body      = all_in_one_invite_codes_replace_shortcode( $body, '[invite_code]', $invite_code );

	// Invite Link.
	$buddypress_active = function_exists( 'bp_is_active' );
	$separator         = ( $buddypress_active || ! all_in_one_invite_codes_is_default_registration() ) ? '?' : '&';
	$invite_link       = '<a href="' . esc_url( wp_registration_url() . $separator . 'invite_code=' . $invite_code ) . '">Link</a>';

	$subject = all_in_one_invite_codes_replace_shortcode( $subject, '[invite_link]', $invite_link );
	$body    = all_in_one_invite_codes_replace_shortcode( $body, '[invite_link]', $invite_link );

	$email_param = apply_filters(
		'all_in_one_invite_code_custom_email',
		array(
			'to'      => $to,
			'subject' => $subject,
			'body'    => $body,
			'headers' => $headers,
		)
	);

	$send = wp_mail( $email_param['to'], $email_param['subject'], $email_param['body'], $email_param['headers'] );

	if ( ! $send ) {
		wp_send_json( array( 'error' => esc_html__( 'Invite could not be sent. Please contact support.', 'all-in-one-invite-codes' ) ) );
	}

	// Bind the recipient email to the code only after a successful send, so a delivery
	// failure doesn't permanently lock the code to an address it never reached.
	$all_in_one_invite_codes_options = get_post_meta( $post_id, 'all_in_one_invite_codes_options', true );
	if ( empty( $all_in_one_invite_codes_options['email'] ) ) {
		$all_in_one_invite_codes_options['email'] = $to;
		update_post_meta( $post_id, 'all_in_one_invite_codes_options', $all_in_one_invite_codes_options );
	}

	wp_send_json( array( 'message' => esc_html__( 'Invite sent successfully.', 'all-in-one-invite-codes' ) ) );
}

add_action( 'wp_ajax_all_in_one_invite_codes_send_invite', 'all_in_one_invite_codes_send_invite' );
