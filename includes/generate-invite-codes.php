<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_ajax_all_in_one_invite_codes_create_code', 'all_in_one_invite_codes_create_code' );
function all_in_one_invite_codes_create_code() {

	if ( ! is_user_logged_in() ) {
		wp_send_json_error( array( 'message' => 'forbidden' ), 403 );
	}

	$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
	if ( ! wp_verify_nonce( $nonce, 'all_in_one_invite_code_nonce' ) ) {
		wp_send_json_error( array( 'message' => 'bad nonce' ), 403 );
	}

	if ( empty( $_POST['tk_invite_code'] ) ) {
		return;
	}

	$tk_invite_code = sanitize_key( trim( wp_unslash( $_POST['tk_invite_code'] ) ) );

	$user_id = get_current_user_id();
	$args    = array(
		'post_type'   => 'tk_invite_codes',
		'post_author' => $user_id,
		'post_status' => 'publish',
		'post_title'  => $tk_invite_code,
	);

	$email          = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$generate_codes = isset( $_POST['generate_codes'] ) ? absint( wp_unslash( $_POST['generate_codes'] ) ) : 0;
	$type           = isset( $_POST['type'] ) ? sanitize_key( wp_unslash( $_POST['type'] ) ) : 'any';

	switch ( $type ) {
		case 'any':
		case 'register':
			$message_text = 'message_text';
			break;
		default:
			$message_text = $type;
			break;
	}

	$all_in_one_invite_codes_new_options                   = array();
	$all_in_one_invite_codes_new_options['email']          = $email;
	$all_in_one_invite_codes_new_options['generate_codes'] = $generate_codes;
	$all_in_one_invite_codes_new_options['type']           = $type;

	$new_code_id = wp_insert_post( $args );

	update_post_meta( $new_code_id, 'tk_all_in_one_invite_code', $tk_invite_code );
	update_post_meta( $new_code_id, 'tk_all_in_one_invite_code_status', 'Active' );
	update_post_meta( $new_code_id, 'all_in_one_invite_codes_options', $all_in_one_invite_codes_new_options );

	if ( ! empty( $email ) ) {
		$all_in_one_invite_codes_mail_templates = get_option( 'all_in_one_invite_codes_mail_templates' );
		$subject   = isset( $all_in_one_invite_codes_mail_templates['subject'] )
			? wp_kses_post( $all_in_one_invite_codes_mail_templates['subject'] )
			: __( "You've Been Invited!", 'all-in-one-invite-codes' );
		$body      = isset( $all_in_one_invite_codes_mail_templates[ $message_text ] )
			? wp_kses_post( $all_in_one_invite_codes_mail_templates[ $message_text ] )
			: __( 'You got an invite from the site [site_name]. Please use this link to register with your invite code [invite_link]', 'all-in-one-invite-codes' );
		$site_name = get_bloginfo( 'name' );
		$subject   = all_in_one_invite_codes_replace_shortcode( $subject, '[site_name]', $site_name );
		$subject   = all_in_one_invite_codes_replace_shortcode( $subject, '[invite_code]', $tk_invite_code );

		$body = all_in_one_invite_codes_replace_shortcode( $body, '[site_name]', $site_name );
		$body = all_in_one_invite_codes_replace_shortcode( $body, '[invite_code]', $tk_invite_code );

		// Invite Link.
		$buddypress_active = function_exists( 'bp_is_active' );
		$separator         = ( $buddypress_active || ! all_in_one_invite_codes_is_default_registration() ) ? '?' : '&';
		$invite_link       = '<a href="' . esc_url( wp_registration_url() . $separator . 'invite_code=' . $tk_invite_code ) . '">Link</a>';

		$subject = all_in_one_invite_codes_replace_shortcode( $subject, '[invite_link]', $invite_link );
		$body    = all_in_one_invite_codes_replace_shortcode( $body, '[invite_link]', $invite_link );

		$headers     = array( 'Content-Type: text/html; charset=UTF-8' );
		$email_param = apply_filters(
			'all_in_one_invite_code_custom_email',
			array(
				'to'      => $email,
				'subject' => $subject,
				'body'    => $body,
				'headers' => $headers,
			)
		);
		$send = wp_mail( $email_param['to'], $email_param['subject'], $email_param['body'], $email_param['headers'] );

		if ( ! $send ) {
			wp_send_json( array( 'error' => esc_html__( 'Invite could not get send. Please contact the Support.', 'all-in-one-invite-codes' ) ) );
		}
	}

	wp_send_json( array( 'message' => esc_html__( 'Invite send out successfully', 'all-in-one-invite-codes' ) ) );
}
